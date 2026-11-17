{{--
    Avatar de persona para el detalle del proyecto: círculo con las iniciales generado en el servidor
    y, si hay foto, la foto encima (si falla al cargar se quita y quedan las iniciales). Nunca sale roto.
    Parámetros: $user (con name y avatar), $size opcional (px, 28 por defecto).
--}}
@php
    $pdAvName = trim((string) ($user->name ?? ''));
    $pdAvParts = preg_split('/\s+/', $pdAvName) ?: [];
    $pdAvInitials = mb_strtoupper(mb_substr($pdAvParts[0] ?? '', 0, 1) . mb_substr($pdAvParts[1] ?? '', 0, 1)) ?: '?';
    $pdAvTone = $pdAvName !== '' ? crc32($pdAvName) % 5 : 0;
    $pdAvSize = (int) ($size ?? 28);
@endphp
<span class="pd-av pd-av-{{ $pdAvTone }}" title="{{ $pdAvName }}" style="--pd-av-size: {{ $pdAvSize }}px">
    <span class="pd-av-ini">{{ $pdAvInitials }}</span>
    @if (!empty($user->avatar))
        <img src="{{ asset($user->avatar) }}" alt="" loading="lazy" onerror="this.remove()">
    @endif
</span>
