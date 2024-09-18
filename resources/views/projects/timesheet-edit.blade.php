{{ Form::model($timesheet, ['route' => ['project.timesheet.update', ['slug' => $currentWorkspace->slug, 'timesheet_id' => $timesheet->id, 'project_id' => $project_id]], 'method' => 'POST']) }}
<div class="modal-body">
    <input type="hidden" name="project_id" value="{{ $parseArray['project_id'] }}">
    <input type="hidden" name="task_id" value="{{ $parseArray['task_id'] }}">
    <input type="hidden" name="date" value="{{ $timesheet->date }}">
    <input type="hidden" id="totaltasktime"
        value="{{ $parseArray['totaltaskhour'] . ':' . $parseArray['totaltaskminute'] }}">


    <div class="form-group">
        <label class="col-form-label">{{ __('Project') }}</label>
        <input type="text" class="form-control" value="{{ $parseArray['project_name'] }}" disabled="disabled">
    </div>

    <div class="form-group">
        <label class="col-form-label">{{ __('Task') }}</label>
        <input type="text" class="form-control" value="{{ $parseArray['task_name'] }}" disabled="disabled">
    </div>

    <div class="row">
        <div class="col-md-12">
            <label for="time" class="col-form-label">{{ __('Time') }}</label>
        </div>
        <div class="col-md-6">
            <select class="form-control select2" name="time_hour" id="time_hour" required="">
                <option value="">{{ __('Hours') }}</option>

                <?php for ($i = 0; $i < 9; $i++) { $i = $i < 10 ? '0' . $i : $i; ?>

                <option value="{{ $i }}" {{ $parseArray['time_hour'] == $i ? 'selected="selected"' : '' }}>
                    {{ $i }}</option>

                <?php } ?>

            </select>
        </div>

        <div class="col-md-6">
            <select class="form-control select2" name="time_minute" id="time_minute" required>
                <option value="">{{ __('Minutes') }}</option>

                <?php for ($i = 0; $i < 60; $i += 10) { $i = $i < 10 ? '0' . $i : $i; ?>

                <option value="{{ $i }}"
                    {{ $parseArray['time_minute'] == $i ? 'selected="selected"' : '' }}>{{ $i }}</option>

                <?php } ?>

            </select>
        </div>
        <div class="col-md-12">
            <div class="display-total-time">
                <i class="fas fa-clock"></i>
                <span>{{ __('Total Time') }} :
                    {{ $parseArray['totaltaskhour'] . ' ' . __('Hours') . ' ' . $parseArray['totaltaskminute'] . ' ' . __('Minutes') }}</span>
            </div>
        </div>

        @php($id = str_replace('.', '', uniqid('', true)))
    </div>
</div>

<div>
    <div class="row">
        <div class="text-end">
            <a href="#" class="action-btn btn-danger  btn btn-sm d-inline-flex"
                data-confirm="{{ __('Are You Sure?') }}" 
                data-text="{{ trans('This action can not be undone. Do you want to continue?') }}"
                data-confirm-yes="delete-form-{{ $id }}">
                <i class="ti ti-trash"></i>
            </a>
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
            <input type="submit" value="{{ __('Save Changes') }}" class="btn  btn-primary me-5">

        </div>

    </div>
</div>
{{ Form::close() }}

<form id="delete-form-{{ $id }}"
    action="{{ route('timesheet.destroy', [$currentWorkspace->slug, $timesheet->id]) }}" method="POST"
    style="display: none;">
    @csrf
    @method('DELETE')
</form>
