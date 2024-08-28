@php
    $user = Auth::user();
    $actionUrl =
        $project_id == -1
            ? route('projects.milestone.store', [$currentWorkspace->slug, 'PLACEHOLDER'])
            : route('projects.milestone.store', [$currentWorkspace->slug, $project->id]);
@endphp
@if ($currentWorkspace)
    <form class="" id="project-form" method="POST" action="{{ $actionUrl }}">
        @csrf <div class="modal-body">
            <div class="row">

                <div class="form-group">
                    <label class="col-form-label">
                        {{ __('Projects') }}
                    </label>
                    <div class="col-md-12">
                        @if (isset($project_id) && $project_id == -1)
                            <select class="form-control form-control-light select2" name="project_id" id="project_id"
                                required>
                                <option value="" disabled selected>{{ __('Select Project') }}</option>
                                @foreach ($project as $proj)
                                    <option value="{{ $proj->id }}" data-project='{{ json_encode($proj) }}'>
                                        {{ $proj->name }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input class="form-control" type="text" value="{{ $project->name }}" disabled>

                            <input class="form-control" type="text" id="project_id" name="project_id"
                                value="{{ $project->name }}" autocomplete="off" style="display: none;">

                        @endif

                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <input type="text" class="form-control form-control-light" id="milestone-title"
                            placeholder="{{ __('Enter Title') }}" name="title" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group ">
                            <label class="form-label">{{ trans('messages.Created_date') }}</label>

                            <div class="input-group date ">
                                <input class="form-control datepicker22" type="text" id="start_date"
                                    name="start_date" value="<?php echo date('Y-m-d'); ?>" autocomplete="off" disabled>
                                <span class="input-group-text">
                                    <i class="feather icon-calendar"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">{{ __('messages.Desired_delivery_date') }}</label>
                            <input onclick="this.showPicker()" type="date"
                                class="form-control form-control-light date" id="end_date" value=""
                                placeholder="{{ __('Date') }}" name="end_date" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="task-summary" class="col-form-label">{{ __('Description') }}</label>
                    <textarea class="form-control form-control-light" id="task-summary" rows="3" name="summary"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <input type="submit" value="{{ __('Save Changes') }}" class="btn  btn-primary">
            </div>
        </div>
    </form>
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
                                {{ __("It's looking like you may have taken a wrong turn. Don't worry... it happens to the
                                                                                                                                                                                                                                                                                                                                                                                                 best of us. Here's a little tip that might help you get back on track.") }}
                            </p>
                            <div class="mt-3">
                                <a class="btn-return-home badge-blue" href="{{ route('home') }}"><i
                                        class="fas fa-reply"></i> {{ __('Return Home') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@if (isset($project_id) && $project_id == -1)
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#project_id').on('change', function() {
                var selectedProjectId = $(this).val();
                var currentWorkspaceSlug = "{{ $currentWorkspace->slug }}";

                // Construir la URL con el projectId seleccionado
                var actionUrl =
                    `{{ route('projects.milestone.store', [$currentWorkspace->slug, 'PLACEHOLDER']) }}`;
                actionUrl = actionUrl.replace('PLACEHOLDER', selectedProjectId);
                console.log(actionUrl);
                // Actualizar el atributo action del formulario
                $('#project-form').attr('action', actionUrl);
            });
        });
    </script>
@endif

<style>
    .disabled {
        color: black !important;
        /* gris típico para elementos deshabilitados */
        background-color: #6c757d !important;
        /* fondo claro */
    }
</style>
