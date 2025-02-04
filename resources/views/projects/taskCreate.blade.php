@php
    $user = Auth::user();
    // Se leen los parámetros (si existen). Si no vienen, quedan como null.
    $selectedProjectId = request()->get('project_id');
    $selectedMilestoneTitle = request()->get('milestoneTitle');
    $selectedMilestoneId = request()->get('milestone_id');
@endphp

@if ($projects && $currentWorkspace)
    <form method="post" action="@auth('web'){{ route('tasks.store', $currentWorkspace->slug) }}@endauth">
        @csrf
        <div class="modal-body">
            <div class="row">
                <!-- Select de Proyectos -->
                <div class="form-group col-md-12">
                    <label class="col-form-label">{{ __('Projects') }}</label>
                    @if ($selectedProjectId)
                        <!-- Si existe proyecto preseleccionado, se muestra un select con el único option seleccionado -->
                        <input type="hidden" name="project_id" value="{{ $selectedProjectId }}" style="display: none;">
                        <select class="form-control form-control-light select2" name="project_id" id="project_id" required disabled>
                            <option value="">{{ __('Select Project') }}</option>
                            @foreach ($projects as $project)
                                @if ($selectedProjectId == $project->id)
                                    <option value="{{ $project->id }}" data-project='{{ json_encode($project) }}' selected>
                                        {{ $project->name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    @else
                        <!-- En caso contrario se muestran todos los proyectos -->
                        <select class="form-control form-control-light select2" name="project_id" id="project_id" required>
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
                <div class="form-group col-md-6">
                    <label class="col-form-label">{{ __('Milestone') }}</label>
                    
                        @if($selectedMilestoneTitle)
                        <input type="hidden" name="milestone_id" value="{{ $selectedMilestoneId }}" style="display: none;">
                        <select class="form-control form-control-light select2" name="milestone_id" id="milestone_id" required disabled>
                            <option value="{{$selectedMilestoneId}}">{{$selectedMilestoneTitle}}</option>
                        @else
                        <select class="form-control form-control-light select2" name="milestone_id" id="milestone_id" required>
                            <option value="">{{ __('Select Milestone') }}</option>
                        @endif
                    </select>
                </div>

                <!-- Select de Task Type -->
                <div class="form-group col-md-6" id="task-container">
                    <label class="col-form-label">{{ __('Task type') }}</label>
                    <select class="form-control form-control-light select2" id="task-list" name="type_id" required>
                        <option value="">{{ __('Select Task') }}</option>
                    </select>
                </div>

                <!-- Fecha de inicio -->
                <div class="form-group col-md-6">
    <label for="start_date" class="col-form-label">{{ __('Start date') }}</label>
    <input type="text" class="form-control form-control-light date"
           id="start_date_display" name="start_date_display"
           value="{{ \Carbon\Carbon::now()->format('d/m/Y') }}" disabled>
    <!-- Campo oculto para enviar el valor -->
    <input type="hidden" id="start_date" name="start_date" value="{{ \Carbon\Carbon::now()->format('d/m/Y') }}">
</div>

                <!-- Fecha estimada -->
                <div class="form-group col-md-6">
                    <label for="estimated_date" class="col-form-label">{{ __('Estimated delivery date') }}</label>
                    <input onclick="this.showPicker()" type="date" class="form-control form-control-light date"
                        id="estimated_date" placeholder="{{ __('Date') }}" name="estimated_date" required>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            @if ($selectedProjectId)
                <!-- Si se viene de la vista 1 se muestra el botón Cancelar -->
                <button type="button" id="cancelBtn" class="btn btn-light" data-bs-dismiss="modal">Descartar</button>
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

        // Si hay un proyecto preseleccionado (vista 1) o se cambia de proyecto (vista 2) se actualizan los selects
        function updateSelects() {
            var selectedOption = $('#project_id').find('option:selected');
            var projectId = selectedOption.val();

            // Reiniciamos los selects de task y milestone
            $('#task-list').empty().append($('<option>', {
                value: '',
                text: "{{ __('Select Task') }}"
            }));
            $('#milestone_id').empty().append($('<option>', {
                value: '',
                text: "{{ __('Select Milestone') }}"
            }));

            // Obtenemos los datos del proyecto seleccionado (asegurando la conversión a objeto)
            var selectedProject = selectedOption.data('project');
            if (typeof selectedProject === 'string') {
                selectedProject = JSON.parse(selectedProject);
            }

            // Cargar opciones para task-list según el tipo de proyecto
            var taskTypes = @json($taskType);
            $.each(taskTypes, function(index, task) {
                if (selectedProject && selectedProject.type == task.project_type) {
                    $('#task-list').append($('<option>', {
                        value: task.id,
                        text: task.name
                    }));
                }
            });

            // Cargar opciones para milestone según el proyecto seleccionado
            var milestones = @json($milestones);
            $.each(milestones, function(index, milestone) {
                if (projectId == milestone.project_id) {
                    var option = $('<option>', {
                        value: milestone.id,
                        text: milestone.title
                    });
                    // Si el milestone coincide con el preseleccionado, se marca como seleccionado
                    @if($selectedMilestoneId)
                        if (milestone.id == '{{ $selectedMilestoneId }}') {
                            option.attr('selected', 'selected');
                        }
                    @endif
                    $('#milestone_id').append(option);
                }
            });
        }

        // Al cambiar el select de proyecto se ejecuta la función
        $('#project_id').on('change', function() {
            updateSelects();
        });

        // Si ya hay proyecto preseleccionado, disparamos el evento change
        @if($selectedProjectId)
            $('#project_id').trigger('change');
        @endif

        // -----------------------------
        // Script para el botón "Descartar"
        // Este bloque solo se activa si existe un proyecto preseleccionado y un milestone (vista 1)
        @if($selectedProjectId && $selectedMilestoneId)
            $('#cancelBtn').on('click', function() {
                // Obtenemos el id del milestone (tarjeta) y el id del proyecto
                var cardId = '{{ $selectedMilestoneId }}';
                var project_id = '{{ $selectedProjectId }}';
                
                // Realizamos la petición AJAX para actualizar el estado del milestone.
                // NOTA: Se modifica la URL para enviar el id del milestone en lugar del id del proyecto,
                // ya que la ruta espera: {slug}/milestone-board/{id}/order-update
                $.ajax({
                    url: '{{ route('milestone.update.order', [$currentWorkspace->slug, $selectedMilestoneId]) }}',
                    type: 'POST',
                    data: {
                        id: cardId,
                        new_status: 1,   // Volver al estado 1
                        old_status: 2,   // Se asume que estaba en estado 2 (en progreso)
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
                        console.error('Error al revertir el estado del milestone:', error);
                        // Aquí puedes agregar la lógica para mostrar un toast o mensaje de error
                    }
                });
            });
        @endif

    });
</script>

<style>
    .estimated_date {
        display: flex;
        justify-content: center;
        align-items: flex-end;
    }
    .estimated_date > p {
        font-size: 14px;
        text-align: center;
    }
</style>
