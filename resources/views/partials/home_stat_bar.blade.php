{{--
    Barra de reparto de una tarjeta del resumen (home): un tramo por estado, con el color de su punto.
    Parámetros: $segments = [[valor, clase de color (stat-dot--x)], ...]. Sin datos no se pinta.
--}}
@php
    $hsSegments = array_filter($segments ?? [], fn ($s) => (int) ($s[0] ?? 0) > 0);
    $hsSum = array_sum(array_map(fn ($s) => (int) $s[0], $hsSegments));
@endphp
@if ($hsSum > 0)
    <div class="hs-bar" aria-hidden="true">
        @foreach ($hsSegments as [$hsValue, $hsClass])
            <i class="{{ $hsClass }}" style="width: {{ round((int) $hsValue * 100 / $hsSum, 2) }}%"></i>
        @endforeach
    </div>
@endif
