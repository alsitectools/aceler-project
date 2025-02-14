{{ Form::model($timesheet, ['route' => ['project.timesheet.update', ['slug' => $currentWorkspace->slug, 'timesheet_id' => $timesheet->id, 'project_id' => $project_id]], 'method' => 'POST']) }}
<div class="modal-body">
    <input type="hidden" name="project_id" value="{{ $parseArray['project_id'] }}">
    <input type="hidden" name="task_id" value="{{ $parseArray['task_id'] }}">
    <input type="hidden" name="date" value="{{ $timesheet->date }}">
    <input type="hidden" id="totaltasktime"
        value="{{ $parseArray['totaltaskhour'] . ':' . $parseArray['totaltaskminute'] }}">
    <div class="row">
        <div class="form-group">
            <label class="col-form-label">{{ __('Project') }}</label>
            <input type="text" class="form-control" value="{{ $parseArray['project_name'] }}" disabled="disabled">
        </div>
        <div class="form-group">
            <label class="col-form-label">{{ __('Milestone') }}</label>
            <input type="text" class="form-control" value="{{ $parseArray['milestone_name'] }}" disabled>
        </div>
        <div class="form-group">
            <label class="col-form-label">{{ __('Task') }}</label>
            <input type="text" class="form-control" value={{ __($parseArray['task_name']) }} disabled="disabled">
        </div>
        <div class="form-group">
            <label class="col-form-label">{{ __('Date') }}</label>
            <input type="date" class="form-control form-control-light date" value="{{ $timesheet->date }}"
                placeholder="{{ __('Date') }}" disabled>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <label for="time" class="col-form-label">{{ __('Time') }}</label>
        </div>
        <div class="col-md-6">
            <select class="form-control select2" name="time_hour" id="time_hour" required="">
                <option value="">{{ __('Hours') }}</option>
                <?php for ($i = 0; $i < 21; $i++) { $i = $i < 10 ? '0' . $i : $i; ?>
                <option value="{{ $i }}" {{ $parseArray['time_hour'] == $i ? 'selected="selected"' : '' }}>
                    {{ $i }}</option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-6">
            <select class="form-control select2" name="time_minute" id="time_minute" required>
                <option value="">{{ __('Minutes') }}</option>
                <?php for ($i = 0; $i < 60; $i += 5) { $i = $i < 10 ? '0' . $i : $i; ?>
                <option value="{{ $i }}"
                    {{ $parseArray['time_minute'] == $i ? 'selected="selected"' : '' }}>{{ $i }}</option>
                <?php } ?>
            </select>
        </div>
        @php($id = str_replace('.', '', uniqid('', true)))
    </div>
    <div class="display-total-time"
        style="background-color: {{ $dayColor }}; padding: 10px; border-radius: 5px; color: #000; font-weight: bold;">
        <i class="fas fa-clock"></i>
        <span>
            {{ __('Total Time') }} :
            {{ $parseArray['totaltaskhour'] . ' ' . __('Hours') . ' ' . $parseArray['totaltaskminute'] . ' ' . __('Minutes') }}
        </span>
    </div>
</div>

<div>
    <div class="row">
        <div class="text-end">
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
