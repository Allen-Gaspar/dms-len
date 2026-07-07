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

    downloadFile(id) {
        window.location.href = (window.DMS_BASE || '') + 'api/download.php?id=' + id;
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
        fetch((window.DMS_BASE || '') + 'api/rename_file.php', { method: 'POST', body: formData })
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
                title.textContent = doc.filename;
                preview.innerHTML = renderPreview(data.preview_url, data.ext, doc.filename);
                info.innerHTML = `
                    <dl class="file-meta-list">
                        <dt>Version</dt><dd>v${doc.version || 1}</dd>
                        <dt>Uploaded By</dt><dd>${escapeHtml(doc.uploader_name || '-')}</dd>
                        <dt>Status</dt><dd>${doc.is_locked == 1 ? 'Locked' : 'Available'}</dd>
                        <dt>Created</dt><dd>${escapeHtml((doc.created_at || '').slice(0, 16))}</dd>
                    </dl>`;
                actions.innerHTML = `<a class="btn btn-primary" href="${data.download_url}">Download</a>`;

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
        document.getElementById('fdTitle').textContent = title;
        document.getElementById('fdPreview').innerHTML = renderPreview(url, ext, title);
    }
};
window.DMS = DMS;

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, ch => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    }[ch]));
}

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
    if (['txt','csv','json','xml','html','htm','css','js','php','sql','md','log'].includes(cleanExt)) {
        return `<iframe class="file-preview-frame code-preview" src="${url}" title="${escapeHtml(filename)}"></iframe>`;
    }
    if (['ppt', 'pptx', 'doc', 'docx', 'xls', 'xlsx'].includes(cleanExt)) {
        return `<div class="preview-fallback office-preview"><strong>${escapeHtml(filename)}</strong><span>This Office file cannot be rendered directly by the browser.</span><a class="btn btn-primary btn-sm" href="${url}">Download / Open</a></div>`;
    }
    return `<div class="preview-fallback"><strong>${escapeHtml(filename)}</strong><span>No browser preview available.</span><a class="btn btn-primary btn-sm" href="${url}">Open File</a></div>`;
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
