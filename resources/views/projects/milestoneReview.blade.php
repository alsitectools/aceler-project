<style>
    .width95 {
        width: 95%;
    }

    .marginRIght6 {
        margin-right: 6%;
    }

    .groupByCenterFlex {
        display: flex;
        flex-direction: row;
        flex-wrap: nowrap;
        align-content: center;
        justify-content: center;
        align-items: center;
        width: 100%;
    }

    .system-list-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px 16px;
        max-height: 250px;
        overflow-y: auto;
        border: 1px solid #e0e0e0;
        padding: 10px;
        border-radius: 6px;
        background-color: #fafafa;
    }

    .system-list-grid .form-check {
        margin-bottom: 4px;
    }

    .system-list-grid::-webkit-scrollbar {
        width: 6px;
    }

    .system-list-grid::-webkit-scrollbar-thumb {
        background-color: #ccc;
        border-radius: 3px;
    }

    .text-success {
        color: #28a745 !important;
    }
</style>

<form method="POST"
    action="{{ route('projects.milestone.review.submit', [$currentWorkspace->slug, $milestone->id]) }}"
    enctype="multipart/form-data">
    @csrf

    <div class="modal-body">

        {{-- ✅ Sección inicial: SOLO subida de PDFs --}}
        <div id="review-upload" class="form-group mb-4">
            <label class="col-form-label fw-bold">
                {{ __('Attach drawing (50MB)') }}
            </label>

            {{-- ✅ Hasta 5 PDFs --}}
            <input type="file"
                   id="review_files"
                   name="review_files[]"
                   class="form-control"
                   accept=".pdf,application/pdf"
                   multiple>

            <small class="form-text text-muted">
                {{ __('You must attach up to 5 PDF files before proceeding.') }}
            </small>
        </div>

        {{-- ✅ Sección secundaria: detalles (inicialmente oculta) --}}
        <div id="review-details" style="display:none;">

            {{-- ✅ Campos que se bloquean en modo "Omit" --}}
            <div id="non-systems-fields">

                {{-- Número de planos --}}
                <div class="form-group mb-4 position-relative">
                    <label class="col-form-label fw-bold">
                        {{ __('Number of plans') }} <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <input type="number" min="1" id="num_plans" name="num_plans" class="form-control" required>
                        <span class="input-group-text" data-bs-toggle="tooltip"
                            title="{{ __('Cover pages and indexes do not count.') }}">
                            <i class="fa-solid fa-info-circle text-muted"></i>
                        </span>
                    </div>
                </div>

                <div class="groupByCenterFlex">
                    {{-- Formato documentación original --}}
                    <div class="form-group mb-4 width95 marginRIght6">
                        <label class="col-form-label fw-bold">
                            {{ __('Original format') }}
                        </label>
                        <select name="document_format" id="document_format" class="form-control">
                            <option value="dwg">DWG</option>
                            <option value="pdf">PDF</option>
                            <option value="papel">{{ __('Paper') }}</option>
                        </select>
                    </div>

                    {{-- Nivel de detalle --}}
                    <div class="form-group mb-4 width95">
                        <label class="col-form-label fw-bold">
                            {{ __('Detail level') }}
                        </label>
                        <select name="detail_level" id="detail_level" class="form-control">
                            <option value="oferta">{{ __('Offer') }}</option>
                            <option value="montaje">{{ __('Assembly') }}</option>
                            <option value="edificacion">{{ __('Building') }}</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- ✅ Sistemas (siempre editable, incluso en Omit) --}}
            <div class="form-group mb-4">
                <label class="col-form-label fw-bold">
                    {{ __('Systems') }}
                    <i class="fa-solid fa-filter text-muted" id="filter-toggle"
                        style="cursor: pointer; margin-left: 8px;"></i>
                    <span class="text-danger">*</span>
                </label>

                <div id="filter-container" style="display: none; margin-bottom: 10px;">
                    <input type="text" id="system-filter" class="form-control"
                        placeholder="{{ __('Search systems...') }}">
                </div>

                <div id="system-list" class="system-list-grid">
                    @foreach ($systemOptions as $option)
                        <div class="form-check" data-system-name="{{ strtolower($option->name_system) }}">
                            <input class="form-check-input system-checkbox"
                                   type="checkbox"
                                   name="systems[]"
                                   value="{{ $option->name_system }}"
                                   id="system_{{ $option->id_system }}">
                            <label class="form-check-label" for="system_{{ $option->id_system }}">
                                {{ __($option->name_system) }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    {{-- ✅ Footer siempre visible --}}
    <div class="modal-footer" id="review-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>

        <button type="button" id="omit-review-btn" class="btn btn-outline-secondary">
            {{ __('Omit') }}
        </button>

        <button type="submit" id="save-review-btn" class="btn btn-primary" disabled>
            {{ __('Save review') }}
        </button>
    </div>
</form>

<script>
    let omitMode = false;

    function showDetailsAndHideUpload() {
        $('#review-upload').slideUp(200);
        $('#omit-review-btn').hide(); // ✅ Omit solo visible cuando está el upload
        $('#review-details').slideDown(250);
    }

    function setNonSystemsDisabled(disabled) {
        const nonSystems = $('#non-systems-fields');
        nonSystems.find('input, select, textarea, button').prop('disabled', disabled);

        // num_plans solo required en modo normal
        $('#num_plans').prop('required', !disabled);

        if (disabled) {
            // limpiar para evitar enviar valores antiguos
            $('#num_plans').val('');
            $('#document_format').prop('selectedIndex', 0);
            $('#detail_level').prop('selectedIndex', 0);
        }
    }

    function resetToInitialState() {
        omitMode = false;

        // limpiar inputs
        $('#review_files').val('');
        $('#num_plans').val('');
        $('#document_format').prop('selectedIndex', 0);
        $('#detail_level').prop('selectedIndex', 0);
        $('.system-checkbox').prop('checked', false);
        $('#system-filter').val('');
        $('#system-list .form-check').show();

        // mostrar solo upload
        $('#review-details').hide();
        $('#review-upload').show();
        $('#omit-review-btn').show(); // ✅ visible solo en upload

        // habilitar campos no-systems
        setNonSystemsDisabled(false);

        // deshabilitar save
        $('#save-review-btn').prop('disabled', true);
    }

    function getSelectedFiles() {
        return Array.from($('#review_files')[0].files || []);
    }

    function areValidPdfsSelected() {
        const files = getSelectedFiles();
        if (files.length === 0) return false;
        if (files.length > 5) return false;

        return files.every(f =>
            f.type === 'application/pdf' || (f.name && f.name.toLowerCase().endsWith('.pdf'))
        );
    }

    function updateSaveButtonState() {
        const systemsChecked = $('.system-checkbox:checked').length > 0;
        const saveBtn = $('#save-review-btn');

        if (omitMode) {
            // En omit: solo Systems
            saveBtn.prop('disabled', !systemsChecked);
            return;
        }

        const numPlans = $('#num_plans').val();
        const hasValidPdfs = areValidPdfsSelected();

        // Modo normal: PDFs + num_plans + systems
        saveBtn.prop('disabled', !(hasValidPdfs && numPlans && systemsChecked));
    }

    // ✅ Cambio de archivos
    $(document).off('change', '#review_files').on('change', '#review_files', function() {
        const files = Array.from(this.files || []);

        if (files.length === 0) {
            resetToInitialState();
            return;
        }

        if (files.length > 5) {
            alert("Puedes subir como máximo 5 archivos PDF.");
            $(this).val('');
            resetToInitialState();
            return;
        }

        const allPdf = files.every(f =>
            f.type === 'application/pdf' || (f.name && f.name.toLowerCase().endsWith('.pdf'))
        );

        if (!allPdf) {
            alert("Por favor, sube solo archivos en formato PDF.");
            $(this).val('');
            resetToInitialState();
            return;
        }

        omitMode = false;
        showDetailsAndHideUpload();
        setNonSystemsDisabled(false);
        updateSaveButtonState();
    });

    // ✅ Botón Omitir
    $(document).off('click', '#omit-review-btn').on('click', '#omit-review-btn', function() {
        omitMode = true;

        // limpiar archivos
        $('#review_files').val('');

        // pasar a detalles, ocultando upload
        showDetailsAndHideUpload();

        // bloquear todo salvo systems
        setNonSystemsDisabled(true);

        updateSaveButtonState();
    });

    // ✅ Validación dinámica
    $(document).on('input change', '#num_plans, .system-checkbox', function() {
        updateSaveButtonState();
    });

    // ✅ Tooltip
    document.addEventListener('DOMContentLoaded', function() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    // ✅ Toggle filtro sistemas
    $(document).off('click', '#filter-toggle').on('click', '#filter-toggle', function() {
        const filterContainer = $('#filter-container');
        const filterInput = $('#system-filter');
        const filterIcon = $('#filter-toggle');

        if (filterContainer.is(':visible')) {
            filterContainer.slideUp(200);
            filterInput.val('');
            $('#system-list .form-check').show();
            filterIcon.removeClass('text-success').addClass('text-muted');
        } else {
            filterContainer.slideDown(200);
            filterIcon.removeClass('text-muted').addClass('text-success');
            filterInput.focus();
        }
    });

    // ✅ Filtrado sistemas
    $(document).off('input', '#system-filter').on('input', '#system-filter', function() {
        const searchTerm = $(this).val().toLowerCase();
        const systemItems = $('#system-list .form-check');

        systemItems.each(function() {
            const systemName = $(this).data('system-name');
            $(this).toggle(systemName.includes(searchTerm));
        });
    });

    // ✅ Estado inicial al cargar
    $(document).ready(function() {
        $('#review-details').hide();
        $('#review-upload').show();
        $('#omit-review-btn').show();
        $('#save-review-btn').prop('disabled', true);
    });

    // (Opcional) Reset al cerrar modal:
    // $(document).on('hidden.bs.modal', '#TU_MODAL_ID', function () {
    //     resetToInitialState();
    // });
</script>
