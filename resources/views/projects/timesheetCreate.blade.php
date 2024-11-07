@if ($currentWorkspace)
    <form class="" method="post" action="{{ route('timesheet.store', $currentWorkspace->slug) }}">
        @csrf
        <div class="modal-body">
            <div class="row">
                <div class="form-group col-md-6 ">
                    <label class="col-form-label">
                        {{ __('Projects') }}
                    </label>
                    <select class="form-control form-control-light select2" name="project_id" id="project_id" required>
                        <option value="">{{ __('Select Project') }}</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}" data-project='{{ json_encode($project) }}'>
                                {{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label class="col-form-label">{{ __('Milestone') }}</label>
                    <select class="form-control form-control-light select2" name="milestone_id" id="milestone_id"
                        required>
                        <option value="">{{ __('Select Milestone') }}</option>
                    </select>
                </div>
                <div class="form-group col-md-6" id="task-container">
                    <label class="col-form-label">{{ __('Task type') }}</label>
                    <select class="form-control form-control-light select2" id="task-list" name="type_id" required>
                        <option value="">{{ __('Select Task') }}</option>
                    </select>
                </div>

                <div class="form-group col-md-6 ">
                    <label for="estimated_date" class="col-form-label">{{ __('Fecha Estimada') }}</label>
                    <input onclick="this.showPicker()" type="date" class="form-control form-control-light date"
                        id="estimated_date" value="" placeholder="{{ __('Date') }}" name="estimated_date"
                        required>
                </div>

                <div class="form-group col-md-6" style="display: none;">
                    <label for="time" class="col-form-label">{{ __('Time') }}</label>
                    <input type="time" class="form-control form-control-light" id="time" value="00:00"
                        placeholder="{{ __('Time') }}" name="time">
                </div>
            </div>
        </div>
        <div class=" modal-footer">
            <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
            <button type="submit" class="btn btn-primary">{{ __('Create') }}</button>
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
                                {{ __("It's looking like you may have taken a wrong turn. Don't worry... it happens to the best of us. Here's a little tip that might help you get back on track.") }}
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
<link rel="stylesheet" href="{{ asset('assets/custom/libs/bootstrap-daterangepicker/daterangepicker.css') }}">
<script src="{{ asset('assets/custom/libs/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#project_id').on('change', function() {
            var selectedOption = $(this).find('option:selected');
            var projectId = selectedOption.val();
            var selectedProject = JSON.parse(selectedOption.attr('data-project'));

            $('#task-list').empty().append(
                '<option value="" readonly>{{ __('Select Task') }}</option>');
            $('#milestone_id').empty().append(
                '<option value="" readonly>{{ __('Select Milestone') }}</option>');

            var taskTypes = @json($taskTypes);
            $.each(taskTypes, function(index, task) {
                if (selectedProject.type == task.project_type) {
                    $('#task-list').append($('<option>', {
                        value: task.id,
                        text: task.name
                    }));
                }
            });

            let milestones = @json($milestones);       
            $.each(milestones, function(index, milestone) {
                if (projectId == milestone.project_id) {
                    $('#milestone_id').append($('<option>', {
                        value: milestone.id,
                        text: milestone.title
                    }));
                }
            });
        });
    });
</script>
<style>
    .estimated_date {
        display: flex;
        justify-content: center;
        align-items: flex-end;
        /* text-align: center */
    }

    .estimated_date>p {
        font-size: 14px;
        text-align: center;
    }
</style>
