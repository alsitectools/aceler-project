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
        /* 3 columnas */
        gap: 8px 16px;
        /* espacio entre columnas y filas */
        max-height: 250px;
        /* ajusta la altura máxima */
        overflow-y: auto;
        /* scroll vertical si se excede */
        border: 1px solid #e0e0e0;
        /* opcional: borde visual */
        padding: 10px;
        border-radius: 6px;
        background-color: #fafafa;
        /* opcional: fondo suave */
    }

    /* Ajuste visual para los checkboxes */
    .system-list-grid .form-check {
        margin-bottom: 4px;
    }

    /* Scrollbar más discreta (solo en navegadores compatibles) */
    .system-list-grid::-webkit-scrollbar {
        width: 6px;
    }

    .system-list-grid::-webkit-scrollbar-thumb {
        background-color: #ccc;
        border-radius: 3px;
    }
</style>

</style>
<form method="POST" action="{{ route('projects.milestone.review.submit', [$currentWorkspace->slug, $milestone->id]) }}"
    enctype="multipart/form-data">
    @csrf

    <div class="modal-body">
        {{-- 1️⃣ Subida obligatoria de archivo PDF --}}
        <div class="form-group mb-4">
            <label class="col-form-label fw-bold">
                {{ __('Adjuntar documento de revisión (PDF obligatorio)') }}
            </label>
            <input type="file" id="review_file" name="review_file" class="form-control" accept=".pdf" required>
            <small class="form-text text-muted">
                {{ __('Debe adjuntar un archivo en formato PDF antes de continuar.') }}
            </small>
        </div>

        {{--  Contenido oculto inicialmente --}}
        <div id="review-details" style="display:none;">

            {{-- Número de planos --}}
            <div class="form-group mb-4 position-relative">
                <label class="col-form-label fw-bold">
                    {{ __('Número de planos') }} <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <input type="number" min="1" id="num_plans" name="num_plans" class="form-control" required>
                    <span class="input-group-text" data-bs-toggle="tooltip"
                        title="{{ __('No cuentan las portadas ni los indices.') }}">
                        <i class="fa-solid fa-info-circle text-muted"></i>
                    </span>
                </div>
            </div>

            {{-- Sistemas --}}
            <div class="form-group mb-4">
                <label class="col-form-label fw-bold">
                    {{ __('Sistemas') }} <span class="text-danger">*</span>
                </label>
                <div id="system-list" class="system-list-grid">
                    @foreach ($systemOptions as $option)
                        <div class="form-check">
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
                        {{ __('Formato original') }}
                    </label>
                    <select name="document_format" id="document_format" class="form-control">
                        <option value="dwg">DWG</option>
                        <option value="pdf">PDF</option>
                        <option value="papel">{{ __('Papel') }}</option>
                    </select>
                </div>

                {{-- Nivel de detalle --}}
                <div class="form-group mb-4 width95">
                    <label class="col-form-label fw-bold">
                        {{ __('Nivel de detalle') }}
                    </label>
                    <select name="detail_level" id="detail_level" class="form-control">
                        <option value="oferta">{{ __('Oferta') }}</option>
                        <option value="montaje">{{ __('Montaje') }}</option>
                        <option value="edificacion">{{ __('Edificación') }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer" id="review-footer" style="display:none;">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancelar') }}</button>
        <button type="submit" id="save-review-btn" class="btn btn-primary"
            disabled>{{ __('Guardar revisión') }}</button>
    </div>
</form>

<script>
    $(document).off('change', '#review_file').on('change', '#review_file', function() {
        const file = this.files[0];
        const detailsSection = $('#review-details');
        const footer = $('#review-footer');
        const saveBtn = $('#save-review-btn');

        if (!file) {
            detailsSection.hide();
            footer.hide();
            return;
        }

        const fileType = file.type;
        const fileName = file.name.toLowerCase();

        if (fileType === 'application/pdf' || fileName.endsWith('.pdf')) {
            // ✅ Mostrar el resto del popup
            detailsSection.slideDown(250);
            footer.fadeIn(250);
        } else {
            alert("Por favor, sube un archivo en formato PDF.");
            $(this).val('');
            detailsSection.hide();
            footer.hide();
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
</script>
