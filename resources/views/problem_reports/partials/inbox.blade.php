{{--
    Informes de errores («Reportar un problema») dentro del perfil de soporte (users.is_support).
    Pestañas por estado (como Encargos), buscador, detalle desplegable con el contexto que se
    guardó solo, y cambio de estado + respuesta (quien lo envió lo ve en «Mis reportes»).
    Parámetros: $reports (con user, workspace y handler cargados).
--}}
@php
    $rpiStatusLabels = \App\Models\ProblemReport::statusLabels();
    $rpiCounts = $reports->countBy('status');
    $rpiDots = ['received' => '#60a5fa', 'in_review' => '#fbbf24', 'done' => '#22c55e', 'discarded' => '#9ca3af'];
@endphp
<style>
    .rpi { font-size: 13.5px; color: #1f2937; }
    .rpi-bar { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; border-bottom: 1px solid #f0f1f4; }
    .rpi-tabs { display: flex; gap: 20px; overflow-x: auto; overflow-y: hidden; scrollbar-width: none; }
    .rpi-tab { display: inline-flex; align-items: center; gap: 6px; margin-bottom: -1px; padding: 10px 0; border: 0; border-bottom: 2px solid transparent; background: none; color: #6b7280; font-size: 13px; white-space: nowrap; cursor: pointer; }
    .rpi-tab i { width: 8px; height: 8px; border-radius: 50%; }
    .rpi-tab b { font-weight: 500; color: #9ca3af; }
    .rpi-tab.is-active { border-bottom-color: #AA182C; color: #AA182C; font-weight: 600; }
    .rpi-tab.is-active b { color: #AA182C; }
    .rpi-search { display: flex; align-items: center; gap: 8px; margin: 0 0 6px auto; width: 240px; max-width: 100%; height: 32px; padding: 0 10px; border: 1px solid #e5e7eb; border-radius: 8px; color: #9ca3af; }
    .rpi-search input { flex: 1; min-width: 0; border: 0; outline: 0; font-size: 13px; color: #1f2937; background: transparent; }
    .rpi-table { width: 100%; border-collapse: collapse; }
    .rpi-table th { padding: 10px 8px; border-bottom: 1px solid #f0f1f4; color: #9ca3af; font-size: 10.5px; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; text-align: left; white-space: nowrap; }
    .rpi-table td { padding: 11px 8px; border-bottom: 1px solid #f4f5f7; vertical-align: top; }
    .rpi-row { cursor: pointer; }
    .rpi-row:hover td { background: #fafafb; }
    .rpi-row.is-open td { background: #fdf7f8; }
    .rpi-row.is-new td:first-child { box-shadow: inset 3px 0 0 #AA182C; }
    .rpi-desc small { display: block; margin-top: 3px; color: #9ca3af; font-size: 12px; }
    .rpi-st { display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
    .rpi-st i { width: 8px; height: 8px; border-radius: 50%; background: #9ca3af; }
    .rpi-st--received i { background: #60a5fa; }
    .rpi-st--in_review i { background: #fbbf24; }
    .rpi-st--done i { background: #22c55e; }
    .rpi-st--discarded i { background: #9ca3af; }
    .rpi-detail[hidden] { display: none; }
    .rpi-detail td { padding: 0 8px 14px; background: #fdf7f8; }
    .rpi-grid { display: grid; grid-template-columns: minmax(0, 1.4fr) minmax(0, 1fr); gap: 18px; padding: 14px 16px; border: 1px solid #f3dfe3; border-radius: 12px; background: #fff; }
    .rpi-grid h6 { margin: 0 0 6px; font-size: 11px; font-weight: 700; color: #9ca3af; letter-spacing: .04em; text-transform: uppercase; }
    .rpi-text { margin-bottom: 12px; white-space: pre-wrap; word-break: break-word; }
    .rpi-meta { margin: 0 0 12px; padding: 0; list-style: none; font-size: 12.5px; color: #4b5563; }
    .rpi-meta li { padding: 2px 0; word-break: break-all; }
    .rpi-meta b { color: #6b7280; font-weight: 600; }
    .rpi-errors { max-height: 140px; margin: 0 0 12px; padding: 8px 10px; overflow: auto; border-radius: 8px; background: #1f2937; color: #fecaca; font-size: 11.5px; white-space: pre-wrap; }
    .rpi-shot img { max-width: 100%; max-height: 240px; border: 1px solid #e5e7eb; border-radius: 8px; }
    .rpi-form label { display: block; margin: 0 0 4px; font-size: 12px; font-weight: 600; color: #6b7280; }
    .rpi-form select, .rpi-form textarea { width: 100%; margin-bottom: 10px; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13.5px; outline: 0; }
    .rpi-form select:focus, .rpi-form textarea:focus { border-color: #AA182C; }
    .rpi-form textarea { min-height: 80px; resize: vertical; }
    .rpi-hint { margin: -4px 0 10px; font-size: 12px; color: #9ca3af; }
    .rpi-save { padding: 7px 16px; border: 0; border-radius: 8px; background: #AA182C; color: #fff; font-weight: 600; cursor: pointer; }
    .rpi-save[disabled] { opacity: .6; }
    .rpi-saved { margin-left: 8px; color: #16a34a; font-size: 12.5px; }
    .rpi-empty { padding: 26px 10px; text-align: center; color: #9ca3af; }
    @media (max-width: 1100px) { .rpi-grid { grid-template-columns: 1fr; } .rpi-hide-sm { display: none; } }
</style>

<div class="rpi" id="rpi">
    <div class="rpi-bar">
        <nav class="rpi-tabs" id="rpiTabs">
            <button type="button" class="rpi-tab" data-status="*">{{ __('All items') }} <b>{{ $reports->count() }}</b></button>
            @foreach ($rpiStatusLabels as $key => $label)
                <button type="button" class="rpi-tab" data-status="{{ $key }}"><i style="background: {{ $rpiDots[$key] }}"></i>{{ $label }} <b data-count="{{ $key }}">{{ $rpiCounts[$key] ?? 0 }}</b></button>
            @endforeach
        </nav>
        <label class="rpi-search">
            <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
            <input type="search" id="rpiSearch" placeholder="{{ __('Search user, page or text') }}" autocomplete="off">
        </label>
    </div>

    @if ($reports->isEmpty())
        <div class="rpi-empty">{{ __('No error reports yet.') }}</div>
    @else
        <table class="rpi-table">
            <thead>
                <tr>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('User') }}</th>
                    <th>{{ __('Report') }}</th>
                    <th class="rpi-hide-sm">{{ __('Workspace') }}</th>
                    <th>{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reports as $r)
                    <tr class="rpi-row {{ $r->status === 'received' ? 'is-new' : '' }}" data-id="{{ $r->id }}" data-status="{{ $r->status }}"
                        data-search="{{ mb_strtolower(($r->user->name ?? '') . ' ' . $r->page_title . ' ' . $r->description) }}"
                        tabindex="0" aria-expanded="false">
                        <td style="white-space:nowrap">{{ $r->created_at->format('d/m/Y') }}<br><small class="text-muted">{{ $r->created_at->format('H:i') }}</small></td>
                        <td>{{ $r->user->name ?? '—' }}</td>
                        <td class="rpi-desc">{{ \Illuminate\Support\Str::limit($r->description, 110) }}
                            <small><i class="fa-solid fa-location-dot"></i> {{ $r->page_title ?: '—' }}@if ($r->screenshot_path) · <i class="fa-regular fa-image"></i> {{ __('Screenshot') }}@endif</small></td>
                        <td class="rpi-hide-sm">{{ $r->workspace->name ?? '—' }}</td>
                        <td><span class="rpi-st rpi-st--{{ $r->status }}" data-status-label><i></i>{{ $rpiStatusLabels[$r->status] ?? $r->status }}</span></td>
                    </tr>
                    <tr class="rpi-detail" data-detail="{{ $r->id }}" hidden>
                        <td colspan="5">
                            <div class="rpi-grid">
                                <div>
                                    <h6>{{ __('What happened?') }}</h6>
                                    <div class="rpi-text">{{ $r->description }}</div>
                                    @if ($r->expected)
                                        <h6>{{ __('What did you expect to happen?') }}</h6>
                                        <div class="rpi-text">{{ $r->expected }}</div>
                                    @endif
                                    <h6>{{ __('Added automatically') }}</h6>
                                    <ul class="rpi-meta">
                                        <li><b>{{ __('User') }}:</b> {{ $r->user->name ?? '—' }} @if ($r->user)&lt;{{ $r->user->email }}&gt;@endif</li>
                                        <li><b>{{ __('Page') }}:</b> @if ($r->page_url)<a href="{{ $r->page_url }}" target="_blank" rel="noopener">{{ $r->page_url }}</a>@else — @endif</li>
                                        <li><b>{{ __('Screen') }}:</b> {{ $r->viewport ?: '—' }}</li>
                                        <li><b>{{ __('Browser') }}:</b> {{ $r->user_agent ?: '—' }}</li>
                                    </ul>
                                    @if ($r->js_errors)
                                        <h6>{{ __('Page errors') }}</h6>
                                        <pre class="rpi-errors">{{ $r->js_errors }}</pre>
                                    @endif
                                    @if ($r->screenshot_path)
                                        <h6>{{ __('Screenshot') }}</h6>
                                        <a class="rpi-shot" href="{{ route('problem_reports.screenshot', $r->id) }}" target="_blank" rel="noopener">
                                            <img loading="lazy" src="{{ route('problem_reports.screenshot', $r->id) }}" alt="{{ __('Screenshot') }}">
                                        </a>
                                    @endif
                                </div>
                                <form class="rpi-form" data-update="{{ route('problem_reports.update', $r->id) }}">
                                    <label for="rpiStatus{{ $r->id }}">{{ __('Status') }}</label>
                                    <select id="rpiStatus{{ $r->id }}" name="status">
                                        @foreach ($rpiStatusLabels as $key => $label)
                                            <option value="{{ $key }}" @selected($r->status === $key)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <label for="rpiNote{{ $r->id }}">{{ __('Answer to the user') }}</label>
                                    <textarea id="rpiNote{{ $r->id }}" name="admin_note" maxlength="2000">{{ $r->admin_note }}</textarea>
                                    <p class="rpi-hint">{{ __('The user sees the status and this answer in «My reports».') }}</p>
                                    <button type="submit" class="rpi-save">{{ __('Save') }}</button>
                                    <span class="rpi-saved" hidden>{{ __('Saved') }}</span>
                                    @if ($r->handler)
                                        <p class="rpi-hint" style="margin-top:10px">{{ __('Last handled by') }} {{ $r->handler->name }}@if ($r->status_changed_at) · {{ $r->status_changed_at->format('d/m/Y H:i') }}@endif</p>
                                    @endif
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="rpi-empty" id="rpiNoMatch" hidden>{{ __('No reports match the filter.') }}</div>
    @endif
</div>

<script>
    (function () {
        const root = document.getElementById('rpi');
        if (!root || root.dataset.bound) return;
        root.dataset.bound = '1';
        const tabs = Array.from(root.querySelectorAll('.rpi-tab'));
        const rows = Array.from(root.querySelectorAll('.rpi-row'));
        const search = document.getElementById('rpiSearch');
        const noMatch = document.getElementById('rpiNoMatch');
        const KEY = 'aceler.reports.tab';
        // Por defecto, los nuevos (si no hay, todos)
        let current = (root.querySelector('[data-count="received"]') || {}).textContent > 0 ? 'received' : '*';
        try { current = localStorage.getItem(KEY) || current; } catch (e) {}

        const detailOf = (row) => root.querySelector('.rpi-detail[data-detail="' + row.dataset.id + '"]');

        function apply() {
            const q = (search.value || '').trim().toLowerCase();
            let shown = 0;
            rows.forEach((row) => {
                const ok = (current === '*' || row.dataset.status === current) && (!q || row.dataset.search.includes(q));
                row.hidden = !ok;
                if (!ok) { detailOf(row).hidden = true; row.classList.remove('is-open'); row.setAttribute('aria-expanded', 'false'); }
                if (ok) shown++;
            });
            tabs.forEach((t) => t.classList.toggle('is-active', t.dataset.status === current));
            if (noMatch) noMatch.hidden = shown > 0 || !rows.length;
        }

        tabs.forEach((t) => t.addEventListener('click', () => {
            current = t.dataset.status;
            try { localStorage.setItem(KEY, current); } catch (e) {}
            apply();
        }));
        search.addEventListener('input', apply);

        function toggle(row) {
            const d = detailOf(row);
            const open = d.hidden;
            d.hidden = !open;
            row.classList.toggle('is-open', open);
            row.setAttribute('aria-expanded', open ? 'true' : 'false');
        }
        rows.forEach((row) => {
            row.addEventListener('click', () => toggle(row));
            row.addEventListener('keydown', (e) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggle(row); } });
        });

        // Guardar estado y respuesta; actualiza contadores, la insignia del menú del perfil y la banderita
        root.querySelectorAll('.rpi-form').forEach((form) => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const btn = form.querySelector('.rpi-save'), ok = form.querySelector('.rpi-saved');
                btn.disabled = true; ok.hidden = true;
                fetch(form.dataset.update, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                    body: new FormData(form),
                    credentials: 'same-origin',
                }).then((r) => r.ok ? r.json() : Promise.reject())
                  .then((j) => {
                      const row = root.querySelector('.rpi-row[data-id="' + form.closest('.rpi-detail').dataset.detail + '"]');
                      const before = row.dataset.status;
                      row.dataset.status = j.status;
                      row.classList.toggle('is-new', j.status === 'received');
                      const st = row.querySelector('[data-status-label]');
                      st.className = 'rpi-st rpi-st--' + j.status;
                      st.innerHTML = '<i></i>' + j.status_label.replace(/[&<>]/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[c]));
                      if (before !== j.status) {
                          const dec = root.querySelector('[data-count="' + before + '"]'), inc = root.querySelector('[data-count="' + j.status + '"]');
                          if (dec) dec.textContent = Math.max(0, +dec.textContent - 1);
                          if (inc) inc.textContent = +inc.textContent + 1;
                      }
                      const n = +j.new_count || 0;
                      document.querySelectorAll('[data-reports-new-count]').forEach((el) => { el.textContent = n; el.hidden = !n; });
                      document.querySelectorAll('.apr-avatar-flag').forEach((el) => { el.hidden = !n; });
                      ok.hidden = false;
                  })
                  .catch(() => alert(@json(__('It could not be saved. Try again in a moment.'))))
                  .then(() => { btn.disabled = false; });
            });
        });

        apply();
    })();
</script>
