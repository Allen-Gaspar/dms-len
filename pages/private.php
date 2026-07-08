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
          <button type="button" class="btn-icon-sm btn-primary" title="Edit File Details" onclick="openMasterEditModal(<?= htmlspecialchars(json_encode($doc)) ?>)">
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

<div id="masterEditModal" class="modal-overlay">
  <div class="modal-wrapper-card">
    <button type="button" class="modal-close" onclick="closeMasterEditModal()" aria-label="Close">&times;</button>
    <h3 id="m_title" style="margin: 0 0 5px 0; color: #1e293b; font-size: 20px; font-weight: bold;">Edit File</h3>
    <p id="m_attribution" style="margin: 0 0 25px 0; color: #64748b; font-size: 13px; font-family: sans-serif; display: flex; align-items: center; gap: 6px;"></p>

    <div class="edit-rename-row">
      <div class="form-group" style="margin:0; flex:1;">
        <label>Rename File</label>
        <input type="text" id="m_rename_filename" placeholder="Filename">
      </div>
      <button type="button" class="btn btn-outline" onclick="renameFromEditModal()">Rename</button>
    </div>

    <div style="display: flex; flex-direction: column; gap: 25px;">
        <div style="border-top: 1px solid #f1f5f9; padding-top: 15px;">
            <h4 style="margin: 0 0 12px 0; font-size: 13px; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; font-weight: bold;">1. Version Upload & File Preview Comparison</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                <div>
                  <small style="color: #64748b; font-weight: bold; display: block; margin-bottom: 4px; text-transform: uppercase; font-size: 11px;">Active Source File</small>
                  <div id="p_old" class="preview-box-split"></div>
                </div>
                <div>
                  <small style="color: #64748b; font-weight: bold; display: block; margin-bottom: 4px; text-transform: uppercase; font-size: 11px;">New Preview</small>
                  <div id="p_new" class="preview-box-split" style="color: #94a3b8; font-size: 12px; font-style: italic; text-align: center;">No preview yet.</div>
                </div>
            </div>

            <form id="m_upload_form" method="POST" action="<?= app_url('api/version_control.php?action=commit_revision') ?>" enctype="multipart/form-data" style="margin: 0;">
                <input type="hidden" name="document_id" id="m_doc_id">
                <label style="display: block; background: #0284c7; color: #fff; padding: 12px; border-radius: 6px; font-weight: bold; font-size: 13px; cursor: pointer; text-align: center; margin-bottom: 10px; transition: background 0.2s;" onmouseover="this.style.background='#0369a1'" onmouseout="this.style.background='#0284c7'">
                    Choose New File
                    <input type="file" name="revised_document" id="m_file_input" style="display: none;" onchange="renderCompareViewDelta(this)">
                </label>
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; margin-top: 15px; font-weight: bold; background: #16a34a; border: none; color: #fff; border-radius: 6px; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">Apply Changes</button>
            </form>
        </div>

        <div style="border-top: 1px solid #f1f5f9; padding-top: 15px;">
            <h4 style="margin: 0 0 12px 0; font-size: 13px; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; font-weight: bold;">2. Version History</h4>
            <div id="m_history_list" style="max-height: 160px; overflow-y: auto; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; display: flex; flex-direction: column; gap: 8px; box-shadow: inset 0 2px 4px rgba(0,0,0,0.01);"></div>
        </div>
    </div>
  </div>
</div>

<script>
window.DMS = window.DMS || {
    toast: function(msg, type = 'success') {
        const t = document.createElement('div');
        t.style.cssText = `position:fixed; bottom:20px; right:20px; padding:12px 24px; background:${type==='error'?'#ef4444':'#10b981'}; color:#fff; font-weight:bold; border-radius:6px; z-index:99999; box-shadow:0 4px 12px rgba(0,0,0,0.15); font-family:sans-serif; font-size:13px;`;
        t.textContent = msg;
        document.body.appendChild(t);
        setTimeout(() => t.remove(), 2800);
    },
    confirm: function(title, text, callback) {
        if (window.confirm(`${title}\n\n${text}`)) callback();
    },
    openModal: function(id) {
        const m = document.getElementById(id);
        if (m) { m.classList.add('is-active'); document.body.style.overflow = 'hidden'; }
    },
    closeModal: function(id) {
        const m = document.getElementById(id);
        if (m) { m.classList.remove('is-active'); document.body.style.overflow = ''; }
    },
    openUploadModal: function(show, folderId) {
        const url = new URL('<?= page_url('admin_dashboard.php') ?>');
        url.searchParams.set('open_upload', '1');
        url.searchParams.set('folder_id', folderId);
        window.location.href = url.toString();
    },
    downloadFile: function(id, filename) {
        window.location.href = `<?= app_url('api/download.php?id=') ?>` + id;
    },
    openFileDetail: function(id) {
        openMasterEditModal({ id: id, filename: 'Fetching metadata...' });
        fetch('<?= app_url('api/version_control.php?action=get_history&id=') ?>' + id)
        .then(res => res.json())
        .then(v => {
            if (v && v.length > 0) {
                document.getElementById('m_title').textContent = "File Details: " + v[0].filename;
                document.getElementById('m_rename_filename').value = v[0].filename;
            }
        });
    },
    renameFileTo: function(id, nextName, currentName) {
        if (!nextName || nextName.trim() === '') { this.toast('Name cannot be empty', 'error'); return; }
        fetch('<?= app_url('api/rename.php') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${id}&filename=${encodeURIComponent(nextName)}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                this.toast('File renamed successfully');
                setTimeout(() => window.location.reload(), 600);
            } else {
                this.toast(data.message || 'Rename failed', 'error');
            }
        });
    },
    previewVersion: function(url, ext, title) {
        window.open(url, '_blank');
    }
};

function openMasterEditModal(doc) {
    const modal = document.getElementById('masterEditModal');
    document.getElementById('m_title').textContent = "Edit File: " + doc.filename;
    document.getElementById('m_doc_id').value = doc.id;
    document.getElementById('m_rename_filename').value = doc.filename;
    
    document.getElementById('m_attribution').innerHTML = ` <strong>Edited By:</strong> ${doc.uploader_name || 'System User'} <small style="text-transform: uppercase; background: #e2e8f0; padding: 2px 6px; border-radius: 4px; font-weight: bold; color: #475569; font-size: 10px; margin-left: 4px;">${doc.uploader_role || 'USER'}</small>`;

    fetch('<?= app_url('api/version_control.php?action=silent_lock&id=') ?>' + doc.id);

    const oldPane = document.getElementById('p_old');
    const ext = doc.filename.split('.').pop().toLowerCase();
    if (['png','jpg','jpeg','gif','webp','svg'].includes(ext)) {
        oldPane.innerHTML = `<img src="<?= app_url('uploads/') ?>${doc.storage_path}" style="max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 4px;">`;
    } else {
        oldPane.innerHTML = `<div style="text-align: center;"><span style="font-size: 36px; display: block; margin-bottom: 4px;">📄</span><small style="color: #64748b; font-weight: bold;">${ext.toUpperCase()} Object Preview Restrained</small></div>`;
    }

    const historyList = document.getElementById('m_history_list');
    historyList.innerHTML = '<small style="color: #64748b; text-align: center; display: block; padding: 10px;">Loading logs history repository...</small>';
    
    fetch('<?= app_url('api/version_control.php?action=get_history&id=') ?>' + doc.id)
    .then(res => res.json())
    .then(versions => {
        historyList.innerHTML = '';
        if (!versions || versions.length === 0) {
            historyList.innerHTML = '<small style="color: #94a3b8; text-align: center; font-style: italic; display: block; padding: 10px;">No history update recorded.</small>';
            return;
        }
        versions.forEach(v => {
            historyList.innerHTML += `
                <div style="display: flex; justify-content: space-between; align-items: center; background: #fff; border: 1px solid #e2e8f0; padding: 8px 12px; border-radius: 6px; font-size: 12px; gap: 10px;">
                    <div><strong>Checkpoint Version v${v.version_number}</strong><br><small style="color: #64748b; display: block; margin-top: 2px;">Updated at: ${v.created_at}</small></div>
                    <div class="actions-container nowrap">
                      <button type="button" class="btn btn-sm btn-outline" onclick="DMS.previewVersion('<?= app_url('api/download.php') ?>?id=${doc.id}&version_id=${v.id}&preview=1', '${ext}', 'Version v${v.version_number}')">Preview</button>
                      <a href="<?= app_url('api/version_control.php') ?>?action=rollback&doc_id=${doc.id}&version_id=${v.id}" class="btn btn-sm btn-warn" style="font-size: 11px; padding: 4px 8px; font-weight: bold; text-decoration: none; border-radius: 4px;" onclick="return confirm('Revert active file to this version state? The existing copy will be replaced.')">Rollback</a>
                    </div>
                </div>`;
        });
    }).catch(() => historyList.innerHTML = '<small style="color: #ef4444; text-align: center; display: block; padding: 10px;">Failed to fetch history logs.</small>');

    modal.classList.add('is-active');
    document.body.style.overflow = 'hidden';
}

function renameFromEditModal() {
    const id = document.getElementById('m_doc_id').value;
    const nextName = document.getElementById('m_rename_filename').value;
    const currentName = document.getElementById('m_title').textContent.replace(/^Edit File:\s*/, '');
    DMS.renameFileTo(id, nextName, currentName);
}

function closeMasterEditModal() {
    const modal = document.getElementById('masterEditModal');
    const docId = document.getElementById('m_doc_id').value;
    if (docId) {
        fetch('<?= app_url('api/version_control.php?action=silent_unlock&id=') ?>' + docId);
    }
    modal.classList.remove('is-active');
    document.body.style.overflow = '';
    document.getElementById('p_new').innerHTML = 'No preview yet.';
    document.getElementById('m_file_input').value = '';
}

function renderCompareViewDelta(input) {
    const pane = document.getElementById('p_new');
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const ext = file.name.split('.').pop().toLowerCase();
        if (['png','jpg','jpeg','gif','webp','svg'].includes(ext)) {
            const reader = new FileReader();
            reader.onload = function(e) {
                pane.innerHTML = `<img src="${e.target.result}" style="max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 4px;">`;
            };
            reader.readAsDataURL(file);
        } else {
            pane.innerHTML = `<div style="text-align: center; font-size: 12px; padding: 10px;">📄 <strong style="display: block; word-break: break-all; margin-top: 4px;">${file.name}</strong><small style="color: #16a34a; font-weight: bold; display: block; margin-top: 4px;">Ready to Update</small></div>`;
        }
    }
}

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
  });
}

document.addEventListener('DOMContentLoaded', () => {
  const folderInput = document.getElementById('privateFolderSearch');
  const folderCards = Array.from(document.querySelectorAll('.private-folder-card'));
  if (folderInput) {
    folderInput.addEventListener('input', () => {
      const q = folderInput.value.trim().toLowerCase();
      folderCards.forEach(card => {
         card.style.display = !q || (card.dataset.folderName || '').includes(q) ? '' : 'none';
      });
    });
  }

  document.body.addEventListener('click', function(e) {
      const btn = e.target.closest('[data-confirm]');
      if (btn) {
          e.preventDefault();
          if (confirm(btn.getAttribute('data-confirm'))) {
              const form = document.getElementById(btn.getAttribute('data-form'));
              if (form) form.submit();
          }
      }
  });

  window.addEventListener('click', function(e) {
      const modal = document.getElementById('masterEditModal');
      if (e.target === modal) { closeMasterEditModal(); }
  });
});
</script>
<?php include APP_ROOT . '/partials/footer.php'; ?>