<style>
    .my-day-shell {
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .my-day-header {
        display: flex;
        /* align-items: flex-end; */
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }

    .my-day-title-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .my-day-filter-toggle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border: none;
        border-radius: 6px;
        background: #f1f5f9;
        color: #64748b;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .my-day-filter-toggle:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    .my-day-filter-toggle.is-active {
        background: #aa182c;
        color: #fff;
    }

    .my-day-filter-wrapper {
        display: grid;
        grid-template-rows: 0fr;
        transition: grid-template-rows 0.3s cubic-bezier(0.4, 0, 0.2, 1), margin-bottom 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        margin-bottom: 0;
    }

    .my-day-filter-wrapper.is-open {
        grid-template-rows: 1fr;
        margin-bottom: 16px;
    }

    .my-day-filter-inner {
        overflow: hidden;
    }

    .my-day-title-wrap h3 {
        margin: 0;
        font-size: clamp(1.1rem, 2.4vw, 1.35rem);
        font-weight: 700;
        color: #111827;
    }

    .my-day-title-wrap p {
        margin: 4px 0 0;
        color: #6b7280;
        font-size: 0.9rem;
    }

    .my-day-count {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 0.5rem 0.85rem;
        border-radius: 999px;
        background: #fef2f2;
        color: #aa182c;
        font-weight: 700;
        border: 1px solid #fecaca;
        white-space: nowrap;
    }

    .my-day-filter-row {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: center;
    }

    .my-day-filter-button {
        border: 1px solid #aa182c;
        background: #fff;
        color: #000;
        border-radius: 999px;
        padding: 10px 14px;
        text-align: left;
        font-weight: 600;
        transition: all 0.2s ease;
        text-decoration: none;
        font-size: 13px;
        line-height: 1;
        min-height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
    }

    .my-day-filter-button:hover,
    .my-day-filter-button.is-active {
        background: #aa182c;
        border-color: #aa182c;
        color: #fff;
    }

    .my-day-custom-trigger {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .my-day-filter-modal .modal-dialog {
        max-width: 560px;
        margin: 1rem auto;
    }

    .my-day-filter-modal .modal-content {
        border: none;
        border-radius: 20px;
        box-shadow: 0 24px 80px rgba(0, 0, 0, 0.1), 0 2px 6px rgba(0, 0, 0, 0.03);
    }

    .my-day-filter-modal .modal-header {
        padding: 22px 24px 14px;
        border-bottom: 1px solid #ececec;
    }

    .my-day-filter-modal .modal-body {
        padding: 18px 24px 24px;
    }

    .my-day-filter-form,
    .my-day-filter-modal-grid,
    .my-day-filter-block {
        display: grid;
        gap: 10px;
    }

    .my-day-filter-modal-grid {
        gap: 12px;
    }

    .my-day-filter-block {
        gap: 8px;
    }

    .my-day-filter-label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #8c5560;
        margin-bottom: 0;
    }

    .my-day-date-range-row {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .my-day-filter-control {
        width: 100%;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 9px 11px;
        color: #1f2937;
        background: #fff;
        min-height: 40px;
        font-size: 16px;
    }

    .my-day-filter-actions {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding-top: 6px;
        flex-wrap: wrap;
        align-items: stretch;
    }

    .my-day-filter-clear,
    .my-day-filter-submit {
        min-height: 42px;
    }

    .my-day-filter-clear {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #ead3d8;
        border-radius: 10px;
        padding: 10px 12px;
        background: #fff;
        color: #aa182c;
        text-decoration: none;
        font-weight: 700;
        font-size: 13px;
    }

    .my-day-filter-submit {
        border: 0;
        border-radius: 10px;
        padding: 10px 12px;
        background: #aa182c;
        color: #fff;
        font-weight: 700;
        font-size: 13px;
    }

    .my-day-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(min(100%, 320px), 1fr));
        gap: clamp(0.875rem, 2vw, 1.25rem);
        max-height: calc(var(--my-summary-section-max-height) - 170px);
        overflow-y: auto;
        padding: 4px;
        margin: 0px;
        scrollbar-width: thin;
        scrollbar-color: var(--my-summary-scrollbar-thumb) transparent;
    }

    .my-day-grid::-webkit-scrollbar {
        width: var(--my-summary-scrollbar-width);
    }

    .my-day-grid::-webkit-scrollbar-track {
        background: transparent;
    }



    .my-day-grid::-webkit-scrollbar-thumb {
        background-color: var(--my-summary-scrollbar-thumb);
        border-radius: var(--my-summary-scrollbar-radius);
    }

    .my-day-card {
        display: flex;
        flex-direction: column;
        gap: 14px;
        padding: clamp(1rem, 2vw, 1.25rem);
        background: linear-gradient(180deg, #ffffff 0%, #fcfcfd 100%);
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        min-width: 0;
    }

    a.my-day-card {
        color: inherit;
        text-decoration: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    a.my-day-card:hover {
        transform: translateY(-2px);
        border-color: rgba(170, 24, 44, 0.35);
        box-shadow: 0 14px 36px rgba(15, 23, 42, 0.1);
    }

    .my-day-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .my-day-card-title {
        margin: 0;
        font-size: 1rem;
        font-weight: 700;
        color: #111827;
        line-height: 1.3;
        word-break: break-word;
    }

    .my-day-card-project {
        margin: 6px 0 0;
        font-size: 0.86rem;
        color: #6b7280;
    }

    .my-day-status-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.35rem 0.7rem;
        border-radius: 999px;
        background: #fff7ed;
        color: #c2410c;
        font-size: 0.74rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        white-space: nowrap;
        border: 1px solid #fed7aa;
    }

    .my-day-meta-list {
        display: grid;
        gap: 10px;
    }

    .my-day-meta-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        min-width: 0;
    }

    .my-day-empty-icon {
        width: auto;
        height: auto;
        background: transparent;
        border-radius: 0;
        font-size: 60px;
        color: #a2a2a282;
    }

    .my-day-status-pill--todo {
        color: #475569;
        background: #f1f5f9;
        border-color: #cbd5e1;
    }

    .my-day-meta-icon {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        background: #f3f4f6;
        color: #4b5563;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .my-day-meta-label {
        display: block;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #9ca3af;
    }

    .my-day-meta-value {
        display: block;
        margin-top: 2px;
        font-size: 0.9rem;
        color: #111827;
        word-break: break-word;
    }

    .my-day-meta-value--overdue {
        color: #b91c1c;
        font-weight: 700;
    }

    .my-day-meta-value--warning {
        color: #c2aa0c;
        font-weight: 700;
    }

    .my-day-meta-value--ok {
        color: #15803d;
        font-weight: 700;
    }

    .my-day-meta-value--neutral {
        color: #6b7280;
    }

    .my-day-task-block {
        display: grid;
        gap: 10px;
        padding-top: 4px;
        border-top: 1px solid #f1f5f9;
    }

    .my-day-task-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        font-size: 0.84rem;
        font-weight: 700;
        color: #374151;
    }

    .my-day-task-count {
        color: #aa182c;
    }

    .my-day-task-list {
        display: grid;
        gap: 8px;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .my-day-task-item {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
        padding: 0.7rem 0.8rem;
        border-radius: 12px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
    }

    .my-day-task-name {
        font-weight: 600;
        color: #0f172a;
        word-break: break-word;
    }

    .my-day-task-date {
        flex-shrink: 0;
        font-size: 0.78rem;
        color: #64748b;
        white-space: nowrap;
    }

    .my-day-empty-card {
        padding: clamp(1.25rem, 3vw, 1.75rem);
        border: 1px dashed #d7dde5;
        border-radius: 14px;
        background: #fbfcfe;
        text-align: center;
        color: #475569;
    }

    .my-day-empty-card p {
        margin: 8px auto 0;
        max-width: 560px;
        color: #6b7280;
    }

    .my-day-empty-card h3 {
        color: #6b7280;
    }

    .my-day-inline-note {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.35rem 0.75rem;
        background: #f1f5f9;
        font-size: 0.8rem;
        font-weight: 500;
        color: #475569;
        border-radius: 8px;
        max-width: 100%;
        overflow-wrap: anywhere;
        border: 1px solid #e5e7eb;
    }

    @media (max-width: 768px) {
        .my-day-grid {
            grid-template-columns: 1fr;
            max-height: none;
        }

        .my-day-filter-row {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            width: 100%;
        }

        .my-day-filter-button,
        .my-day-custom-trigger {
            width: 100%;
        }

        .my-day-date-range-row {
            grid-template-columns: 1fr;
        }

        .my-day-filter-actions {
            flex-direction: column-reverse;
        }

        .my-day-filter-clear,
        .my-day-filter-submit {
            width: 100%;
        }
    }

    @media (max-width: 520px) {
        .my-day-filter-row {
            grid-template-columns: 1fr;
        }

        .my-day-filter-button {
            justify-content: flex-start;
        }
    }
</style>

<div class="my-day-shell">
    <div class="my-day-header">
        <div class="my-day-title-wrap">
            <h3>{{ __('My assigned milestones') }}</h3>
            <button type="button" class="my-day-filter-toggle is-active" id="myDayFilterToggle" aria-expanded="true"
                aria-label="{{ __('Toggle filters') }}"
                onclick="
                const w = document.getElementById('myDayFilterWrapper');
                const isOp = w.classList.toggle('is-open');
                this.classList.toggle('is-active', isOp);
                this.setAttribute('aria-expanded', isOp);
            ">
                <i class="fa-solid fa-filter"></i>
            </button>
            {{-- <p>
                @if ($myDaySelectedFilters['status'] == 1)
                    {{ __('Milestones To do and assigned to you.') }}
                @else
                    {{ __('Milestones currently in progress and assigned to you, together with your tasks inside each one.') }}
                @endif
            </p> --}}
        </div>
        <div class="my-day-count">
            <i class="fa-solid fa-file-lines"></i>
            <span>{{ trans_choice(':count milestone|:count milestones', $myDayMilestones->count(), ['count' => $myDayMilestones->count()]) }}</span>
        </div>
    </div>

    <div class="my-day-filter-wrapper is-open" id="myDayFilterWrapper">
        <div class="my-day-filter-inner">
            <div class="my-day-filter-row">
                <div class="my-summary-view-switch" role="tablist" aria-label="{{ __('Status Filter') }}"
                    data-status-mode="{{ $myDaySelectedFilters['status'] == 1 ? 'todo' : 'progress' }}">
                    <a href="{{ route('my_summary', ['my_day_status' => 1]) }}" id="myDayTodoToggle"
                        class="my-summary-view-option my-day-status-toggle {{ $myDaySelectedFilters['status'] == 1 ? 'is-active' : '' }}">
                        {{ __('To do') }}
                    </a>
                    <a href="{{ route('my_summary', ['my_day_status' => 2]) }}" id="myDayProgressToggle"
                        class="my-summary-view-option my-day-status-toggle {{ $myDaySelectedFilters['status'] == 2 ? 'is-active' : '' }}">
                        {{ __('In progress') }}
                    </a>
                </div>

                <a href="{{ route('my_summary', ['my_day_range_type' => 'preset', 'my_day_preset' => 'all']) }}"
                    class="my-day-filter-button my-day-range-button {{ $myDaySelectedFilters['range_type'] === 'preset' && $myDaySelectedFilters['preset'] === 'all' ? 'is-active' : '' }}">
                    {{ __('Anytime') }}
                </a>
                <a href="{{ route('my_summary', ['my_day_range_type' => 'preset', 'my_day_preset' => 'this_month']) }}"
                    class="my-day-filter-button my-day-range-button {{ $myDaySelectedFilters['range_type'] === 'preset' && $myDaySelectedFilters['preset'] === 'this_month' ? 'is-active' : '' }}">
                    {{ __('For this month') }}
                </a>
                <a href="{{ route('my_summary', ['my_day_range_type' => 'preset', 'my_day_preset' => 'this_week']) }}"
                    class="my-day-filter-button my-day-range-button {{ $myDaySelectedFilters['range_type'] === 'preset' && $myDaySelectedFilters['preset'] === 'this_week' ? 'is-active' : '' }}">
                    {{ __('For this week') }}
                </a>
                <a href="{{ route('my_summary', ['my_day_range_type' => 'preset', 'my_day_preset' => 'today']) }}"
                    class="my-day-filter-button my-day-range-button {{ $myDaySelectedFilters['range_type'] === 'preset' && $myDaySelectedFilters['preset'] === 'today' ? 'is-active' : '' }}">
                    {{ __('For today') }}
                </a>
                <button type="button"
                    class="my-day-filter-button my-day-range-button my-day-custom-trigger {{ $myDaySelectedFilters['range_type'] === 'custom' ? 'is-active' : '' }}"
                    data-bs-toggle="modal" data-bs-target="#myDayCustomRangeModal">
                    <i class="fa-solid fa-calendar-days"></i>
                    {{ __('Custom Range') }}
                </button>
            </div>
        </div>
    </div>

    <div class="modal fade my-day-filter-modal" id="myDayCustomRangeModal" tabindex="-1"
        aria-labelledby="myDayCustomRangeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myDayCustomRangeModalLabel">{{ __('Custom Range') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{ __('Close') }}"></button>
                </div>
                <div class="modal-body">
                    <form method="GET" action="{{ route('my_summary') }}" class="my-day-filter-form">
                        <input type="hidden" name="my_day_range_type" value="custom">

                        <div class="my-day-filter-modal-grid">
                            <div class="my-day-filter-block">
                                <span class="my-day-filter-label">{{ __('Date range') }}</span>
                                <div class="my-day-date-range-row">
                                    <div>
                                        <span class="my-day-filter-label">{{ __('From') }}</span>
                                        <input type="date" name="my_day_start_date"
                                            value="{{ $myDaySelectedFilters['start_date'] }}"
                                            class="my-day-filter-control" required>
                                    </div>
                                    <div>
                                        <span class="my-day-filter-label">{{ __('To') }}</span>
                                        <input type="date" name="my_day_end_date"
                                            value="{{ $myDaySelectedFilters['end_date'] }}"
                                            class="my-day-filter-control" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="my-day-filter-actions">
                            {{-- <a href="{{ route('my_summary', ['my_day_range_type' => 'preset', 'my_day_preset' => 'this_week']) }}"
                                class="my-day-filter-clear">{{ __('Reset to this week') }}</a> --}}
                            <button type="submit" class="my-day-filter-submit">{{ __('Apply') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if ($myDayMilestones->isEmpty())
        <div class="my-day-empty-card">
            <div class="my-day-empty-icon mx-auto mb-3">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <h3>{{ __('No active milestones assigned to you') }}</h3>
            <p>{{ __('There are no milestones matching the selected period') }}</p>
        </div>
    @else
        <div class="my-day-grid">
            @foreach ($myDayMilestones as $milestone)
                @php
                    $isExternalWorkspace =
                        !$currentWorkspace ||
                        (int) optional(optional($milestone->project)->workspaceData)->id !==
                            (int) $currentWorkspace->id;
                    $dueDateClass = 'my-day-meta-value--neutral';

                    if (!empty($milestone->planned_end_date) && $milestone->planned_end_date !== '0000-00-00') {
                        $dueDateCarbon = \Carbon\Carbon::parse($milestone->planned_end_date)->startOfDay();
                        $today = \Carbon\Carbon::today();
                        $daysUntilDue = $today->diffInDays($dueDateCarbon, false);

                        if ($daysUntilDue < 0) {
                            $dueDateClass = 'my-day-meta-value--overdue';
                        } elseif ($daysUntilDue <= 7) {
                            $dueDateClass = 'my-day-meta-value--warning';
                        } else {
                            $dueDateClass = 'my-day-meta-value--ok';
                        }

                        $dueDate = $dueDateCarbon->format('d-m-Y');
                    } else {
                        $dueDate = __('No due date');
                    }
                @endphp

                @if ($milestone->board_url)
                    <a href="{{ $milestone->board_url }}" class="my-day-card">
                    @else
                        <div class="my-day-card">
                @endif
                <div class="my-day-card-top">
                    <div>
                        <h4 class="my-day-card-title">{{ $milestone->title }}</h4>
                        @if ($milestone->project_name)
                            <p class="my-day-card-project">{{ $milestone->project_name }}</p>
                        @endif
                    </div>
                    @if ($myDaySelectedFilters['status'] == 1)
                        <span class="my-day-status-pill my-day-status-pill--todo">{{ __('To do') }}</span>
                    @else
                        <span class="my-day-status-pill">{{ __('In progress') }}</span>
                    @endif
                </div>

                <div class="my-day-meta-list">
                    <div class="my-day-meta-item">
                        <span class="my-day-meta-icon">
                            <i class="fa-solid fa-user-tie"></i>
                        </span>
                        <div>
                            <span class="my-day-meta-label">{{ __('Created by') }}</span>
                            <span
                                class="my-day-meta-value">{{ $milestone->requested_by_name ?: __('Unassigned') }}</span>
                        </div>
                    </div>

                    <div class="my-day-meta-item">
                        <span class="my-day-meta-icon">
                            <i class="fa-regular fa-calendar"></i>
                        </span>
                        <div>
                            <span class="my-day-meta-label">{{ __('Expected delivery') }}</span>
                            <span class="my-day-meta-value {{ $dueDateClass }}">{{ $dueDate }}</span>
                        </div>
                    </div>

                    @if ($isExternalWorkspace && $milestone->workspace_name)
                        <div class="my-day-meta-item">
                            <span class="my-day-meta-icon">
                                <i class="fa-solid fa-building"></i>
                            </span>
                            <div>
                                <span class="my-day-meta-label">{{ __('Workspace') }}</span>
                                <span class="my-day-meta-value">{{ $milestone->workspace_display_name ?? $milestone->workspace_name }}</span>
                            </div>
                        </div>
                    @endif
                </div>

                @if ($myDaySelectedFilters['status'] != 1)
                    <div class="my-day-task-block">
                        <div class="my-day-task-heading">
                            <span>{{ __('My tasks in this milestone') }}</span>
                            <span class="my-day-task-count">{{ $milestone->tasks->count() }}</span>
                        </div>

                        @if ($milestone->tasks->isEmpty())
                            <div class="my-day-inline-note">
                                {{ __('This milestone has no tasks assigned to you yet.') }}
                            </div>
                        @else
                            <ul class="my-day-task-list">
                                @foreach ($milestone->tasks as $task)
                                    @php
                                        $taskName = $task->customTask?->name;

                                        if (!$taskName) {
                                            $taskName = $task->type?->name ? __($task->type->name) : __('Task');
                                        }

                                        $taskDueDate =
                                            !empty($task->estimated_date) && $task->estimated_date !== '0000-00-00'
                                                ? \Carbon\Carbon::parse($task->estimated_date)->format('d-m-Y')
                                                : __('No date');
                                    @endphp
                                    <li class="my-day-task-item">
                                        <span class="my-day-task-name">{{ $taskName }}</span>
                                        <span class="my-day-task-date">{{ $taskDueDate }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endif
                @if ($milestone->board_url)
                    </a>
                @else
        </div>
    @endif
    @endforeach
</div>
@endif
</div>
