<div class="card milestone-card-frame milestone-status-{{ $status->id }}">
    <div class="milestone-flip">
        <div class="milestone-flip-inner {{ (int) $status->id === 3 ? 'is-flipped' : '' }}">
            {{-- ============================ --}}
            {{--  CARA FRONTAL (Por hacer / En curso / Hecho) --}}
            {{-- ============================ --}}
            <div class="milestone-flip-face milestone-flip-face-front">
                @php
                    $initialVariant = (int) $status->id === 1
                        ? 'todo'
                        : ((int) $status->id === 4 ? 'hecho' : 'curso');
                @endphp
                <div class="card-header text-center milestone-face-header milestone-face-header-todo {{ $initialVariant === 'todo' ? '' : 'd-none' }}">
                    <div class="milestone-status-header">
                        <h4 class="mb-0">
                            <i class="ti ti-clipboard-list" style="color: #aa182c;"></i>
                            {{ __('To Do') }}
                        </h4>
                    </div>
                </div>
                <div class="card-header text-center milestone-face-header milestone-face-header-curso {{ $initialVariant === 'curso' ? '' : 'd-none' }}">
                    <div class="milestone-status-header">
                        @if (in_array((int) $status->id, [2, 3], true))
                        <button type="button" class="milestone-status-btn invisible" tabindex="-1" aria-hidden="true">
                            <i class="ti ti-arrow-right"></i>{{ __('Review') }}
                        </button>
                        @endif
                        <h4 class="mb-0">
                            <i class="ti ti-activity" style="color: #aa182c;"></i>
                            {{ __('In Progress') }}
                        </h4>
                        @if (in_array((int) $status->id, [2, 3], true))
                        <button type="button" class="milestone-status-btn milestone-status-btn-right" data-flip-to="revision"
                            title="{{ __('Pasar a revisión') }}" aria-label="{{ __('Pasar a revisión') }}">
                            <i class="ti ti-arrow-right"></i>{{ __('Review') }}
                        </button>
                        @endif
                    </div>
                </div>
                <div class="card-header text-center milestone-face-header milestone-face-header-hecho {{ $initialVariant === 'hecho' ? '' : 'd-none' }}">
                    <div class="milestone-status-header">
                        <h4 class="mb-0">
                            <i class="ti ti-circle-check" style="color: #aa182c;"></i>
                            {{ __('Done') }}
                        </h4>
                    </div>
                </div>
                <div class="card-body">
                    @include('projects.partials.milestone_card', [
                        'milestone' => $milestone,
                        'status' => $status,
                        'currentWorkspace' => $currentWorkspace,
                        'project_id' => $project_id,
                        'extraClass' => $extraClass ?? 'milestone-card-modal',
                        'inlineStyle' => $inlineStyle ?? '',
                        'ownerShip' => $ownerShip ?? 'yes',
                    ])
                </div>
            </div>

            {{-- ============================ --}}
            {{--  CARA TRASERA (Revisión)     --}}
            {{-- ============================ --}}
            @if (in_array((int) $status->id, [2, 3], true))
            <div class="milestone-flip-face milestone-flip-face-back">
                <div class="card-header text-center">
                    <div class="milestone-status-header">
                        <button type="button" class="milestone-status-btn milestone-status-btn-left" data-flip-to="curso"
                            title="{{ __('Volver a en curso') }}" aria-label="{{ __('Volver a en curso') }}">
                            <i class="ti ti-arrow-left"></i>{{ __('In Progress') }}
                        </button>
                        <h4 class="mb-0">
                            <i class="ti ti-eye" style="color: #aa182c;"></i>
                            {{ __('Review') }}
                        </h4>
                        <button type="button" class="milestone-status-btn milestone-status-btn-right" data-flip-to="hecho"
                            title="{{ __('Pasar a hecho') }}" aria-label="{{ __('Pasar a hecho') }}">
                            <i class="ti ti-arrow-right"></i>{{ __('Done') }}
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @include('projects.partials.milestone_card', [
                        'milestone' => $milestone,
                        'status' => $status,
                        'currentWorkspace' => $currentWorkspace,
                        'project_id' => $project_id,
                        'extraClass' => $extraClass ?? 'milestone-card-modal',
                        'inlineStyle' => $inlineStyle ?? '',
                        'ownerShip' => $ownerShip ?? 'yes',
                    ])
                </div>
            </div>
            @endif
        </div>
    </div>
</div>