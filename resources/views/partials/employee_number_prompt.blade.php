{{--
    Modal «Falta tu número de empleado»: sale al entrar en la aplicación a quien no lo tiene
    (users.number_employee vacío). Se guarda con users.my.employee_number (solo una vez).
    «Más tarde» lo pospone ENP_SNOOZE_MINUTES y solo se puede usar ENP_MAX_SNOOZES veces; después
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

            const state = readState();
            const canSnooze = state.count < MAX_SNOOZES;
            // Pospuesto y aún dentro de la pausa: no se muestra en esta página
            if (canSnooze && state.until > Date.now()) return;

            later.hidden = !canSnooze;
            const left = MAX_SNOOZES - state.count;
            hint.textContent = !canSnooze ? msgLast
                : (left === 1 ? msgLeftOne : msgLeftMany).replace(':count', left).replace(':minutes', SNOOZE_MINUTES);
            hint.classList.toggle('is-final', !canSnooze);

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
                if (!canSnooze) return;
                writeState({ count: state.count + 1, until: Date.now() + SNOOZE_MINUTES * 60000 });
                close();
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
                    .then(function(r) { return r.json().catch(function() { return {}; }); })
                    .then(function(d) {
                        save.disabled = false;
                        if (d && d.is_success) {
                            try { localStorage.removeItem(STATE_KEY); } catch (e) {}
                            close();
                            if (typeof show_toastr === 'function') show_toastr('success', d.success || msgSaved, 'success');
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

            // Se abre al terminar de cargar la página, sin tapar el primer pintado
            const open = function() {
                modal.hidden = false;
                document.addEventListener('keydown', onKey);
                setTimeout(function() { input.focus(); }, 50);
            };
            if (document.readyState === 'complete') setTimeout(open, 400);
            else window.addEventListener('load', function() { setTimeout(open, 400); });
        })();
    </script>
@endif
