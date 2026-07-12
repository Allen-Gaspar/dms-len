/**
 * FILESTAC DMS — Shared UI scripts
 */
const DMS = {
    toast(msg, type = 'success') {
        let el = document.getElementById('dms-toast');
        if (!el) {
            el = document.createElement('div');
            el.id = 'dms-toast';
            el.className = 'dms-toast';
            document.body.appendChild(el);
        }
        el.className = 'dms-toast dms-toast-' + type + ' show';
        el.textContent = msg;
        clearTimeout(el._timer);
        el._timer = setTimeout(() => el.classList.remove('show'), 3000);
    },

    confirm(title, message, onConfirm) {
        const modal = document.getElementById('dmsConfirmModal');
        if (!modal) return onConfirm && onConfirm();
        document.getElementById('dmsConfirmTitle').textContent = title;
        document.getElementById('dmsConfirmMsg').textContent = message;
        modal.classList.add('is-active');
        const btn = document.getElementById('dmsConfirmBtn');
        const handler = () => {
            modal.classList.remove('is-active');
            btn.removeEventListener('click', handler);
            if (onConfirm) onConfirm();
        };
        btn.onclick = handler;
    },

    openModal(id) {
        const m = document.getElementById(id);
        if (m) { m.classList.add('is-active'); document.body.style.overflow = 'hidden'; }
    },

    openUploadModal(privateMode = false, folderId = 'root') {
        const privateInput = document.getElementById('uploadPrivate');
        const folderPrivateInput = document.getElementById('newFolderPrivate');
        const folderSelect = document.getElementById('uploadFolderId');
        if (privateInput) privateInput.checked = !!privateMode;
        if (folderPrivateInput) folderPrivateInput.checked = !!privateMode;
        if (folderSelect) folderSelect.value = folderId || 'root';
        document.querySelectorAll('.upload-tab').forEach(tab => tab.classList.toggle('active', tab.dataset.tab === 'file'));
        const filePanel = document.getElementById('uploadTabFile');
        const folderPanel = document.getElementById('uploadTabFolder');
        if (filePanel) filePanel.style.display = '';
        if (folderPanel) folderPanel.style.display = 'none';
        this.openModal('uploadModal');
    },

    closeModal(id) {
        const m = document.getElementById(id);
        if (m) { m.classList.remove('is-active'); document.body.style.overflow = ''; }
    },

    showLoading() {
        document.body.classList.add('dms-loading');
        const overlay = document.getElementById('page-loading-overlay');
        if (overlay) {
            overlay.style.display = 'flex';
            overlay.classList.remove('is-hidden');
        }
    },

    hideLoading() {
        document.body.classList.remove('dms-loading');
        const overlay = document.getElementById('page-loading-overlay');
        if (overlay) overlay.classList.add('is-hidden');
    },

    async downloadFile(id, suggestedName = 'download') {
        const url = (window.DMS_BASE || '') + 'api/download.php?id=' + id;
        if (!window.showSaveFilePicker) {
            window.open(url, '_blank');
            return;
        }
        this.showLoading();
        try {
            const handle = await window.showSaveFilePicker({ suggestedName });
            const response = await fetch(url);
            if (!response.ok) throw new Error('Download failed');
            const blob = await response.blob();
            const writable = await handle.createWritable();
            await writable.write(blob);
            await writable.close();
            this.toast('File saved.');
        } catch (err) {
            if (err.name !== 'AbortError') {
                this.toast('Opening normal download instead.', 'info');
                window.open(url, '_blank');
            }
        } finally {
            this.hideLoading();
        }
    },

    async downloadBulkFiles(ids, suggestedName = 'selected-files.zip') {
        const cleanName = suggestedName.toLowerCase().endsWith('.zip') ? suggestedName : suggestedName + '.zip';
        const url = (window.DMS_BASE || '') + 'api/bulk_download.php?ids=' + encodeURIComponent(ids.join(','));
        if (!window.showSaveFilePicker) {
            window.open(url, '_blank');
            return;
        }
        this.showLoading();
        try {
            const handle = await window.showSaveFilePicker({ suggestedName: cleanName });
            const response = await fetch(url);
            if (!response.ok) throw new Error(await response.text());
            const blob = await response.blob();
            const writable = await handle.createWritable();
            await writable.write(blob);
            await writable.close();
            this.toast('Files saved.');
        } catch (err) {
            if (err.name !== 'AbortError') {
                this.toast(err.message || 'Bulk download failed.', 'error');
            }
        } finally {
            this.hideLoading();
        }
    },

    async downloadFolder(id, suggestedName = 'folder.zip') {
        const cleanName = suggestedName.toLowerCase().endsWith('.zip') ? suggestedName : suggestedName + '.zip';
        const url = (window.DMS_BASE || '') + 'api/folder_download.php?id=' + encodeURIComponent(id);
        if (!window.showSaveFilePicker) {
            window.open(url, '_blank');
            return;
        }
        this.showLoading();
        try {
            const handle = await window.showSaveFilePicker({ suggestedName: cleanName });
            const response = await fetch(url);
            if (!response.ok) throw new Error(await response.text());
            const blob = await response.blob();
            const writable = await handle.createWritable();
            await writable.write(blob);
            await writable.close();
            this.toast('Folder saved.');
        } catch (err) {
            if (err.name !== 'AbortError') {
                this.toast(err.message || 'Folder download failed.', 'error');
            }
        } finally {
            this.hideLoading();
        }
    },

    async downloadBulkFolders(ids, suggestedName = 'selected-folders.zip') {
        const cleanName = suggestedName.toLowerCase().endsWith('.zip') ? suggestedName : suggestedName + '.zip';
        const url = (window.DMS_BASE || '') + 'api/bulk_folder_download.php?ids=' + encodeURIComponent(ids.join(','));
        if (!window.showSaveFilePicker) {
            window.open(url, '_blank');
            return;
        }
        this.showLoading();
        try {
            const handle = await window.showSaveFilePicker({ suggestedName: cleanName });
            const response = await fetch(url);
            if (!response.ok) throw new Error(await response.text());
            const blob = await response.blob();
            const writable = await handle.createWritable();
            await writable.write(blob);
            await writable.close();
            this.toast('Folders saved.');
        } catch (err) {
            if (err.name !== 'AbortError') {
                this.toast(err.message || 'Folder download failed.', 'error');
            }
        } finally {
            this.hideLoading();
        }
    },

    renameFile(id, currentName = '') {
        const nextName = prompt('Rename file', currentName);
        if (nextName === null) return;
        this.renameFileTo(id, nextName, currentName);
    },

    renameFileTo(id, nextName, currentName = '') {
        const cleanName = nextName.trim();
        if (!cleanName || cleanName === currentName) {
            this.toast('No update made.', 'info');
            return;
        }
        const formData = new FormData();
        formData.append('id', id);
        formData.append('filename', cleanName);
        this.showLoading();
        fetch((window.DMS_BASE || '') + 'api/rename.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                this.toast(data.message || (data.success ? 'Updated' : 'No update made.'), data.success ? 'success' : 'error');
                if (data.success) setTimeout(() => window.location.reload(), 650);
            })
            .catch(() => this.toast('Rename failed.', 'error'))
            .finally(() => this.hideLoading());
    },

    openFileDetail(id) {
        const modal = document.getElementById('fileDetailModal');
        if (!modal) return;
        const title = document.getElementById('fdTitle');
        const preview = document.getElementById('fdPreview');
        const info = document.getElementById('fdInfo');
        const versions = document.getElementById('fdVersions');
        const actions = document.getElementById('fdActions');

        title.textContent = 'File Details';
        preview.innerHTML = 'Loading...';
        info.innerHTML = '';
        versions.innerHTML = '';
        actions.innerHTML = '';
        this.openModal('fileDetailModal');

        fetch((window.DMS_BASE || '') + 'api/document_detail.php?id=' + encodeURIComponent(id))
            .then(r => r.json())
            .then(data => {
                if (!data.success) {
                    preview.innerHTML = '<div class="empty-row">' + (data.message || 'Unable to load file') + '</div>';
                    return;
                }
                const doc = data.doc;
                this._currentPreview = { url: data.preview_url, ext: data.ext, title: doc.filename };
                title.textContent = doc.filename;
                preview.innerHTML = renderPreview(data.preview_url, data.ext, doc.filename);
                info.innerHTML = `
                    <dl class="file-meta-list">
                        <dt>Filename</dt><dd>${escapeHtml(doc.filename || '-')}</dd>
                        <dt>Type</dt><dd>${escapeHtml((data.ext || 'file').toUpperCase())}</dd>
                        <dt>Version</dt><dd>v${doc.version || 1}</dd>
                        <dt>Size</dt><dd>${formatBytes(Number(doc.size || 0))}</dd>
                        <dt>Uploaded By</dt><dd>${escapeHtml(doc.uploader_name || '-')}</dd>
                        <dt>Status</dt><dd>${doc.is_locked == 1 ? 'Locked' : 'Available'}</dd>
                        <dt>Created</dt><dd>${escapeHtml((doc.created_at || '').slice(0, 16))}</dd>
                    </dl>`;
                const safeNameArg = JSON.stringify(doc.filename || '').replace(/"/g, '&quot;');
                actions.innerHTML = `
                    ${data.can_edit ? `<button type="button" class="btn btn-outline" onclick="DMS.renameFile(${doc.id}, ${safeNameArg})">Rename</button>` : ''}
                    <button type="button" class="btn btn-outline" onclick="DMS.previewCurrent()">Current</button>
                    <button type="button" class="btn btn-primary" onclick="DMS.downloadFile(${doc.id}, ${safeNameArg || '&quot;download&quot;'})">Download</button>`;

                if (data.versions && data.versions.length) {
                    versions.innerHTML = `
                        <h4 class="modal-section-title">Versions</h4>
                        <table class="data-table version-table">
                            <thead><tr><th>Version</th><th>Date</th><th>Actions</th></tr></thead>
                            <tbody>${data.versions.map(v => `
                                <tr>
                                    <td><button type="button" class="version-link" onclick="DMS.previewVersion('${v.preview_url}', '${escapeHtml(data.ext)}', 'Version v${v.version_number}')">v${v.version_number}</button></td>
                                    <td>${escapeHtml((v.created_at || '').slice(0, 16))}</td>
                                    <td><div class="actions-container nowrap">
                                        <button type="button" class="btn btn-outline btn-sm" onclick="DMS.previewVersion('${v.preview_url}', '${escapeHtml(data.ext)}', 'Version v${v.version_number}')">Preview</button>
                                        <a class="btn btn-sm btn-warn" onclick="return confirm('Rollback to version v${v.version_number}?')" href="${(window.DMS_BASE || '')}api/version_control.php?action=rollback&doc_id=${doc.id}&version_id=${v.id}">Rollback</a>
                                    </div></td>
                                </tr>`).join('')}</tbody>
                        </table>`;
                } else {
                    versions.innerHTML = '<p class="muted">No previous versions yet.</p>';
                }
            })
            .catch(() => {
                preview.innerHTML = '<div class="empty-row">Unable to load file preview.</div>';
            });
    },

    previewVersion(url, ext, title) {
        const master = document.getElementById('masterEditModal');
        const masterPreview = document.getElementById('p_new');
        if (master?.classList.contains('is-active') && masterPreview) {
            masterPreview.dataset.lockedByUser = '1';
            masterPreview.innerHTML = renderPreview(url, ext, title);
            return;
        }
        document.getElementById('fdTitle').textContent = title;
        document.getElementById('fdPreview').innerHTML = renderPreview(url, ext, title);
    },

    previewCurrent() {
        if (!this._currentPreview) return;
        document.getElementById('fdTitle').textContent = this._currentPreview.title;
        document.getElementById('fdPreview').innerHTML = renderPreview(this._currentPreview.url, this._currentPreview.ext, this._currentPreview.title);
    },

    previewMasterCurrent() {
        const modal = document.getElementById('masterEditModal');
        const pane = document.getElementById('p_new');
        if (!modal || !pane) return;
        const url = modal.dataset.currentPreviewUrl;
        const ext = modal.dataset.currentPreviewExt;
        const title = modal.dataset.currentPreviewTitle || 'Current file';
        pane.dataset.lockedByUser = '1';
        pane.innerHTML = `
            <div class="preview-toolbar">
                <strong>Current file</strong>
            </div>
            ${renderPreview(url, ext, title)}`;
    }
};
window.DMS = DMS;

function ensureFolderShareModal() {
    let modal = document.getElementById('folderShareModal');
    if (!modal) {
        document.body.insertAdjacentHTML('beforeend', '<div id="folderShareModal" class="modal-overlay"></div>');
        modal = document.getElementById('folderShareModal');
    }
    if (modal.querySelector('#folderShareId')) return;
    modal.innerHTML = `
      <div class="modal-card modal-lg">
        <button class="modal-close" onclick="DMS.closeModal('folderShareModal')" aria-label="Close">&times;</button>
        <h3>Share Folder</h3>
        <p id="folderShareName"></p>
        <input type="hidden" id="folderShareId">
        <label class="privacy-check" style="margin-bottom:12px"><input type="checkbox" id="folderShareAllUsers" onchange="document.getElementById('folderShareEmail').disabled=this.checked"> <strong>Share with all active users</strong></label>
        <div class="form-group">
          <label>User email</label>
          <input type="email" id="folderShareEmail" placeholder="name@example.com" autocomplete="off">
        </div>
        <div class="perm-grid">
          <label><input type="checkbox" id="fs_all" checked onchange="toggleFolderShareAll(this)"> Select all</label>
          <label><input type="checkbox" id="fs_add" checked> Add</label>
          <label><input type="checkbox" id="fs_edit" checked> Edit</label>
          <label><input type="checkbox" id="fs_delete" checked> Delete</label>
          <label><input type="checkbox" id="fs_download" checked> Download</label>
          <label><input type="checkbox" id="fs_checkout" checked> Lock/Unlock</label>
          <label><input type="checkbox" id="fs_share" checked> Share</label>
        </div>
        <div class="modal-actions">
          <button type="button" class="btn btn-outline" onclick="DMS.closeModal('folderShareModal')">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="submitFolderShare()">Share Folder</button>
        </div>
      </div>`;
}

window.openFolderShareModal = function(folderId, folderName) {
    ensureFolderShareModal();
    DMS._bulkFolderShareIds = Array.isArray(folderId) ? folderId.map(Number).filter(Boolean) : null;
    const idInput = document.getElementById('folderShareId');
    const nameLabel = document.getElementById('folderShareName');
    const emailInput = document.getElementById('folderShareEmail');
    const allUsers = document.getElementById('folderShareAllUsers');
    if (idInput) idInput.value = folderId;
    if (nameLabel) nameLabel.textContent = DMS._bulkFolderShareIds ? `${DMS._bulkFolderShareIds.length} selected folder(s)` : (folderName || 'Selected folder');
    if (emailInput) {
        emailInput.value = '';
        emailInput.disabled = false;
    }
    if (allUsers) allUsers.checked = false;
    ['fs_all','fs_add','fs_edit','fs_delete','fs_download','fs_checkout','fs_share'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.checked = true;
    });
    DMS.openModal('folderShareModal');
};

window.submitFolderShare = function() {
    ensureFolderShareModal();
    const folderId = document.getElementById('folderShareId')?.value;
    const allUsers = document.getElementById('folderShareAllUsers')?.checked ? 1 : 0;
    const email = document.getElementById('folderShareEmail')?.value.trim() || '';
    const selectedIds = DMS._bulkFolderShareIds && DMS._bulkFolderShareIds.length ? DMS._bulkFolderShareIds : [Number(folderId)];
    if (!selectedIds.length || !selectedIds[0]) return;
    if (!allUsers && !email) {
        DMS.toast('Type the user email first', 'error');
        return;
    }
    DMS.showLoading();
    const payloadBase = {
            all_users: allUsers,
            email,
            can_add: document.getElementById('fs_add')?.checked ? 1 : 0,
            can_edit: document.getElementById('fs_edit')?.checked ? 1 : 0,
            can_delete: document.getElementById('fs_delete')?.checked ? 1 : 0,
            can_download: document.getElementById('fs_download')?.checked ? 1 : 0,
            can_checkout: document.getElementById('fs_checkout')?.checked ? 1 : 0,
            can_share: document.getElementById('fs_share')?.checked ? 1 : 0
    };
    Promise.all(selectedIds.map(id => fetch((window.DMS_BASE || '') + 'api/folder_control.php?action=grant_access_by_email', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ ...payloadBase, folder_id: id })
    }).then(r => r.json())))
        .then(results => {
            const failed = results.find(data => !data.success);
            if (!failed) {
                DMS.toast(selectedIds.length > 1 ? 'Selected folders shared successfully' : 'Folder shared successfully');
                DMS.closeModal('folderShareModal');
            } else {
                DMS.toast(failed.message || 'Folder share failed', 'error');
            }
        })
        .catch(() => DMS.toast('Folder share failed', 'error'))
        .finally(() => DMS.hideLoading());
};

window.toggleFolderShareAll = function(master) {
    ['fs_add','fs_edit','fs_delete','fs_download','fs_checkout','fs_share'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.checked = master.checked;
    });
};

function installUnifiedFolderShareModal() {
    ensureFolderShareModal();
    window.openFolderAccessModal = function(folderId, folderName) {
        window.openFolderShareModal(folderId, folderName);
    };
}

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, ch => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    }[ch]));
}

function formatBytes(bytes) {
    if (!Number.isFinite(bytes) || bytes <= 0) return '0 B';
    if (bytes >= 1048576) return (bytes / 1048576).toFixed(1).replace(/\.0$/, '') + ' MB';
    if (bytes >= 1024) return (bytes / 1024).toFixed(1).replace(/\.0$/, '') + ' KB';
    return bytes + ' B';
}

const TEXT_PREVIEW_EXTENSIONS = ['txt','csv','json','xml','html','htm','css','js','php','py','java','c','cpp','cs','rb','go','rs','ts','tsx','jsx','sql','md','log','ini','env','yml','yaml'];

function renderPreview(url, ext, filename) {
    if (!url) return `<div class="preview-fallback"><strong>${escapeHtml(filename)}</strong><span>No browser preview available.</span></div>`;
    const cleanExt = String(ext || '').toLowerCase();
    if (['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'].includes(cleanExt)) {
        return `<img class="file-preview-media" src="${url}" alt="${escapeHtml(filename)}">`;
    }
    if (cleanExt === 'pdf') {
        return `<iframe class="file-preview-frame" src="${url}" title="${escapeHtml(filename)}"></iframe>`;
    }
    if (['mp4', 'webm', 'mov'].includes(cleanExt)) {
        return `<video class="file-preview-media" controls src="${url}"></video>`;
    }
    if (['mp3', 'wav', 'ogg', 'm4a'].includes(cleanExt)) {
        return `<div class="preview-fallback"><strong>${escapeHtml(filename)}</strong><audio controls src="${url}" style="width:min(520px,100%)"></audio></div>`;
    }
    if (TEXT_PREVIEW_EXTENSIONS.includes(cleanExt)) {
        return renderRemoteTextPreview(url, filename);
    }
    if (['ppt', 'pptx', 'doc', 'docx', 'xls', 'xlsx'].includes(cleanExt)) {
        return `<div class="preview-fallback office-preview"><strong>${escapeHtml(filename)}</strong><span>Office preview depends on browser support for this file type.</span><iframe class="file-preview-frame" src="${url}" title="${escapeHtml(filename)}"></iframe></div>`;
    }
    return `<iframe class="file-preview-frame" src="${url}" title="${escapeHtml(filename)}"></iframe>`;
}

function renderRemoteTextPreview(url, filename) {
    const previewId = 'remote-preview-' + Math.random().toString(36).slice(2);
    setTimeout(() => {
        const target = document.getElementById(previewId);
        if (!target) return;
        fetch(url, { credentials: 'same-origin' })
            .then(response => {
                if (!response.ok) throw new Error('Preview failed');
                return response.text();
            })
            .then(text => {
                target.textContent = text || '(empty file)';
            })
            .catch(() => {
                target.innerHTML = `<strong>${escapeHtml(filename)}</strong><span>Preview unavailable.</span>`;
                target.classList.add('preview-fallback');
            });
    }, 0);
    return `<pre id="${previewId}" class="file-preview-text">Loading ${escapeHtml(filename)}...</pre>`;
}

function renderLocalFilePreview(file) {
    const ext = (file.name.split('.').pop() || '').toLowerCase();
    const objectUrl = URL.createObjectURL(file);
    if (['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'].includes(ext)) {
        return `<img class="file-preview-media" src="${objectUrl}" alt="${escapeHtml(file.name)}">`;
    }
    if (ext === 'pdf') {
        return `<iframe class="file-preview-frame" src="${objectUrl}" title="${escapeHtml(file.name)}"></iframe>`;
    }
    if (['mp4', 'webm', 'mov'].includes(ext)) {
        return `<video controls class="file-preview-media" src="${objectUrl}"></video>`;
    }
    if (['mp3', 'wav', 'ogg', 'm4a'].includes(ext)) {
        return `<div class="preview-fallback"><strong>${escapeHtml(file.name)}</strong><audio controls src="${objectUrl}" style="width:min(520px,100%)"></audio></div>`;
    }
    if (TEXT_PREVIEW_EXTENSIONS.includes(ext)) {
        const readerId = 'local-preview-' + Math.random().toString(36).slice(2);
        const reader = new FileReader();
        reader.onload = event => {
            const target = document.getElementById(readerId);
            if (target) target.textContent = String(event.target.result || '');
        };
        reader.readAsText(file);
        return `<pre id="${readerId}" class="file-preview-text">Loading ${escapeHtml(file.name)}...</pre>`;
    }
    return `<iframe class="file-preview-frame" src="${objectUrl}" title="${escapeHtml(file.name)}"></iframe>`;
}

function installMasterEditModalCompatibility() {
    const modal = document.getElementById('masterEditModal');
    if (!modal || modal.dataset.compatReady === '1') return;
    modal.dataset.compatReady = '1';

    const oldOpen = window.openMasterEditModal;
    if (typeof oldOpen === 'function') {
        window.openMasterEditModal = function(doc) {
            DMS.confirm(
                'Lock file for editing?',
                'Editing this file will lock it so nobody else can overwrite it while you are working. Continue?',
                () => openMasterEditModalAfterLockConfirm(doc)
            );
        };
    }

    function openMasterEditModalAfterLockConfirm(doc) {
            oldOpen(doc);
            markDocumentLocked(doc.id);
            modal.dataset.currentFilename = doc.filename || '';
            const oldPane = document.getElementById('p_old');
            const newPane = document.getElementById('p_new');
            const ext = String(doc.filename || '').split('.').pop().toLowerCase();
            if (oldPane && doc.id) {
                const currentUrl = (window.DMS_BASE || '') + 'api/download.php?id=' + encodeURIComponent(doc.id) + '&preview=1';
                modal.dataset.currentPreviewUrl = currentUrl;
                modal.dataset.currentPreviewExt = ext;
                modal.dataset.currentPreviewTitle = doc.filename || 'Current file';
                oldPane.innerHTML = renderPreview(currentUrl, ext, doc.filename || 'Current file');
            }
            if (newPane) {
                delete newPane.dataset.lockedByUser;
                newPane.innerHTML = '<span class="muted">No preview yet.</span>';
            }
            wireMasterEditRenameButton();
    }

    window.renderCompareViewDelta = function(input) {
        const pane = document.getElementById('p_new');
        if (!pane || !input.files || !input.files[0]) return;
        const file = input.files[0];
        const renameInput = document.getElementById('m_rename_filename');
        if (renameInput) renameInput.value = file.name;
        pane.dataset.lockedByUser = '1';
        pane.innerHTML = renderLocalFilePreview(file);
    };

    const forms = ['m_upload_form', 'm_staging_master_form']
        .map(id => document.getElementById(id))
        .filter(Boolean);
    forms.forEach(form => {
        if (form.dataset.renameCompat === '1') return;
        form.dataset.renameCompat = '1';
        form.addEventListener('submit', event => handleMasterEditApply(event, form), true);
    });

    wireMasterEditRenameButton();
    window.closeMasterEditModal = function() {
        const activeModal = document.getElementById('masterEditModal');
        if (!activeModal) return;
        activeModal.classList.remove('is-active');
        document.body.style.overflow = '';
        const newPane = document.getElementById('p_new');
        const fileInput = document.getElementById('m_file_input');
        if (newPane) {
            delete newPane.dataset.lockedByUser;
            newPane.innerHTML = 'No preview yet.';
        }
        if (fileInput) fileInput.value = '';
        DMS.toast('File remains locked until you apply changes or unlock it.', 'info');
    };
}

function wireMasterEditRenameButton() {
    const renameButton = document.querySelector('#masterEditModal .edit-rename-row button');
    if (!renameButton || renameButton.dataset.stageOnly === '1') return;
    renameButton.dataset.stageOnly = '1';
    renameButton.textContent = 'Stage Rename';
    renameButton.onclick = event => {
        event.preventDefault();
        DMS.toast('Rename will apply when you click Apply Changes.', 'info');
    };
}

function handleMasterEditApply(event, form) {
    if (form.dataset.submittingAfterRename === '1') return;
    const docId = document.getElementById('m_doc_id')?.value;
    const renameInput = document.getElementById('m_rename_filename');
    if (!docId || !renameInput) return;

    const currentName = document.getElementById('masterEditModal')?.dataset.currentFilename || '';
    const nextName = renameInput.value.trim();
    const selectedFile = document.getElementById('m_file_input')?.files?.[0];
    const stagedRollback = document.getElementById('m_staged_rollback_id')?.value;
    const stagedShares = document.getElementById('m_staged_sharing_json')?.value || '[]';
    const hasStagedShares = stagedShares !== '[]';
    const needsRename = nextName && nextName !== currentName;

    if (selectedFile) {
        event.preventDefault();
        DMS.confirm(
            'Apply changes and unlock?',
            'Applying this update will save the new file, update the filename, and unlock the file for other users.',
            () => {
                let hidden = form.querySelector('input[name="revised_filename"]');
                if (!hidden) {
                    hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'revised_filename';
                    form.appendChild(hidden);
                }
                hidden.value = nextName || selectedFile.name;
                form.dataset.submittingAfterRename = '1';
                if (typeof form.requestSubmit === 'function') form.requestSubmit();
                else form.submit();
            }
        );
        return;
    }

    if (!needsRename) {
        if (!selectedFile && !stagedRollback && !hasStagedShares && form.id === 'm_upload_form') {
            event.preventDefault();
            DMS.toast('No changes staged.', 'info');
        }
        return;
    }

    event.preventDefault();
    const data = new FormData();
    data.append('id', docId);
    data.append('filename', nextName);
    DMS.showLoading();
    fetch((window.DMS_BASE || '') + 'api/rename.php', { method: 'POST', body: data })
        .then(r => r.json())
        .then(result => {
            if (!result.success) {
                DMS.toast(result.message || 'Rename failed.', 'error');
                return;
            }
            if (selectedFile || stagedRollback || hasStagedShares) {
                form.dataset.submittingAfterRename = '1';
                if (typeof form.requestSubmit === 'function') form.requestSubmit();
                else form.submit();
            } else {
                unlockMasterEditFile().finally(() => {
                    DMS.toast(result.message || 'File renamed.');
                    setTimeout(() => window.location.reload(), 650);
                });
            }
        })
        .catch(() => DMS.toast('Rename failed.', 'error'))
        .finally(() => DMS.hideLoading());
}

function unlockMasterEditFile() {
    const docId = document.getElementById('m_doc_id')?.value;
    if (!docId) return Promise.resolve();
    return fetch((window.DMS_BASE || '') + 'api/version_control.php?action=silent_unlock&id=' + encodeURIComponent(docId))
        .catch(() => {});
}

function markDocumentLocked(docId) {
    if (!docId) return;
    document.querySelectorAll('.file-table tbody tr').forEach(row => {
        const onclickText = Array.from(row.querySelectorAll('[onclick]'))
            .map(el => el.getAttribute('onclick') || '')
            .join(' ');
        const matchesRow = onclickText.includes(`openFileDetail(${docId})`)
            || onclickText.includes(`"id":${docId}`)
            || onclickText.includes(`&quot;id&quot;:${docId}`)
            || onclickText.includes(`'id':${docId}`);
        if (!matchesRow) return;
        const statusBadge = row.querySelector('.badge');
        if (statusBadge) {
            statusBadge.className = 'badge badge-warn';
            statusBadge.textContent = 'Locked';
        }
        const lockButton = Array.from(row.querySelectorAll('.btn-icon-sm')).find(btn => /lock/i.test(btn.title || ''));
        if (lockButton) {
            lockButton.classList.remove('btn-warn');
            lockButton.classList.add('btn-ok');
            lockButton.title = 'Locked by you';
            lockButton.innerHTML = lockIconSvg(false);
        }
    });
}

function shareIconSvg() {
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path><path d="m16 6-4-4-4 4"></path><path d="M12 2v13"></path></svg>';
}

function editIconSvg() {
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"></path></svg>';
}

function downloadIconSvg() {
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><path d="m7 10 5 5 5-5"></path><path d="M12 15V3"></path></svg>';
}

function lockIconSvg(locked) {
    return locked
        ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="10" rx="2"></rect><path d="M7 11V8a5 5 0 0 1 9.8-1.4"></path></svg>'
        : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="10" rx="2"></rect><path d="M7 11V8a5 5 0 0 1 10 0v3"></path></svg>';
}

function deleteIconSvg() {
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M8 6V4h8v2"></path><path d="m19 6-1 14H6L5 6"></path></svg>';
}

function folderDownloadIconSvg() {
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7a2 2 0 0 1 2-2h5l2 2h7a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Z"></path><path d="M12 10v6"></path><path d="m9 13 3 3 3-3"></path></svg>';
}

function jsString(value) {
    return JSON.stringify(String(value || ''));
}

function ensureShareModal() {
    let modal = document.getElementById('shareModal');
    if (!modal) {
        document.body.insertAdjacentHTML('beforeend', '<div id="shareModal" class="modal-overlay"></div>');
        modal = document.getElementById('shareModal');
    }
    if (modal.querySelector('#shareAllUsers')) return;
    modal.innerHTML = `
          <div class="modal-card modal-lg">
            <button class="modal-close" onclick="DMS.closeModal('shareModal')" aria-label="Close">&times;</button>
            <h3>Share Document</h3>
            <p id="shareDocName"></p>
            <input type="hidden" id="shareDocId">
            <label class="privacy-check" style="margin-bottom:12px"><input type="checkbox" id="shareAllUsers" onchange="document.getElementById('shareUserEmail').disabled=this.checked"> <strong>Share with all active users</strong></label>
            <div class="form-group">
              <label>User email</label>
              <input type="email" id="shareUserEmail" placeholder="name@example.com" autocomplete="off">
            </div>
            <div class="perm-grid">
              <label><input type="checkbox" id="sh_all" checked onchange="toggleShareAll(this)"> Select all</label>
              <label><input type="checkbox" id="sh_add" checked> Add</label>
              <label><input type="checkbox" id="sh_edit" checked> Edit</label>
              <label><input type="checkbox" id="sh_delete" checked> Delete</label>
              <label><input type="checkbox" id="sh_download" checked> Download</label>
              <label><input type="checkbox" id="sh_checkout" checked> Lock/Unlock</label>
              <label><input type="checkbox" id="sh_share" checked> Share</label>
            </div>
            <div class="modal-actions">
              <button type="button" class="btn btn-outline" onclick="DMS.closeModal('shareModal')">Cancel</button>
              <button type="button" class="btn btn-primary" onclick="submitShare()">Share Document</button>
            </div>
          </div>`;
}

function installUnifiedShareModal() {
    ensureShareModal();
    window.openShareModal = function(id, name) {
        ensureShareModal();
        DMS._bulkShareIds = Array.isArray(id) ? id.map(Number).filter(Boolean) : null;
        document.getElementById('shareDocId').value = id;
        document.getElementById('shareDocName').textContent = DMS._bulkShareIds ? `${DMS._bulkShareIds.length} selected file(s)` : (name || 'Selected document');
        const allUsers = document.getElementById('shareAllUsers');
        const email = document.getElementById('shareUserEmail');
        if (allUsers) allUsers.checked = false;
        if (email) {
            email.value = '';
            email.disabled = false;
        }
        ['sh_all','sh_add','sh_edit','sh_delete','sh_download','sh_checkout','sh_share'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.checked = true;
        });
        DMS.openModal('shareModal');
    };
    window.toggleShareAll = function(master) {
        ['sh_add','sh_edit','sh_delete','sh_download','sh_checkout','sh_share'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.checked = master.checked;
        });
    };
    window.submitShare = function() {
        const allUsers = document.getElementById('shareAllUsers')?.checked ? 1 : 0;
        const email = document.getElementById('shareUserEmail')?.value.trim() || '';
        if (!allUsers && !email) {
            DMS.toast('Type the user email first', 'error');
            return;
        }
        DMS.showLoading();
        const selectedIds = DMS._bulkShareIds && DMS._bulkShareIds.length ? DMS._bulkShareIds : [document.getElementById('shareDocId').value];
        const payloadBase = {
            share_all_users: allUsers,
            email,
            can_add: document.getElementById('sh_add').checked ? 1 : 0,
            can_edit: document.getElementById('sh_edit').checked ? 1 : 0,
            can_delete: document.getElementById('sh_delete').checked ? 1 : 0,
            can_download: document.getElementById('sh_download').checked ? 1 : 0,
            can_checkout: document.getElementById('sh_checkout').checked ? 1 : 0,
            can_share: document.getElementById('sh_share').checked ? 1 : 0
        };
        Promise.all(selectedIds.map(documentId => fetch((window.DMS_BASE || '') + 'api/share.php?action=grant_direct_access', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ...payloadBase, document_id: documentId })
        })
            .then(async r => {
                const text = await r.text();
                try {
                    return JSON.parse(text);
                } catch (error) {
                    throw new Error(text.replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim() || 'Share failed');
                }
            })))
            .then(results => {
                const failed = results.find(data => !data.success);
                if (!failed) {
                    DMS.toast(selectedIds.length > 1 ? 'Selected files shared successfully' : 'Shared successfully');
                    DMS.closeModal('shareModal');
                } else {
                    DMS.toast(failed.message || 'Share failed', 'error');
                }
            })
            .catch(error => DMS.toast(error.message || 'Share failed', 'error'))
            .finally(() => DMS.hideLoading());
    };

    document.querySelectorAll('a[href*="api/share.php?id="]').forEach(link => {
        if (link.dataset.shareModalReady === '1') return;
        link.dataset.shareModalReady = '1';
        link.addEventListener('click', event => {
            const url = new URL(link.href, window.location.href);
            const id = url.searchParams.get('id');
            const row = link.closest('tr');
            const name = row?.querySelector('.file-name-link')?.textContent?.trim()
                || row?.querySelector('td')?.textContent?.trim()
                || 'Selected document';
            if (id) {
                event.preventDefault();
                window.openShareModal(id, name);
            }
        });
    });
}

function installFolderDownloadActions() {
    document.querySelectorAll('button[title="Folder Access"], .folder-item-node button').forEach(button => {
        const label = (button.textContent || '').trim().toLowerCase();
        if ((button.title === 'Folder Access' || label === 'share') && button.dataset.shareIconReady !== '1') {
            button.dataset.shareIconReady = '1';
            button.classList.add('btn-icon-sm');
            button.style.background = '#6366f1';
            button.style.color = '#fff';
            button.textContent = '';
            button.innerHTML = shareIconSvg();
        }
    });

    const folderLinks = document.querySelectorAll('a[href*="folders.php?id="], a[href*="private.php?folder_id="]');
    folderLinks.forEach(link => {
        const holder = link.closest('.folder-item-node') || link.closest('.private-folder-card');
        if (!holder || holder.querySelector('.folder-download-action')) return;
        const url = new URL(link.getAttribute('href'), window.location.href);
        const folderId = url.searchParams.get('id') || url.searchParams.get('folder_id');
        if (!folderId) return;
        const folderName = link.querySelector('.private-folder-name')?.textContent?.trim()
            || link.textContent.replace(/Folder|📁/g, '').trim()
            || 'folder';
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'btn-icon-sm btn-outline folder-download-action';
        btn.title = 'Download Folder';
        btn.innerHTML = downloadIconSvg();
        btn.addEventListener('click', event => {
            event.preventDefault();
            event.stopPropagation();
            DMS.confirm('Download folder', 'Download this folder and everything inside it?', () => DMS.downloadFolder(folderId, folderName + '.zip'));
        });
        holder.appendChild(btn);
    });
}

function setupFolderBulkTools() {
    document.querySelectorAll('.folder-list-grid, .private-folder-grid').forEach(grid => {
        if (grid.dataset.folderBulkReady === '1') return;
        const cards = Array.from(grid.querySelectorAll('.folder-item-node, a.private-folder-card'))
            .map(card => ({ card, ...getFolderCardData(card) }))
            .filter(item => item.id);
        if (!cards.length) return;
        grid.dataset.folderBulkReady = '1';

        if (grid.previousElementSibling?.classList.contains('folder-bulk-toolbar')) {
            grid.previousElementSibling.remove();
        }

        const toolbar = document.createElement('div');
        toolbar.className = 'table-toolbar folder-bulk-toolbar';
        toolbar.innerHTML = `
            <button type="button" class="btn btn-sm btn-outline" data-folder-select-all>Select All</button>
            <button type="button" class="btn btn-sm btn-outline" data-folder-share-selected>Share Selected</button>
            <button type="button" class="btn btn-sm btn-outline" data-folder-download-selected>Download Selected</button>
        `;
        grid.parentElement?.insertBefore(toolbar, grid);

        cards.forEach(({ card, id }) => {
            if (card.querySelector('.folder-row-check')) return;
            const wrap = document.createElement('label');
            wrap.className = 'folder-select-check';
            wrap.title = 'Select folder';
            wrap.innerHTML = `<input type="checkbox" class="folder-row-check" value="${id}">`;
            wrap.addEventListener('click', event => event.stopPropagation());
            wrap.addEventListener('mousedown', event => event.stopPropagation());
            card.prepend(wrap);
        });

        const checkedItems = () => Array.from(grid.querySelectorAll('.folder-row-check:checked'))
            .map(cb => cards.find(item => String(item.id) === String(cb.value)))
            .filter(Boolean);
        const setAll = checked => grid.querySelectorAll('.folder-row-check').forEach(cb => { cb.checked = checked; });

        toolbar.querySelector('[data-folder-select-all]')?.addEventListener('click', () => setAll(true));
        toolbar.querySelector('[data-folder-share-selected]')?.addEventListener('click', () => {
            const selected = checkedItems();
            if (!selected.length) return DMS.toast('Select at least one folder.', 'error');
            window.openFolderShareModal(selected.map(item => item.id), `${selected.length} selected folder(s)`);
        });
        toolbar.querySelector('[data-folder-download-selected]')?.addEventListener('click', () => {
            const selected = checkedItems();
            if (!selected.length) return DMS.toast('Select at least one folder.', 'error');
            DMS.confirm('Download selected folders', 'Download selected folders and everything inside them?', () => {
                DMS.downloadBulkFolders(selected.map(item => item.id));
            });
        });
    });
}

function getFolderCardData(card) {
    const link = card.matches('a[href]') ? card : card.querySelector('a[href*="folders.php?id="], a[href*="private.php?folder_id="]');
    if (!link) return { id: 0, name: '' };
    const url = new URL(link.getAttribute('href'), window.location.href);
    const id = Number(url.searchParams.get('id') || url.searchParams.get('folder_id') || 0);
    const name = link.querySelector('.private-folder-name')?.textContent?.trim()
        || link.querySelector('.folder-node-meta-title')?.textContent?.replace(/Folder|ðŸ“/g, '').trim()
        || link.textContent.replace(/Folder|ðŸ“/g, '').trim()
        || 'folder';
    return { id, name };
}

function setupFileTableBulkTools() {
    document.querySelectorAll('table.file-table').forEach(table => {
        if (table.dataset.fileBulkReady === '1') return;
        const rows = Array.from(table.querySelectorAll('tbody tr')).filter(row => !row.querySelector('.empty-row'));
        if (!rows.length) return;
        table.dataset.fileBulkReady = '1';
        table.classList.add('has-bulk-select');

        if (table.previousElementSibling?.classList.contains('file-bulk-toolbar')) {
            table.previousElementSibling.remove();
        }
        const headerRow = table.querySelector('thead tr');
        if (headerRow?.querySelector('[data-file-master]')) {
            headerRow.querySelector('[data-file-master]').closest('th')?.remove();
        }
        rows.forEach(row => row.querySelector('.file-row-check')?.closest('td')?.remove());

        const toolbar = document.createElement('div');
        toolbar.className = 'table-toolbar file-bulk-toolbar';
        toolbar.innerHTML = `
            <button type="button" class="btn btn-sm btn-outline" data-file-select-all>Select All</button>
            <button type="button" class="btn btn-sm btn-outline" data-file-share-selected>Share Selected</button>
            <button type="button" class="btn btn-sm btn-outline" data-file-download-selected>Download Selected</button>
            <button type="button" class="btn btn-sm btn-danger" data-file-delete-selected>Delete Selected</button>
        `;
        table.parentElement?.insertBefore(toolbar, table);

        if (headerRow) headerRow.insertAdjacentHTML('afterbegin', '<th><input type="checkbox" data-file-master></th>');
        rows.forEach(row => {
            const id = getFileIdFromRow(row);
            row.insertAdjacentHTML('afterbegin', `<td>${id ? `<input type="checkbox" class="file-row-check" value="${id}">` : ''}</td>`);
        });

        const checkedIds = () => Array.from(table.querySelectorAll('.file-row-check:checked')).map(cb => Number(cb.value)).filter(Boolean);
        const setAll = checked => table.querySelectorAll('.file-row-check').forEach(cb => { cb.checked = checked; });

        toolbar.querySelector('[data-file-select-all]')?.addEventListener('click', () => setAll(true));
        table.querySelector('[data-file-master]')?.addEventListener('change', event => setAll(event.target.checked));
        toolbar.querySelector('[data-file-share-selected]')?.addEventListener('click', () => {
            const ids = checkedIds();
            if (!ids.length) return DMS.toast('Select at least one file.', 'error');
            window.openShareModal(ids, `${ids.length} selected file(s)`);
        });
        toolbar.querySelector('[data-file-download-selected]')?.addEventListener('click', () => {
            const ids = checkedIds();
            if (!ids.length) return DMS.toast('Select at least one file.', 'error');
            DMS.downloadBulkFiles(ids);
        });
        toolbar.querySelector('[data-file-delete-selected]')?.addEventListener('click', () => {
            const ids = checkedIds();
            if (!ids.length) return DMS.toast('Select at least one file.', 'error');
            DMS.confirm('Delete selected files', 'Move selected files to trash?', () => {
                DMS.showLoading();
                fetch((window.DMS_BASE || '') + 'api/bulk_documents.php?action=delete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ ids })
                })
                    .then(r => r.json())
                    .then(data => {
                        DMS.toast(data.message || 'Bulk action complete', data.success ? 'success' : 'error');
                        if (data.success) setTimeout(() => window.location.reload(), 650);
                    })
                    .catch(() => DMS.toast('Bulk delete failed', 'error'))
                    .finally(() => DMS.hideLoading());
            });
        });
    });
}

function getFileIdFromRow(row) {
    const trigger = row.querySelector('.file-name-link[onclick], .version-link[onclick], [onclick*="openMasterEditModal"]');
    const text = trigger?.getAttribute('onclick') || '';
    const detailMatch = text.match(/openFileDetail\((\d+)\)/);
    if (detailMatch) return Number(detailMatch[1]);
    const modalMatch = text.match(/"id"\s*:\s*(\d+)/);
    return modalMatch ? Number(modalMatch[1]) : 0;
}

function renderPrivateFolderFiles() {
    const params = new URLSearchParams(window.location.search);
    const folderId = params.get('folder_id');
    const table = document.querySelector('table.file-table');
    if (!folderId || !table || !window.location.pathname.includes('private.php')) return;

    fetch((window.DMS_BASE || '') + 'api/folder_files.php?folder_id=' + encodeURIComponent(folderId))
        .then(r => r.json())
        .then(data => {
            if (!data.success || !Array.isArray(data.files)) return;
            const tbody = table.querySelector('tbody');
            if (!tbody) return;
            tbody.innerHTML = data.files.length ? data.files.map(file => {
                const date = (file.created_at || '').slice(0, 10);
                const time = file.created_at ? new Date(file.created_at.replace(' ', 'T')).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';
                const actions = [
                    file.can_edit ? `<button type="button" class="btn-icon-sm btn-primary" title="Edit File Details" onclick="openMasterEditModal(${JSON.stringify(file).replace(/"/g, '&quot;')})">${editIconSvg()}</button>` : '',
                    file.can_share ? `<button type="button" class="btn-icon-sm" style="background:#6366f1;color:#fff" title="Share" onclick="openShareModal(${file.id}, ${jsString(file.filename).replace(/"/g, '&quot;')})">${shareIconSvg()}</button>` : '',
                    file.can_download ? `<button class="btn-icon-sm btn-outline" title="Download" onclick="DMS.confirm('Download file','Choose where to save ${escapeHtml(file.filename)}?', ()=>DMS.downloadFile(${file.id}, ${jsString(file.filename).replace(/"/g, '&quot;')}))">${downloadIconSvg()}</button>` : '',
                    file.can_checkout ? `<button type="button" class="btn-icon-sm ${file.is_locked ? 'btn-ok' : 'btn-warn'}" title="${file.is_locked ? 'Unlock' : 'Lock'}" onclick="DMS.confirm('${file.is_locked ? 'Unlock file' : 'Lock file'}','${file.is_locked ? 'Release the lock on this file?' : 'Lock this file for editing?'}', ()=>location.href='${(window.DMS_BASE || '')}api/version_control.php?action=${file.is_locked ? 'checkin' : 'checkout'}&origin=private&id=${file.id}&folder_id=${folderId}')">${lockIconSvg(file.is_locked)}</button>` : '',
                    file.can_delete ? `<button class="btn-icon-sm btn-danger" title="Delete" onclick="DMS.confirm('Delete','Move this file to trash?', ()=>location.href='${(window.DMS_BASE || '')}api/delete.php?id=${file.id}&origin=private')">${deleteIconSvg()}</button>` : ''
                ].filter(Boolean).join('');
                return `<tr data-private-file-name="${escapeHtml(file.filename.toLowerCase())}">
                    <td><button type="button" class="file-name-link" onclick="DMS.openFileDetail(${file.id})">${escapeHtml(file.filename)}</button></td>
                    <td><span class="file-type-pill">${escapeHtml(file.ext)}</span></td>
                    <td><button type="button" class="version-link" onclick="DMS.openFileDetail(${file.id})">v${file.version || 1}</button></td>
                    <td>${formatBytes(Number(file.size || 0))}</td>
                    <td>${escapeHtml(file.uploaded_by || '-')}</td>
                    <td>${file.is_locked ? '<span class="badge badge-warn">Locked</span>' : '<span class="badge badge-ok">Available</span>'}</td>
                    <td class="date-col">${escapeHtml(date)}<span class="timestamp-time">${escapeHtml(time)}</span></td>
                    <td><div class="actions-container nowrap">${actions}</div></td>
                </tr>`;
            }).join('') : '<tr><td colspan="8" class="empty-row">No private files.</td></tr>';
            table.dataset.fileBulkReady = '';
            setupFileTableBulkTools();
        })
        .catch(() => {});
}

function setupAuditBulkTools() {
    if (!window.location.pathname.includes('audit.php')) return;
    const table = document.querySelector('.audit-table');
    if (!table || table.dataset.bulkReady === '1') return;
    table.dataset.bulkReady = '1';
    const params = new URLSearchParams(window.location.search);

    fetch((window.DMS_BASE || '') + 'api/audit_bulk.php?action=page_ids&' + params.toString())
        .then(r => r.json())
        .then(data => {
            if (!data.success || !Array.isArray(data.ids)) return;
            const headerRow = table.querySelector('thead tr');
            const bodyRows = Array.from(table.querySelectorAll('tbody tr')).filter(row => !row.querySelector('.empty-row'));
            if (!headerRow || !bodyRows.length) return;

            const toolbar = document.createElement('div');
            toolbar.className = 'table-toolbar audit-bulk-toolbar';
            toolbar.innerHTML = `
                <button type="button" class="btn btn-sm btn-outline" data-audit-select-all>Select All</button>
                <button type="button" class="btn btn-sm btn-danger" data-audit-delete>Delete Selected</button>
            `;
            table.closest('.table-responsive')?.before(toolbar);

            const th = document.createElement('th');
            th.innerHTML = '<input type="checkbox" data-audit-master>';
            headerRow.prepend(th);
            bodyRows.forEach((row, index) => {
                const id = data.ids[index];
                const td = document.createElement('td');
                td.innerHTML = id ? `<input type="checkbox" class="audit-row-check" value="${id}">` : '';
                row.prepend(td);
            });

            const setAll = checked => document.querySelectorAll('.audit-row-check').forEach(cb => { cb.checked = checked; });
            toolbar.querySelector('[data-audit-select-all]')?.addEventListener('click', () => setAll(true));
            document.querySelector('[data-audit-master]')?.addEventListener('change', event => setAll(event.target.checked));
            toolbar.querySelector('[data-audit-delete]')?.addEventListener('click', () => {
                const ids = Array.from(document.querySelectorAll('.audit-row-check:checked')).map(cb => Number(cb.value)).filter(Boolean);
                if (!ids.length) {
                    DMS.toast('Select at least one audit log', 'error');
                    return;
                }
                DMS.confirm('Delete audit logs', 'Delete selected audit logs?', () => {
                    DMS.showLoading();
                    fetch((window.DMS_BASE || '') + 'api/audit_bulk.php?action=delete', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ ids })
                    })
                        .then(r => r.json())
                        .then(result => {
                            if (result.success) {
                                DMS.toast(result.message || 'Audit logs deleted');
                                setTimeout(() => window.location.reload(), 600);
                            } else {
                                DMS.toast(result.message || 'Delete failed', 'error');
                            }
                        })
                        .catch(() => DMS.toast('Delete failed', 'error'))
                        .finally(() => DMS.hideLoading());
                });
            });
        })
        .catch(() => {});
}

document.addEventListener('DOMContentLoaded', () => {
    const toastData = document.getElementById('toast-data');
    if (toastData) {
        DMS.toast(toastData.dataset.msg, toastData.dataset.type || 'success');
    }

    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', e => {
            e.preventDefault();
            const href = el.href || el.dataset.href;
            DMS.confirm(el.dataset.confirmTitle || 'Confirm', el.dataset.confirm || 'Are you sure?', () => {
                if (href) window.location.href = href;
                else if (el.dataset.form) document.getElementById(el.dataset.form).submit();
            });
        });
    });

    const notifBtn = document.getElementById('notif-btn');
    const notifPanel = document.getElementById('notif-panel');
    if (notifBtn && notifPanel) {
        notifBtn.addEventListener('click', e => {
            e.stopPropagation();
            notifPanel.classList.toggle('open');
        });
        document.addEventListener('click', () => notifPanel.classList.remove('open'));
    }

    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', () => {
            if ((form.method || '').toLowerCase() === 'post') DMS.showLoading();
        });
    });

    document.querySelectorAll('a[href*="api/version_control.php"], a[href*="api/delete.php"]').forEach(link => {
        link.addEventListener('click', () => DMS.showLoading());
    });

    setupUploadModal();
    installUnifiedShareModal();
    installUnifiedFolderShareModal();
    installFolderDownloadActions();
    setupFolderBulkTools();
    renderPrivateFolderFiles();
    setupFileTableBulkTools();
    setupAuditBulkTools();
    installMasterEditModalCompatibility();

    if (document.getElementById('folderShareModal')) {
        document.querySelectorAll('a.private-folder-card[href*="folder_id="]').forEach(card => {
            if (card.querySelector('.private-folder-share')) return;
            const url = new URL(card.getAttribute('href'), window.location.href);
            const folderId = url.searchParams.get('folder_id');
            const folderName = card.querySelector('.private-folder-name')?.textContent?.trim() || 'Private folder';
            if (!folderId) return;
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-sm btn-outline private-folder-share';
            btn.textContent = 'Share';
            btn.addEventListener('click', event => {
                event.preventDefault();
                event.stopPropagation();
                window.openFolderShareModal(folderId, folderName);
            });
            card.appendChild(btn);
        });
    }
});

function setupUploadModal() {
    const fileInput = document.getElementById('fileInput');
    const folderInput = document.getElementById('folderInput');
    const previewList = document.getElementById('uploadPreview');
    const uploadBtn = document.getElementById('btnDoUpload');
    const createFolderBtn = document.getElementById('btnCreateFolder');
    const tabs = document.querySelectorAll('.upload-tab');
    const filePanel = document.getElementById('uploadTabFile');
    const folderPanel = document.getElementById('uploadTabFolder');
    const dropZone = document.getElementById('dropZone');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(item => item.classList.remove('active'));
            tab.classList.add('active');
            const showFile = tab.dataset.tab === 'file';
            if (filePanel) filePanel.style.display = showFile ? '' : 'none';
            if (folderPanel) folderPanel.style.display = showFile ? 'none' : '';
        });
    });

    const renderSelectedFiles = () => {
        if (!previewList || !fileInput || !folderInput) return;
        const files = [...Array.from(fileInput.files || []), ...Array.from(folderInput.files || [])];
        previewList.innerHTML = files.length
            ? files.map(file => `<div class="upload-preview-item"><span>${escapeHtml(file.webkitRelativePath || file.name)}</span><small>${Math.ceil(file.size / 1024)} KB</small></div>`).join('')
            : '';
    };

    if (fileInput) fileInput.addEventListener('change', renderSelectedFiles);
    if (folderInput) folderInput.addEventListener('change', renderSelectedFiles);
    if (dropZone && fileInput) {
        dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('drag-over'); });
        dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-over'));
        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            dropZone.classList.remove('drag-over');
            fileInput.files = e.dataTransfer.files;
            renderSelectedFiles();
        });
    }

    if (uploadBtn && fileInput) {
        uploadBtn.addEventListener('click', () => {
            const selectedFiles = [...Array.from(fileInput.files || []), ...Array.from(folderInput?.files || [])];
            if (!selectedFiles.length) {
                DMS.toast('Choose at least one file', 'error');
                return;
            }
            const formData = new FormData();
            selectedFiles.forEach(file => formData.append('files[]', file, file.webkitRelativePath || file.name));
            formData.append('folder_id', document.getElementById('uploadFolderId')?.value || 'root');
            formData.append('is_private', document.getElementById('uploadPrivate')?.checked ? '1' : '0');
            formData.append('sharing_scope', document.getElementById('uploadPrivate')?.checked ? 'private' : 'all');

            uploadBtn.disabled = true;
            DMS.showLoading();
            fetch((window.DMS_BASE || '') + 'api/upload.php', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        DMS.toast(data.message || 'Uploaded');
                        setTimeout(() => window.location.reload(), 700);
                    } else {
                        DMS.toast(data.message || 'Upload failed', 'error');
                    }
                })
                .catch(() => DMS.toast('Upload failed', 'error'))
                .finally(() => { uploadBtn.disabled = false; DMS.hideLoading(); });
        });
    }

    if (createFolderBtn) {
        createFolderBtn.addEventListener('click', () => {
            const nameInput = document.getElementById('newFolderName');
            const name = nameInput?.value.trim() || '';
            if (!name) {
                DMS.toast('Folder name required', 'error');
                return;
            }
            const formData = new FormData();
            formData.append('action', 'create_folder');
            formData.append('folder_name', name);
            const createdPrivate = document.getElementById('newFolderPrivate')?.checked;
            formData.append('is_private', createdPrivate ? '1' : '0');
            formData.append('parent_folder_id', document.getElementById('uploadFolderId')?.value || 'root');

            createFolderBtn.disabled = true;
            DMS.showLoading();
            fetch((window.DMS_BASE || '') + 'api/upload.php', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        DMS.toast(data.message || 'Folder created');
                        setTimeout(() => {
                            window.location.href = createdPrivate ? ((window.DMS_BASE || '') + 'pages/private.php') : window.location.href;
                        }, 700);
                    } else {
                        DMS.toast(data.message || 'Create folder failed', 'error');
                    }
                })
                .catch(() => DMS.toast('Create folder failed', 'error'))
                .finally(() => { createFolderBtn.disabled = false; DMS.hideLoading(); });
        });
    }
}

window.addEventListener('load', () => {
    document.body.classList.remove('dms-loading');
    const overlay = document.getElementById('page-loading-overlay');
    if (overlay) {
        overlay.classList.add('is-hidden');
        setTimeout(() => overlay.style.display = 'none', 500);
    }
});
