{{ Form::model($timesheet, ['route' => ['project.timesheet.update', ['slug' => $currentWorkspace->slug, 'timesheet_id'
=> $timesheet->id, 'project_id' => $project_id]], 'method' => 'POST']) }}
<div class="modal-body">
    <input type="hidden" name="project_id" value="{{ $parseArray['project_id'] }}">
    <input type="hidden" name="task_id" value="{{ $parseArray['task_id'] }}">
    <input type="hidden" name="timesheet_edit" value="{{ $timesheet->id }}">
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
            <input type="date" class="form-control form-control-light date" 
                placeholder="{{ __('Date') }}" disabled>
                <input type="hidden" id="date" name="date" value="{{ $timesheet->date }}" class="form-control form-control-light date">
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
                <option value="{{ $i }}" {{ $parseArray['time_hour']==$i ? 'selected="selected"' : '' }}>
                    {{ $i }}</option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-6">
            <select class="form-control select2" name="time_minute" id="time_minute" required>
                <option value="">{{ __('Minutes') }}</option>
                <?php for ($i = 0; $i < 60; $i += 5) { $i = $i < 10 ? '0' . $i : $i; ?>
                <option value="{{ $i }}" {{ $parseArray['time_minute']==$i ? 'selected="selected"' : '' }}>{{ $i }}
                </option>
                <?php } ?>
            </select>
        </div>
        @php($id = str_replace('.', '', uniqid('', true)))
    </div>

<div class="display-total-time"
        style="background-color: {{ $dayColor }}; padding: 10px; border-radius: 5px; color: #000; font-weight: bold;">
        <i class="fas fa-clock"></i>
        <span>
            {{ __('Hours charged') }} :
            {{ $parseArray['totaltaskhour'] . ' ' . __('Hours') . ' ' . $parseArray['totaltaskminute'] . ' ' .
            __('Minutes') }}
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
    console.log("Script cargado en la vista timesheet-edit");

    // Elementos
    const totalTimeDisplay = $('.display-total-time span');
    const timeHourSelect = $('select[name="time_hour"]');
    const timeMinuteSelect = $('select[name="time_minute"]');
    const dateInput = $('input[name="date"]'); // Aunque en esta vista está disabled

    const timesheet = @json($timesheetEdit); 
    
    console.log(timesheet['time']);
    
    const totalhourToday = "{{ $parseArray['totaltaskhour'] }}";
    const totalMinutsToday = "{{ $parseArray['totaltaskminute'] }}";
    const totalTimeToday = parseInt(totalhourToday) + (parseInt(totalMinutsToday) / 60); 

    console.log("Total Time Today:", totalTimeToday);
        

    // Función para actualizar el Total Time (solo con los nuevos valores seleccionados)
    function updateTotalTime() {
        const selectedHour = parseInt(timeHourSelect.val()) || 0;
        const selectedMinute = parseInt(timeMinuteSelect.val()) || 0;
        const timetable = @json($timeTable);
        var dayOfWeek = new Date().toLocaleString('en-us', { weekday: 'long' }).toLowerCase();
        
        var expectedHour = 0;
        if (timetable && timetable[dayOfWeek]) {
            var expectedTime = timetable[dayOfWeek].split(':');
            expectedHour = parseInt(expectedTime[0], 10);
        }
        
        var workedHoursFormatted = selectedHour + totalhourToday + (selectedMinute + totalMinutsToday / 60);
        
        var dayColor = '';
        if (workedHoursFormatted === 0) {
            dayColor = '#e06c71'; // Rojo (sin horas)
        } else if (workedHoursFormatted < expectedHour) {
            dayColor = '#fcf75e'; // Amarillo (horas parciales)
        } else if (workedHoursFormatted === expectedHour) {
            dayColor = '#89e186'; // Verde (horas completas)
        } else {
            dayColor = '#b2e2f2'; // Azul (horas extras)
        }
        
        $('.display-total-time').css('background-color', dayColor);
        totalTimeDisplay.html(`
            <span>
                {{ __('Hours charged') }}: ${selectedHour.toString().padStart(2, '0')} {{ __('Hours') }} 
                ${selectedMinute.toString().padStart(2, '0')} {{ __('Minutes') }}
            </span>
        `);
    }

    // Escuchar cambios en los selects de horas y minutos
    timeHourSelect.on('change', updateTotalTime);
    timeMinuteSelect.on('change', updateTotalTime);

    // Si en algún momento se habilita el input de fecha, se puede actualizar vía AJAX
    dateInput.on('change', function() {
        const selectedDate = $(this).val();
        console.log("Fecha seleccionada:", selectedDate);

        $.ajax({
            url: '{{ route('getTotalTime') }}',
            method: 'POST',
            dataType: 'json',
            data: {
                _token: '{{ csrf_token() }}',
                project_id: "{{ $parseArray['project_id'] }}",
                task_id: "{{ $parseArray['task_id'] }}",
                selected_date: selectedDate,
                user_id: "{{ Auth::id() }}"
            },
            success: function(data) {
                console.log("Success: Response received", data);
                totalTimeDisplay.text(`Hours charged: ${data.totaltaskhour} Hours ${data.totaltaskminute.toString().padStart(2, '0')} Minutes`);
                $('.display-total-time').css('background-color', data.dayColor);
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
                console.error("Detalles del error:", xhr.responseText);
            }
        });
    });
});
</script>

{{ Form::close() }}

<form id="delete-form-{{ $id }}" action="{{ route('timesheet.destroy', [$currentWorkspace->slug, $timesheet->id]) }}"
    method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>