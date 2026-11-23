/*
 * Estadísticas del resumen (home): tiempo medio de los encargos finalizados.
 * Barras de recorrido: cada periodo (mes, trimestre o año) es una barra horizontal con sus tramos
 * en el orden en que los vive el encargo: tiempo de inicio → en plazo → retraso. La longitud son días,
 * a la misma escala en todas las barras para poder compararlas. Se anima al cargar y al cambiar de vista.
 * Datos: window.averageTimes (AverageTimeService) · textos: window.statisticsI18n (home.blade.php).
 */
(function () {
    'use strict';

    // Los meses llegan con el nombre en el idioma de la app (Carbon translatedFormat('F')): orden por idioma
    var MONTHS = [
        ['january', 'enero', 'gennaio', 'janvier', 'janeiro', 'ianuarie', 'styczeń'],
        ['february', 'febrero', 'febbraio', 'février', 'fevereiro', 'februarie', 'luty'],
        ['march', 'marzo', 'marzo', 'mars', 'março', 'martie', 'marzec'],
        ['april', 'abril', 'aprile', 'avril', 'abril', 'aprilie', 'kwiecień'],
        ['may', 'mayo', 'maggio', 'mai', 'maio', 'mai', 'maj'],
        ['june', 'junio', 'giugno', 'juin', 'junho', 'iunie', 'czerwiec'],
        ['july', 'julio', 'luglio', 'juillet', 'julho', 'iulie', 'lipiec'],
        ['august', 'agosto', 'agosto', 'août', 'agosto', 'august', 'sierpień'],
        ['september', 'septiembre', 'settembre', 'septembre', 'setembro', 'septembrie', 'wrzesień'],
        ['october', 'octubre', 'ottobre', 'octobre', 'outubro', 'octombrie', 'październik'],
        ['november', 'noviembre', 'novembre', 'novembre', 'novembro', 'noiembrie', 'listopad'],
        ['december', 'diciembre', 'dicembre', 'décembre', 'dezembro', 'decembrie', 'grudzień']
    ];
    var QUARTER_ORDER = ['Q1', 'Q2', 'Q3', 'Q4'];

    var averageTimes = window.averageTimes || {};
    var i18n = window.statisticsI18n || {};
    var currentView = 'monthly';
    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function monthIndex(name) {
        var key = String(name || '').toLowerCase();
        for (var i = 0; i < MONTHS.length; i++) {
            if (MONTHS[i].indexOf(key) !== -1) return i;
        }
        return 99;
    }

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function round(n) {
        return Math.round((Number(n) || 0) * 10) / 10;
    }

    function init() {
        if (!document.getElementById('hsJourney')) return;

        var values = document.getElementById('hsjValues');
        if (values) {
            try { values.checked = localStorage.getItem('aceler.home.statsValues') !== '0'; } catch (e) {}
            values.addEventListener('change', function () {
                document.getElementById('hsJourney').classList.toggle('no-values', !values.checked);
                try { localStorage.setItem('aceler.home.statsValues', values.checked ? '1' : '0'); } catch (e) {}
            });
            document.getElementById('hsJourney').classList.toggle('no-values', !values.checked);
        }

        bindYearDropdown();
        window.updateChart('monthly');
    }

    function bindYearDropdown() {
        var dropdown = document.getElementById('yearDropdown');
        var list = document.getElementById('yearList');

        if (dropdown) {
            dropdown.addEventListener('click', function () {
                if (!list) return;
                var isHidden = getComputedStyle(list).display === 'none';
                list.style.display = isHidden ? 'block' : 'none';
            });
        }

        document.querySelectorAll('.yearOption').forEach(function (option) {
            option.addEventListener('click', function () {
                var year = option.getAttribute('data-year');
                var select = document.getElementById('yearSelect');
                var display = document.getElementById('yearDisplay');

                if (select) select.value = year;
                if (display) display.textContent = year;
                if (list) list.style.display = 'none';

                window.updateChart(currentView);
            });
        });

        document.addEventListener('click', function (event) {
            if (list && !event.target.closest('#yearDropdown, #yearList')) {
                list.style.display = 'none';
            }
        });
    }

    // Periodos de la vista elegida, ordenados: [{label, count, s, p, r}]
    function periodsFor(data, view, year) {
        if (view === 'yearly') {
            var y = data.yearly || {};
            return [{ label: year, count: y.count || 0, s: y.averageStartUp || 0, p: y.averageWorking || 0, r: y.averageDelay || 0 }];
        }
        var source = view === 'monthly' ? (data.months || {}) : (data.quarters || {});
        var keys = Object.keys(source);
        keys.sort(view === 'monthly'
            ? function (a, b) { return monthIndex(a) - monthIndex(b); }
            : function (a, b) { return QUARTER_ORDER.indexOf(a) - QUARTER_ORDER.indexOf(b); });

        return keys.map(function (k) {
            var p = source[k] || {};
            return { label: k, count: p.count || 0, s: p.averageStartUp || 0, p: p.averageWorking || 0, r: p.averageDelay || 0 };
        });
    }

    window.updateChart = function (view) {
        var select = document.getElementById('yearSelect');
        var year = select ? select.value : '';
        var data = averageTimes[year];
        var box = document.getElementById('hsJourney');
        if (!box) return;

        currentView = view;
        document.querySelectorAll('.divStatisticsButtons button').forEach(function (btn) {
            btn.classList.toggle('active', btn.getAttribute('data-view') === view);
        });

        var periods = data ? periodsFor(data, view, year) : [];
        var finished = periods.reduce(function (acc, p) { return acc + p.count; }, 0);

        // Sin encargos finalizados: franja compacta. Con finalizados a 0 días sí se pintan las filas
        // («Terminados el mismo día»), así se ve que existen.
        toggleEmptyState(finished === 0);
        if (finished === 0) {
            refit();
            return;
        }

        render(box, periods, view, year, finished);
        refit();
    };

    function toggleEmptyState(empty) {
        var box = document.getElementById('hsJourney');
        var message = document.getElementById('chartEmptyMessage');
        var kpis = document.getElementById('hsjKpis');
        if (box) box.hidden = empty;
        if (kpis) kpis.hidden = empty;
        if (message) {
            message.style.display = empty ? 'flex' : 'none';
            var title = message.querySelector('b');
            var text = message.querySelector('span');
            if (title && i18n.noData) title.textContent = i18n.noData;
            if (text && i18n.noDataHint) text.textContent = i18n.noDataHint;
        }
    }

    function render(box, periods, view, year, finished) {
        // Cifras resumen, ponderadas por el número de encargos de cada periodo
        var sumAvg = 0, sumDelay = 0;
        periods.forEach(function (p) {
            sumAvg += (p.s + p.p + p.r) * p.count;
            sumDelay += p.r * p.count;
        });
        setKpi(box, 'count', finished, 0);
        setKpi(box, 'avg', round(sumAvg / finished), 1);
        setKpi(box, 'delay', round(sumDelay / finished), 1);

        var title = document.getElementById('hsjTitle');
        if (title) {
            var mode = view === 'monthly' ? i18n.month : (view === 'quarterly' ? i18n.quarter : null);
            title.textContent = (mode ? (i18n.averagePer + ' ' + String(mode).toLowerCase()) : (i18n.annualAverage + ' (' + year + ')'))
                + (i18n.finishedCount ? ' · ' + i18n.finishedCount.replace(':n', finished) : '');
        }

        var max = Math.max.apply(null, periods.map(function (p) { return p.s + p.p + p.r; }).concat([1]));
        var names = { s: i18n.startingTime, p: i18n.onTime, r: i18n.delay };
        var unit = i18n.daysUnit || '';
        var rows = document.getElementById('hsjRows');

        rows.innerHTML = periods.map(function (p) {
            var total = round(p.s + p.p + p.r);
            var orders = ((p.count === 1 && i18n.ordersOne) || i18n.orders || ':n').replace(':n', p.count);
            var inner;
            if (total === 0) {
                inner = '<div class="hsj-same">' + esc(i18n.sameDay) + '</div>';
            } else {
                inner = ['s', 'p', 'r'].filter(function (k) { return p[k] > 0; }).map(function (k) {
                    var v = round(p[k]);
                    return '<div class="hsj-seg hsj-seg--' + k + '" data-w="' + (p[k] / max * 100).toFixed(2) + '"'
                        + ' title="' + esc(names[k] + ': ' + v + ' ' + unit) + '">' + v + '</div>';
                }).join('');
            }
            return '<div class="hsj-row" role="listitem" aria-label="' + esc(p.label + ': ' + total + ' ' + unit + ', ' + orders) + '">'
                + '<span class="hsj-label">' + esc(p.label) + '</span>'
                + '<div class="hsj-track">' + inner + '</div>'
                + '<span class="hsj-total"><b class="hsj-num" data-to="' + total + '">0</b> ' + esc(unit) + ' <small>· ' + esc(orders) + '</small></span>'
                + '</div>';
        }).join('');

        animate(rows);
    }

    function setKpi(box, key, value, decimals) {
        var el = document.querySelector('#hsjKpis [data-kpi="' + key + '"]');
        if (!el) return;
        el.setAttribute('data-to', value);
        el.setAttribute('data-dec', decimals);
        countUp(el);
    }

    // Las barras se llenan fila a fila y tramo a tramo; los números suben a la vez
    function animate(rows) {
        var segs = rows.querySelectorAll('.hsj-seg');
        var nums = rows.querySelectorAll('.hsj-num');
        if (reduceMotion) {
            segs.forEach(function (s) { s.style.width = s.getAttribute('data-w') + '%'; });
            nums.forEach(function (n) { n.textContent = n.getAttribute('data-to'); });
            return;
        }
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                rows.querySelectorAll('.hsj-row').forEach(function (row, i) {
                    row.querySelectorAll('.hsj-seg').forEach(function (s, j) {
                        setTimeout(function () {
                            s.style.width = s.getAttribute('data-w') + '%';
                            if (s.classList.contains('hsj-seg--r')) setTimeout(function () { s.classList.add('is-live'); }, 900);
                        }, Math.min(i, 8) * 90 + j * 260);
                    });
                });
                nums.forEach(countUp);
            });
        });
    }

    function countUp(el) {
        var to = Number(el.getAttribute('data-to')) || 0;
        var dec = Number(el.getAttribute('data-dec') || (to % 1 ? 1 : 0));
        if (reduceMotion || !to) { el.textContent = to.toFixed(dec).replace(/\.0$/, ''); return; }
        var t0 = performance.now(), dur = 1100;
        (function step(t) {
            var k = Math.min(1, (t - t0) / dur);
            var v = to * (1 - Math.pow(1 - k, 3));
            el.textContent = (k < 1 ? v.toFixed(dec) : to.toFixed(dec)).replace(/\.0$/, '');
            if (k < 1) requestAnimationFrame(step);
        })(t0);
    }

    // El alto de la tarjeta cambia con el número de filas. Las listas del equipo tienen un mínimo
    // (~200px, fitTeamLists en home.blade.php); si con eso la página no cabe, encoge primero la lista
    // de periodos (con su propio scroll) hasta ~3 filas, y solo después la página hace scroll.
    var ROWS_MAX = 236, ROWS_MIN = 110;
    function refit() {
        setTimeout(function () {
            var rows = document.getElementById('hsjRows');
            var fit = typeof window.fitTeamLists === 'function' ? window.fitTeamLists : function () {};
            if (rows) rows.style.maxHeight = ROWS_MAX + 'px';
            fit();
            var over = document.documentElement.scrollHeight - window.innerHeight;
            if (rows && over > 0 && rows.scrollHeight > ROWS_MIN) {
                var h = Math.min(rows.scrollHeight, ROWS_MAX);
                rows.style.maxHeight = Math.max(ROWS_MIN, h - over - 1) + 'px';
                fit();
            }
        }, 50);
    }
    window.addEventListener('resize', function () { refit(); });

    document.addEventListener('DOMContentLoaded', init);
})();
