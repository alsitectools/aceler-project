<style>
    .modal-dialog {
        max-width: 50%;
        margin: 1.75rem auto;
    }
</style>
{{ Form::open(['url' => route('project.timesheet.store', ['slug' => $currentWorkspace->slug, 'project_id' => $parseArray['project_id']]), 'id' => 'project_form']) }}
<div class="modal-body">
    <input type="hidden" name="project_id" value="{{ $parseArray['project_id'] }}">
    <input type="hidden" name="task_id" value="{{ $parseArray['task_id'] }}">
    <input type="hidden" name="milestone_id" value="{{ $parseArray['milestone_id'] }}">
    <input type="hidden" name="date" value="{{ $parseArray['date'] }}">
    <input type="hidden" id="totaltasktime"
        value="{{ $parseArray['totaltaskhour'] . ':' . $parseArray['totaltaskminute'] }}">

    <div class="row">
        <div class="form-group">
            <label class="col-form-label">{{ __('Project') }}</label>
            <input type="text" class="form-control" value="{{ $parseArray['project_name'] }}" disabled>
        </div>
        <div class="form-group">
            <label class="col-form-label">{{ __('Milestone') }}</label>
            <input type="text" class="form-control" value="{{ $parseArray['milestone_name'] }}" disabled>
        </div>
        <div class="form-group">
            <label class="col-form-label">{{ __('Task') }}</label>
            <input type="text" class="form-control" value={{ __($parseArray['task_name']) }} disabled>
        </div>
        <div class="form-group">
            <label class="col-form-label">{{ __('Date') }}</label>
            <input type="date" class="form-control form-control-light date" value="{{ $parseArray['date'] }}"
                placeholder="{{ __('Date') }}" disabled>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <label for="time" class="col-form-label">{{ __('Time') }}</label>
        </div>
        <div class="col-md-6">
            <select class="form-control select2" name="time_hour" id="time_hour" required>
                <option value="">{{ __('Hours') }}</option>
                @for ($i = 0; $i <= 20; $i++)
                    <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ $i }}</option>
                @endfor
            </select>
        </div>

        <div class="col-md-6">
            <select class="form-control select2" name="time_minute" id="time_minute" required>
                <option value="">{{ __('Minutes') }}</option>
                @for ($i = 0; $i < 60; $i += 5)
                    <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ $i }}</option>
                @endfor
            </select>
        </div>
    </div>

    <div class="display-total-time">
        <i class="fas fa-clock"></i>
        <span>{{ __('Total Time') }} :
            {{ $parseArray['totaltaskhour'] . ' ' . __('Hours') . ' ' . $parseArray['totaltaskminute'] . ' ' . __('Minutes') }}</span>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
    <input type="submit" value="{{ __('Save Changes') }}" class="btn btn-primary">
</div>
{{ Form::close() }}
