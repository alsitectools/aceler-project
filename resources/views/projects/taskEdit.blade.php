@if ($project && $currentWorkspace && $task)
    <form class="" method="post"
        action="@auth('web'){{ route('tasks.update', [$currentWorkspace->slug, $project->id, $task->id]) }}@elseauth{{ route('client.tasks.update', [$currentWorkspace->slug, $project->id, $task->id]) }}@endauth">
        @csrf
        <div class="modal-body">
            <div class="row">
                <div class="form-group col-md-8">
                    <label class="col-form-label">{{ __('Project') }}</label>
                    <select class="form-control form-control-light select2" disabled>
                        @foreach ($projects as $p)
                            <option> {{ $p->name }}</option>
                        @endforeach
                    </select>
                    <select class="form-control form-control-light select2" name="project_id" style="display:none">
                        @foreach ($projects as $p)
                            <option value="{{ $p->id }}" @if ($task->project_id == $p->id) selected @endif>
                                {{ $p->name }}</option>
                        @endforeach
                    </select>

                </div>
                <div class="form-group col-md-4">
                    <label class="col-form-label">{{ __('Milestone') }}</label>
                    <select class="form-control form-control-light select2" disabled>
                        @foreach ($project->milestones as $milestone)
                            <option> {{ $milestone->title }}</option>
                        @endforeach
                    </select>
                    <select class="form-control form-control-light select2" name="milestone_id" id="task-milestone"
                        style="display:none;">
                        @foreach ($project->milestones as $milestone)
                            <option value="{{ $milestone->id }}" @if ($task->milestone_id == $milestone->id) selected @endif>
                                {{ $milestone->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-12">
                    <label class="col-form-label">{{ __('Title') }}</label>
                    <input type="text" class="form-control form-control-light" id="task-title"
                        placeholder="{{ __('Enter Title') }}" value="{{ $task->title }}" disabled>

                    <input type="text" class="form-control form-control-light" id="task-title"
                        placeholder="{{ __('Enter Title') }}" name="title" required value="{{ $task->title }}"
                        style="display:none">
                </div>

                <div class="form-group col-md-12">
                    <label class="col-form-label">{{ __('Assign To') }}</label>
                    <select class="multi-select" multiple="multiple" id="assign_to" name="assign_to[]" required>
                        @foreach ($users as $u)
                            <option @if (in_array($u->id, $task->assign_to)) selected @endif value="{{ $u->id }}">
                                {{ $u->name }} - {{ $u->email }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12">

                    <label class="col-form-label">{{ __('Duration') }}</label>
                    <div class='input-group form-group'>
                        <input type='text' class=" form-control pc-daterangepicker-3" id="duration" name="duration"
                            value="{{ __('Select Date Range') }}" placeholder="Select date range" />
                        <input type="hidden" name="start_date" id="start_date1">
                        <input type="hidden" name="due_date" id="end_date1">
                        <span class="input-group-text"><i class="feather icon-calendar"></i></span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-form-label">{{ __('Description') }}</label>
                    <textarea class="form-control form-control-light" id="task-description" rows="3" name="description">{{ $task->description }}</textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
            <input type="submit" value="{{ __('Save Changes') }}" class="btn btn-primary">
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
        $(function() {
            var start = moment('{{ $task->start_date }}', 'YYYY-MM-DD HH:mm:ss');
            var end = moment('{{ $task->due_date }}', 'YYYY-MM-DD HH:mm:ss');

            function cb(start, end) {
                $("form #duration").val(start.format('MMM D, YY hh:mm A') + ' - ' + end.format(
                    'MMM D, YY hh:mm A'));
                $('form input[name="start_date"]').val(start.format('YYYY-MM-DD HH:mm:ss'));
                $('form input[name="due_date"]').val(end.format('YYYY-MM-DD HH:mm:ss'));
            }

            $('form #duration').daterangepicker({
                /*autoApply: true,
                autoclose: true,*/
                timePicker: true,
                autoUpdateInput: false,
                startDate: start,
                endDate: end,
                /*startDate: start,
                endDate: end,
                autoApply: true,
                autoclose: true,
                autoUpdateInput: false,*/
                locale: {
                    format: 'MMMM D, YYYY hh:mm A',
                    applyLabel: "{{ __('Apply') }}",
                    cancelLabel: "{{ __('Cancel') }}",
                    fromLabel: "{{ __('From') }}",
                    toLabel: "{{ __('To') }}",
                    daysOfWeek: [
                        "{{ __('Sun') }}",
                        "{{ __('Mon') }}",
                        "{{ __('Tue') }}",
                        "{{ __('Wed') }}",
                        "{{ __('Thu') }}",
                        "{{ __('Fri') }}",
                        "{{ __('Sat') }}"
                    ],
                    monthNames: [
                        "{{ __('January') }}",
                        "{{ __('February') }}",
                        "{{ __('March') }}",
                        "{{ __('April') }}",
                        "{{ __('May') }}",
                        "{{ __('June') }}",
                        "{{ __('July') }}",
                        "{{ __('August') }}",
                        "{{ __('September') }}",
                        "{{ __('October') }}",
                        "{{ __('November') }}",
                        "{{ __('December') }}"
                    ],
                }
            }, cb);

            cb(start, end);
        });
    </script>
    <script>
        $(document).on('change', "select[name=project_id]", function() {
            $.get('@auth('web'){{ route('home') }}@endauth' +
                '/userProjectJson/' + $(this).val(),
                function(data) {
                    $('select[name=assign_to]').html('');
                    data = JSON.parse(data);
                    $(data).each(function(i, d) {
                        $('select[name=assign_to]').append('<option value="' + d.id + '">' + d.name +
                            ' - ' + d.email + '</option>');
                    });
                });
            $.get('@auth('web'){{ route('home') }}@endauth' +
                '/projectMilestoneJson/' + $(this).val(),
                function(data) {
                    $('select[name=milestone_id]').html(
                        '<option value="">{{ __('Select Milestone') }}</option>');
                    data = JSON.parse(data);
                    $(data).each(function(i, d) {
                        $('select[name=milestone_id]').append('<option value="' + d.id + '">' + d
                            .title + '</option>');
                    });
                });
        })
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
                                        class="fas fa-reply"></i> {{ __('Return Home') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
