{{ Form::open([
    'url' => route('project.timesheet.store', [
        'slug' => $currentWorkspace->slug,
        'project_id' => $parseArray['project_id'],
    ]),
    'id' => 'project_form',
]) }}
<div class="modal-body">
    <input type="hidden" name="project_id" value="{{ $parseArray['project_id'] }}">
    <input type="hidden" name="task_id" value="{{ $parseArray['task_id'] }}">
    <input type="hidden" name="milestone_id" value="{{ $parseArray['milestone_id'] }}">
    <input type="hidden" name="date" value="{{ $parseArray['date'] }}" @disabled(!$fromTimesheet)>
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
            <input type="date" onclick="this.showPicker()" class="form-control form-control-light date"
                value="{{ $parseArray['date'] }}" placeholder="{{ __('Date') }}" name="date"
                max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" 
                @disabled($fromTimesheet)>
            <small id="holiday-date-alert" class="text-danger d-none mt-1 d-block">
                {{ __('You cannot log hours on a holiday.') }}
            </small>
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
                    <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">
                        {{ $i == 0 ? '0' . $i : $i }}</option>
                @endfor
            </select>
        </div>

        <div class="col-md-6">
            <select class="form-control select2" name="time_minute" id="time_minute" required>
                <option value="">{{ __('Minutes') }}</option>
                @for ($i = 0; $i < 60; $i += 5)
                    <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">
                        {{ $i == 0 ? '0' . $i : $i }}</option>
                @endfor
            </select>
        </div>
    </div>

    <div class="display-total-time"
        style="background-color: {{ $dayColor }}; padding: 10px; border-radius: 5px; color: #000; font-weight: bold;">
        <i class="fas fa-clock"></i>
        <span>
            {{ __('Hours charged') }} :
            {{ (int) $parseArray['totaltaskhour'] }} {{ __('Hours') }}
            {{ str_pad($parseArray['totaltaskminute'], 2, '0', STR_PAD_LEFT) }} {{ __('Minutes') }}
        </span>
    </div>

</div>
<div class="modal-footer">
    <button type="button" class="btn btn-dark" style="position: absolute; left:18px;" id="delete-task-btn">
        {{ __('Delete task') }}
    </button>
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
    <input type="submit" value="{{ __('Save Changes') }}" class="btn btn-primary" id="timesheet-save-btn">
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Elementos
        const timetable = @json($timeTable);
        const totalTimeDisplay = $('.display-total-time span');
        const timeHourSelect = $('select[name="time_hour"]');
        const timeMinuteSelect = $('select[name="time_minute"]');
        const dateInput = $('input[name="date"][type="date"]').first();
        const saveButton = $('#timesheet-save-btn');
        const holidayAlert = $('#holiday-date-alert');
        const holidayCheckUrl = "{{ route('timesheet.check.holiday', $currentWorkspace->slug) }}";
        let isCurrentDateHoliday = false;
        let lastValidatedDate = null;

        function applyHolidayUiState(isHoliday) {
            isCurrentDateHoliday = !!isHoliday;
            holidayAlert.toggleClass('d-none', !isCurrentDateHoliday);
            saveButton.prop('disabled', isCurrentDateHoliday);
        }

        function validateHolidayDate() {
            const selectedDate = (dateInput.val() || '').trim();

            if (!selectedDate) {
                lastValidatedDate = null;
                applyHolidayUiState(false);
                return $.Deferred().resolve(true).promise();
            }

            return $.ajax({
                url: holidayCheckUrl,
                method: 'POST',
                dataType: 'json',
                data: {
                    _token: '{{ csrf_token() }}',
                    date: selectedDate
                }
            }).then(function(response) {
                lastValidatedDate = selectedDate;
                applyHolidayUiState(!!response?.is_holiday);
                return !isCurrentDateHoliday;
            }).catch(function() {
                lastValidatedDate = null;
                applyHolidayUiState(false);
                return true;
            });
        }

        // Función para actualizar el Total Time (solo con los nuevos valores seleccionados)
        function updateTotalTime() {
            const selectedHour = parseInt(timeHourSelect.val()) || 0;
            const selectedMinute = parseInt(timeMinuteSelect.val()) || 0;

            const timetable = @json($timeTable);
            var dayOfWeek = new Date().toLocaleString('en-us', {
                weekday: 'long'
            }).toLowerCase();

            var expectedHour = 0;
            if (timetable && timetable[dayOfWeek]) {
                var expectedTime = timetable[dayOfWeek].split(':');
                expectedHour = parseInt(expectedTime[0], 10);
            }

            var workedHoursFormatted = selectedHour + timeHourSelect + (selectedMinute + timeMinuteSelect / 60);

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
                {{ __('Hours charged') }}: ${selectedHour.toString().padStart(1, '0')} {{ __('Hours') }} 
                ${selectedMinute.toString().padStart(2, '0')} {{ __('Minutes') }}
            </span>
        `);
        }

        timeHourSelect.on('change', updateTotalTime);
        timeMinuteSelect.on('change', updateTotalTime);

        // Si en algún momento se habilita el input de fecha, se puede actualizar vía AJAX
        dateInput.on('change', function() {
            const selectedDate = $(this).val();
            console.log("Fecha seleccionada:", selectedDate);

            validateHolidayDate().then(function(canLogHours) {
                if (!canLogHours) {
                    return;
                }

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
                        totalTimeDisplay.text(
                            `Hours charged: ${data.totaltaskhour} Hours ${data.totaltaskminute.toString().padStart(2, '0')} Minutes`
                        );
                        $('.display-total-time').css('background-color', data.dayColor);
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", error);
                        console.error("Detalles del error:", xhr.responseText);
                    }
                });
            });
        });

        $('#project_form').on('submit', function(event) {
            const selectedDate = (dateInput.val() || '').trim();

            if (selectedDate && lastValidatedDate === selectedDate) {
                if (isCurrentDateHoliday) {
                    event.preventDefault();
                }
                return;
            }

            event.preventDefault();
            const form = this;

            validateHolidayDate().then(function(canLogHours) {
                if (!canLogHours) {
                    return;
                }

                $('#project_form').off('submit');
                form.submit();
            });
        });

        validateHolidayDate();
    });
</script>
<script>
    $('#delete-task-btn').on('click', function() {
        // if (!confirm('Are you sure you want to delete this task?')) return;

        $.ajax({
            url: '{{ route('client.tasks.destroy', ['slug' => $currentWorkspace->slug, 'id' => $parseArray['project_id'], 'tid' => $parseArray['task_id']]) }}',
            method: 'POST',
            data: {
                _method: 'DELETE',
                _token: '{{ csrf_token() }}',
            },
            success: function(response) {

                location.reload();
            },
            error: function(xhr) {
                alert('Error deleting task');
                console.error(xhr.responseText);
            }
        });
    });
</script>

{{ Form::close() }}
