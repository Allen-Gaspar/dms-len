<?php
require_once __DIR__ . '/../core/auth.php';
$user = require_login();
$db   = get_db();
$uid  = (int)$user['id'];
$role = $user['role'];

date_default_timezone_set('Asia/Manila');

$offset = max(0, (int)($_GET['offset'] ?? 0));
$limit  = ROWS_PER_PAGE;
$search = trim($_GET['search'] ?? '');
$fileType = strtolower(trim($_GET['type'] ?? ''));
$folderId = (int)($_GET['folder_id'] ?? 0);
$like = '%' . $search . '%';
$typeLike = $fileType !== '' ? '%.' . $fileType : '%';

$folder = null;
if ($folderId > 0) {
    $fs = $db->prepare('SELECT DISTINCT f.* FROM folders f LEFT JOIN folder_shares fs ON fs.folder_id=f.id AND fs.shared_with_user_id=? WHERE f.id=? AND f.is_private=1 AND (f.created_by=? OR fs.shared_with_user_id IS NOT NULL) LIMIT 1');
    $fs->bind_param('iii', $uid, $folderId, $uid);
    $fs->execute();
    $folder = $fs->get_result()->fetch_assoc();
    if (!$folder) $folderId = 0;
}

$folderSql = $folderId > 0 ? ' AND d.folder_id=?' : '';
$countSql = "SELECT COUNT(DISTINCT d.id)
             FROM documents d
             LEFT JOIN document_shares ds ON ds.document_id=d.id AND ds.shared_with_user_id=?
             WHERE d.is_deleted=0 AND d.is_private=1
               AND (d.uploaded_by=? OR ds.shared_with_user_id IS NOT NULL)
               AND d.filename LIKE ? AND d.filename LIKE ? $folderSql";
$countStmt = $db->prepare($countSql);
if ($folderId > 0) $countStmt->bind_param('iissi', $uid, $uid, $like, $typeLike, $folderId);
else $countStmt->bind_param('iiss', $uid, $uid, $like, $typeLike);
$countStmt->execute();
$total = (int)$countStmt->get_result()->fetch_row()[0];

$sql = "SELECT DISTINCT d.*, u.username AS uploader_name, ur.role AS uploader_role, lk.username AS locker_name, lk.role AS locker_role
        FROM documents d
        LEFT JOIN document_shares ds ON ds.document_id=d.id AND ds.shared_with_user_id=?
        LEFT JOIN users u ON u.id=d.uploaded_by
        LEFT JOIN users ur ON ur.id=d.uploaded_by
        LEFT JOIN users lk ON lk.id=d.locked_by
        WHERE d.is_deleted=0 AND d.is_private=1
          AND (d.uploaded_by=? OR ds.shared_with_user_id IS NOT NULL)
          AND d.filename LIKE ? AND d.filename LIKE ? $folderSql
        ORDER BY d.created_at DESC LIMIT ? OFFSET ?";
$stmt = $db->prepare($sql);
if ($folderId > 0) $stmt->bind_param('iissiii', $uid, $uid, $like, $typeLike, $folderId, $limit, $offset);
else $stmt->bind_param('iissii', $uid, $uid, $like, $typeLike, $limit, $offset);
$stmt->execute();
$documents = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$folders = $db->prepare('SELECT DISTINCT f.* FROM folders f LEFT JOIN folder_shares fs ON fs.folder_id=f.id AND fs.shared_with_user_id=? WHERE f.is_private=1 AND (f.created_by=? OR fs.shared_with_user_id IS NOT NULL) ORDER BY f.name');
$folders->bind_param('ii', $uid, $uid);
$folders->execute();
$privateFolders = $folders->get_result()->fetch_all(MYSQLI_ASSOC);

$typesRes = $db->prepare("SELECT DISTINCT LOWER(SUBSTRING_INDEX(filename,'.',-1)) AS ext FROM documents WHERE is_deleted=0 AND is_private=1 AND uploaded_by=? AND filename LIKE '%.%' ORDER BY ext");
$typesRes->bind_param('i', $uid);
$typesRes->execute();
$fileTypes = $typesRes->get_result()->fetch_all(MYSQLI_ASSOC);

function format_bytes(int $b): string {
    if ($b >= 1048576) return round($b/1048576, 1) . ' MB';
    if ($b >= 1024) return round($b/1024, 1) . ' KB';
    return $b . ' B';
}

$page_title = $folder ? 'Private: ' . $folder['name'] : 'Private Files';
include APP_ROOT . '/partials/header.php';
?>

<div class="doc-actions-bar private-page-actions">
  <div class="private-actions">
    <button type="button" class="btn btn-primary" onclick="DMS.openUploadModal(true, '<?= $folderId > 0 ? (int)$folderId : 'root' ?>')">Upload Private File</button>
    <button type="button" class="btn btn-outline" onclick="DMS.openUploadModal(true, '<?= $folderId > 0 ? (int)$folderId : 'root' ?>'); document.querySelector('[data-tab=\'folder\']')?.click();">Create Private Folder</button>
    <?php if ($folder): ?><a class="btn btn-outline" href="<?= page_url('private.php') ?>">Back to Private</a><?php endif; ?>
  </div>
  <form method="GET" class="doc-search-box suggest-box-wrap">
    <?php if ($folderId > 0): ?><input type="hidden" name="folder_id" value="<?= (int)$folderId ?>"><?php endif; ?>
    <input type="text" id="privateFileSearch" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search private files..." autocomplete="off">
    <select name="type" class="table-filter-select" onchange="this.form.submit()">
      <option value="">All file types</option>
      <?php foreach ($fileTypes as $t): $ext = $t['ext'] ?? ''; if ($ext === '') continue; ?>
        <option value="<?= htmlspecialchars($ext) ?>" <?= $fileType === $ext ? 'selected' : '' ?>><?= strtoupper(htmlspecialchars($ext)) ?></option>
      <?php endforeach; ?>
    </select>
    <div id="privateFileSuggest" class="suggestion-dropdown"></div>
    <button type="submit" class="btn btn-secondary">Search</button>
  </form>
</div>

<?php if (!$folder): ?>
<section class="private-folder-panel">
  <div class="private-folder-header">
    <div><h3 class="section-title">Private Folders</h3><p>Only you can see these folders unless you share access to files.</p></div>
    <div class="suggest-box-wrap private-folder-search">
      <input type="text" id="privateFolderSearch" placeholder="Search folders..." autocomplete="off">
      <div id="privateFolderSuggest" class="suggestion-dropdown"></div>
    </div>
  </div>
  <?php if ($privateFolders): ?>
    <div class="private-folder-grid" id="privateFolderGrid">
      <?php foreach ($privateFolders as $f): ?>
        <a href="<?= page_url('private.php?folder_id=' . (int)$f['id']) ?>" class="private-folder-card" data-folder-name="<?= htmlspecialchars(strtolower($f['name'])) ?>">
          <span class="private-folder-lock" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="10" rx="2"></rect><path d="M7 11V8a5 5 0 0 1 10 0v3"></path></svg></span>
          <span class="private-folder-name"><?= htmlspecialchars($f['name']) ?></span>
          <small>Private folder</small>
        </a>
      <?php endforeach; ?>
    </div>
  <?php else: ?><div class="empty-row private-empty">No private folders yet.</div><?php endif; ?>
</section>
<?php endif; ?>

<table class="data-table file-table">
  <thead><tr><th>Filename</th><th>Type</th><th>Version</th><th>Size</th><th>Uploaded By</th><th>Status</th><th class="date-col">Date & Time</th><th>Actions</th></tr></thead>
  <tbody>
  <?php if (empty($documents)): ?><tr><td colspan="8" class="empty-row">No private files.</td></tr><?php endif; ?>
  <?php foreach ($documents as $doc):
    $locker_role = $doc['locker_role'] ?? 'user';
    $can_write = $role !== 'casual' && (int)$doc['uploaded_by'] === $uid;
  ?>
    <tr data-private-file-name="<?= htmlspecialchars(strtolower($doc['filename'])) ?>">
      <td><button type="button" class="file-name-link" onclick="DMS.openFileDetail(<?= (int)$doc['id'] ?>)"><?= htmlspecialchars($doc['filename']) ?></button></td>
      <td><span class="file-type-pill"><?= strtoupper(htmlspecialchars(pathinfo($doc['filename'] ?? '', PATHINFO_EXTENSION) ?: 'FILE')) ?></span></td>
      <td><button type="button" class="version-link" onclick="DMS.openFileDetail(<?= (int)$doc['id'] ?>)">v<?= (int)($doc['version'] ?? 1) ?></button></td>
      <td><?= format_bytes((int)$doc['size']) ?></td>
      <td><?= htmlspecialchars($doc['uploader_name'] ?? '-') ?></td>
      <td><?= !empty($doc['is_locked']) ? '<span class="badge badge-warn">Locked</span>' : '<span class="badge badge-ok">Available</span>' ?></td>
      <td class="date-col"><?= htmlspecialchars(substr($doc['created_at'], 0, 10)) ?><span class="timestamp-time"><?= htmlspecialchars(date('h:i A', strtotime($doc['created_at']))) ?></span></td>
      <td><div class="actions-container nowrap">
        <?php if ($can_write): ?>
          <button class="btn-icon-sm btn-primary" title="Rename" onclick="DMS.renameFile(<?= (int)$doc['id'] ?>, '<?= htmlspecialchars(addslashes($doc['filename'])) ?>')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"></path></svg>
          </button>
        <?php endif; ?>
        <?php if ($role !== 'casual' && (int)$doc['uploaded_by'] === $uid): ?>
          <button type="button" class="btn-icon-sm" style="background:#6366f1;color:#fff;text-decoration:none" title="Share" onclick="openShareModal(<?= (int)$doc['id'] ?>, '<?= htmlspecialchars(addslashes($doc['filename'])) ?>')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path><path d="m16 6-4-4-4 4"></path><path d="M12 2v13"></path></svg>
          </button>
        <?php endif; ?>
        <button class="btn-icon-sm btn-outline" title="Download" onclick="DMS.confirm('Download file','Choose where to save <?= htmlspecialchars(addslashes($doc['filename'])) ?>?', ()=>DMS.downloadFile(<?= (int)$doc['id'] ?>, '<?= htmlspecialchars(addslashes($doc['filename'])) ?>'))">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><path d="m7 10 5 5 5-5"></path><path d="M12 15V3"></path></svg>
        </button>
        <?php if ($role !== 'casual'): ?>
          <?php if (empty($doc['is_locked'])): ?>
            <button class="btn-icon-sm btn-warn" title="Lock" onclick="DMS.confirm('Lock file','Lock this file for editing?', ()=>location.href='<?= app_url('api/version_control.php?action=checkout&origin=private&id=' . (int)$doc['id'] . '&folder_id=' . (int)$folderId) ?>')">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="10" rx="2"></rect><path d="M7 11V8a5 5 0 0 1 10 0v3"></path></svg>
            </button>
          <?php elseif ((int)$doc['locked_by'] === $uid): ?>
            <button class="btn-icon-sm btn-ok" title="Unlock" onclick="DMS.confirm('Unlock file','Release the lock on this file?', ()=>location.href='<?= app_url('api/version_control.php?action=checkin&origin=private&id=' . (int)$doc['id'] . '&folder_id=' . (int)$folderId) ?>')">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="10" rx="2"></rect><path d="M7 11V8a5 5 0 0 1 9.8-1.4"></path></svg>
            </button>
          <?php endif; ?>
          <?php if ($can_write): ?><button class="btn-icon-sm btn-danger" title="Delete" data-confirm="Move to trash?" data-form="del-<?= (int)$doc['id'] ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M8 6V4h8v2"></path><path d="m19 6-1 14H6L5 6"></path></svg></button><?php endif; ?>
        <?php endif; ?>
        <form method="POST" action="<?= app_url('api/delete.php?id=' . (int)$doc['id'] . '&origin=private') ?>" id="del-<?= (int)$doc['id'] ?>" style="display:none"></form>
      </div></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<?= Pagination::render($total, $offset, $limit, page_url('private.php'), ['search' => $search, 'type' => $fileType, 'folder_id' => $folderId ?: '']) ?>

<div id="shareModal" class="modal-overlay">
  <div class="modal-card modal-lg">
    <button class="modal-close" onclick="DMS.closeModal('shareModal')">&times;</button>
    <h3>Share Document</h3>
    <p id="shareDocName"></p>
    <input type="hidden" id="shareDocId">
    <div class="form-group">
      <label>User email</label>
      <div class="share-email-row">
        <input type="email" id="shareUserEmail" placeholder="name@example.com" autocomplete="off">
        <button type="button" class="btn btn-secondary" onclick="submitShare()">Search & Share</button>
      </div>
    </div>
    <div class="perm-grid">
      <label><input type="checkbox" id="sh_all" onchange="toggleShareAll(this)"> Select all</label>
      <label><input type="checkbox" id="sh_add"> Add</label>
      <label><input type="checkbox" id="sh_edit"> Edit</label>
      <label><input type="checkbox" id="sh_delete"> Delete</label>
      <label><input type="checkbox" id="sh_download" checked> Download</label>
      <label><input type="checkbox" id="sh_checkout"> Lock/Unlock</label>
      <label><input type="checkbox" id="sh_share"> Share</label>
    </div>
    <div class="modal-actions">
      <button class="btn btn-outline" onclick="DMS.closeModal('shareModal')">Cancel</button>
      <button class="btn btn-primary" onclick="submitShare()">Share</button>
    </div>
  </div>
</div>

<script>
function openShareModal(id, name) {
  document.getElementById('shareDocId').value = id;
  document.getElementById('shareDocName').textContent = name;
  document.getElementById('shareUserEmail').value = '';
  ['sh_all','sh_add','sh_edit','sh_delete','sh_checkout','sh_share'].forEach(id => document.getElementById(id).checked = false);
  document.getElementById('sh_download').checked = true;
  DMS.openModal('shareModal');
}
function toggleShareAll(master) {
  ['sh_add','sh_edit','sh_delete','sh_download','sh_checkout','sh_share'].forEach(id => document.getElementById(id).checked = master.checked);
}
function submitShare() {
  const email = document.getElementById('shareUserEmail').value.trim();
  if (!email) { DMS.toast('Type the user email first', 'error'); return; }
  DMS.showLoading();
  fetch('<?= app_url('api/share.php?action=grant_direct_access') ?>', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({
      document_id: document.getElementById('shareDocId').value,
      email: email,
      can_add: document.getElementById('sh_add').checked ? 1 : 0,
      can_download: document.getElementById('sh_download').checked ? 1 : 0,
      can_edit: document.getElementById('sh_edit').checked ? 1 : 0,
      can_delete: document.getElementById('sh_delete').checked ? 1 : 0,
      can_checkout: document.getElementById('sh_checkout').checked ? 1 : 0,
      can_share: document.getElementById('sh_share').checked ? 1 : 0
    })
  }).then(r=>r.json()).then(d => {
    if (d.success) { DMS.toast('Shared successfully'); DMS.closeModal('shareModal'); }
    else DMS.toast(d.message || 'Share failed', 'error');
  }).finally(() => DMS.hideLoading());
}
document.addEventListener('DOMContentLoaded', () => {
  const folderInput = document.getElementById('privateFolderSearch');
  const folderSuggest = document.getElementById('privateFolderSuggest');
  const folderCards = Array.from(document.querySelectorAll('.private-folder-card'));
  if (folderInput && folderSuggest) {
    folderInput.addEventListener('input', () => {
      const q = folderInput.value.trim().toLowerCase();
      const matches = folderCards.filter(card => (card.dataset.folderName || '').includes(q));
      folderCards.forEach(card => { card.style.display = !q || matches.includes(card) ? '' : 'none'; });
      folderSuggest.innerHTML = matches.slice(0, 6).map(card => `<button type="button" class="suggestion-item" onclick="location.href='${card.href}'">${card.querySelector('.private-folder-name')?.textContent || 'Folder'}</button>`).join('') || '<button type="button" class="suggestion-item">No folders found.</button>';
      folderSuggest.style.display = q ? 'block' : 'none';
    });
  }
});
</script>
<?php include APP_ROOT . '/partials/footer.php'; ?>
