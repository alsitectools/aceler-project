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
                        @foreach ($users as $u)
                            <div class="task-assignee-option list-group-item list-group-item-action stylelist ps-3"
                                collected-data-id="{{ $u->id }}" style="padding: 8px; cursor: pointer;">
                                {{ $u->name }}
                            </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="task_assign_override" id="task_assign_override" value="">
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

<script>
    $(document).ready(function() {
        const taskCreateForm = $('#taskCreateForm');
        const projectFieldContainer = $('#project-field-container');
        const milestoneFieldContainer = $('#milestone-field-container');
        const taskTypeContainer = $('#task-container');
        const taskAssignContainer = $('#task-assign-container');
        const taskAssigneeInput = $('#search-task-assignee');
        const taskAssigneeDropdown = $('#user-select-task-assignee');
        const taskAssigneeHidden = $('#task_assign_override');
        const taskAssigneeFeedback = $('#task-assignee-feedback');
        const currentUserId = "{{ Auth::id() }}";
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
            if (taskAssignContainer.hasClass('d-none')) {
                taskAssigneeInput[0].setCustomValidity('');
                clearTaskAssigneeInvalidState();
                taskAssigneeHidden.val('');
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
            "{{ $fromMilestoneBoard ? 1 : 0 }}" === "1" ||
            "{{ $fromMyMilestoneBoard ? 1 : 0 }}" === "1";

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
</style>
