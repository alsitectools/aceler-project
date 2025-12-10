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

<form method="POST" action="{{ route('projects.milestone.review.submit', [$currentWorkspace->slug, $milestone->id]) }}"
    enctype="multipart/form-data">
    @csrf

    <div class="modal-body">
        {{-- 1️⃣ Subida obligatoria de archivo PDF --}}
        <div class="form-group mb-4">
            <label class="col-form-label fw-bold">
                {{ __('Attach drawing (200MB)') }}
            </label>
            <input type="file" id="review_file" name="review_file" class="form-control" accept=".pdf" required>
            <small class="form-text text-muted">
                {{ __('You must attach a PDF file before proceeding.') }}
            </small>
        </div>

        {{--  Contenido oculto inicialmente --}}
        <div id="review-details" style="display:none;">

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

            {{-- Sistemas --}}
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
                            <input class="form-check-input system-checkbox" type="checkbox" name="systems[]"
                                value="{{ $option->name_system }}" id="{{ $option->id_system }}">
                            <label class="form-check-label"
                                for="{{ $option->id_system }}">{{ __($option->name_system) }}</label>
                        </div>
                    @endforeach
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
    </div>

    {{-- ✅ Footer siempre visible --}}
    <div class="modal-footer" id="review-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
        <button type="submit" id="save-review-btn" class="btn btn-primary" disabled>{{ __('Save review') }}</button>
    </div>
</form>

<script>
    $(document).off('change', '#review_file').on('change', '#review_file', function() {
        const file = this.files[0];
        const detailsSection = $('#review-details');
        const saveBtn = $('#save-review-btn');

        if (!file) {
            detailsSection.hide();
            saveBtn.prop('disabled', true);
            return;
        }

        const fileType = file.type;
        const fileName = file.name.toLowerCase();

        if (fileType === 'application/pdf' || fileName.endsWith('.pdf')) {
            // ✅ Mostrar el resto del popup
            detailsSection.slideDown(250);
        } else {
            alert("Por favor, sube un archivo en formato PDF.");
            $(this).val('');
            detailsSection.hide();
            saveBtn.prop('disabled', true);
        }
    });

    // 🧩 Validación dinámica: habilitar el botón Guardar solo cuando los campos obligatorios estén llenos
    $(document).on('input change', '#num_plans, .system-checkbox', function() {
        const numPlans = $('#num_plans').val();
        const systemsChecked = $('.system-checkbox:checked').length > 0;
        const saveBtn = $('#save-review-btn');

        if (numPlans && systemsChecked) {
            saveBtn.prop('disabled', false);
        } else {
            saveBtn.prop('disabled', true);
        }
    });

    // Tooltip del icono de información
    document.addEventListener('DOMContentLoaded', function() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    // Toggle del filtro de sistemas
    $(document).off('click', '#filter-toggle').on('click', '#filter-toggle', function() {
        const filterContainer = $('#filter-container');
        const filterInput = $('#system-filter');
        const filterIcon = $('#filter-toggle');

        if (filterContainer.is(':visible')) {
            // Ocultar filtro y restaurar lista completa
            filterContainer.slideUp(200);
            filterInput.val('');
            $('#system-list .form-check').show();
            filterIcon.removeClass('text-success').addClass('text-muted');
        } else {
            // Mostrar filtro
            filterContainer.slideDown(200);
            filterIcon.removeClass('text-muted').addClass('text-success');
            filterInput.focus();
        }
    });

    // 🔍 Filtrado en tiempo real de sistemas
    $(document).off('input', '#system-filter').on('input', '#system-filter', function() {
        const searchTerm = $(this).val().toLowerCase();
        const systemItems = $('#system-list .form-check');

        systemItems.each(function() {
            const systemName = $(this).data('system-name');
            if (systemName.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
</script>
