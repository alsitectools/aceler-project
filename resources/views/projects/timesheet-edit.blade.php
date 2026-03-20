{{ Form::model($timesheet, [
    'route' => [
        'project.timesheet.update',
        ['slug' => $currentWorkspace->slug, 'timesheet_id' => $timesheet->id, 'project_id' => $project_id],
    ],
    'method' => 'POST',
    'id' => 'project_timesheet_edit_form',
]) }}
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
            <input type="date" class="form-control form-control-light date" placeholder="{{ __('Date') }}"
                value="{{ $timesheet->date }}" name="date" max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
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
                    {{ $parseArray['time_minute'] == $i ? 'selected="selected"' : '' }}>
                    {{ $i }}
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
        const totalTimeDisplay = $('.display-total-time span');
        const timeHourSelect = $('select[name="time_hour"]');
        const timeMinuteSelect = $('select[name="time_minute"]');
        const dateInput = $('input[name="date"][type="date"]');
        const saveButton = $('#timesheet-save-btn');
        const holidayAlert = $('#holiday-date-alert');
        const holidayCheckUrl = "{{ route('timesheet.check.holiday', $currentWorkspace->slug) }}";
        let expectedHourByDate = Number(@json($expectedHour ?? 0));
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

        function renderChargedTime(hour, minute, dayColor) {
            $('.display-total-time').css('background-color', dayColor);
            totalTimeDisplay.html(`
            <span>
                {{ __('Hours charged') }}: ${Number(hour || 0).toString().padStart(1, '0')} {{ __('Hours') }} 
                ${Number(minute || 0).toString().padStart(2, '0')} {{ __('Minutes') }}
            </span>
        `);
        }

        function updateTotalTime() {
            const selectedHour = parseInt(timeHourSelect.val()) || 0;
            const selectedMinute = parseInt(timeMinuteSelect.val()) || 0;
            const workedHoursFormatted = selectedHour + (selectedMinute / 60);

            var dayColor = '';
            if (workedHoursFormatted === 0) {
                dayColor = '#e06c71'; // Rojo (sin horas)
            } else if (workedHoursFormatted < expectedHourByDate) {
                dayColor = '#fcf75e'; // Amarillo (horas parciales)
            } else if (workedHoursFormatted === expectedHourByDate) {
                dayColor = '#89e186'; // Verde (horas completas)
            } else {
                dayColor = '#b2e2f2'; // Azul (horas extras)
            }

            renderChargedTime(selectedHour, selectedMinute, dayColor);
        }

        timeHourSelect.on('change', updateTotalTime);
        timeMinuteSelect.on('change', updateTotalTime);

        dateInput.on('change', function() {
            const selectedDate = $(this).val();

            if (!selectedDate) {
                return;
            }

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
                        expectedHourByDate = Number(data.expectedHour || 0);
                        renderChargedTime(data.totaltaskhour, data.totaltaskminute, data.dayColor);
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        console.error('Detalles del error:', xhr.responseText);
                    }
                });
            });
        });

        $('#project_timesheet_edit_form').on('submit', function(event) {
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

                $('#project_timesheet_edit_form').off('submit');
                form.submit();
            });
        });

        updateTotalTime();
        validateHolidayDate();
    });
</script>

<script>
    $('#delete-task-btn').on('click', function() {
        $.ajax({
            url: '{{ route('client.tasks.destroy', ['slug' => $currentWorkspace->slug, 'id' => $parseArray['project_id'], 'tid' => $parseArray['task_id']]) }}',
            method: 'POST',
            data: {
                _method: 'DELETE',
                _token: '{{ csrf_token() }}',
            },
            success: function() {
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

<form id="delete-form-{{ $id }}"
    action="{{ route('timesheet.destroy', [$currentWorkspace->slug, $timesheet->id]) }}" method="POST"
    style="display: none;">
    @csrf
    @method('DELETE')
</form>
