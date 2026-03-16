<style>
    .my-summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(min(100%, 380px), 1fr));
        gap: clamp(0.875rem, 2vw, 1.5rem);
        align-items: stretch;
        max-height: 500px;
        overflow-y: auto;
        padding: 9px;
    }

    .my-summary-grid::-webkit-scrollbar {
        width: var(--my-summary-scrollbar-width);
    }

    .my-summary-grid::-webkit-scrollbar-track {
        background: transparent;
    }

    .my-summary-grid::-webkit-scrollbar-thumb {
        background-color: var(--my-summary-scrollbar-thumb);
        border-radius: var(--my-summary-scrollbar-radius);
    }

    .my-summary-grid {
        scrollbar-width: thin;
        scrollbar-color: var(--my-summary-scrollbar-thumb) transparent;
    }

    .my-summary-empty-state {
        padding: 22px;
        text-align: center;
    }

    .my-summary-empty-state p {
        max-width: 520px;
        margin: 8px auto 0;
        color: #6b7280;
        font-size: 13px;
    }

    .my-summary-empty-card {
        grid-column: 1 / -1;
        padding: clamp(1.5rem, 3vw, 2rem);
        border: 1px dashed #d7dde5;
        border-radius: 12px;
        background: #fbfcfe;
        color: #a2a2a282;
    }

    .my-summary-content-empty-icon {
        width: auto;
        height: auto;
        background: transparent;
        border-radius: 0;
        font-size: 60px;
        color: #a2a2a282;
    }

    .my-summary-empty-state h3 {
        color: #6b7280;
    }

    .my-summary-project-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        display: flex;
        align-items: stretch;
        min-height: 125px;
        text-decoration: none;
        color: inherit;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        overflow: hidden;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        min-width: 0;
    }

    a.my-summary-project-card:hover {
        transform: translateY(-1px);
        border: 1px solid #dfacb4;
    }

    .my-summary-project-card-left {
        background: #999999fa;
        color: #ffffff;
        padding: clamp(1rem, 2vw, 1.5rem) 1rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: clamp(104px, 22vw, 140px);
        flex-shrink: 0;
    }

    .my-summary-project-card-right {
        padding: clamp(1rem, 2.5vw, 1.5rem) clamp(1rem, 3vw, 1.75rem);
        display: flex;
        flex-direction: column;
        justify-content: center;
        flex-grow: 1;
        min-width: 0;
    }

    .my-summary-hours-block {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.2rem;
        min-width: 0;
    }

    .my-summary-type-icon {
        width: clamp(36px, 6vw, 45px);
        height: clamp(36px, 6vw, 45px);
        object-fit: contain;
        margin-bottom: 0.75rem;
        border-radius: 6px;
        background: #f9fafb;
        padding: 4px;
        border: 1px solid #f3f4f6;
    }

    .my-summary-hours-value {
        font-size: clamp(1.6rem, 4vw, 2.25rem);
        font-weight: 700;
        line-height: 1;
        margin-bottom: 0;
        color: #ffffff;
        letter-spacing: -0.02em;
    }

    .my-summary-hours-label {
        font-size: clamp(0.65rem, 1.8vw, 0.75rem);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #ffffffed;
        text-align: center;
        margin-bottom: 0;
    }

    .my-summary-project-name {
        font-size: clamp(1rem, 2.6vw, 1.35rem);
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.85rem;
        line-height: 1.2;
        max-height: 2.4em;
        word-break: break-word;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .my-summary-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .my-summary-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.35rem 0.75rem;
        background: #f1f5f9;
        font-size: clamp(0.74rem, 1.8vw, 0.8rem);
        font-weight: 500;
        color: #475569;
        border-radius: 8px;
        max-width: 100%;
        overflow-wrap: anywhere;
        border: 1px solid #e5e7eb;
    }

    @media (max-width: 768px) {
        .my-summary-grid {
            grid-template-columns: 1fr;
            max-height: none;
        }

        .my-summary-project-card {
            min-height: 0;
        }

        .my-summary-empty-card {
            grid-column: auto;
        }
    }

    @media (max-width: 520px) {
        .my-summary-type-icon {
            margin-bottom: 0;
            flex-shrink: 0;
        }

        .my-summary-hours-label {
            text-align: left;
        }

        .my-summary-project-card-right {
            padding-top: 0.875rem;
        }

        .my-summary-meta {
            gap: 0.4rem;
        }

        .my-summary-project-card {
            flex-direction: column;
        }

        .my-summary-project-card-left {
            width: 100%;
            flex-direction: row;
            justify-content: flex-start;
            gap: 12px;
            text-align: left;
        }

        .my-summary-hours-block {
            align-items: flex-start;
        }
    }
</style>

<div class="my-summary-grid">
    @if ($projectSummaries->isEmpty())
        <div class="my-summary-empty-state my-summary-empty-card">
            <div class="my-summary-content-empty-icon mx-auto mb-3">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <h3>{{ __('No imputed hours found') }}</h3>
            <p>{{ __('There are no time entries for the selected range.') }}
            </p>
        </div>
    @else
        @foreach ($projectSummaries as $summary)
            @php
                $isExternalWorkspace =
                    !$currentWorkspace || (int) $summary->workspace_id !== (int) $currentWorkspace->id;
            @endphp

            @if ($summary->project_url)
                <a href="{{ $summary->project_url }}" class="my-summary-project-card">
                    <div class="my-summary-project-card-left">
                        @if ($summary->project_type_name)
                            <img class="my-summary-type-icon"
                                src="{{ asset('assets/img/' . $summary->project_type_name . '.png') }}"
                                alt="{{ $summary->project_type_name }}">
                        @endif
                        <div class="my-summary-hours-block">
                            <div class="my-summary-hours-value">{{ $summary->formatted_total_time }}</div>
                            <div class="my-summary-hours-label">{{ __('Total hours') }}</div>
                        </div>
                    </div>

                    <div class="my-summary-project-card-right">
                        <div class="my-summary-project-name" title="{{ $summary->name }}">
                            {{ $summary->name }}
                        </div>

                        <div class="my-summary-meta">
                            @if ($summary->project_type_name)
                                <span class="my-summary-badge">
                                    {{ __($summary->project_type_name) }}
                                </span>
                            @endif

                            @if ($isExternalWorkspace && $summary->workspace_name)
                                <span class="my-summary-badge">
                                    {{ $summary->workspace_name }}
                                </span>
                            @endif
                        </div>
                    </div>
                </a>
            @else
                <div class="my-summary-project-card">
                    <div class="my-summary-project-card-left">
                        @if ($summary->project_type_name)
                            <img class="my-summary-type-icon"
                                src="{{ asset('assets/img/' . $summary->project_type_name . '.png') }}"
                                alt="{{ $summary->project_type_name }}">
                        @endif
                        <div class="my-summary-hours-block">
                            <div class="my-summary-hours-value">{{ $summary->formatted_total_time }}</div>
                            <div class="my-summary-hours-label">{{ __('Total hours') }}</div>
                        </div>
                    </div>

                    <div class="my-summary-project-card-right">
                        <div class="my-summary-project-name" title="{{ $summary->name }}">
                            {{ $summary->name }}
                        </div>

                        <div class="my-summary-meta">
                            @if ($summary->project_type_name)
                                <span class="my-summary-badge">
                                    {{ __($summary->project_type_name) }}
                                </span>
                            @endif

                            @if ($isExternalWorkspace && $summary->workspace_name)
                                <span class="my-summary-badge">
                                    {{ $summary->workspace_name }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @endif
</div>
