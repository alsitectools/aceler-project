@php
    $user = Auth::user();
    // Se leen los parámetros (si existen). Si no vienen, quedan como null.
    $selectedProjectId = request()->get('project_id');
    $selectedProjectName = request()->get('projectName');
    $selectedMilestoneTitle = request()->get('milestoneTitle');
    $selectedMilestoneId = request()->get('milestone_id');
    $fromMilestoneBoard = request()->get('fromMilestoneBoard');
    $fromMyMilestoneBoard = request()->get('fromMyMilestoneBoard');

    // Detectar si viene de my-milestone-board
    $isMyMilestoneBoard = $fromMyMilestoneBoard || strpos(request()->url(), 'my-milestone-board') !== false;
    $formAction = $isMyMilestoneBoard
        ? route('my_milestone.tasks.store', $currentWorkspace->slug)
        : route('tasks.store', $currentWorkspace->slug);
@endphp

@if ($projects && $currentWorkspace)
    <form id="taskCreateForm" method="post" action="@auth('web'){{ $formAction }}@endauth">
        @csrf
        <div class="modal-body">
            <!-- DEBUG INFO -->
            <script>
                console.log('=== TaskCreate Vista DEBUG ===');
                console.log('Raw selectedProjectId:', '{{ request()->get('project_id') }}');
                console.log('Raw selectedProjectName:', '{{ request()->get('projectName') }}');
                console.log('Raw selectedMilestoneTitle:', '{{ request()->get('milestoneTitle') }}');
                console.log('Raw selectedMilestoneId:', '{{ request()->get('milestone_id') }}');
                console.log('From My Milestone Board:', '{{ request()->get('fromMyMilestoneBoard') }}');
                console.log('=== FIN DEBUG ===');
            </script>
            <div class="row">
                <!-- Select de Proyectos -->
                <div class="form-group col-md-12" id="project-field-container">
                    <label class="col-form-label">{{ __('Projects') }}</label>
                    @if ($selectedProjectId && $selectedProjectName)
                        <!-- Si viene preseleccionado de my_milestone_board, mostrar como input de texto -->
                        <input type="hidden" name="project_id" value="{{ $selectedProjectId }}" style="display: none;">
                        <input type="text" class="form-control form-control-light" value="{{ $selectedProjectName }}"
                            disabled>
                    @elseif ($selectedProjectId)
                        <!-- Si existe proyecto preseleccionado, se muestra un select con el único option seleccionado -->
                        <input type="hidden" name="project_id" value="{{ $selectedProjectId }}" style="display: none;">
                        <select class="form-control form-control-light select2" name="project_id" id="project_id"
                            required disabled>
                            <option value="">{{ __('Select Project') }}</option>
                            @foreach ($projects as $project)
                                @if ((int) $selectedProjectId == (int) $project->id)
                                    <option value="{{ $project->id }}" data-project='{{ json_encode($project) }}'
                                        selected>
                                        {{ $project->name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    @else
                        <!-- En caso contrario se muestran todos los proyectos -->
                        <select class="form-control form-control-light select2" name="project_id" id="project_id"
                            required>
                            <option value="">{{ __('Select Project') }}</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}" data-project='{{ json_encode($project) }}'>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <!-- Select de Milestone -->
                <div class="form-group col-md-6" id="milestone-field-container">
                    <label class="col-form-label">{{ __('Milestone') }}</label>

                    @if ($selectedMilestoneTitle)
                        <!-- Si viene preseleccionado, mostrar como input de texto y guardar el valor en hidden -->
                        <input type="hidden" name="milestone_id" value="{{ $selectedMilestoneId }}">
                        <input type="text" class="form-control form-control-light"
                            value="{{ $selectedMilestoneTitle }}" disabled>
                    @else
                        <!-- En caso contrario mostrar el select -->
                        <select class="form-control form-control-light select2" name="milestone_id" id="milestone_id"
                            required>
                            <option value="">{{ __('Select Milestone') }}</option>
                        </select>
                    @endif
                </div>

                <!-- Select de Task Type -->
                <div class="form-group col-md-6" id="task-container">
                    <label class="col-form-label">{{ __('Task type') }}</label>
                    <select class="form-control form-control-light select2" id="task-list" name="type_id" required>
                        <option value="">{{ __('Select Task') }}</option>
                    </select>

                    <div class="form-group col-md-12 d-none" id="custom-task-name-container">
                        <label class="col-form-label">{{ __('Custom task name') }}</label>
                        <input type="text" class="form-control form-control-light" id="custom_task_name"
                            name="custom_task_name" placeholder="{{ __('Write the custom task name...') }}">
                    </div>

                </div>

                <div class="form-group col-12 col-md-6 d-none" id="task-assign-container" style="position: relative;">
                    <label class="col-form-label">{{ __('Assign Task') }}</label>
                    <input type="text" class="form-control form-control-light" id="search-task-assignee"
                        placeholder="{{ __('Search') }}" autocomplete="off">
                    <small id="task-assignee-feedback" class="task-assignee-feedback d-none">
                        {{ __('Please select an assignee from the list.') }}
                    </small>
                    <div id="user-select-task-assignee" class="dropdown-menu" style="width: 100%;">
                        @foreach ($users ?? collect() as $u)
                            <div class="task-assignee-option list-group-item list-group-item-action stylelist ps-3"
                                collected-data-id="{{ $u->id }}" style="padding: 8px; cursor: pointer;">
                                {{ $u->name }}
                            </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="task_assign_override" id="task_assign_override" value="">
                </div>

                <!-- Master -->
                <div class="form-group col-md-12 mt-2 d-none" id="master-container">
                    <label class="col-form-label">{{ __('Referencia') }}
                        <i id="referencia-help" class="fa-solid fa-info-circle text-muted" tabindex="0"
                            role="button" aria-label="{{ __('Ayuda para rellenar la referencia') }}"
                            style="cursor: pointer;"></i>
                    </label>
                    <div class="d-flex align-items-center gap-1">
                        <input type="text" class="form-control master-box text-center" maxlength="2" data-master-index="1" readonly placeholder="YY">
                        <select class="form-control master-box master-select text-center" data-master-index="2">
                            <option value=""></option>
                            @foreach ($delegations as $del)
                                <option value="{{ $del->id }}">{{ $del->id }}</option>
                            @endforeach
                        </select>
                        <input type="text" class="form-control master-box master-num text-center" maxlength="3" data-master-index="3" inputmode="numeric" placeholder="000">
                        <input type="text" class="form-control master-box master-letter text-center" maxlength="1" data-master-index="4" placeholder="A">
                        <div class="master-sys-dd">
                            <button type="button" class="master-sys-btn text-center" aria-expanded="false">
                                <span class="master-sys-value"></span>
                            </button>
                            <div class="master-sys-menu">
                                <a class="master-sys-item" href="#" data-value="">0</a>
                                @foreach ($systems as $sys)
                                    <a class="master-sys-item" href="#" data-value="{{ $sys->id_system }}">{{ $sys->id_system }} · {{ $sys->code_system }}</a>
                                @endforeach
                            </div>
                            <input type="hidden" class="master-box" data-master-index="5" value="">
                        </div>
                        <span class="mx-1">.</span>
                        <input type="text" class="form-control master-box text-center" maxlength="1" data-master-index="6" inputmode="numeric" placeholder="0">
                        <span class="mx-1">-</span>
                        <input type="text" class="form-control master-box text-center" maxlength="1" data-master-index="7" inputmode="numeric" placeholder="0">
                        <input type="text" class="form-control master-box text-center" maxlength="1" data-master-index="8" inputmode="numeric" placeholder="0">
                    </div>
                    <input type="hidden" id="task-referencia" name="referencia" value="">
                </div>

                <div class="form-group col-md-12 mt-2 d-none" id="empresa-container">
                    <label class="col-form-label">{{ __('Empresa') }}</label>
                    <div class="empresa-dd">
                        <input type="text" class="form-control empresa-input" id="empresa_input"
                            placeholder="{{ __('Buscar empresa...') }}" autocomplete="off" aria-expanded="false">
                        <div class="empresa-menu">
                            <a class="empresa-item" href="#" data-value="">—</a>
                            @foreach ($empresas as $emp)
                                <a class="empresa-item" href="#" data-value="{{ $emp->id }}" data-id="{{ $emp->id }}"
                                    data-tosearch="{{ strtolower($emp->id . ' ' . $emp->name) }}">{{ $emp->id }} · {{ $emp->name }}</a>
                            @endforeach
                            <a class="empresa-item empresa-no-match" href="#" data-value="" style="display:none;">
                                {{ __('No hay coincidencia') }}
                            </a>
                        </div>
                        <input type="hidden" name="empresa" id="empresa_id" value="">
                    </div>
                </div>

                <div class="form-group col-md-12 mt-2" id="description-toggle-container">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="addDescriptionCheck" style="cursor: pointer;">
                        <label class="form-check-label" for="addDescriptionCheck" style="cursor: pointer; user-select: none;">
                            {{ __('Añadir descripcion ') }}<span style="font-weight: normal; font-size: 0.85em;">(opcional)</span>
                        </label>
                    </div>
                    <div class="d-none mt-2" id="description-box-container">
                        <textarea class="form-control form-control-light" id="task_description" name="description" rows="3"
                            placeholder="{{ __('Write a description...') }}"></textarea>
                    </div>
                </div>

                {{-- <!-- Fecha de inicio -->
                <div class="form-group col-md-6" style="width: 100% !important;">
                    <label for="start_date" class="col-form-label">{{ __('Start date') }}</label>
                    <input type="text" class="form-control form-control-light date" id="start_date_display"
                        name="start_date_display" value="{{ \Carbon\Carbon::now()->format('d/m/Y') }}" disabled>
                    <!-- Campo oculto para enviar el valor -->
                    <input type="hidden" id="start_date" name="start_date"
                        value="{{ \Carbon\Carbon::now()->format('d/m/Y') }}">
                </div> --}}

                <!-- Fecha estimada -->
                <!-- Campo oculto con fecha estimada (por defecto hoy) -->
                <input type="hidden" id="estimated_date" name="estimated_date"
                    value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                <input type="hidden" name="fromMyMilestoneBoard" value="{{ $fromMyMilestoneBoard ?? 0 }}">

            </div>
        </div>
        <div class="modal-footer">
            @if ($selectedProjectId)
                <!-- Si se viene de la vista 1 se muestra el botón Cancelar -->
                <button type="button" id="cancelBtn" class="btn btn-light"
                    data-bs-dismiss="modal">{{ __('Discard') }}</button>
            @else
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
            @endif
            <button type="submit" class="btn btn-primary">{{ __('Create') }}</button>
        </div>
    </form>
@else
    <!-- En caso de que no exista $projects o $currentWorkspace -->
    <div class="container mt-5">
        <div class="card">
            <div class="card-body p-4">
                <div class="page-error">
                    <div class="page-inner">
                        <h1>404</h1>
                        <div class="page-description">
                            {{ __('Page Not Found') }}
                        </div>
                        <div class="page-search">
                            <p class="text-muted mt-3">
                                {{ __("It's looking like you may have taken a wrong turn. Don't worry... it happens to the best of us. Here's a little tip that might help you get back on track.") }}
                            </p>
                            <div class="mt-3">
                                <a class="btn-return-home badge-blue" href="{{ route('home') }}">
                                    <i class="fas fa-reply"></i> {{ __('Return Home') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Incluimos los estilos y scripts necesarios -->
<link rel="stylesheet" href="{{ asset('assets/custom/libs/bootstrap-daterangepicker/daterangepicker.css') }}">
<script src="{{ asset('assets/custom/libs/bootstrap-daterangepicker/daterangepicker.js') }}"></script>

<style>
    .referencia-help-popover {
        --bs-popover-zindex: 1080 !important;
        z-index: 1080 !important;
    }
</style>

<script>
    $(document).ready(function() {
        const taskCreateForm = $('#taskCreateForm');
        const projectFieldContainer = $('#project-field-container');
        const milestoneFieldContainer = $('#milestone-field-container');
        const taskTypeContainer = $('#task-container');
        const taskAssignContainer = $('#task-assign-container');
        const masterContainer = $('#master-container');
        const empresaContainer = $('#empresa-container');
        const taskAssigneeInput = $('#search-task-assignee');
        const taskAssigneeDropdown = $('#user-select-task-assignee');
        const taskAssigneeHidden = $('#task_assign_override');
        const taskAssigneeFeedback = $('#task-assignee-feedback');
        const currentUserId = "{{ Auth::id() }}";
        const fromStatusChange = "{{ $fromMilestoneBoard ? 1 : 0 }}" === "1";
        const milestonesData = @json($milestones);

        function getSelectedMilestoneId() {
            const milestoneSelect = $('#milestone_id');
            if (milestoneSelect.length) {
                const selectedValue = milestoneSelect.val();
                if (selectedValue) {
                    return String(selectedValue);
                }
            }

            const milestoneHidden = $('input[name="milestone_id"]');
            if (milestoneHidden.length && milestoneHidden.val()) {
                return String(milestoneHidden.val());
            }

            return '';
        }

        function canShowTaskAssignSelector(selectedProject) {
            if (!selectedProject) {
                return false;
            }

            const projectType = String(selectedProject.type);
            if (projectType !== '3' && projectType !== '5') {
                return false;
            }

            const selectedMilestoneId = getSelectedMilestoneId();
            if (!selectedMilestoneId) {
                return false;
            }

            const selectedMilestone = milestonesData.find(m => String(m.id) === selectedMilestoneId);
            if (!selectedMilestone) {
                return false;
            }

            return String(selectedMilestone.milestone_assigned_to_user || '') === String(currentUserId);
        }

        function toggleMasterContainer(selectedProject) {
            const isJobsite = selectedProject && String(selectedProject.type) === '1';
            masterContainer.toggleClass('d-none', !isJobsite);
            empresaContainer.toggleClass('d-none', !isJobsite);

            if (isJobsite) {
                const delegation = selectedProject.ref_delegation;
                $('.master-box[data-master-index="2"]').val(delegation || '');
            } else {
                $('#empresa_id').val('');
                $('#empresa_input').val('');
                $('.empresa-dd').removeClass('show');
            }
        }

        function applyProjectTypeLayout(selectedProject, shouldShowTaskAssign) {
            projectFieldContainer.removeClass('col-md-12 col-md-6').addClass(shouldShowTaskAssign ?
                'col-md-6' : 'col-md-12');

            milestoneFieldContainer.removeClass('col-md-12 col-md-6').addClass('col-md-6');
            taskTypeContainer.removeClass('col-md-12 col-md-6').addClass('col-md-6');
            taskAssignContainer.removeClass('col-md-12 col-md-6 offset-md-6').addClass('col-md-6');
        }

        function setTaskAssigneeInvalidState() {
            taskAssigneeInput.addClass('task-assignee-invalid');
            taskAssigneeFeedback.removeClass('d-none');
        }

        function clearTaskAssigneeInvalidState() {
            taskAssigneeInput.removeClass('task-assignee-invalid');
            taskAssigneeFeedback.addClass('d-none');
        }

        function resetTaskAssigneeSelection() {
            taskAssigneeInput.val('');
            taskAssigneeInput.prop('required', false);
            taskAssigneeInput[0].setCustomValidity('');
            clearTaskAssigneeInvalidState();
            taskAssigneeHidden.val('');
            taskAssigneeDropdown.hide();
            taskAssigneeDropdown.find('.task-assignee-option').show();
        }

        function toggleTaskAssignSelector(selectedProject) {
            const shouldShowTaskAssign = canShowTaskAssignSelector(selectedProject);
            taskAssignContainer.toggleClass('d-none', !shouldShowTaskAssign);

            taskAssigneeInput.prop('required', !!shouldShowTaskAssign);

            if (!shouldShowTaskAssign) {
                resetTaskAssigneeSelection();
            }

            return shouldShowTaskAssign;
        }

        taskAssigneeInput.on('click', function(event) {
            if (taskAssignContainer.hasClass('d-none')) {
                return;
            }

            event.stopPropagation();
            taskAssigneeDropdown.show();
        });

        taskAssigneeInput.on('input', function() {
            const filter = taskAssigneeInput.val().toLowerCase();
            let hasVisibleOption = false;

            taskAssigneeInput[0].setCustomValidity('');
            clearTaskAssigneeInvalidState();
            taskAssigneeHidden.val('');

            taskAssigneeDropdown.find('.task-assignee-option').each(function() {
                const text = $(this).text().toLowerCase();
                if (text.includes(filter)) {
                    $(this).show();
                    hasVisibleOption = true;
                } else {
                    $(this).hide();
                }
            });

            taskAssigneeDropdown.toggle(hasVisibleOption);
        });

        taskAssigneeDropdown.on('click', '.task-assignee-option', function() {
            taskAssigneeInput.val($(this).text().trim());
            taskAssigneeHidden.val($(this).attr('collected-data-id'));
            taskAssigneeInput[0].setCustomValidity('');
            clearTaskAssigneeInvalidState();
            taskAssigneeDropdown.hide();
        });

        $(document).on('click', function(event) {
            if (!$(event.target).closest('#task-assign-container').length) {
                taskAssigneeDropdown.hide();
            }
        });

        taskCreateForm.on('submit', function(event) {
            const useAjax = !fromStatusChange && typeof window.milestoneBoardAppendTask === 'function';

            // Validar cuadros obligatorios de la referencia (solo tipo obra / master-container visible)
            if (!masterContainer.hasClass('d-none')) {
                const fixedBoxes = [1, 2, 3, 4, 5, 6];
                const missing = fixedBoxes.some(function(idx) {
                    const value = ($('.master-box[data-master-index="' + idx + '"]').val() || '').trim();
                    if (value !== '') return false;
                    return true;
                });

                if (missing) {
                    event.preventDefault();
                    if (typeof show_toastr === 'function') {
                        show_toastr('Error', '{{ __("Complete los cuadros obligatorios de la referencia (año, delegación, código, zona, sistema y versión).") }}', 'error');
                    } else {
                        alert('{{ __("Complete los cuadros obligatorios de la referencia.") }}');
                    }
                    return false;
                }
            }

            // Envío AJAX: spinner + actualizar solo la tarjeta del tablero (sin recargar la página).
            // Se intercepta siempre desde el menú de la tarjeta para evitar el rerender completo.
            if (useAjax) {
                event.preventDefault();
            }

            function submitTaskViaAjax() {
                var esperaOverlay = document.getElementById('espera-overlay');
                if (esperaOverlay) {
                    esperaOverlay.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                }

                var $submitBtn = taskCreateForm.find('button[type="submit"]');
                $submitBtn.prop('disabled', true);

                $.ajax({
                    url: taskCreateForm.attr('action'),
                    type: 'POST',
                    data: new FormData(taskCreateForm[0]),
                    processData: false,
                    contentType: false,
                    headers: { 'Accept': 'application/json' },
                    success: function(response) {
                        if (esperaOverlay) {
                            esperaOverlay.style.display = 'none';
                            document.body.style.overflow = 'auto';
                        }
                        $submitBtn.prop('disabled', false);

                        var modalEl = document.getElementById('commonModal');
                        if (modalEl) {
                            var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                            modal.hide();
                        }

                        if (window.milestoneBoardAppendTask) {
                            window.milestoneBoardAppendTask(response);
                        }

                        if (typeof show_toastr === 'function') {
                            show_toastr('Success', response.message || '{{ __("Task Created Successfully!") }}', 'success');
                        }
                    },
                    error: function(xhr) {
                        if (esperaOverlay) {
                            esperaOverlay.style.display = 'none';
                            document.body.style.overflow = 'auto';
                        }
                        $submitBtn.prop('disabled', false);

                        var msg = '{{ __("Something went wrong.") }}';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            msg = Object.values(xhr.responseJSON.errors)[0];
                        }
                        if (typeof show_toastr === 'function') {
                            show_toastr('Error', msg, 'error');
                        }
                    }
                });
            }

            if (taskAssignContainer.hasClass('d-none')) {
                taskAssigneeInput[0].setCustomValidity('');
                clearTaskAssigneeInvalidState();
                taskAssigneeHidden.val('');
                if (useAjax) {
                    submitTaskViaAjax();
                }
                return;
            }

            const inputValue = taskAssigneeInput.val().trim().toLowerCase();
            if (!inputValue) {
                taskAssigneeHidden.val('');
                taskAssigneeInput[0].setCustomValidity(
                    "{{ __('Please select an assignee from the list.') }}");
                setTaskAssigneeInvalidState();
                taskAssigneeInput[0].reportValidity();
                event.preventDefault();
                return;
            }

            let matchedUserId = '';
            taskAssigneeDropdown.find('.task-assignee-option').each(function() {
                if ($(this).text().trim().toLowerCase() === inputValue) {
                    matchedUserId = $(this).attr('collected-data-id');
                    return false;
                }
            });

            taskAssigneeHidden.val(matchedUserId);

            if (!matchedUserId) {
                taskAssigneeInput[0].setCustomValidity(
                    "{{ __('Please select an assignee from the list.') }}");
                setTaskAssigneeInvalidState();
                taskAssigneeInput[0].reportValidity();
                event.preventDefault();
                return;
            }

            taskAssigneeInput[0].setCustomValidity('');
            clearTaskAssigneeInvalidState();

            if (useAjax) {
                submitTaskViaAjax();
            }
        });

        // Si hay un proyecto preseleccionado (vista 1) o se cambia de proyecto (vista 2) se actualizan los selects
        function toggleCustomTaskName() {
            var opt = $('#task-list option:selected');
            var isCustom = opt.data('is-custom') == 1; // ojo: usa .data()

            $('#custom-task-name-container').toggleClass('d-none', !isCustom);
            $('#custom_task_name').prop('required', !!isCustom);

            if (!isCustom) $('#custom_task_name').val('');
        }

        // ✅ Listener SOLO UNA VEZ
        $('#task-list').on('change', toggleCustomTaskName);

        $('#addDescriptionCheck').on('change', function() {
            $('#description-box-container').toggleClass('d-none', !this.checked);
            if (!this.checked) $('#task_description').val('');
        });

        function updateMasterHidden() {
            const values = [];
            $('.master-box').each(function() {
                values.push($(this).val().toUpperCase());
            });
            const [a, b, c, d, e, f, g, h] = values;
            $('#task-referencia').val((a || '') + (b || '') + (c || '') + (d || '') + (e || '') + '.' +
                (f || '') + '-' + (g || '') + (h || ''));
        }

        function setMasterYearBox() {
            const year = String(new Date().getFullYear()).slice(-2);
            const box = $('.master-box[data-master-index="1"]');
            if (box.val() !== year) {
                box.val(year);
                updateMasterHidden();
            }
        }
        setMasterYearBox();
        setInterval(setMasterYearBox, 60000);

        // Popover de ayuda para la referencia (cuadraditos)
        var referenciaHelpContent = '' +
            '<div style="max-width: 320px; padding: 4px 2px;">' +
            '<p class="mb-1" style="font-weight:600; font-size:0.85rem;">{{ __("Cómo rellenar la referencia") }}</p>' +
            '<div class="d-flex justify-content-between" style="font-size:0.75rem;"><span>{{ __("Año") }} (auto)</span><code>26</code></div>' +
            '<div class="d-flex justify-content-between" style="font-size:0.75rem;"><span>{{ __("Delegación") }}</span><code>EN</code></div>' +
            '<div class="d-flex justify-content-between" style="font-size:0.75rem;"><span>{{ __("Código de obra") }}</span><code>123</code></div>' +
            '<div class="d-flex justify-content-between" style="font-size:0.75rem;"><span>{{ __("Zona") }}</span><code>L</code></div>' +
            '<div class="d-flex justify-content-between" style="font-size:0.75rem;"><span>{{ __("Sistema") }}</span><code>3</code></div>' +
            '<div class="d-flex justify-content-between" style="font-size:0.75rem;"><span>{{ __("Versión") }}</span><code>2</code></div>' +
            '<div class="d-flex justify-content-between" style="font-size:0.75rem;"><span>{{ __("Nº de planos") }} <em>({{ __("opcional") }})</em></span><code>1</code></div>' +
            '<div class="d-flex justify-content-between" style="font-size:0.75rem;"><span>{{ __("Desglose por falta de campos") }} <em>({{ __("opcional") }})</em></span><code>1</code></div>' +
            '<hr style="margin:6px 0;">' +
            '<div style="font-size:0.72rem; color:#6b7280;">' +
            '{{ __("Formato") }}: código + delegación + obra + zona + sistema . versión - planos + desglose<br>' +
            '{{ __("Solo los 2 últimos cuadros son opcionales. Los 6 primeros son obligatorios.") }}' +
            '</div>' +
            '</div>';

        if (typeof bootstrap !== 'undefined' && typeof bootstrap.Popover !== 'undefined' && document.getElementById('referencia-help')) {
            var referenciaHelpPopover = new bootstrap.Popover(document.getElementById('referencia-help'), {
                html: true,
                placement: 'right',
                trigger: 'click',
                customClass: 'referencia-help-popover',
                sanitize: false,
                title: '<div class="d-flex align-items-center justify-content-between w-100" style="gap: 16px;">{{ __("Referencia") }}<button type="button" class="btn-close position-static flex-shrink-0" id="referencia-help-close" aria-label="Cerrar"></button></div>',
                content: referenciaHelpContent
            });

            $(document).on('click', '#referencia-help-close', function() {
                referenciaHelpPopover.hide();
            });
        }

        $(document).on('input', '.master-box[type="text"]', function() {
            const index = Number($(this).data('master-index'));
            if (index === 3) {
                const cleaned = this.value.replace(/\D/g, '').slice(0, 3);
                if (this.value !== cleaned) this.value = cleaned;
            }
            if (index === 4) {
                const cleaned = this.value.toUpperCase().replace(/[^A-Z]/g, '');
                if (this.value !== cleaned) this.value = cleaned;
            }
            if (index === 6 || index === 7 || index === 8) {
                const cleaned = this.value.replace(/\D/g, '').slice(0, 1);
                if (this.value !== cleaned) this.value = cleaned;
            }
            const val = $(this).val();
            const maxLen = index === 3 ? 3 : 1;
            const next = $('.master-box[data-master-index="' + (index + 1) + '"]');
            if (val.length >= maxLen && next.length) {
                next.focus().select();
            }
            updateMasterHidden();
        });

        $('.master-box[data-master-index="2"]').on('change', function() {
            const next = $('.master-box[data-master-index="' + (Number($(this).data('master-index')) + 1) + '"]');
            if ($(this).val() && next.length) {
                next.focus().select();
            }
            updateMasterHidden();
        });

        $('.master-sys-btn').on('click', function() {
            $('.master-sys-dd').toggleClass('show');
        });

        $('.master-sys-item').on('click', function(e) {
            e.preventDefault();
            const val = $(this).data('value');
            $('.master-box[data-master-index="5"]').val(val || '');
            $('.master-sys-value').text(val ? val : '');
            $('.master-sys-dd').toggleClass('show', false);
            const next = $('.master-box[data-master-index="6"]');
            if (val && next.length) {
                next.focus().select();
            }
            updateMasterHidden();
        });

        $('.empresa-input').on('click', function() {
            $('.empresa-dd').addClass('show');
        });

        $('.empresa-input').on('input', function() {
            const term = $(this).val().trim().toLowerCase();
            const clearItem = $('.empresa-item[data-value=""]').first();
            const noMatch = $('.empresa-no-match');
            let anyVisible = false;

            $('.empresa-dd .empresa-item:not(.empresa-no-match)').each(function() {
                if (term === '') {
                    $(this).show();
                    anyVisible = true;
                } else {
                    const hay = String($(this).data('tosearch') || '').indexOf(term) !== -1;
                    $(this).toggle(hay);
                    if (hay) {
                        anyVisible = true;
                    }
                }
            });

            clearItem.toggle(term === '');
            noMatch.toggle(term !== '' && !anyVisible);

            const selected = $('#empresa_id').val();
            if (selected && $('.empresa-dd .empresa-item[data-value="' + selected + '"]').is(':hidden')) {
                $('#empresa_id').val('');
            }
            $('.empresa-dd').addClass('show');
        });

        $('.empresa-item:not(.empresa-no-match)').on('click', function(e) {
            e.preventDefault();
            const val = $(this).data('value');
            $('#empresa_id').val(val || '');
            $('#empresa_input').val(val || '');
            $('.empresa-dd').removeClass('show');
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('.master-sys-dd').length) {
                $('.master-sys-dd').removeClass('show');
            }
            if (!$(e.target).closest('.empresa-dd').length) {
                $('.empresa-dd').removeClass('show');
            }
        });

        $(document).on('keydown', '.master-box[type="text"]', function(e) {
            if (e.key === 'Backspace' && $(this).val() === '') {
                const prev = $('.master-box[data-master-index="' + (Number($(this).data('master-index')) - 1) + '"]');
                if (prev.length) {
                    prev.focus().select();
                    e.preventDefault();
                }
            }
        });

        function updateSelects(projectIdOverride = null) {
            var projectId, selectedProject;

            // Si viene projectIdOverride (cuando está preseleccionado), usarlo
            if (projectIdOverride !== null) {
                projectId = projectIdOverride;
                // Buscar el proyecto en @json($projects) para obtener sus datos
                var projects = @json($projects);
                selectedProject = projects.find(p => String(p.id) === String(projectId));
            } else {
                // Normal: obtener del select
                var selectedOption = $('#project_id').find('option:selected');
                projectId = selectedOption.val();
                selectedProject = selectedOption.data('project');
                if (typeof selectedProject === 'string') {
                    selectedProject = JSON.parse(selectedProject);
                }
            }

            // Reset selects
            $('#task-list').empty().append($('<option>', {
                value: '',
                text: "{{ __('Select Task') }}"
            }));
            $('#milestone_id').empty().append($('<option>', {
                value: '',
                text: "{{ __('Select Milestone') }}"
            }));

            // ✅ taskTypes ANTES de iterar
            var taskTypes = @json($taskType);

            // Cargar task types y marcar "custom"
            $.each(taskTypes, function(index, task) {
                if (selectedProject && String(selectedProject.type) == String(task.project_type)) {

                    var isCustom = String(task.name).trim().toLowerCase() === 'custom';

                    var $opt = $('<option>', {
                        value: task.id,
                        text: task.name
                    });

                    // ✅ marcar atributo para detectarlo al seleccionar
                    $opt.attr('data-is-custom', isCustom ? '1' : '0');

                    $('#task-list').append($opt);
                }
            });

            // Cargar milestones
            var milestones = @json($milestones);
            $.each(milestones, function(index, milestone) {
                if (String(projectId) == String(milestone.project_id)) {
                    var option = $('<option>', {
                        value: milestone.id,
                        text: milestone.title
                    });

                    @if ($selectedMilestoneId)
                        if (String(milestone.id) == '{{ $selectedMilestoneId }}') {
                            option.attr('selected', 'selected');
                        }
                    @endif

                    $('#milestone_id').append(option);
                }
            });

            // ✅ Ajustar visibilidad del input tras repintar
            toggleCustomTaskName();
            const shouldShowTaskAssign = toggleTaskAssignSelector(selectedProject);
            toggleMasterContainer(selectedProject);
            applyProjectTypeLayout(selectedProject, shouldShowTaskAssign);
        }

        $('#project_id').on('change', updateSelects);
        $('#milestone_id').on('change', function() {
            var selectedOption = $('#project_id').find('option:selected');
            var selectedProject = selectedOption.data('project');
            if (typeof selectedProject === 'string') {
                selectedProject = JSON.parse(selectedProject);
            }
            const shouldShowTaskAssign = toggleTaskAssignSelector(selectedProject);
            toggleMasterContainer(selectedProject);
            applyProjectTypeLayout(selectedProject, shouldShowTaskAssign);
        });

        // Si ya hay proyecto preseleccionado, disparar updateSelects con el ID
        @if ($selectedProjectId)
            updateSelects('{{ $selectedProjectId }}');
        @endif



        // Al cambiar el select de proyecto se ejecuta la función
        $('#project_id').on('change', function() {
            updateSelects();
        });
        var openedFromStatusChangeTrigger = '{{ $fromMilestoneBoard }}';
        console.log(openedFromStatusChangeTrigger)
        var closeBtnCollection = document.getElementsByClassName('btn-close').length;
        console.log(closeBtnCollection)
        if (openedFromStatusChangeTrigger) {
            for (let index = 0; index < closeBtnCollection; index++) {
                document.getElementsByClassName('btn-close')[index].style.display = 'none'
            }

        }
        // -----------------------------
        // Script para el botón "Descartar"
        // Este bloque solo se activa si existe un proyecto preseleccionado y un milestone (vista 1)
        @if ($selectedProjectId && $selectedMilestoneId)
            $('#cancelBtn').on('click', function() {
                // var openedFromStatusChangeTrigger = '{{ $fromMilestoneBoard }}';
                // console.log(openedFromStatusChangeTrigger);
                // console.log('Milestone ID:', '{{ $selectedMilestoneId }}');
                // console.log('Project ID:', '{{ $selectedProjectId }}');
                // Obtenemos el id del milestone (tarjeta) y el id del proyecto
                var cardId = '{{ $selectedMilestoneId }}';
                var project_id = '{{ $selectedProjectId }}';

                // Realizamos la petición AJAX para actualizar el estado del milestone.
                // NOTA: Se modifica la URL para enviar el id del milestone en lugar del id del proyecto,
                // ya que la ruta espera: {slug}/milestone-board/{id}/order-update
                if (openedFromStatusChangeTrigger) {
                    $.ajax({
                        url: '{{ route('milestone.update.order', [$currentWorkspace->slug, $selectedMilestoneId]) }}',
                        type: 'POST',
                        data: {
                            id: cardId,
                            new_status: 1, // Volver al estado 1
                            old_status: 2, // Se asume que estaba en estado 2 (en progreso)
                            project_id: project_id,
                            // Agregar otros parámetros que sean necesarios según la lógica del controlador
                        },
                        success: function(data) {
                            console.log('El estado del milestone se ha revertido a 1');
                            // Se recarga la página después de 1 segundo
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        },
                        error: function(xhr, status, error) {
                            console.error('Error al revertir el estado del milestone:',
                                error);
                            // Aquí puedes agregar la lógica para mostrar un toast o mensaje de error
                        }
                    });
                }

            });
        @endif

    });
</script>
<script>
    (function() {
        // ✅ Detectar si este Create Task viene del cambio de estado 1->2
        const fromStatusChange =
            "{{ $fromMilestoneBoard ? 1 : 0 }}" === "1";

        if (!fromStatusChange) return;

        const modalEl = document.getElementById('commonModal');
        if (!modalEl) return;

        // Evitar múltiples handlers si reabres el modal varias veces
        $(modalEl).off('hidden.bs.modal.taskCreateReload');
        $(modalEl).on('hidden.bs.modal.taskCreateReload', function() {
            location.reload();
        });
    })();
</script>

<style>
    .estimated_date {
        display: flex;
        justify-content: center;
        align-items: flex-end;
    }

    #user-select-task-assignee {
        max-height: 180px;
        overflow-y: auto;
    }

    .task-assignee-invalid {
        border-color: #b73a3a !important;
        box-shadow: 0 0 0 2px rgba(183, 58, 58, 0.14) !important;
        background-color: #fff8f8;
    }

    .task-assignee-feedback {
        color: #b73a3a;
        font-size: 12px;
        font-weight: 500;
        margin-top: 6px;
        display: block;
    }

    .estimated_date>p {
        font-size: 14px;
        text-align: center;
    }

    .master-box {
        width: 44px;
        min-width: 44px;
        max-width: 44px;
        height: 38px;
        padding: 0.4rem 0.25rem;
        font-size: 0.8rem;
        font-weight: 500;
        line-height: 1;
        text-align: center;
    }

    .master-num {
        width: 50px;
        min-width: 50px;
        max-width: 50px;
        height: 38px;
        font-size: 0.8rem;
        font-weight: 500;
        letter-spacing: 1px;
    }

    .master-select {
        width: auto;
        min-width: 52px;
        max-width: none;
        height: 38px;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        vertical-align: middle;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        padding: 0.25rem 1.25rem 0.25rem 0.5rem;
        font-size: 0.8rem;
        font-weight: 500;
        text-align: center;
        background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'><path d='M1 1l4 4 4-4' stroke='%236c757d' stroke-width='1.5' fill='none' stroke-linecap='round'/></svg>");
        background-repeat: no-repeat;
        background-position: right 0.45rem center;
        cursor: pointer;
    }

    .master-select:hover,
    .master-select:focus {
        border-color: #6c757d;
        box-shadow: none;
    }

    .master-box[data-master-index="2"] {
        width: 120px;
        min-width: 75px;
        max-width: 120px;
    }

    .master-sys-dd {
        position: relative;
        display: inline-block;
    }

    .master-sys-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: auto;
        min-width: 52px;
        max-width: none;
        height: 38px;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        background-color: #fff;
        color: #212529;
        font-size: 0.8rem;
        font-weight: 500;
        appearance: none;
        -webkit-appearance: none;
        padding: 0.25rem 1.25rem 0.25rem 0.5rem;
        background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'><path d='M1 1l4 4 4-4' stroke='%236c757d' stroke-width='1.5' fill='none' stroke-linecap='round'/></svg>");
        background-repeat: no-repeat;
        background-position: right 0.45rem center;
        cursor: pointer;
    }

    .master-sys-btn:hover,
    .master-sys-btn:focus {
        border-color: #6c757d;
        box-shadow: none;
    }

    .master-sys-value:empty::before {
        content: "0";
        color: #adb5bd;
        font-weight: 400;
    }

    .master-sys-menu {
        display: none;
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        z-index: 1500;
        min-width: 180px;
        max-height: 250px;
        overflow-y: auto;
        padding: 0.25rem 0;
        background-color: #fff;
        border: 1px solid #e4e7e9;
        border-radius: 0.25rem;
        box-shadow: 0 0.3rem 0.8rem rgba(0, 0, 0, 0.15);
        text-align: left;
    }

    .master-sys-dd.show .master-sys-menu {
        display: block;
    }

    .master-sys-item {
        display: block;
        padding: 0.4rem 0.75rem;
        font-size: 0.8rem;
        font-weight: 500;
        color: #293240;
        text-decoration: none;
        cursor: pointer;
    }

    .master-sys-item:hover {
        background-color: #eff0f2;
        color: #293240;
    }

    .empresa-dd {
        position: relative;
        display: inline-block;
    }

    .empresa-input {
        width: min(250px, 100%);
        min-width: 180px;
        max-width: none;
        height: 38px;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        background-color: #fff;
        color: #212529;
        font-size: 0.8rem;
        font-weight: 500;
        padding: 0.25rem 1.75rem 0.25rem 0.5rem;
        background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'><path d='M1 1l4 4 4-4' stroke='%236c757d' stroke-width='1.5' fill='none' stroke-linecap='round'/></svg>");
        background-repeat: no-repeat;
        background-position: right 0.6rem center;
    }

    .empresa-input:hover,
    .empresa-input:focus {
        border-color: #6c757d;
        box-shadow: none;
        outline: none;
    }

    .empresa-menu {
        display: none;
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        z-index: 1500;
        min-width: 200px;
        max-height: 250px;
        overflow-y: auto;
        padding: 0.25rem 0;
        background-color: #fff;
        border: 1px solid #e4e7e9;
        border-radius: 0.25rem;
        box-shadow: 0 0.3rem 0.8rem rgba(0, 0, 0, 0.15);
        text-align: left;
    }

    .empresa-dd.show .empresa-menu {
        display: block;
    }

    .empresa-item {
        display: block;
        padding: 0.4rem 0.75rem;
        font-size: 0.8rem;
        font-weight: 500;
        color: #293240;
        text-decoration: none;
        cursor: pointer;
    }

    .empresa-item:hover {
        background-color: #eff0f2;
        color: #293240;
    }

    .empresa-no-match {
        color: #dc3545;
        font-style: italic;
        cursor: default;
    }

    .empresa-no-match:hover {
        background-color: transparent;
        color: #dc3545;
    }

    .master-box[data-master-index]:not([data-master-index="1"])::placeholder {
        color: #adb5bd;
        font-weight: 400;
    }
</style>
