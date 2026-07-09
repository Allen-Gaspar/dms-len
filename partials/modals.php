<?php
/** Shared modals: Upload + File Detail - included in footer for logged-in pages */
if (!isset($user) || !$user) return;
$db = get_db();
$folders = $db->query("SELECT id, name FROM folders WHERE (is_private=0 OR created_by=" . (int)$user['id'] . ") ORDER BY name")->fetch_all(MYSQLI_ASSOC);
?>
<!-- Upload Modal -->
<div id="uploadModal" class="modal-overlay">
  <div class="modal-card modal-lg upload-modal-card">
    <button class="modal-close" onclick="DMS.closeModal('uploadModal')" aria-label="Close">&times;</button>
    <h3>Add New</h3>
    <div class="upload-tabs">
      <button type="button" class="upload-tab active" data-tab="file">File</button>
      <button type="button" class="upload-tab" data-tab="folder">Folder</button>
    </div>
    <div id="uploadTabFile" class="upload-tab-panel">
      <div id="dropZone" class="drop-zone">
        <p>Drop files or folders here</p>
        <small>Preview your selection before uploading. Max 200 MB each.</small>
        <input type="file" id="fileInput" multiple hidden>
        <input type="file" id="folderInput" webkitdirectory directory multiple hidden>
        <div class="drop-zone-actions">
          <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('fileInput').click()">Browse Files</button>
          <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('folderInput').click()">Browse Folder</button>
        </div>
      </div>
      <div id="uploadPreview" class="upload-preview-list"></div>
      <div class="form-group">
        <label>Destination Folder</label>
        <select id="uploadFolderId">
          <option value="root">Main Root</option>
          <?php foreach ($folders as $f): ?>
            <option value="<?= (int)$f['id'] ?>"><?= htmlspecialchars($f['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label class="privacy-check"><input type="checkbox" id="uploadPrivate"> <strong>Private</strong><span>Only you can see it unless you share access.</span></label>
      </div>
      <button type="button" class="btn btn-primary btn-full" id="btnDoUpload">Upload Files</button>
    </div>
    <div id="uploadTabFolder" class="upload-tab-panel" style="display:none">
      <div class="form-group"><label>Folder Name</label><input type="text" id="newFolderName" placeholder="Folder name"></div>
      <div class="form-group"><label class="privacy-check"><input type="checkbox" id="newFolderPrivate"> <strong>Private folder</strong><span>Only you can see it unless you share access.</span></label></div>
      <button type="button" class="btn btn-primary btn-full" id="btnCreateFolder">Create Folder</button>
    </div>
  </div>
</div>

<!-- File Detail Modal -->
<div id="fileDetailModal" class="modal-overlay">
  <div class="modal-card modal-lg">
    <button class="modal-close" onclick="DMS.closeModal('fileDetailModal')" aria-label="Close">&times;</button>
    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px;">
      <h3 id="fdTitle" style="margin: 0; flex: 1; word-break: break-word;">File Details</h3>
      <button type="button" class="btn btn-outline btn-sm" id="fdEditToggle" onclick="toggleFileDetailEdit()" style="display: none;">Edit</button>
      <button type="button" class="btn btn-primary btn-sm" id="fdSaveBtn" onclick="saveFileDetailChanges()" style="display: none;">Save</button>
      <button type="button" class="btn btn-outline btn-sm" id="fdCancelBtn" onclick="cancelFileDetailEdit()" style="display: none;">Cancel</button>
    </div>
    <div style="display: none; background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; padding: 12px; margin-bottom: 15px; font-size: 13px; color: #dc2626;" id="fdEditHint">
      Editing mode - changes will be saved only when you click Save.
    </div>
    <div class="file-detail-grid">
      <div class="file-detail-preview" id="fdPreview">Loading...</div>
      <div class="file-detail-info" id="fdInfo"></div>
    </div>
    <div id="fdVersions"></div>
    <div class="modal-actions" id="fdActions"></div>
  </div>
</div>
