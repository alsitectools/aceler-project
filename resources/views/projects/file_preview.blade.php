<!-- Modal for File Preview -->
<div class="modal fade" id="filePreviewModal" tabindex="-1" role="dialog" aria-labelledby="filePreviewModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filePreviewModalLabel">{{ __('File Preview') }}</h5>
                <div class="ms-auto d-flex align-items-center">
                    <a id="filePreviewDownloadBtn" href="#" class="btn btn-sm btn-primary me-2" target="_blank"
                        style="display: none;" title="Descargar">
                        <i class="fa fa-download"></i>
                    </a>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body" id="filePreviewModalBody">
                <!-- Content will be injected here via JavaScript -->
            </div>
        </div>
    </div>
</div>

<style>
    #filePreviewModal {
        z-index: 2000;
    }

    .modal-backdrop.file-preview-backdrop {
        z-index: 1990;
    }

    .txtPreviewContent {
        background: rgb(249 244 232 / 47%);
        color: #3b2f2f;
        border: 1px solid #ab182c;
        border-radius: 8px;
        padding: 16px;
        max-height: 500px;
        overflow: auto;
        white-space: pre-wrap;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        scrollbar-color: #AA182C #ffffff;
        scrollbar-width: thin;
    }

    .txtPreviewContent::-webkit-scrollbar {
        width: 8px;
        height: 10px;
    }

    .txtPreviewContent::-webkit-scrollbar-track {
        background: #ffffff;
        border-radius: 4px;
    }

    .txtPreviewContent::-webkit-scrollbar-thumb {
        background: #AA182C;
        border-radius: 4px;
        height: 10px;
    }
</style>

@push('scripts')
    <!-- Three.js & OrbitControls from CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dxf-parser@1.1.2/dist/dxf-parser.js"></script>
    <script>
        const previewRuntime = {
            sessionId: 0,
            activeSessionId: 0,
            modalInstance: null,
            controllers: [],
            timeouts: [],
            lifecycleBound: false
        };

        function startPreviewSession() {
            previewRuntime.sessionId += 1;
            previewRuntime.activeSessionId = previewRuntime.sessionId;
            return previewRuntime.activeSessionId;
        }

        function isPreviewSessionActive(sessionId) {
            return sessionId === previewRuntime.activeSessionId;
        }

        function registerPreviewController(controller) {
            previewRuntime.controllers.push(controller);
        }

        function registerPreviewTimeout(timeoutId) {
            previewRuntime.timeouts.push(timeoutId);
        }

        function clearPreviewAsyncResources() {
            previewRuntime.controllers.forEach(controller => {
                try {
                    controller.abort();
                } catch (e) {
                    console.warn('Abort controller cleanup warning:', e);
                }
            });
            previewRuntime.controllers = [];

            previewRuntime.timeouts.forEach(timeoutId => clearTimeout(timeoutId));
            previewRuntime.timeouts = [];
        }

        function ensurePreviewModalLifecycle() {
            const modalEl = document.getElementById('filePreviewModal');
            if (!modalEl) {
                return null;
            }

            previewRuntime.modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);

            if (!previewRuntime.lifecycleBound) {
                // Listener para log de descarga
                const btnDownload = document.getElementById('filePreviewDownloadBtn');
                if (btnDownload) {
                    btnDownload.addEventListener('click', (e) => {
                        console.log('Download URL from Inside:', btnDownload.href);
                    });
                }

                modalEl.addEventListener('hidden.bs.modal', () => {
                    previewRuntime.activeSessionId = 0;
                    clearPreviewAsyncResources();

                    if (window._dxfViewerDestroy) {
                        window._dxfViewerDestroy();
                        window._dxfViewerDestroy = null;
                    }

                    const downloadBtn = document.getElementById('filePreviewDownloadBtn');
                    if (downloadBtn) {
                        downloadBtn.style.display = 'none';
                        downloadBtn.href = '#';
                    }

                    const body = document.getElementById('filePreviewModalBody');
                    if (body) {
                        body.innerHTML = '';
                        body.style.cssText = '';
                        body.className = 'modal-body';
                    }
                });

                previewRuntime.lifecycleBound = true;
            }

            return previewRuntime.modalInstance;
        }

        function showPreviewModal() {
            const modalEl = document.getElementById('filePreviewModal');
            const modalInstance = ensurePreviewModalLifecycle();
            if (!modalEl || !modalInstance) {
                return;
            }

            modalEl.style.zIndex = '2000';
            modalInstance.show();

            const backdropTimeout = setTimeout(() => {
                const backdrops = document.querySelectorAll('.modal-backdrop');
                const latestBackdrop = backdrops[backdrops.length - 1];
                if (latestBackdrop) {
                    latestBackdrop.classList.add('file-preview-backdrop');
                }
            }, 0);

            registerPreviewTimeout(backdropTimeout);
        }

        // --- Helper: Render Archive UI (for ZIP) ---
        function renderArchiveUI(data, url, title, modalBodyId, sessionId = null) {
            if (sessionId && !isPreviewSessionActive(sessionId)) {
                return;
            }

            const modalBody = document.getElementById(modalBodyId);
            if (!modalBody) {
                return;
            }

            // Helper: Format Bytes
            const formatBytes = (bytes, decimals = 2) => {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const dm = decimals < 0 ? 0 : decimals;
                const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
            };

            // Helper: Get Icon Class
            const getIcon = (filename, isDir) => {
                if (isDir) return 'fa-solid fa-folder text-warning';
                const ext = filename.split('.').pop().toLowerCase();
                const icons = {
                    'pdf': 'fa-solid fa-file-pdf text-danger',
                    'doc': 'fa-solid fa-file-word text-primary',
                    'docx': 'fa-solid fa-file-word text-primary',
                    'xls': 'fa-solid fa-file-excel text-success',
                    'xlsx': 'fa-solid fa-file-excel text-success',
                    'jpg': 'fa-solid fa-file-image text-info',
                    'jpeg': 'fa-solid fa-file-image text-info',
                    'png': 'fa-solid fa-file-image text-info',
                    'gif': 'fa-solid fa-file-image text-info',
                    'dwg': 'fa-solid fa-compass-drafting text-danger',
                    'dxf': 'fa-solid fa-compass-drafting text-danger',
                    'txt': 'fa-solid fa-file-lines text-secondary',
                    'zip': 'fa-solid fa-file-zipper text-warning',
                    'rar': 'fa-solid fa-file-zipper text-warning'
                };
                return icons[ext] || 'fa-solid fa-file text-secondary';
            };

            const totalSize = formatBytes(data.stats.total_size);
            const fileCount = data.stats.total_files;

            // Sort files
            const files = data.files.sort((a, b) => {
                if (a.is_dir === b.is_dir) return a.name.localeCompare(b.name);
                return a.is_dir ? -1 : 1;
            });

            // Helper: Escape HTML
            const escapeHtml = (text) => {
                if (!text) return text;
                return text
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            };

            const headerHtml = `
                <div class="bg-light border-bottom p-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h5 class="mb-0 text-primary"><i class="fa-solid fa-box-archive me-2"></i>Vista previa del contenido</h5>
                        <small class="text-muted">
                            <span class="fw-bold">${fileCount}</span> archivos | <span class="fw-bold">${totalSize}</span> tamaño descomprimido
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                        <input type="text" id="archive-search" class="form-control form-control-sm" placeholder="Buscar archivos..." style="width: 200px;">
                    </div>
                </div>
            `;

            const listHtml = `
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-hover table-striped mb-0 text-sm">
                        <thead class="table-light sticky-top" style="top: 0; z-index: 10;">
                            <tr>
                                <th style="width: 60%;">Nombre</th>
                                <th style="width: 20%;" class="text-end">Tamaño</th>
                                <th style="width: 20%;" class="text-end">Tipo</th>
                            </tr>
                        </thead>
                        <tbody id="archive-file-list">
                            ${files.map(f => {
                                const icon = getIcon(f.name, f.is_dir);
                                const sizeDisplay = f.is_dir ? '-' : formatBytes(f.size);
                                const typeDisplay = f.is_dir ? 'Carpeta' : escapeHtml(f.name.split('.').pop().toUpperCase());
                                const safeName = escapeHtml(f.name);
                                return `
                                                                                                                                                                                                                            <tr class="archive-item" data-name="${safeName.toLowerCase()}">
                                                                                                                                                                                                                                <td class="text-break">
                                                                                                                                                                                                                                    <i class="${icon} me-2" style="width: 20px; text-align: center;"></i>
                                                                                                                                                                                                                                    ${safeName}
                                                                                                                                                                                                                                </td>
                                                                                                                                                                                                                                <td class="text-end text-nowrap">${sizeDisplay}</td>
                                                                                                                                                                                                                                <td class="text-end text-nowrap"><small class="badge bg-secondary opacity-50">${typeDisplay}</small></td>
                                                                                                                                                                                                                            </tr>
                                                                                                                                                                                                                        `;
                            }).join('')}
                        </tbody>
                    </table>
                </div>
            `;

            modalBody.innerHTML = `
                <div class="d-flex flex-column h-100">
                    ${headerHtml}
                    ${listHtml}
                    ${files.length === 0 ? '<div class="p-4 text-center text-muted">El archivo está vacío</div>' : ''}
                </div>
            `;

            const searchInput = document.getElementById('archive-search');
            if (searchInput) {
                let searchDebounce = null;
                searchInput.addEventListener('input', (e) => {
                    if (searchDebounce) {
                        clearTimeout(searchDebounce);
                    }

                    searchDebounce = setTimeout(() => {
                        if (sessionId && !isPreviewSessionActive(sessionId)) {
                            return;
                        }

                        const term = e.target.value.toLowerCase();
                        const rows = document.querySelectorAll('.archive-item');
                        rows.forEach(row => {
                            const name = row.dataset.name;
                            row.style.display = name.includes(term) ? '' : 'none';
                        });
                    }, 120);

                    registerPreviewTimeout(searchDebounce);
                });
            }
        }

        function previewFile(idProject, titleMilestone, fileName, extension) {
            const allowedExtensions = ['txt', 'png', 'jpeg', 'jpg', 'pdf', 'doc', 'docx', 'zip', 'rar', 'dxf', 'dwg'];
            const normalizedExtension = (extension || '').toLowerCase().replace('.', '');
            if (!allowedExtensions.includes(normalizedExtension)) {
                show_toastr('{{ __('Error') }}', '{{ __('This file type cannot be previewed.') }}', 'error');
                return;
            }

            const downloadUrl = "{{ route('project.downloadFile') }}";

            $.ajax({
                url: downloadUrl,
                method: 'POST',
                data: {
                    "idProject": idProject,
                    "milestoneTitle": titleMilestone,
                    "fileName": fileName,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        // Pass parameters to openFilePreview including ID and milestone for ZIP reader
                        openFilePreview(response.file_url, normalizedExtension, fileName, idProject,
                            titleMilestone);
                    } else {
                        console.error("Error: File not found or could not be loaded.");
                        openPreviewError(fileName);
                    }
                },
                error: function(xhr) {
                    console.error("Error fetching file url", xhr);
                    openPreviewError(fileName);
                }
            });
        }

        function openPreviewError(title) {
            const modalBody = document.getElementById('filePreviewModalBody');
            modalBody.innerHTML = `
                <div class="d-flex flex-column align-items-center justify-content-center" style="height: 100%; min-height: 300px;">
                    <i class="fa-solid fa-circle-exclamation" style="font-size: 5rem; color: #aa182c;"></i>
                    <p class="mt-3 fw-bold fs-3" style="color: #aa182c;">{{ __('Algo ha salido mal') }}</p>
                </div>
            `;
            document.getElementById('filePreviewModalLabel').innerText = title;
            showPreviewModal();
        }

        function openFilePreview(url, extension, title, fileProjectId = null, milestoneTitle = null) {
            const sessionId = startPreviewSession();
            clearPreviewAsyncResources();

            if (window._dxfViewerDestroy) {
                window._dxfViewerDestroy();
                window._dxfViewerDestroy = null;
            }

            const modalBody = document.getElementById('filePreviewModalBody');
            modalBody.innerHTML = ''; // Limpiar contenido previo

            // Reset base styles
            modalBody.style.cssText = '';
            modalBody.className = 'modal-body';

            modalBody.style.borderBottomRightRadius = '10px';
            modalBody.style.borderBottomLeftRadius = '10px';

            // Find the modal dialog to manipulate classes
            const modalDialog = document.querySelector('#filePreviewModal .modal-dialog');

            // Safety check
            if (!modalDialog) {
                console.warn('Modal dialog not found!');
                return;
            }

            // Reset modal size classes and inline styles
            modalDialog.classList.remove('modal-xl', 'modal-lg', 'modal-sm');
            modalDialog.style.maxWidth = '';
            modalDialog.style.width = '';

            const ext = (extension || '').toLowerCase().replace('.', '');
            let element = null;

            // Reset download button
            const headerDownloadBtn = document.getElementById('filePreviewDownloadBtn');
            if (headerDownloadBtn) {
                if (['png', 'jpeg', 'jpg', 'txt', 'zip'].includes(ext)) {
                    headerDownloadBtn.href = url;
                    headerDownloadBtn.setAttribute('download', title || 'download'); // Force download
                    headerDownloadBtn.style.display = 'inline-block';
                    headerDownloadBtn.title = 'Descargar';
                    headerDownloadBtn.innerHTML = '<i class="fa fa-download"></i>';
                } else {
                    headerDownloadBtn.style.display = 'none';
                    headerDownloadBtn.removeAttribute('download');
                }
            }

            if (['png', 'jpeg', 'jpg'].includes(ext)) {
                // Apply specific styles for Image centering. 
                // User requirement: Modal fits image, image max 400px.

                // Use fit-content so the modal shrinks to the image size
                modalDialog.style.width = 'fit-content';
                modalDialog.style.maxWidth = '100vw'; // Reset bootstrap limit to avoid conflict, but fit-content handles it

                modalBody.style.padding = '0';
                modalBody.style.display = 'flex';
                // modalBody.style.flexDirection = 'column'; // Allow button below image -> REVERTED: Button moved to header
                modalBody.style.justifyContent = 'center';
                modalBody.style.alignItems = 'center';
                modalBody.style.backgroundColor = '#f8f9fa';

                element = document.createElement('div');
                element.style.display = 'flex';
                element.style.flexDirection = 'column';
                element.style.alignItems = 'center';

                const img = document.createElement('img');
                img.src = url;

                img.style.maxWidth = '400px';
                img.style.maxHeight = '400px';
                img.style.padding = '15px'; // Standard padding again

                img.style.objectFit = 'contain';
                img.style.display = 'block';

                img.onerror = function() {
                    openPreviewError(title);
                };

                element.appendChild(img);
            } else if (ext === 'pdf') {
                modalDialog.classList.add('modal-lg');
            } else {
                // For other files (DXF, etc.), use the large modal
                modalDialog.classList.add('modal-xl');
            }

            if (ext === 'txt') {
                modalBody.style.minHeight = '400px';
                modalBody.style.display = 'flex';
                modalBody.style.justifyContent = 'center';
                modalBody.style.alignItems = 'center';
                // modalBody.style.flexDirection = 'column'; // REVERTED

                element = document.createElement('div');
                element.className = 'w-100 position-relative';

                const pre = document.createElement('pre');
                pre.className = 'txtPreviewContent';
                pre.textContent = '{{ __('Loading...') }}';

                element.appendChild(pre);

                const txtController = new AbortController();
                registerPreviewController(txtController);

                fetch(url, {
                        cache: 'no-store',
                        signal: txtController.signal
                    })
                    .then(response => {
                        if (!isPreviewSessionActive(sessionId)) {
                            return Promise.reject(new Error('Preview session canceled'));
                        }
                        if (!response.ok) {
                            throw new Error('Failed to load text file');
                        }
                        return response.text();
                    })
                    .then(text => {
                        if (!isPreviewSessionActive(sessionId)) {
                            return;
                        }
                        pre.textContent = text;
                    })
                    .catch((error) => {
                        if (error && error.name === 'AbortError') {
                            return;
                        }
                        if (!isPreviewSessionActive(sessionId)) {
                            return;
                        }
                        openPreviewError(title);
                    });
            } else if (ext === 'pdf') {
                element = document.createElement('iframe');
                element.src = url;
                element.style.width = '100%';
                element.style.height = '100%';
                element.style.minHeight = '85vh';
                element.frameBorder = '0';
                // iframe.onerror no siempre es fiable para errores HTTP, pero ayuda en algunos casos
                element.onerror = function() {
                    openPreviewError(title);
                };
            } else if (['doc', 'docx'].includes(ext)) {
                element = document.createElement('iframe');
                element.src = `https://docs.google.com/gview?url=${encodeURIComponent(url)}&embedded=true`;
                element.style.width = '100%';
                element.style.height = '500px';
                element.frameBorder = '0';

                if (url.includes('localhost') || url.includes('127.0.0.1') || url.endsWith('.test')) {
                    console.warn('Google Docs Viewer cannot access localhost URLs. Use a tunnel or deploy to production.');
                }
            } else if (ext === 'dxf') {
                // Apply specific styles for DXF (Full height, no padding)
                modalBody.classList.add('p-0');
                modalBody.style.height = '85vh';
                modalBody.style.overflow = 'hidden';

                // Main Container
                element = document.createElement('div');
                element.style.width = '100%';
                element.style.height = '100%';

                // Necessary for absolute positioning of controls
                element.style.position = 'relative';
                element.style.overflow = 'hidden';
                element.className = 'd-flex justify-content-center align-items-center bg-light';

                // Spinner
                const spinner = document.createElement('div');
                spinner.className = 'spinner-border text-primary';
                spinner.role = 'status';
                spinner.style.position = 'absolute';
                spinner.style.zIndex = '5';
                element.appendChild(spinner);

                // Canvas Container
                const canvas = document.createElement('canvas');
                canvas.style.display = 'none';
                canvas.style.cursor = 'grab';
                element.appendChild(canvas);

                // Controls Toolbar (Top-Right, Vertical)
                const controls = document.createElement('div');
                controls.className = "btn-group-vertical shadow-sm";
                controls.style.position = 'absolute';
                controls.style.top = '20px';
                controls.style.right = '20px';
                controls.style.zIndex = '10';
                controls.style.display = 'none'; // Hidden until loaded
                controls.innerHTML = `
                    <button class="btn btn-light border" id="dxfZoomIn" title="Acercar"><i class="fa fa-plus"></i></button>
                    <button class="btn btn-light border" id="dxfZoomOut" title="Alejar"><i class="fa fa-minus"></i></button>
                    <button class="btn btn-light border" id="dxfReset" title="Restablecer vista"><i class="fa fa-compress"></i></button>
                    <button class="btn btn-light border fw-bold text-primary" id="dxfToggle3D" title="Alternar 2D/3D">3D</button>
                    <button class="btn btn-light border" id="dxfToggleText" title="Alternar textos"><i class="fa fa-font"></i></button>
                    <a href="${url}" class="btn btn-primary border" title="Descargar" target="_blank"><i class="fa fa-download"></i></a>
                `;
                element.appendChild(controls);

                // 3D Container (Hidden by default)
                const container3D = document.createElement('div');
                container3D.style.width = '100%';
                container3D.style.height = '100%';
                container3D.style.display = 'none';
                container3D.style.background = '#1a1a1a'; // Dark background for 3D
                element.appendChild(container3D);

                const dxfController = new AbortController();
                registerPreviewController(dxfController);

                // La URL ya viene codificada desde backend; no volver a codificar para evitar %2520
                fetch(url, {
                        cache: 'no-store',
                        signal: dxfController.signal
                    })
                    .then(res => {
                        if (!isPreviewSessionActive(sessionId)) {
                            return Promise.reject(new Error('Preview session canceled'));
                        }
                        if (!res.ok) throw new Error("Error al obtener el archivo DXF");
                        return res.text();
                    })
                    .then(text => {
                        if (!isPreviewSessionActive(sessionId)) {
                            return;
                        }

                        try {
                            if (typeof DxfParser === 'undefined') {
                                throw new Error("Librería DxfParser no cargada");
                            }
                            const parser = new DxfParser();
                            const dxf = parser.parseSync(text);

                            if (!isPreviewSessionActive(sessionId)) {
                                return;
                            }

                            spinner.remove();
                            canvas.style.display = 'block';
                            controls.style.display = 'flex';

                            // Initialize Interactive Viewer
                            const dxfInitTimeout = setTimeout(() => {
                                if (!isPreviewSessionActive(sessionId)) {
                                    return;
                                }
                                window._dxfViewerDestroy = setupDxfViewer(dxf, canvas, controls, container3D);
                            }, 50);
                            registerPreviewTimeout(dxfInitTimeout);

                        } catch (e) {
                            if (!isPreviewSessionActive(sessionId)) {
                                return;
                            }
                            console.error(e);
                            spinner.remove();
                            element.innerHTML = `
                                <div class="text-center p-4">
                                    <i class="fa-solid fa-file-circle-xmark text-danger fs-1 mb-3"></i>
                                    <p class="text-danger fw-bold">Error al visualizar el DXF</p>
                                    <p class="small text-muted mb-3">${e.message}</p>
                                    <a href="${url}" class="btn btn-primary" target="_blank">Descargar archivo</a>
                                </div>
                            `;
                        }
                    })
                    .catch(e => {
                        if (e && e.name === 'AbortError') {
                            return;
                        }
                        if (!isPreviewSessionActive(sessionId)) {
                            return;
                        }
                        console.error(e);
                        openPreviewError(title);
                    });

            } else if (ext === 'zip') {

                // --- ARCHIVE VIEWER (ZIP only) ---
                modalBody.style.padding = '0';
                modalBody.style.minHeight = '500px';
                modalBody.innerHTML = `
                    <div id="archive-loading" class="d-flex flex-column justify-content-center align-items-center h-100 py-5" style="min-height: 400px;">
                        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="text-muted">Analizando contenido ${ext.toUpperCase()}...</p>
                    </div>
                `;

                // Handle Archive Error Helper
                const handleArchiveError = (msg, title, url) => {
                    modalBody.style.padding = '1rem';
                    modalBody.innerHTML = `
                           <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 300px;">
                                <i class="fa-solid fa-file-zipper" style="font-size: 5rem; color: #aa182c;"></i>
                                <p class="mt-3 fw-bold fs-5" style="color: #aa182c;">${title}</p>
                                <div class="alert alert-warning mt-2 text-center" style="max-width: 80%;">
                                    <small><i class="fa-solid fa-triangle-exclamation me-1"></i> Vista previa no disponible: ${msg}</small>
                                </div>
                                <p class="text-muted">Aún puedes descargar el archivo.</p>
                                <a class="btn btn-primary mt-2" href="${url}" target="_blank" rel="noopener">
                                    {{ __('Download') }}
                                </a>
                            </div>
                        `;
                };

                if (ext === 'zip') {
                    // ZIP: Use Backend
                    const formData = new FormData();
                    formData.append('idProject', fileProjectId);
                    formData.append('fileName', title);
                    if (milestoneTitle) formData.append('milestoneTitle', milestoneTitle);
                    formData.append('_token', '{{ csrf_token() }}');

                    const zipController = new AbortController();
                    registerPreviewController(zipController);

                    fetch('{{ route('project.archiveList') }}', {
                            method: 'POST',
                            body: formData,
                            signal: zipController.signal
                        })
                        .then(response => {
                            if (!isPreviewSessionActive(sessionId)) {
                                return Promise.reject(new Error('Preview session canceled'));
                            }
                            if (!response.ok) {
                                return response.json().then(err => {
                                    throw new Error(err.error || response.statusText)
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (!isPreviewSessionActive(sessionId)) {
                                return;
                            }
                            if (data.supported === false) throw new Error("Formato no soportado por el servidor");
                            renderArchiveUI(data, url, title, 'filePreviewModalBody', sessionId);
                        })
                        .catch(e => {
                            if (e && e.name === 'AbortError') {
                                return;
                            }
                            if (!isPreviewSessionActive(sessionId)) {
                                return;
                            }
                            console.error("Archive preview error:", e);
                            handleArchiveError(e.message, title, url);
                        });
                }

            } else if (['dwg', 'rar'].includes(ext)) {

                modalBody.style.minHeight = '400px';
                modalBody.style.display = 'flex';
                modalBody.style.justifyContent = 'center';
                modalBody.style.alignItems = 'center';

                let iconClass = ext === 'dwg' ? 'fa-solid fa-compass-drafting' : 'fa-solid fa-file-zipper';

                element = document.createElement('div');
                element.className = 'd-flex flex-column align-items-center justify-content-center';
                element.style.minHeight = '300px';
                element.innerHTML = `
                    <i class="${iconClass}" style="font-size: 5rem; color: #aa182c;"></i>
                    <p class="mt-3 fw-bold fs-5" style="color: #aa182c;">${title}</p>
                    <p class="text-muted"><span class="text-uppercase">${ext}</span> - {{ __('Vista Previa No Disponible') }}</p>
                    <a class="btn btn-primary mt-2" href="${url}" target="_blank" rel="noopener">
                        {{ __('Download') }}
                    </a>
                `;
            }

            if (element) {
                modalBody.appendChild(element);
            }

            document.getElementById('filePreviewModalLabel').innerText = title;

            showPreviewModal();
        }

        function setupDxfViewer(dxf, canvas, controlsGroup, container3D) {
            if (!dxf || (!dxf.entities && !dxf.blocks)) return;

            const parent = canvas.parentElement;
            const ctx = canvas.getContext('2d');

            // --- 1. OPTIMIZATION: FLATTEN GEOMETRY LOGIC ---
            // Instead of traversing recursively every frame, we cache everything into flat arrays once.
            // This solves the performance bottlenecks.

            const cached = {
                lines: [], // Array of {x1,y1,x2,y2, z1,z2} - flattened
                circles: [], // Array of {x,y,z, r, start, end}
                ellipses: [], // Array of {x,y,z, rx, ry, rot, start, end}
                polys: [], // Array of points for filled polygons (solids)
                texts: [] // Array of {x, y, z, text, height, rot} - for optional text display
            };

            let showTexts = false; // Texts disabled by default for performance

            // Bounds
            let minX = Infinity,
                minY = Infinity,
                minZ = Infinity;
            let maxX = -Infinity,
                maxY = -Infinity,
                maxZ = -Infinity;

            const updateBounds = (x, y, z) => {
                if (x < minX) minX = x;
                if (y < minY) minY = y;
                if (z < minZ) minZ = z;
                if (x > maxX) maxX = x;
                if (y > maxY) maxY = y;
                if (z > maxZ) maxZ = z;
            };

            // Recursive flattener
            const traverseAndCache = (entities, tx) => {
                if (!entities) return;

                // Pre-calc trig values for rotation
                const cos = Math.cos(tx.rot);
                const sin = Math.sin(tx.rot);

                const transform = (p) => {
                    // Scale
                    const sx = p.x * tx.sx;
                    const sy = p.y * tx.sy;
                    const sz = (p.z || 0) * tx.sz;
                    // Rotate (2D around local origin implies Z-axis rotation) + Translate
                    return {
                        x: (sx * cos - sy * sin) + tx.x,
                        y: (sx * sin + sy * cos) + tx.y,
                        z: sz + tx.z
                    };
                };

                entities.forEach(ent => {
                    if (ent.type === 'LINE') {
                        const start = transform(ent.vertices[0]);
                        const end = transform(ent.vertices[1]);
                        cached.lines.push(start.x, start.y, end.x, end.y, start.z, end.z);
                        updateBounds(start.x, start.y, start.z);
                        updateBounds(end.x, end.y, end.z);
                    } else if (ent.type === 'LWPOLYLINE' || ent.type === 'POLYLINE') {
                        if (ent.vertices && ent.vertices.length > 1) {
                            let prev = transform(ent.vertices[0]);
                            updateBounds(prev.x, prev.y, prev.z);

                            for (let i = 1; i < ent.vertices.length; i++) {
                                const curr = transform(ent.vertices[i]);
                                cached.lines.push(prev.x, prev.y, curr.x, curr.y, prev.z, curr.z);
                                updateBounds(curr.x, curr.y, curr.z);
                                prev = curr;
                            }
                            if (ent.shape || ent.closed || (ent.type === 'LWPOLYLINE' && (ent.shape & 1))) {
                                const first = transform(ent.vertices[0]);
                                cached.lines.push(prev.x, prev.y, first.x, first.y, prev.z, first.z);
                            }
                        }
                    } else if (ent.type === 'CIRCLE' || ent.type === 'ARC') {
                        const center = transform(ent.center);
                        // Approximation: handle scale roughly (take max scale)
                        const scale = Math.max(Math.abs(tx.sx), Math.abs(tx.sy));
                        const r = ent.radius * scale;
                        // Global rotation affects angles
                        const start = (ent.startAngle || 0) + tx.rot;
                        const end = (ent.endAngle || 2 * Math.PI) + tx.rot;

                        cached.circles.push({
                            x: center.x,
                            y: center.y,
                            z: center.z,
                            r: r,
                            start: start,
                            end: end
                        });
                        updateBounds(center.x - r, center.y - r, center.z);
                        updateBounds(center.x + r, center.y + r, center.z);
                    } else if (ent.type === 'INSERT') {
                        const block = dxf.blocks[ent.name];
                        if (block) {
                            const entRot = (ent.rotation || 0) * (Math.PI / 180);
                            const nextTx = {
                                x: transform({
                                    x: 0,
                                    y: 0,
                                    z: 0
                                }).x + (ent.x || ent.position?.x ||
                                    0), // Simplifying nesting logic for performance
                                // Correct nested transform requires accumulating matrices, but this simplified version works for 99% of 2D plans
                                // To be strict: We should compose matrices. 
                                // Let's use the transform() of local 0,0 plus the offset in parent space? 
                                // Actually, for Insert, 'ent.x' is local translation inside current context.
                                // Let's simplify: New Transform = Old Transform * Local Transform
                                // But since we only have x/y/rot/scale, we accumulate.
                                x: 0,
                                y: 0,
                                z: 0,
                                rot: 0,
                                sx: 0,
                                sy: 0,
                                sz: 0
                            };

                            // Re-calculating full matrix chain for the recursive step
                            // Position of the block insertion point in World Space:
                            const insP = transform({
                                x: ent.x || ent.position?.x || 0,
                                y: ent.y || ent.position?.y || 0,
                                z: ent.z || ent.position?.z || 0
                            });

                            const nextCtx = {
                                x: insP.x,
                                y: insP.y,
                                z: insP.z,
                                rot: tx.rot + entRot,
                                sx: tx.sx * (ent.xScale || 1),
                                sy: tx.sy * (ent.yScale || 1),
                                sz: tx.sz * (ent.zScale || 1)
                            };
                            traverseAndCache(block.entities, nextCtx);
                        }
                    } else if (ent.type === 'SOLID' || ent.type === '3DFACE') {
                        if (ent.points) {
                            const pts = ent.points.map(pt => transform(pt));
                            cached.polys.push(pts);
                        }
                    } else if (ent.type === 'SPLINE') {
                        // Tessellate Spline to lines immediately
                        if (ent.controlPoints && ent.controlPoints.length > 1) {
                            // Poor man's spline: just connect control points (Splines are mathematically complex to exact without a library)
                            // For visualization context, linear interpolation of control points is often 'okay' enough or we break it down
                            let prev = transform(ent.controlPoints[0]);
                            for (let i = 1; i < ent.controlPoints.length; i++) {
                                const curr = transform(ent.controlPoints[i]);
                                cached.lines.push(prev.x, prev.y, curr.x, curr.y, prev.z, curr.z);
                                prev = curr;
                            }
                        }
                    } else if (ent.type === 'TEXT' || ent.type === 'MTEXT') {
                        const pos = transform({
                            x: ent.startPoint?.x || ent.position?.x || ent.x || 0,
                            y: ent.startPoint?.y || ent.position?.y || ent.y || 0,
                            z: ent.startPoint?.z || ent.position?.z || ent.z || 0
                        });
                        const textVal = (ent.text || ent.string || '').replace(
                            /\\[PpAaCcFfHhLlOoQqSsTtWw][^;]*;/g, '').replace(/[{}\\]/g, '').trim();
                        if (textVal) {
                            const scale = Math.max(Math.abs(tx.sx), Math.abs(tx.sy));
                            cached.texts.push({
                                x: pos.x,
                                y: pos.y,
                                z: pos.z,
                                text: textVal,
                                height: (ent.textHeight || ent.height || 1) * scale,
                                rot: (ent.rotation || 0) * (Math.PI / 180) + tx.rot
                            });
                            updateBounds(pos.x, pos.y, pos.z);
                        }
                    } else if (ent.type === 'ELLIPSE') {
                        // Approximate with ellipse properties
                        const center = transform(ent.center);
                        // Major axis vector transformation
                        const majVec = {
                            x: ent.majorAxisEndPoint.x,
                            y: ent.majorAxisEndPoint.y,
                            z: 0
                        };
                        // We need length and rotation of this vector after transform
                        // This is tricky with non-uniform scaling.
                        // Simplified: Calculate dist.
                        const targetMaj = transform({
                            x: ent.center.x + majVec.x,
                            y: ent.center.y + majVec.y,
                            z: 0
                        });
                        const dx = targetMaj.x - center.x;
                        const dy = targetMaj.y - center.y;
                        const rx = Math.hypot(dx, dy);
                        const rot = Math.atan2(dy, dx);

                        const start = ent.startAngle + tx.rot; // Rough approx
                        const end = ent.endAngle + tx.rot;

                        cached.ellipses.push({
                            x: center.x,
                            y: center.y,
                            z: center.z,
                            rx: rx,
                            ry: rx * ent.axisRatio,
                            rot: rot,
                            start: start,
                            end: end
                        });
                    }
                });
            };

            // Start Flattening
            traverseAndCache(dxf.entities, {
                x: 0,
                y: 0,
                z: 0,
                rot: 0,
                sx: 1,
                sy: 1,
                sz: 1
            });

            // Fallback bounds
            if (minX === Infinity) {
                minX = 0;
                maxX = 100;
                minY = 0;
                maxY = 100;
                minZ = 0;
                maxZ = 0;
            }
            if (maxX - minX < 0.1) maxX = minX + 1;
            if (maxY - minY < 0.1) maxY = minY + 1;

            const dataCx = (minX + maxX) / 2;
            const dataCy = (minY + maxY) / 2;
            const dataCz = (minZ + maxZ) / 2;

            // --- 2D STATE ---
            const state = {
                scale: 1,
                panX: 0,
                panY: 0,
                baseScale: 1,
                centerX: dataCx,
                centerY: dataCy,
                isDragging: false,
                lastX: 0,
                lastY: 0,
                initialized: false
            };

            // --- OFFSCREEN CANVAS BUFFER (INSTANT PAN/ZOOM) ---
            // Strategy: Render ALL geometry ONCE to a hidden buffer canvas.
            // Pan/zoom only does a single drawImage() call (GPU blit) → instant.
            // Buffer is rebuilt only when the user stops interacting (debounce 150ms).
            const offCanvas = document.createElement('canvas');
            const offCtx = offCanvas.getContext('2d');
            let bufferState = null; // snapshot of {scale, panX, panY, w, h} when buffer was painted
            let rebuildTimer = null;
            let rafPending = false;

            /** Paint every cached entity to the offscreen buffer at current state */
            const renderGeometryToBuffer = () => {
                const w = canvas.width;
                const h = canvas.height;
                offCanvas.width = w;
                offCanvas.height = h;

                offCtx.clearRect(0, 0, w, h);
                offCtx.fillStyle = '#ffffff';
                offCtx.fillRect(0, 0, w, h);

                offCtx.save();
                offCtx.translate(w / 2, h / 2);
                offCtx.translate(state.panX, state.panY);
                offCtx.scale(state.scale, -state.scale);
                offCtx.translate(-state.centerX, -state.centerY);

                offCtx.lineWidth = 1 / state.scale;
                offCtx.strokeStyle = '#444';
                offCtx.lineCap = 'round';

                // 1. Lines – batched strokes (flush every 5000 to avoid path-size limits)
                if (cached.lines.length > 0) {
                    let n = 0;
                    offCtx.beginPath();
                    for (let i = 0; i < cached.lines.length; i += 6) {
                        offCtx.moveTo(cached.lines[i], cached.lines[i + 1]);
                        offCtx.lineTo(cached.lines[i + 2], cached.lines[i + 3]);
                        if (++n >= 5000) {
                            offCtx.stroke();
                            offCtx.beginPath();
                            n = 0;
                        }
                    }
                    if (n > 0) offCtx.stroke();
                }

                // 2. Circles / Arcs
                if (cached.circles.length > 0) {
                    let n = 0;
                    offCtx.beginPath();
                    cached.circles.forEach(c => {
                        offCtx.moveTo(c.x + c.r * Math.cos(c.start), c.y + c.r * Math.sin(c.start));
                        offCtx.arc(c.x, c.y, c.r, c.start, c.end);
                        if (++n >= 1000) {
                            offCtx.stroke();
                            offCtx.beginPath();
                            n = 0;
                        }
                    });
                    if (n > 0) offCtx.stroke();
                }

                // 3. Ellipses
                if (cached.ellipses.length > 0) {
                    let n = 0;
                    offCtx.beginPath();
                    cached.ellipses.forEach(e => {
                        offCtx.moveTo(e.x + e.rx * Math.cos(e.start), e.y + e.ry * Math.sin(e.start));
                        offCtx.ellipse(e.x, e.y, e.rx, e.ry, e.rot, e.start, e.end);
                        if (++n >= 1000) {
                            offCtx.stroke();
                            offCtx.beginPath();
                            n = 0;
                        }
                    });
                    if (n > 0) offCtx.stroke();
                }

                // 4. Solids / Filled polygons
                if (cached.polys.length > 0) {
                    offCtx.fillStyle = '#ddd';
                    cached.polys.forEach(pts => {
                        offCtx.beginPath();
                        offCtx.moveTo(pts[0].x, pts[0].y);
                        for (let i = 1; i < pts.length; i++) offCtx.lineTo(pts[i].x, pts[i].y);
                        offCtx.closePath();
                        offCtx.fill();
                        offCtx.stroke();
                    });
                }

                // 5. Texts (only when enabled)
                if (showTexts && cached.texts.length > 0) {
                    offCtx.fillStyle = '#222';
                    offCtx.textBaseline = 'bottom';
                    cached.texts.forEach(t => {
                        offCtx.save();
                        offCtx.translate(t.x, t.y);
                        offCtx.scale(1, -1); // Flip Y back for readable text
                        offCtx.rotate(-t.rot);
                        const fontSize = Math.max(t.height, 0.5);
                        offCtx.font = `${fontSize}px sans-serif`;
                        offCtx.fillText(t.text, 0, 0);
                        offCtx.restore();
                    });
                }

                offCtx.restore();

                bufferState = {
                    scale: state.scale,
                    panX: state.panX,
                    panY: state.panY,
                    w,
                    h
                };
            };

            /**
             * Fast composite: copy the offscreen buffer to the visible canvas
             * applying only the delta transform since last full render.
             * Cost ≈ 1 drawImage call (GPU texture blit) → ~0.1 ms.
             */
            const render2D = () => {
                const w = canvas.width;
                const h = canvas.height;

                // First paint or canvas resized → must rebuild buffer
                if (!bufferState || bufferState.w !== w || bufferState.h !== h) {
                    renderGeometryToBuffer();
                    ctx.drawImage(offCanvas, 0, 0);
                    return;
                }

                // Calculate delta between current interaction state and cached buffer
                const dpx = state.panX - bufferState.panX;
                const dpy = state.panY - bufferState.panY;
                const zoomRatio = state.scale / bufferState.scale;

                // Single drawImage with transform = instant composite
                ctx.clearRect(0, 0, w, h);
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, w, h);

                ctx.save();
                ctx.translate(w / 2 + dpx, h / 2 + dpy);
                ctx.scale(zoomRatio, zoomRatio);
                ctx.translate(-w / 2, -h / 2);
                ctx.drawImage(offCanvas, 0, 0);
                ctx.restore();

                // Schedule a crisp re-render after the user stops moving
                clearTimeout(rebuildTimer);
                rebuildTimer = setTimeout(() => {
                    renderGeometryToBuffer();
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    ctx.drawImage(offCanvas, 0, 0);
                }, 150);
            };

            /** Coalesced rAF scheduler – ensures at most 1 render2D per frame */
            const scheduleRender = () => {
                if (rafPending) return;
                rafPending = true;
                requestAnimationFrame(() => {
                    rafPending = false;
                    render2D();
                });
            };

            // --- 3D SETUP (Merged Geometry) ---
            let renderer3D, scene3D, camera3D, controls3D, animationId3D;
            let is3DInitialized = false;

            const init3D = () => {
                if (is3DInitialized) return;

                const width = container3D.clientWidth;
                const height = container3D.clientHeight;

                scene3D = new THREE.Scene();
                scene3D.background = new THREE.Color(0x1a1a1a);

                // Camera
                camera3D = new THREE.PerspectiveCamera(45, width / height, 0.1, 100000);
                const size = Math.max(maxX - minX, maxY - minY);
                camera3D.position.set(dataCx, dataCy - size * 1.5, maxZ + size);
                camera3D.up.set(0, 0, 1);

                renderer3D = new THREE.WebGLRenderer({
                    antialias: true
                });
                renderer3D.setSize(width, height);
                container3D.appendChild(renderer3D.domElement);
                controls3D = new THREE.OrbitControls(camera3D, renderer3D.domElement);
                controls3D.target.set(dataCx, dataCy, dataCz);
                controls3D.update();

                // --- MERGE GEOMETRY ---
                const linePoints = [];

                // 1. Add flat lines
                // cached.lines is [x1,y1,x2,y2, z1,z2...]
                for (let i = 0; i < cached.lines.length; i += 6) {
                    linePoints.push(cached.lines[i], cached.lines[i + 1], cached.lines[i + 4]);
                    linePoints.push(cached.lines[i + 2], cached.lines[i + 3], cached.lines[i + 5]);
                }

                // 2. Tessellate circles/ellipses for 3D
                const tessellate = (cx, cy, cz, rx, ry, rot, start, end, segments = 24) => {
                    const step = (end - start) / segments;
                    let prevX = cx + rx * Math.cos(start) * Math.cos(rot) - ry * Math.sin(start) * Math.sin(rot);
                    let prevY = cy + rx * Math.cos(start) * Math.sin(rot) + ry * Math.sin(start) * Math.cos(rot);

                    for (let i = 1; i <= segments; i++) {
                        const theta = start + i * step;
                        const tx = cx + rx * Math.cos(theta) * Math.cos(rot) - ry * Math.sin(theta) * Math.sin(rot);
                        const ty = cy + rx * Math.cos(theta) * Math.sin(rot) + ry * Math.sin(theta) * Math.cos(rot);
                        linePoints.push(prevX, prevY, cz, tx, ty, cz);
                        prevX = tx;
                        prevY = ty;
                    }
                };

                cached.circles.forEach(c => tessellate(c.x, c.y, c.z, c.r, c.r, 0, c.start, c.end));
                cached.ellipses.forEach(e => tessellate(e.x, e.y, e.z, e.rx, e.ry, e.rot, e.start, e.end));

                // 3. Create SINGLE Geometry
                const geometry = new THREE.BufferGeometry();
                geometry.setAttribute('position', new THREE.Float32BufferAttribute(linePoints, 3));
                const material = new THREE.LineBasicMaterial({
                    color: 0xffffff,
                    opacity: 0.8,
                    transparent: true
                });
                const mesh = new THREE.LineSegments(geometry, material);
                scene3D.add(mesh);

                const animate = () => {
                    animationId3D = requestAnimationFrame(animate);
                    controls3D.update();
                    renderer3D.render(scene3D, camera3D);
                };
                animate();
                is3DInitialized = true;
            };

            // Resize Observer handling both containers
            const observer = new ResizeObserver(entries => {
                for (let entry of entries) {
                    const width = entry.contentRect.width;
                    const height = entry.contentRect.height;

                    if (width > 0 && height > 0) {
                        if (canvas.style.display !== 'none') {
                            canvas.width = width;
                            canvas.height = height;
                            if (!state.initialized) {
                                const padding = 40;
                                const scaleX = (width - padding) / (maxX - minX);
                                const scaleY = (height - padding) / (maxY - minY);
                                state.baseScale = Math.min(scaleX, scaleY);
                                if (!isFinite(state.baseScale) || state.baseScale <= 0) state.baseScale = 1;
                                state.scale = state.baseScale;
                                state.initialized = true;
                            }
                            render2D();
                        }

                        if (is3DInitialized && renderer3D && container3D.style.display !== 'none') {
                            camera3D.aspect = width / height;
                            camera3D.updateProjectionMatrix();
                            renderer3D.setSize(width, height);
                        }
                    }
                }
            });
            observer.observe(parent);


            // --- Listeners 2D ---
            canvas.addEventListener('wheel', (e) => {
                e.preventDefault();
                const zoomSpeed = 0.1;
                const factor = e.deltaY < 0 ? (1 + zoomSpeed) : (1 - zoomSpeed);
                state.scale *= factor;
                scheduleRender();
            });

            canvas.addEventListener('pointerdown', (e) => {
                state.isDragging = true;
                state.lastX = e.clientX;
                state.lastY = e.clientY;
                canvas.setPointerCapture(e.pointerId);
                canvas.style.cursor = 'grabbing';
            });
            canvas.addEventListener('pointermove', (e) => {
                if (!state.isDragging) return;
                const dx = e.clientX - state.lastX;
                const dy = e.clientY - state.lastY;
                state.lastX = e.clientX;
                state.lastY = e.clientY;
                state.panX += dx;
                state.panY += dy;
                scheduleRender();
            });
            canvas.addEventListener('pointerup', (e) => {
                state.isDragging = false;
                canvas.style.cursor = 'grab';
                canvas.releasePointerCapture(e.pointerId);
            });

            // --- Toggle Logic ---
            if (controlsGroup) {
                const btn3D = controlsGroup.querySelector('#dxfToggle3D');
                const btnZoomIn = controlsGroup.querySelector('#dxfZoomIn');
                const btnZoomOut = controlsGroup.querySelector('#dxfZoomOut');
                const btnReset = controlsGroup.querySelector('#dxfReset');
                const btnText = controlsGroup.querySelector('#dxfToggleText');

                // --- Text Toggle ---
                btnText.onclick = () => {
                    showTexts = !showTexts;
                    if (showTexts) {
                        btnText.classList.remove('btn-light', 'text-dark');
                        btnText.classList.add('bg-primary', 'text-white');
                    } else {
                        btnText.classList.remove('bg-primary', 'text-white');
                        btnText.classList.add('btn-light');
                    }
                    // Force full buffer rebuild with/without texts
                    bufferState = null;
                    scheduleRender();
                };

                btnZoomIn.onclick = () => {
                    if (container3D.style.display !== 'none') {
                        const direction = new THREE.Vector3().subVectors(camera3D.position, controls3D.target);
                        direction.multiplyScalar(0.85);
                        camera3D.position.copy(controls3D.target).add(direction);
                        controls3D.update();
                    } else {
                        state.scale *= 1.2;
                        scheduleRender();
                    }
                };

                btnZoomOut.onclick = () => {
                    if (container3D.style.display !== 'none') {
                        const direction = new THREE.Vector3().subVectors(camera3D.position, controls3D.target);
                        direction.multiplyScalar(1.15);
                        camera3D.position.copy(controls3D.target).add(direction);
                        controls3D.update();
                    } else {
                        state.scale *= 0.8;
                        scheduleRender();
                    }
                };

                btnReset.onclick = () => {
                    if (canvas.style.display !== 'none' && state.initialized) {
                        state.scale = state.baseScale;
                        state.panX = 0;
                        state.panY = 0;
                        scheduleRender();
                    } else if (is3DInitialized) {
                        controls3D.reset();
                    }
                };

                btn3D.onclick = () => {
                    if (container3D.style.display === 'none') {
                        canvas.style.display = 'none';
                        container3D.style.display = 'block';
                        btn3D.innerHTML = '2D';
                        btn3D.classList.remove('text-primary');
                        btn3D.classList.add('bg-primary', 'text-white');

                        init3D();
                    } else {
                        container3D.style.display = 'none';
                        canvas.style.display = 'block';
                        btn3D.innerHTML = '3D';
                        btn3D.classList.remove('bg-primary', 'text-white');
                        btn3D.classList.add('text-primary');

                        render2D();
                    }
                };
            }

            // --- CLEANUP FUNCTION (called when modal closes) ---
            return function destroy() {
                // 1. Stop 3D animation loop
                if (animationId3D) cancelAnimationFrame(animationId3D);

                // 2. Dispose Three.js resources
                if (is3DInitialized) {
                    if (controls3D) controls3D.dispose();
                    if (renderer3D) {
                        renderer3D.dispose();
                        renderer3D.forceContextLoss();
                        renderer3D.domElement.remove();
                    }
                    if (scene3D) {
                        scene3D.traverse(obj => {
                            if (obj.geometry) obj.geometry.dispose();
                            if (obj.material) {
                                if (Array.isArray(obj.material)) obj.material.forEach(m => m.dispose());
                                else obj.material.dispose();
                            }
                        });
                    }
                    scene3D = null;
                    camera3D = null;
                    renderer3D = null;
                    controls3D = null;
                }

                // 3. Stop ResizeObserver
                observer.disconnect();

                // 4. Clear timers
                clearTimeout(rebuildTimer);

                // 5. Release offscreen canvas
                offCanvas.width = 0;
                offCanvas.height = 0;

                // 6. Release visible canvas
                canvas.width = 0;
                canvas.height = 0;

                // 7. Null out heavy data
                cached.lines.length = 0;
                cached.circles.length = 0;
                cached.ellipses.length = 0;
                cached.polys.length = 0;
                cached.texts.length = 0;
                bufferState = null;
            };
        }
    </script>
@endpush
