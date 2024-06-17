@php
    $user = Auth::user();
@endphp
@if ($projects && $currentWorkspace)
    <form class="" method="post" action="@auth('web'){{ route('tasks.store', $currentWorkspace->slug) }}@endauth">
        @csrf
        <div class="modal-body">
            <div class="row">
                <div class="form-group col-md-8">
                    <label class="col-form-label">{{ __('Projects') }}</label>
                    <select class="form-control form-control-light select2" name="project_id" id="project_id" required>
                        <option value="">{{ __('Select Project') }}</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}" data-project='{{ json_encode($project) }}'>
                                {{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-4">
                    <label class="col-form-label">{{ __('Milestone') }}</label>
                    <select class="form-control form-control-light select2" name="milestone_id" id="milestone_id"
                        required>
                        <option value="">{{ __('Select Milestone') }}</option>
                    </select>
                </div>
                <div class="form-group col-md-8" id="task-container">
                    <label class="col-form-label">{{ __('dictionary.Task_type') }}</label>
                    <select class="form-control form-control-light select2" id="task-list" name="type_id" required>
                        <option value="">{{ __('Select Task') }}</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label class="col-form-label">{{ __('Entrega estimada') }}</label>
                    <div class="form-group">
                        <div class="input-group date">
                            <input class="form-control datepicker23" type="text" id="end_date" name="estimated_date"
                                autocomplete="off">
                            <span class="input-group-text">
                                <i class="feather icon-calendar"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-12" style="display:none">
                    <label class="col-form-label">{{ __('Assign To') }}</label>
                    <select class=" multi-select" id="assign_to" name="assign_to[]" data-toggle="select2"
                        multiple="multiple" data-placeholder="{{ __('Select Users ...') }}">
                        <option value="{{ $user->id }}" selected></option>
                    </select>
                </div>
                <div class="form-group col-md-12">
                    <label class="col-form-label">{{ __('Description') }}</label>
                    <textarea class="form-control form-control-light" id="task-description" rows="3" name="description"></textarea>
                </div>

                @if ($currentWorkspace->is_googlecalendar_enabled == 'on')
                    <div class="form-group col-md-6">
                        {{ Form::label('synchronize_type', __('Synchroniz in Google Calendar ?'), ['class' => 'col-form-label']) }}
                        <div class=" form-switch">
                            <input type="checkbox" class="form-check-input mt-2" name="synchronize_type"
                                id="switch-shadow" value="google_calender">
                            <label class="form-check-label" for="switch-shadow"></label>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
            <input type="submit" value="{{ __('Save Changes') }}" class="btn  btn-primary">
        </div>

    </form>
    <link rel="stylesheet" href="{{ asset('assets/custom/libs/bootstrap-daterangepicker/daterangepicker.css') }}">
    <script src="{{ asset('assets/custom/libs/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script>
        if ($(".multi-select").length > 0) {
            $($(".multi-select")).each(function(index, element) {
                var id = $(element).attr('id');
                var multipleCancelButton = new Choices(
                    '#' + id, {
                        removeItemButton: true,
                    }
                );
            });
        }

        (function() {
            var locale = '{{ app()->getLocale() }}';
            var dateInput = document.querySelector('.datepicker23');
            var datepicker = new Datepicker(dateInput, {
                locale: locale,
                buttonClass: 'btn',
                format: 'yyyy-mm-dd',
            });

            dateInput.addEventListener('changeDate', function() {
                datepicker.hide();
            });
        })();
    </script>
    <script>
        $(document).ready(function() {
            $('#project_id').on('change', function() {
                var selectedOption = $(this).find('option:selected');
                var projectId = selectedOption.val();
                var selectedProject = JSON.parse(selectedOption.attr('data-project'));
    
                // Vacía las listas antes de agregar nuevas opciones
                $('#task-list').empty().append(
                    '<option value="" readonly>{{ __('Select Task') }}</option>');
                $('#milestone_id').empty().append(
                    '<option value="" readonly>{{ __('Select Milestone') }}</option>');
    
                // Itera sobre cada tipo de tarea y agrega los que coincidan con el tipo de proyecto
                var taskTypes = @json($taskTypes);
                $.each(taskTypes, function(index, type) {
                    if (selectedProject.type == type.project_type) {
                        $('#task-list').append($('<option>', {
                            value: type.id,
                            text: type.name,
                        }));
                    }
                });
    
                // Itera sobre cada milestone y agrega los que coincidan con el project_id
                let milestones = @json($milestones);
                $.each(milestones, function(index, milestone) {
                    if (projectId == milestone.project_id) {
                        $('#milestone_id').append($('<option>', {
                            value: milestone.id,
                            text: milestone.title
                        }));
                    }
                });
    
                // console.log('Milestones: ', milestones);
                // console.log('task type: ', taskTypes);
                // console.log('Selected Project ID: ', projectId);
                // console.log('Selected Project: ', selectedProject);
            });
        });
    
    </script>

@else
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
                                <a class="btn-return-home badge-blue" href="{{ route('home') }}"><i
                                        class="fas fa-reply"></i>
                                    {{ __('Return Home') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
