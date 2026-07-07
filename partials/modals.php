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
    <h3 id="fdTitle">File Details</h3>
    <div class="file-detail-grid">
      <div class="file-detail-preview" id="fdPreview">Loading...</div>
      <div class="file-detail-info" id="fdInfo"></div>
    </div>
    <div id="fdVersions"></div>
    <div class="modal-actions" id="fdActions"></div>
  </div>
</div>
