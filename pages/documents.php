<?php
/**
 * documents.php — Document browser with Unified Advanced Edit Modal
 */
require_once __DIR__ . '/../core/auth.php';
$user = require_login();
$db   = get_db();
$role = $user['role'];
$userPerms = (new User())->getPermissions((int)$user['id']);

// Force Philippine Standard Time explicitly for database sync
date_default_timezone_set('Asia/Manila');

// FIXED: Define the helper function at the top so it is available globally during page rendering
function format_bytes(int $bytes): string {
    if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
    if ($bytes >= 1024)    return round($bytes / 1024, 1) . ' KB';
    return $bytes . ' B';
}

$search = trim($_GET['search'] ?? '');
$fileType = strtolower(trim($_GET['type'] ?? ''));
$like   = '%' . $search . '%';
$typeLike = $fileType !== '' ? '%.' . $fileType : '%';
$offset = max(0, (int)($_GET['offset'] ?? 0));
$limit  = ROWS_PER_PAGE;

$countStmt = $db->prepare('SELECT COUNT(DISTINCT d.id) FROM documents d LEFT JOIN document_shares ds ON ds.document_id=d.id AND ds.shared_with_user_id=? WHERE d.is_deleted=0 AND (d.is_private=0 OR d.uploaded_by=? OR ds.shared_with_user_id IS NOT NULL) AND d.filename LIKE ? AND d.filename LIKE ?');
$countStmt->bind_param('iiss', $user['id'], $user['id'], $like, $typeLike);
$countStmt->execute();
$total = (int)$countStmt->get_result()->fetch_row()[0];

$stmt = $db->prepare(
    'SELECT d.*, u.username AS uploader_name, ur.role AS uploader_role, lk.username AS locker_name, lk.role AS locker_role
     FROM documents d
     LEFT JOIN document_shares ds ON ds.document_id = d.id AND ds.shared_with_user_id = ?
     LEFT JOIN users u  ON u.id = d.uploaded_by
     LEFT JOIN users ur ON ur.id = d.uploaded_by
     LEFT JOIN users lk ON lk.id = d.locked_by
     WHERE d.is_deleted = 0 AND (d.is_private = 0 OR d.uploaded_by = ? OR ds.shared_with_user_id IS NOT NULL) AND d.filename LIKE ? AND d.filename LIKE ?
     ORDER BY d.created_at DESC LIMIT ? OFFSET ?'
);
$stmt->bind_param('iissii', $user['id'], $user['id'], $like, $typeLike, $limit, $offset);
$stmt->execute();
$documents = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch system users for the sharing matrix inside the modal
$users_stmt = $db->prepare("SELECT id, username, role FROM users WHERE id != ? AND status = 'active' ORDER BY username ASC");
$users_stmt->bind_param('i', $user['id']);
$users_stmt->execute();
$system_collaborators = $users_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$types_res = $db->query("SELECT DISTINCT LOWER(SUBSTRING_INDEX(filename,'.',-1)) AS ext FROM documents WHERE is_deleted=0 AND filename LIKE '%.%' ORDER BY ext");
$fileTypes = $types_res ? $types_res->fetch_all(MYSQLI_ASSOC) : [];

$success = $_GET['ok']  ?? '';
$error   = $_GET['err'] ?? '';

$page_title = 'Files';
include __DIR__ . '/../partials/header.php';
?>

<div class="doc-actions-bar">
  <?php if ($role !== 'casual' && !empty($userPerms['can_add'])): ?>
    <button type="button" class="btn btn-primary" onclick="DMS.openUploadModal(false)">
    Upload New File or Folder
</button>
  <?php else: ?><span></span><?php endif; ?>
  <form method="GET" action="<?= page_url('documents.php') ?>" class="doc-search-box">
    <div class="suggest-box-wrap">
      <input type="text" id="documentSearchInput" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search by filename..." autocomplete="off">
      <div id="documentSearchSuggest" class="suggestion-dropdown"></div>
    </div>
    <select name="type" class="table-filter-select" onchange="this.form.submit()">
      <option value="">All file types</option>
      <?php foreach ($fileTypes as $t): $ext = $t['ext'] ?? ''; if ($ext === '') continue; ?>
        <option value="<?= htmlspecialchars($ext) ?>" <?= $fileType === $ext ? 'selected' : '' ?>><?= strtoupper(htmlspecialchars($ext)) ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-secondary">Search</button>
    <?php if ($search): ?><a href="<?= page_url('documents.php') ?>" class="btn btn-outline">Clear</a><?php endif; ?>
  </form>
</div>

<table class="data-table file-table">
  <thead>
    <tr>
      <th>Filename</th>
      <th>Type</th>
      <th>Version</th>
      <th>Size</th>
      <th>Uploaded By</th>
      <th>Status</th>
      <th class="date-col">Date & Time</th>
      <th style="width: 220px; text-align: left;">Actions</th>
    </tr>
  </thead>
  <tbody>
  <?php 
  $items_list = isset($folder_files) ? $folder_files : ($documents ?? []);
  
  if (empty($items_list)): 
  ?>
    <tr><td colspan="8" class="empty-row">No files found.</td></tr>
  <?php else: ?>
    <?php foreach ($items_list as $doc): 
        // 1. Determine locker's hierarchy role 
        $locker_role = $doc['locker_role'] ?? 'user';
        if (!empty($doc['is_locked']) && !isset($doc['locker_role'])) {
            $locker_role = $doc['uploader_role'] ?? 'user';
        }

        // 2. Enforce Access Rules Permissions Layer
        $can_write = true; 
        if (!empty($doc['is_locked'])) {
            // Admin lock blocks everyone else
            if ($locker_role === 'admin' && $role !== 'admin') {
                $can_write = false;
            }
            // Contributor lock blocks users, but Admin bypasses safely
            elseif ($locker_role === 'contributor' && $role === 'user') {
                $can_write = false;
            }
        }
    ?>
    <tr>
      <td>
        <button type="button" class="file-name-link" onclick="DMS.openFileDetail(<?= (int)$doc['id'] ?>)">
          <?= htmlspecialchars($doc['filename'] ?? $doc['foldername'] ?? '') ?>
        </button>
      </td>
      <td><span class="file-type-pill"><?= strtoupper(htmlspecialchars(pathinfo($doc['filename'] ?? '', PATHINFO_EXTENSION) ?: 'FILE')) ?></span></td>
      <td>
        <button type="button" class="version-link" onclick="DMS.openFileDetail(<?= (int)$doc['id'] ?>)">
          v<?= (int)($doc['version'] ?? 1) ?>
        </button>
      </td>
      <td><?= isset($doc['size']) ? format_bytes((int)$doc['size']) : '—' ?></td>
      <td><?= htmlspecialchars($doc['uploader_name'] ?? '—') ?></td>
      <td>
        <?php if (!empty($doc['is_locked'])): ?>
          <span class="badge badge-warn">Locked by <?= htmlspecialchars($doc['locker_name'] ?? '?') ?></span>
        <?php else: ?>
          <span class="badge badge-ok">Available</span>
        <?php endif; ?>
      </td>
      <td class="date-col">
        <div class="timestamp-wrapper">
          <?php 
            try {
                $dateObj = new DateTime($doc['created_at']);
                $ph_date = $dateObj->format('Y-m-d');
                $ph_time = $dateObj->format('h:i A');
            } catch (Exception $e) {
                $ph_date = isset($doc['created_at']) ? substr($doc['created_at'], 0, 10) : '—';
                $ph_time = '—';
            }
          ?>
          <span><?= htmlspecialchars($ph_date) ?></span>
          <span class="timestamp-time"><?= htmlspecialchars($ph_time) ?></span>
        </div>
      </td>
      <td>
        <div class="actions-container">
          <?php if ($can_write && !empty($userPerms['can_edit'])): ?>
            <button type="button" class="btn-icon-sm btn-primary" title="Edit File Details" onclick="openMasterEditModal(<?= htmlspecialchars(json_encode($doc)) ?>)">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
              </svg>
            </button>
          <?php endif; ?>

<?php if (!empty($userPerms['can_share'])): ?>
          <button type="button" class="btn-icon-sm" style="background:#6366f1;color:white;border:none;" title="Share" onclick="openShareModal(<?= (int)$doc['id'] ?>, '<?= htmlspecialchars(addslashes($doc['filename'])) ?>')">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path>
              <polyline points="16 6 12 2 8 6"></polyline>
              <line x1="12" y1="2" x2="12" y2="15"></line>
            </svg>
          </button>
          <?php endif; ?>

<?php if ($role === 'admin' || !empty($userPerms['can_download'])): ?>
          <button type="button" class="btn-icon-sm" style="background: transparent; border: 1px solid #cbd5e1; color: #475569; display: inline-flex; align-items: center; justify-content: center; cursor: pointer;" title="Download" onclick="DMS.confirm('Download file','Choose where to save <?= htmlspecialchars(addslashes($doc['filename'])) ?>?', ()=>DMS.downloadFile(<?= (int)$doc['id'] ?>, '<?= htmlspecialchars(addslashes($doc['filename'])) ?>'))">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
              <polyline points="7 10 12 15 17 10"></polyline>
              <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
          </button>
          <?php endif; ?>

          <?php if ($role !== 'casual' && !empty($userPerms['can_checkout'])): ?>
            <?php if (empty($doc['is_locked'])): ?>
              <button type="button" class="btn-icon-sm btn-warn" title="Lock" onclick="DMS.confirm('Lock File','Lock this file for editing?', ()=>location.href='<?= app_url('api/version_control.php?action=checkout&id=' . (int)$doc['id']) ?>')">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                  <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
              </button>
            <?php else:
              $can_unlock = false;
              if ((int)$doc['locked_by'] === (int)$user['id']) { $can_unlock = true; }
              elseif ($role === 'admin' && $locker_role !== 'admin') { $can_unlock = true; }
              elseif ($role === 'contributor' && in_array($locker_role, ['user','casual'], true)) { $can_unlock = true; }
              if ($can_unlock):
            ?>
              <button type="button" class="btn-icon-sm btn-ok" title="Unlock" onclick="DMS.confirm('Unlock File','Release the lock on this file?', ()=>location.href='<?= app_url('api/version_control.php?action=checkin&id=' . (int)$doc['id']) ?>')">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                  <path d="M7 11V7a5 5 0 0 1 9.9-1"></path>
                </svg>
              </button>
              <?php endif; ?>
            <?php endif; ?>

          <?php endif; ?>
          <?php if ($can_write && !empty($userPerms['can_delete'])): ?>
            <button type="button" class="btn-icon-sm btn-danger" title="Delete" onclick="DMS.confirm('Delete','Move this file to trash?', ()=>location.href='<?= app_url('api/delete.php?id=' . (int)$doc['id'] . '&origin=documents') ?>')">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                <line x1="10" y1="11" x2="10" y2="17"></line>
                <line x1="14" y1="11" x2="14" y2="17"></line>
              </svg>
            </button>
          <?php endif; ?>
        </div>
      </td>
    </tr>
    <?php endforeach; ?>
  <?php endif; ?>
  </tbody>
</table>

<?= Pagination::render($total, $offset, $limit, page_url('documents.php'), ['search' => $search, 'type' => $fileType]) ?>

<!-- Share Modal -->
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
  document.getElementById('sh_all').checked = false;
  document.getElementById('sh_add').checked = false;
  document.getElementById('sh_download').checked = true;
  document.getElementById('sh_edit').checked = false;
  document.getElementById('sh_delete').checked = false;
  document.getElementById('sh_checkout').checked = false;
  document.getElementById('sh_share').checked = false;
  DMS.openModal('shareModal');
}
function toggleShareAll(master) {
  ['sh_add','sh_edit','sh_delete','sh_download','sh_checkout','sh_share'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.checked = master.checked;
  });
}
function submitShare() {
  const email = document.getElementById('shareUserEmail').value.trim();
  if (!email) { DMS.toast('Type the user email first', 'error'); return; }
  DMS.showLoading();
  fetch('<?= app_url('api/share.php?action=grant_direct_access') ?>', {
    method: 'POST', headers: {'Content-Type':'application/json'},
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
  const searchInput = document.getElementById('documentSearchInput');
  const suggestBox = document.getElementById('documentSearchSuggest');
  let suggestTimer;
  if (!searchInput || !suggestBox) return;

  searchInput.addEventListener('input', () => {
    clearTimeout(suggestTimer);
    const q = searchInput.value.trim();
    if (q.length < 2) {
      suggestBox.style.display = 'none';
      suggestBox.innerHTML = '';
      return;
    }
    suggestTimer = setTimeout(() => {
      fetch('<?= app_url('api/document_detail.php?action=suggest&q=') ?>' + encodeURIComponent(q))
        .then(r => r.json())
        .then(rows => {
          suggestBox.innerHTML = '';
          if (!rows.length) {
            suggestBox.style.display = 'none';
            return;
          }
          rows.forEach(row => {
            const item = document.createElement('button');
            item.type = 'button';
            item.className = 'suggestion-item';
            item.textContent = row.filename + '  v' + row.version;
            item.addEventListener('click', () => DMS.openFileDetail(row.id));
            suggestBox.appendChild(item);
          });
          suggestBox.style.display = 'block';
        })
        .catch(() => { suggestBox.style.display = 'none'; });
    }, 180);
  });

  document.addEventListener('click', e => {
    if (!suggestBox.contains(e.target) && e.target !== searchInput) {
      suggestBox.style.display = 'none';
    }
  });
});
</script>

<!-- UNIFIED MASTER ADVANCED EDIT & MANAGEMENT MODAL -->
<div id="masterEditModal" class="modal-overlay">
  <div class="modal-wrapper-card">
    <button type="button" class="modal-close" onclick="closeMasterEditModal()" aria-label="Close">&times;</button>
    
    <h3 id="m_title" style="margin: 0 0 5px 0; color: #1e293b; font-size: 20px; font-weight: bold;">Edit File</h3>
    
    <!-- ATTRIBUTION PANEL: Shows who gave access and their role -->
    <p id="m_attribution" style="margin: 0 0 25px 0; color: #64748b; font-size: 13px; font-family: sans-serif; display: flex; align-items: center; gap: 6px;"></p>

    <div class="edit-rename-row">
      <div class="form-group" style="margin:0; flex:1;">
        <label>Rename File</label>
        <input type="text" id="m_rename_filename" placeholder="Filename">
      </div>
      <button type="button" class="btn btn-outline" onclick="renameFromEditModal()">Rename</button>
    </div>

    <div style="display: flex; flex-direction: column; gap: 25px;">
        
        <!-- SECTION 1: SIDE-BY-SIDE VISUAL COMPARISON AND REVISION UPLOAD -->
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
                <!-- <p style="font-size: 11px; color: #ef4444; margin: 0; line-height: 1.4;">⚠️ Note: Staging a file immediately signs an auto-lock on your name, preventing others from checking out or downloading until you apply modifications.</p> -->
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; margin-top: 15px; font-weight: bold; background: #16a34a; border: none; color: #fff; border-radius: 6px; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">Apply Changes</button>
            </form>
        </div>

        <!-- SECTION 2: HISTORY LOGS / ROLLBACK CHECKPOINTS -->
        <div style="border-top: 1px solid #f1f5f9; padding-top: 15px;">
            <h4 style="margin: 0 0 12px 0; font-size: 13px; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; font-weight: bold;">2. Version History</h4>
            <div id="m_history_list" style="max-height: 160px; overflow-y: auto; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; display: flex; flex-direction: column; gap: 8px; box-shadow: inset 0 2px 4px rgba(0,0,0,0.01);"></div>
        </div>

        <!-- SECTION 3: ADVANCED ACCESS MATRIX / COLLABORATION ASSIGNMENT
        <div style="border-top: 1px solid #f1f5f9; padding-top: 15px;">
            <h4 style="margin: 0 0 12px 0; font-size: 13px; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; font-weight: bold;">3. Collaborative Access Shares</h4>
            
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; box-sizing: border-box; display: flex; flex-direction: column; gap: 15px;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed #cbd5e1; padding-bottom: 8px; margin-bottom: 5px;">
                    <select id="m_collab_picker" style="width: 45%; padding: 8px; font-size: 13px; border-radius: 6px; border: 1px solid #cbd5e1; color: #334155; background: #fff;">
                        <option value="">-- Choose User / Collaborator --</option>
                        <?php foreach ($system_collaborators as $cb_u): ?>
                            <option value="<?= $cb_u['id'] ?>"><?= htmlspecialchars($cb_u['username']) ?> (<?= htmlspecialchars($cb_u['role']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                    
                    <label style="font-size: 12px; color: #0284c7; font-weight: bold; cursor: pointer; display: flex; align-items: center; gap: 4px;">
                        <input type="checkbox" id="m_select_all_perms" onclick="document.querySelectorAll('.m-perm-cb').forEach(cb => cb.checked = this.checked)" style="cursor: pointer; width: 15px; height: 15px; margin: 0;"> Select All Permissions
                    </label>
                </div> -->

                <!-- Granular Permissions Checklist Row -->
                <!-- <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; padding: 5px 0;">
                    <label style="font-size: 13px; color: #334155; display: flex; align-items: center; gap: 6px; cursor: pointer;"><input type="checkbox" class="m-perm-cb" id="p_edit" value="1" style="width: 16px; height: 16px;"> Can Edit</label>
                    <label style="font-size: 13px; color: #334155; display: flex; align-items: center; gap: 6px; cursor: pointer;"><input type="checkbox" class="m-perm-cb" id="p_delete" value="1" style="width: 16px; height: 16px;"> Can Delete</label>
                    <label style="font-size: 13px; color: #334155; display: flex; align-items: center; gap: 6px; cursor: pointer;"><input type="checkbox" class="m-perm-cb" id="p_download" value="1" checked style="width: 16px; height: 16px;"> Can Download</label>
                    <label style="font-size: 13px; color: #334155; display: flex; align-items: center; gap: 6px; cursor: pointer;"><input type="checkbox" class="m-perm-cb" id="p_checkout" value="1" style="width: 16px; height: 16px;"> Can Lock/Unlock</label>
                </div>

                <button type="button" class="btn btn-secondary" style="width: 100%; font-weight: bold; padding: 10px; border-radius: 6px; font-size: 13px; cursor: pointer;" onclick="submitNewAccessAssignmentRow()">Grant Access</button>
            </div>
        </div> -->

    </div>
  </div>
</div>

<!-- JAVASCRIPT EVENT INTERFACE CONTROLLER MODULE -->
<script>
function openMasterEditModal(doc) {
    const modal = document.getElementById('masterEditModal');
    document.getElementById('m_title').textContent = "Edit File: " + doc.filename;
    document.getElementById('m_doc_id').value = doc.id;
    document.getElementById('m_rename_filename').value = doc.filename;
    
    // Attribution Tracking Assignment Box
    document.getElementById('m_attribution').innerHTML = ` <strong>Edited By:</strong> ${doc.uploader_name || 'System Root'} <small style="text-transform: uppercase; background: #e2e8f0; padding: 2px 6px; border-radius: 4px; font-weight: bold; color: #475569; font-size: 10px; margin-left: 4px;">${doc.uploader_role || 'ADMIN'}</small>`;

    // Execute Auto-Locking Signal Route Hook via version controller
    fetch('<?= app_url('api/version_control.php?action=silent_lock&id=') ?>' + doc.id);

    // Render current active layout file graphic context preview window
    const oldPane = document.getElementById('p_old');
    const ext = doc.filename.split('.').pop().toLowerCase();
    if (['png','jpg','jpeg','gif','webp','svg'].includes(ext)) {
        oldPane.innerHTML = `<img src="<?= app_url('uploads/') ?>${doc.storage_path}" style="max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 4px;">`;
    } else {
        oldPane.innerHTML = `<div style="text-align: center;"><span style="font-size: 36px; display: block; margin-bottom: 4px;">📄</span><small style="color: #64748b; font-weight: bold;">${ext.toUpperCase()} Object Preview Restrained</small></div>`;
    }

    // Populate Version History logs checklist
    const historyList = document.getElementById('m_history_list');
    historyList.innerHTML = '<small style="color: #64748b; text-align: center; display: block; padding: 10px;">Loading logs history repository...</small>';
    
    fetch('<?= app_url('api/version_control.php?action=get_history&id=') ?>' + doc.id)
    .then(res => res.json())
    .then(versions => {
        historyList.innerHTML = '';
        if (versions.length === 0) {
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
    
    // Release Auto-Lock parameters upon safe exit
    fetch('<?= app_url('api/version_control.php?action=silent_unlock&id=') ?>' + docId);
    
    modal.classList.remove('is-active');
    document.body.style.overflow = '';
    document.getElementById('p_new').innerHTML = 'No update.';
    document.getElementById('m_file_input').value = '';
    const selectAll = document.getElementById('m_select_all_perms');
    if (selectAll) selectAll.checked = false;
    document.querySelectorAll('.m-perm-cb').forEach(cb => cb.checked = cb.id === 'p_download');
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

function submitNewAccessAssignmentRow() {
    const picker = document.getElementById('m_collab_picker');
    if (!picker) return;
    const userId = picker.value;
    const docId = document.getElementById('m_doc_id').value;
    if (!userId) { alert('Please select a target user first.'); return; }

    const payload = {
        document_id: docId,
        shared_with_user_id: userId,
        can_edit: document.getElementById('p_edit').checked ? 1 : 0,
        can_delete: document.getElementById('p_delete').checked ? 1 : 0,
        can_download: document.getElementById('p_download').checked ? 1 : 0,
        can_checkout: document.getElementById('p_checkout').checked ? 1 : 0
    };

    fetch('<?= app_url('api/share.php?action=grant_direct_access') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Access permissions successfully!');
            picker.value = '';
            const selectAll = document.getElementById('m_select_all_perms');
            if (selectAll) selectAll.checked = false;
            document.querySelectorAll('.m-perm-cb').forEach(cb => cb.checked = cb.id === 'p_download');
        } else { 
            alert('Access Error: ' + data.message); 
        }
    }).catch(() => alert('Error Updating.'));
}

window.addEventListener('click', function(e) {
    const modal = document.getElementById('masterEditModal');
    if (e.target === modal) { closeMasterEditModal(); }
});

function triggerNativeDownload(downloadUrl) {
    // 1. Activate your global layout loading animation spinner panel
    if (typeof showLoader === 'function') {
        showLoader();
    } else if (typeof DMS !== 'undefined' && typeof DMS.showLoader === 'function') {
        DMS.showLoader();
    } else {
        const loader = document.getElementById('loadingSpinner') || document.querySelector('.loader-wrapper');
        if (loader) loader.style.display = 'flex';
    }

    // 2. Point the hidden background frame to the stream source path 
    const iframe = document.getElementById('hiddenDownloadFrame');
    if (iframe) {
        iframe.src = downloadUrl;
    }

    // 3. Keep the loading animation visible briefly, then close it once hand-off occurs
    setTimeout(() => {
        if (typeof hideLoader === 'function') {
            hideLoader();
        } else if (typeof DMS !== 'undefined' && typeof DMS.hideLoader === 'function') {
            DMS.hideLoader();
        } else {
            const loader = document.getElementById('loadingSpinner') || document.querySelector('.loader-wrapper');
            if (loader) loader.style.display = 'none';
        }
    }, 2000); // 2000ms = 2 seconds loading duration animation screen
}
</script>

<iframe id="hiddenDownloadFrame" style="display:none;"></iframe>

<?php 
include __DIR__ . '/../partials/footer.php';
?>
