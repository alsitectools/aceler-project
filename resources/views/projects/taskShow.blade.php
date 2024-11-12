@php
    $logo = \App\Models\Utility::get_file('avatars/');
    $logo_tasks = \App\Models\Utility::get_file('tasks/');
@endphp
<div class="modal-body">
    @if ($taskDetail['workspace'] && $taskDetail['task'])
        <div class="p-2">
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="form-control-label">{{ __('Task type') }}:</div>
                    <p class="text-muted mb-4">
                        {{ $taskDetail['task_name'] }}
                    </p>
                </div>
                <div class="col-md-4">
                    <div class="form-control-label">{{ __('Milestone') }}:</div>
                    <p class="mt-1">
                        {{ $taskDetail['milestone'] }}
                    </p>
                </div>
                <div class="col-md-4">
                    <div class="form-control-label">{{ __('Project') }}:</div>
                    <p class="mt-1">
                        {{ $taskDetail['project']->name }}
                    </p>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="form-control-label">{{ __('Estimated Date') }}:</div>
                    <p class="mt-1">{{ \App\Models\Utility::dateFormat($taskDetail['task']->start_date) }}</p>
                </div>
                <div class="col-md-4">
                    <div class="form-control-label">{{ __('Due Date') }}:</div>
                    <p class="mt-1">{{ \App\Models\Utility::dateFormat($taskDetail['task']->estiated_date) }}</p>
                </div>
                <div class="col-md-4">
                    <div class="form-control-label">{{ __('Assigned') }}:</div>
                    <img style="width: 35px; height: 35px;"
                        @if ($taskDetail['assign_to']->avatar) src="{{ asset($logo . $taskDetail['assign_to']) }}" 
                        @else avatar="{{ $taskDetail['assign_to']->name }}" @endif
                        class="rounded-circle mt-1">
                </div>
            </div>
        </div>
        <div class="d-flex">
            <div class="bordar_styless m-2">
                <p class="me-2">Total horas</p>{{ $taskDetail['total_time_this_week'] }}
            </div>
            <div class="bordar_styless m-2">
                <p class="me-2">Periodo</p>
                {{ ucfirst($taskDetail['start_of_week']) }} a
                {{ ucfirst($taskDetail['end_of_week']) }}
            </div>
        </div>
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
</div>

<script>
    (function() {
        const d_week = new Datepicker(document.querySelector('.datepicker2'), {
            buttonClass: 'btn',
            todayBtn: true,
            clearBtn: true,
            format: 'yyyy-mm-dd',
        });
    })();
</script>
