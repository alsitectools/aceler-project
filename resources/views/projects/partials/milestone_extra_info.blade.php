@if (!empty($milestone['is_waiting']))
    <div class="milestone-extra-info milestone-extra-info--paused">
        <i class="far fa-pause-circle"></i>
        <span>{{ __('Encargo pausado') }}</span>
        <a href="#" class="milestone-resume-btn"
           onclick="event.preventDefault(); document.getElementById('resume-milestone-{{ $milestone['id'] }}').submit();">
            {{ __('Activar') }}
        </a>
    </div>
@elseif ($status->id == 1)
    <div class="milestone-extra-info">
        <i class="fa-solid fa-arrows-alt"></i>
        <span>{{ __('Arrastrar a en curso' ) }}<br>{{ __('para empezar el encargo') }}</span>
    </div>
@elseif ($status->id == 2)
    @php
        $statusClass2 = '';
        $statusIcon2 = 'fa-solid fa-arrow-circle-right';
        $statusLabel2 = __('Puede pasar a revisión');
        $taskList = $milestone['tasks'] ?? [];
        $hasChanges = collect($taskList)->contains(fn($t) => ($t['review_state'] ?? null) === 'changes');
        if (count($taskList) === 0) {
            $statusClass2 = 'milestone-extra-info--no-tasks';
            $statusIcon2 = 'fa-solid fa-exclamation-triangle';
            $statusLabel2 = __('No puede pasar a revisión.') . '<br>' . __('Se necesita añadir tareas');
        } elseif (!collect($taskList)->every(fn($t) => ($t['logged_hours'] ?? '00:00') !== '00:00')) {
            $statusClass2 = 'milestone-extra-info--hours';
            $statusIcon2 = 'far fa-clock';
            $statusLabel2 = __('Imputar las horas') . '<br>' . __('para pasar a revisión');
        }
    @endphp
    @if ($hasChanges)
        <div class="milestone-extra-info milestone-extra-info--changes">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span>{!! __('There are tasks pending correction') !!}<br>{{ __('before moving to review') }}</span>
        </div>
    @else
        <div class="milestone-extra-info milestone-extra-info--ready {{ $statusClass2 }}">
            <i class="{{ $statusIcon2 }}"></i>
            <span>{!! $statusLabel2 !!}</span>
        </div>
    @endif
@elseif ($status->id == 3)
    <div class="milestone-extra-info milestone-extra-info--orange">
        <i class="fa-solid fa-magnifying-glass"></i>
        <span>{{ __('Encargo en revisión') }}</span>
    </div>
@elseif ($status->id == 4)
    @php
        $checkDelayed = '';
        $checkLabel = __('Entregado en plazo');
        $finalDate = ($milestone['finalization_date'] ?? '') !== '' && ($milestone['finalization_date'] ?? '') !== '0000-00-00'
            ? $milestone['finalization_date']
            : null;
        $endDate = ($milestone['end_date'] ?? '') !== '' && ($milestone['end_date'] ?? '') !== '0000-00-00'
            ? $milestone['end_date']
            : null;
        if ($endDate && $finalDate && new DateTime($finalDate) > new DateTime($endDate)) {
            $checkDelayed = 'milestone-check-late';
            $checkLabel = __('Entregado fuera de plazo');
        } elseif ($endDate && !$finalDate && new DateTime('now') > new DateTime($endDate)) {
            $checkDelayed = 'milestone-check-late';
            $checkLabel = __('Entregado fuera de plazo');
        }
    @endphp
    <div class="milestone-extra-info milestone-extra-info--check {{ $checkDelayed }}">
        <i class="far fa-check-circle"></i>
        <span>{{ $checkLabel }}</span>
    </div>
@endif