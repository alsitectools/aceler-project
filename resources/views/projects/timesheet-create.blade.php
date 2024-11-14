<style>
    .modal-dialog {
        max-width: 50%;
        /* Ajusta el ancho según tus necesidades */
        margin: 1.75rem auto;
        /* Margen superior e inferior */
    }
</style>
{{ Form::open(['url' => route('project.timesheet.store', ['slug' => $currentWorkspace->slug, 'project_id' => $parseArray['project_id']]), 'id' => 'project_form']) }}
<div class="modal-body">
    <input type="hidden" name="project_id" value="{{ $parseArray['project_id'] }}">
    <input type="hidden" name="task_id" value="{{ $parseArray['task_id'] }}">
    <input type="hidden" name="date" value="{{ $parseArray['date'] }}">
    <input type="hidden" id="totaltasktime"
        value="{{ $parseArray['totaltaskhour'] . ':' . $parseArray['totaltaskminute'] }}">

    <div class="row">
        <div class="form-group">
            <label class="col-form-label">{{ __('Project') }}</label>
            <input type="text" class="form-control" value="{{ $parseArray['project_name'] }}" disabled>
        </div>
        <div class="form-group">
            <label class="col-form-label">{{ __('Task') }}</label>
            <input type="text" class="form-control" value="{{ $parseArray['task_name'] }}" disabled>
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
                <!-- Opciones de horas de 0 a 8 -->
                @for ($i = 0; $i <= 8; $i++)
                    <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ $i }}</option>
                @endfor
            </select>
        </div>

        <div class="col-md-6">
            <select class="form-control select2" name="time_minute" id="time_minute" required>
                <option value="">{{ __('Minutes') }}</option>
                <!-- Opciones de minutos de 0 a 59 -->
                @for ($i = 0; $i < 60; $i += 1)
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

<script>
    document.getElementById('project_form').addEventListener('submit', function(event) {
        const hours = parseInt(document.getElementById('time_hour').value) || 0;
        const minutes = parseInt(document.getElementById('time_minute').value) || 0;

        // Verifica si la selección excede las 7:59 horas o es mayor a 8:00 horas
        if (hours > 8 || (hours === 8 && minutes > 0) || (hours === 7 && minutes > 59)) {
            alert('Por favor, selecciona un máximo de 7 horas y 59 minutos o 8 horas exactas.');
            event.preventDefault();
        }
    });
</script>
