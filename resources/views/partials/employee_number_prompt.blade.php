{{--
    Modal «Falta tu número de empleado»: sale al entrar en la aplicación a quien no lo tiene
    (users.number_employee vacío). Se guarda con users.my.employee_number (solo una vez).
    «Más tarde» lo pospone SNOOZE_MINUTES (vuelve a salir solo, aunque no se cambie de página) y solo se puede usar MAX_SNOOZES veces; después
    el botón desaparece y hay que rellenarlo (se guarda por usuario en localStorage).
    Se incluye en layouts/admin.blade.php; solo se pinta para usuarios (guard web) sin número.
--}}
@php
    $enpUser = \Illuminate\Support\Facades\Auth::guard('web')->user();
    $enpShow = $enpUser && trim((string) $enpUser->number_employee) === '';
@endphp
@if ($enpShow)
    <div class="enp" id="enpModal" role="dialog" aria-modal="true" aria-labelledby="enpTitle" hidden>
        <div class="enp-backdrop"></div>
        <form class="enp-card" id="enpForm" novalidate>
            <div class="enp-icon" aria-hidden="true"><i class="ti ti-id"></i></div>
            <h2 id="enpTitle">{{ __('Add your employee number') }}</h2>
            <p class="enp-text">{{ __('We need it to send your hours to Axapta. You only have to enter it once.') }}</p>

            <label class="enp-label" for="enpInput">{{ __('Employee number') }}</label>
            <input type="text" id="enpInput" name="number_employee" class="enp-input"
                inputmode="numeric" autocomplete="off" maxlength="9" pattern="\d*"
                placeholder="{{ __('E.g. 11840') }}" required>
            <div class="enp-error" id="enpError" role="alert" hidden></div>

            <div class="enp-actions">
                <button type="button" class="enp-later" id="enpLater">{{ __('Later') }}</button>
                <button type="submit" class="enp-save" id="enpSave">{{ __('Save') }}</button>
            </div>
            <p class="enp-hint" id="enpHint"></p>
        </form>
    </div>

    <style>
        .enp { position: fixed; inset: 0; z-index: 1090; display: flex; align-items: center; justify-content: center; padding: 16px; font-size: 14px; color: #1f2937; }
        .enp[hidden] { display: none; }
        .enp-backdrop { position: absolute; inset: 0; background: rgba(15, 23, 42, .45); }
        .enp-card { position: relative; width: 100%; max-width: 400px; background: #fff; border-radius: 16px; padding: 24px 24px 18px; box-shadow: 0 24px 60px rgba(15, 23, 42, .3); text-align: left; }
        .enp-icon { width: 44px; height: 44px; border-radius: 12px; background: #fdf2f4; color: #AA182C; display: flex; align-items: center; justify-content: center; font-size: 22px; margin-bottom: 12px; }
        .enp h2 { margin: 0 0 6px; font-size: 18px; font-weight: 700; color: #111827; }
        .enp-text { margin: 0 0 16px; color: #4b5563; line-height: 1.45; }
        .enp-label { display: block; margin-bottom: 6px; font-size: 12.5px; font-weight: 600; color: #374151; }
        .enp-input { width: 100%; height: 42px; padding: 0 12px; border: 1px solid #d1d5db; border-radius: 10px; font-size: 16px; letter-spacing: .04em; color: #111827; outline: 0; }
        .enp-input:focus { border-color: #AA182C; box-shadow: 0 0 0 3px rgba(170, 24, 44, .12); }
        .enp-input.is-invalid { border-color: #dc2626; }
        .enp-error { margin-top: 6px; font-size: 12.5px; color: #b91c1c; }
        .enp-error[hidden] { display: none; }
        .enp-actions { display: flex; justify-content: flex-end; gap: 8px; margin-top: 18px; }
        .enp-later, .enp-save { height: 38px; padding: 0 16px; border-radius: 10px; font-size: 13.5px; font-weight: 600; cursor: pointer; }
        .enp-later { border: 1px solid #e5e7eb; background: #fff; color: #4b5563; }
        .enp-later:hover { background: #f8fafc; }
        .enp-save { border: 0; background: #AA182C; color: #fff; min-width: 96px; }
        .enp-save:hover { background: #8f1423; }
        .enp-save[disabled] { opacity: .6; cursor: wait; }
        .enp-hint { margin: 12px 0 0; font-size: 11.5px; color: #94a3b8; }
        .enp-hint.is-final { color: #b45309; font-weight: 600; }
        .enp-later[hidden] { display: none; }
    </style>

    <script>
        (function() {
            // Ciclos: cada «Más tarde» lo pospone SNOOZE_MINUTES; tras MAX_SNOOZES ya no se puede posponer
            const SNOOZE_MINUTES = 30;
            const MAX_SNOOZES = 3;
            const STATE_KEY = 'aceler.employeeNumber.{{ (int) $enpUser->id }}';
            const modal = document.getElementById('enpModal');
            if (!modal) return;
            const form = document.getElementById('enpForm');
            const input = document.getElementById('enpInput');
            const error = document.getElementById('enpError');
            const save = document.getElementById('enpSave');
            const later = document.getElementById('enpLater');
            const url = @json(route('users.my.employee_number'));
            const csrf = @json(csrf_token());
            const msgDigits = @json(__('The employee number must contain only digits (up to 9).'));
            const msgError = @json(__('It could not be saved. Try again.'));
            const msgSaved = @json(__('Employee number saved.'));
            const msgLeftMany = @json(__('You can postpone it :count more times. We will ask you again in :minutes minutes.'));
            const msgLeftOne = @json(__('You can postpone it one last time. We will ask you again in :minutes minutes.'));
            const msgLast = @json(__('You can no longer postpone it: enter your employee number to continue.'));
            const hint = document.getElementById('enpHint');

            // Estado por usuario: { count: veces pospuesto, until: ms hasta cuando no se muestra }
            const readState = function() {
                try {
                    const s = JSON.parse(localStorage.getItem(STATE_KEY) || '{}');
                    return { count: parseInt(s.count, 10) || 0, until: parseInt(s.until, 10) || 0 };
                } catch (e) {
                    return { count: 0, until: 0 };
                }
            };
            const writeState = function(s) {
                try { localStorage.setItem(STATE_KEY, JSON.stringify(s)); } catch (e) {}
            };

            let done = false;      // número guardado: no se vuelve a mostrar
            let timer = null;

            // Prepara el modal según el estado actual (se relee cada vez: puede haber cambiado en otra pestaña)
            const prepare = function() {
                const s = readState();
                const canSnooze = s.count < MAX_SNOOZES;
                const left = MAX_SNOOZES - s.count;
                later.hidden = !canSnooze;
                hint.textContent = !canSnooze ? msgLast
                    : (left === 1 ? msgLeftOne : msgLeftMany).replace(':count', left).replace(':minutes', SNOOZE_MINUTES);
                hint.classList.toggle('is-final', !canSnooze);
            };

            // ms que faltan para volver a mostrarlo (0 = ya toca). También la última pausa se respeta:
            // al acabar, sale ya sin «Más tarde».
            const waitMs = function() {
                return Math.max(0, readState().until - Date.now());
            };

            const showError = function(text) {
                error.textContent = text;
                error.hidden = !text;
                input.classList.toggle('is-invalid', !!text);
            };
            const close = function() {
                modal.hidden = true;
                document.removeEventListener('keydown', onKey);
            };
            // Esc equivale a «Más tarde» solo mientras se pueda posponer
            const onKey = function(e) {
                if (e.key === 'Escape' && !later.hidden) later.click();
            };

            // Solo dígitos al escribir o pegar
            input.addEventListener('input', function() {
                const clean = input.value.replace(/\D/g, '').slice(0, 9);
                if (clean !== input.value) input.value = clean;
                showError('');
            });

            later.addEventListener('click', function() {
                const s = readState();
                if (s.count >= MAX_SNOOZES) return;
                writeState({ count: s.count + 1, until: Date.now() + SNOOZE_MINUTES * 60000 });
                close();
                schedule();
            });

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const value = input.value.trim();
                if (!/^\d{1,9}$/.test(value) || parseInt(value, 10) <= 0) {
                    showError(msgDigits);
                    input.focus();
                    return;
                }
                save.disabled = true;
                fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify({ number_employee: value })
                })
                    .then(function(r) {
                        return r.json().catch(function() { return {}; }).then(function(d) { d = d || {}; d.status = r.status; return d; });
                    })
                    .then(function(d) {
                        save.disabled = false;
                        // 409: ya lo tiene (p. ej. lo guardó en otra pestaña): no hay nada más que pedir
                        if (d.is_success || d.status === 409) {
                            done = true;
                            clearTimeout(timer);
                            try { localStorage.removeItem(STATE_KEY); } catch (e) {}
                            close();
                            if (d.is_success && typeof show_toastr === 'function') show_toastr('success', d.success || msgSaved, 'success');
                        } else {
                            showError((d && d.error) || msgError);
                            input.focus();
                        }
                    })
                    .catch(function() {
                        save.disabled = false;
                        showError(msgError);
                    });
            });

            const open = function() {
                if (done || !modal.hidden) return;
                prepare();
                showError('');
                modal.hidden = false;
                document.addEventListener('keydown', onKey);
                setTimeout(function() { input.focus(); }, 50);
            };

            // Lo abre ya o programa cuándo volver a abrirlo, sin necesidad de cambiar de página.
            // Se recalcula al dispararse porque otra pestaña puede haberlo pospuesto entretanto.
            const schedule = function() {
                clearTimeout(timer);
                if (done) return;
                const ms = waitMs();
                if (ms === 0) {
                    open();
                } else {
                    // setTimeout admite como mucho ~24,8 días; la pausa es mucho menor
                    timer = setTimeout(schedule, Math.min(ms + 250, 2147000000));
                }
            };

            // Con la pestaña en segundo plano o el portátil en reposo los temporizadores se retrasan:
            // al volver a la pestaña se comprueba si ya toca
            document.addEventListener('visibilitychange', function() {
                if (document.visibilityState === 'visible') schedule();
            });

            // Primera vez: al terminar de cargar la página, sin tapar el primer pintado
            const start = function() { setTimeout(schedule, 400); };
            if (document.readyState === 'complete') start();
            else window.addEventListener('load', start);
        })();
    </script>
@endif
