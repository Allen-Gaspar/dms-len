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
        document.getElementById('fdTitle').textContent = title;
        document.getElementById('fdPreview').innerHTML = renderPreview(url, ext, title);
    }
};
window.DMS = DMS;

window.openFolderShareModal = window.openFolderShareModal || function(folderId, folderName) {
    if (!document.getElementById('folderShareModal')) return;
    const idInput = document.getElementById('folderShareId');
    const nameLabel = document.getElementById('folderShareName');
    const emailInput = document.getElementById('folderShareEmail');
    const allUsers = document.getElementById('folderShareAllUsers');
    if (idInput) idInput.value = folderId;
    if (nameLabel) nameLabel.textContent = folderName || 'Selected folder';
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

window.submitFolderShare = window.submitFolderShare || function() {
    const folderId = document.getElementById('folderShareId')?.value;
    const allUsers = document.getElementById('folderShareAllUsers')?.checked ? 1 : 0;
    const email = document.getElementById('folderShareEmail')?.value.trim() || '';
    if (!folderId) return;
    if (!allUsers && !email) {
        DMS.toast('Type the user email first', 'error');
        return;
    }
    DMS.showLoading();
    fetch((window.DMS_BASE || '') + 'api/folder_control.php?action=grant_access_by_email', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            folder_id: folderId,
            all_users: allUsers,
            email,
            can_add: document.getElementById('fs_add')?.checked ? 1 : 0,
            can_edit: document.getElementById('fs_edit')?.checked ? 1 : 0,
            can_delete: document.getElementById('fs_delete')?.checked ? 1 : 0,
            can_download: document.getElementById('fs_download')?.checked ? 1 : 0,
            can_checkout: document.getElementById('fs_checkout')?.checked ? 1 : 0,
            can_share: document.getElementById('fs_share')?.checked ? 1 : 0
        })
    })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                DMS.toast(data.message || 'Folder shared successfully');
                DMS.closeModal('folderShareModal');
            } else {
                DMS.toast(data.message || 'Folder share failed', 'error');
            }
        })
        .catch(() => DMS.toast('Folder share failed', 'error'))
        .finally(() => DMS.hideLoading());
};

window.toggleFolderShareAll = window.toggleFolderShareAll || function(master) {
    ['fs_add','fs_edit','fs_delete','fs_download','fs_checkout','fs_share'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.checked = master.checked;
    });
};

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
    if (['txt','csv','json','xml','html','htm','css','js','php','sql','md','log'].includes(cleanExt)) {
        return `<iframe class="file-preview-frame code-preview" src="${url}" title="${escapeHtml(filename)}"></iframe>`;
    }
    if (['ppt', 'pptx', 'doc', 'docx', 'xls', 'xlsx'].includes(cleanExt)) {
        return `<div class="preview-fallback office-preview"><strong>${escapeHtml(filename)}</strong><span>Office preview depends on browser support for this file type.</span><iframe class="file-preview-frame" src="${url}" title="${escapeHtml(filename)}"></iframe></div>`;
    }
    return `<iframe class="file-preview-frame" src="${url}" title="${escapeHtml(filename)}"></iframe>`;
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
                    file.can_download ? `<button class="btn-icon-sm btn-outline" title="Download" onclick="DMS.confirm('Download file','Choose where to save ${escapeHtml(file.filename)}?', ()=>DMS.downloadFile(${file.id}, '${escapeHtml(file.filename).replace(/'/g, '&#039;')}'))">Download</button>` : '',
                    file.can_checkout ? `<a class="btn-icon-sm btn-warn" title="Lock/Unlock" href="${(window.DMS_BASE || '')}api/version_control.php?action=${file.is_locked ? 'checkin' : 'checkout'}&origin=private&id=${file.id}&folder_id=${folderId}">${file.is_locked ? 'Unlock' : 'Lock'}</a>` : '',
                    file.can_delete ? `<button class="btn-icon-sm btn-danger" title="Delete" onclick="DMS.confirm('Delete','Move this file to trash?', ()=>location.href='${(window.DMS_BASE || '')}api/delete.php?id=${file.id}&origin=private')">Delete</button>` : ''
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
    renderPrivateFolderFiles();
    setupAuditBulkTools();

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
