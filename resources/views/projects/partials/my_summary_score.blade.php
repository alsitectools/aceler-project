{{--
    Franja superior de «Mi resumen»: puntuación y horas imputadas del periodo elegido en «Horas imputadas por proyecto»
    (la puntuación se calcula con ese mismo periodo, por eso el rótulo dice cuál es). Se recarga por AJAX con ese filtro.
--}}
@php
    $periodLabel = $dateRange['appliedLabel'] ?? __('Last month');
    $periodRange = isset($dateRange['startDate'], $dateRange['endDate'])
        ? $dateRange['startDate']->format('d/m/Y') . ' – ' . $dateRange['endDate']->format('d/m/Y')
        : '';
    $projectsCount = isset($projectSummaries) ? $projectSummaries->count() : null;
@endphp
<style>
    .my-summary-strip {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .my-summary-kpi {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 18px;
        border: 1px solid #eceef2;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
    }

    .my-summary-kpi-icon {
        width: 42px;
        height: 42px;
        flex: none;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
    }

    .my-summary-kpi-icon--score { background: #fbeaec; color: #aa182c; }
    .my-summary-kpi-icon--hours { background: #eef2ff; color: #4338ca; }

    .my-summary-kpi-label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: #94a3b8;
    }

    .my-summary-kpi-value {
        font-size: 26px;
        font-weight: 800;
        line-height: 1.15;
        color: #1e293b;
    }

    .my-summary-kpi-sub {
        margin-left: 8px;
        font-size: 12.5px;
        font-weight: 500;
        color: #94a3b8;
    }

    @media (max-width: 640px) {
        .my-summary-strip {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="my-summary-strip" title="{{ $periodRange }}">
    <div class="my-summary-kpi">
        <span class="my-summary-kpi-icon my-summary-kpi-icon--score"><i class="fa-solid fa-star"></i></span>
        <div>
            <span class="my-summary-kpi-label">{{ __('Score') }} · {{ $periodLabel }}</span>
            <div class="my-summary-kpi-value">{{ floatval($userScore) }}</div>
        </div>
    </div>
    <div class="my-summary-kpi">
        <span class="my-summary-kpi-icon my-summary-kpi-icon--hours"><i class="fa-regular fa-clock"></i></span>
        <div>
            <span class="my-summary-kpi-label">{{ __('Imputed hours') }} · {{ $periodLabel }}</span>
            <div class="my-summary-kpi-value">
                {{ $totalImputedTime ?? '0:00' }}
                @if ($projectsCount !== null)
                    <span class="my-summary-kpi-sub">{{ trans_choice(':count project|:count projects', $projectsCount, ['count' => $projectsCount]) }}</span>
                @endif
            </div>
        </div>
    </div>
</div>
