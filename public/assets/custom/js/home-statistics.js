(function () {
    'use strict';

    var MONTH_ORDER = ["January", "February", "March", "April", "May", "June", "July", "August", "September",
        "October", "November", "December"
    ];
    var QUARTER_ORDER = ["Q1", "Q2", "Q3", "Q4"];

    var averageTimes = window.averageTimes || {};
    var i18n = window.statisticsI18n || {};
    var currentView = 'monthly';
    var currentCount = 0;
    var chart = null;

    function init() {
        var ctx = document.getElementById('myChart');
        if (!ctx) return;

        chart = new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                        label: i18n.startingTime,
                        data: [],
                        backgroundColor: '#cbd5e1',
                        maxBarThickness: 56,
                        hidden: false
                    },
                    {
                        label: i18n.onTime,
                        data: [],
                        backgroundColor: '#5cc45a',
                        maxBarThickness: 56,
                        hidden: false
                    },
                    {
                        label: i18n.delay,
                        data: [],
                        backgroundColor: '#e06c71',
                        maxBarThickness: 56,
                        hidden: false
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        align: 'center',
                        labels: {
                            boxWidth: 12,
                            boxHeight: 12,
                            color: '#6b7280',
                            generateLabels: function(ch) {
                                var labels = Chart.defaults.plugins.legend.labels.generateLabels(ch);

                                labels.push({
                                    text: i18n.showValues,
                                    fillStyle: 'black',
                                    strokeStyle: 'black',
                                    hidden: ch.$showValues === false,
                                    datasetIndex: -1
                                });

                                return labels;
                            }
                        },
                        onClick: function(e, legendItem, legend) {
                            if (legendItem.datasetIndex === -1) {
                                legend.chart.$showValues = legend.chart.$showValues === false;

                                legend.options.labels.generateLabels(legend.chart);
                                legend.chart.update();
                            } else {
                                var dataset = legend.chart.data.datasets[legendItem.datasetIndex];
                                dataset.hidden = !dataset.hidden;
                                legend.chart.update();
                            }
                        }
                    },
                    title: {
                        display: true
                    },
                    datalabels: {
                        anchor: 'center',
                        align: 'center',
                        clamp: true,
                        // Solo se escribe el valor si cabe en su tramo: con tramos finos los números
                        // se montaban unos sobre otros. El tooltip sigue enseñando todos los valores.
                        display: function(ctx) {
                            var chart = ctx.chart;
                            if (chart.$showValues === false) return false;
                            var value = Number(ctx.dataset.data[ctx.dataIndex]);
                            if (!value) return false;
                            var el = chart.getDatasetMeta(ctx.datasetIndex).data[ctx.dataIndex];
                            if (!el) return false;
                            var height = Math.abs(el.base - el.y);
                            var width = el.width || 0;
                            return height >= 16 && width >= 18;
                        },
                        color: function(ctx) {
                            // Texto blanco sobre verde/rojo, oscuro sobre el gris claro
                            return ctx.datasetIndex === 0 ? '#334155' : '#ffffff';
                        },
                        font: {
                            weight: 'bold',
                            size: 11
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: true
                    },
                    y: {
                        stacked: true,
                        title: {
                            display: true,
                            text: i18n.days
                        }
                    }
                },
                elements: {
                    bar: {
                        borderRadius: 4
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

        bindYearDropdown();
        updateChart('monthly');
    }

    function bindYearDropdown() {
        var dropdown = document.getElementById('yearDropdown');
        var list = document.getElementById('yearList');

        if (dropdown) {
            dropdown.addEventListener('click', function() {
                if (!list) return;
                var isHidden = getComputedStyle(list).display === 'none';
                list.style.display = isHidden ? 'block' : 'none';
            });
        }

        document.querySelectorAll('.yearOption').forEach(function(option) {
            option.addEventListener('click', function() {
                var year = option.getAttribute('data-year');
                var select = document.getElementById('yearSelect');
                var display = document.getElementById('yearDisplay');

                if (select) select.value = year;
                if (display) display.textContent = year;
                if (list) list.style.display = 'none';

                updateYear();
            });
        });

        document.addEventListener('click', function(event) {
            if (list && !event.target.closest('#yearDropdown, #yearList')) {
                list.style.display = 'none';
            }
        });
    }

    function updateYear() {
        updateChart(currentView);
    }

    window.updateChart = function(view) {
        var selectedYear = document.getElementById('yearSelect').value;
        var data = averageTimes[selectedYear];

        if (!data) return;

        currentView = view;

        document.querySelectorAll('.divStatisticsButtons button').forEach(function(btn) {
            btn.classList.toggle('active', btn.getAttribute('data-view') === view);
        });

        // Sin nada que dibujar en dos casos distintos:
        //  - no hay encargos finalizados en el periodo, o
        //  - sí los hay, pero su tiempo medio es 0 días (p. ej. finalizados el mismo día en que se crearon).
        // Antes del segundo caso salía un eje vacío con «0 · noviembre».
        var periods = view === 'monthly' ? data.months : (view === 'quarterly' ? data.quarters : { year: data.yearly });
        var list = Object.keys(periods || {}).map(function(key) { return periods[key] || {}; });
        var finishedCount = list.reduce(function(acc, p) { return acc + (p.count || 0); }, 0);
        var hasValues = list.some(function(p) {
            return (p.averageStartUp || 0) + (p.averageWorking || 0) + (p.averageDelay || 0) > 0;
        });
        currentCount = finishedCount;

        toggleEmptyState(!hasValues, finishedCount);
        if (!hasValues) {
            clearChart();
            return;
        }

        if (view === 'monthly') {
            updateChartData(data.months, 'month');
        } else if (view === 'quarterly') {
            updateChartData(data.quarters, 'quarter');
        } else if (view === 'yearly') {
            updateYearlyChart(data.yearly);
        }
    };

    function toggleEmptyState(empty, finishedCount) {
        var canvas = document.getElementById('myChart');
        var message = document.getElementById('chartEmptyMessage');

        if (canvas) canvas.style.display = empty ? 'none' : 'block';
        if (message) {
            message.style.display = empty ? 'flex' : 'none';
            var title = message.querySelector('b');
            var text = message.querySelector('span');
            // Con encargos finalizados pero a 0 días se dice cuántos hay y por qué no hay barras
            if (title && i18n.zeroTitle) {
                title.textContent = finishedCount > 0
                    ? i18n.zeroTitle.replace(':n', finishedCount)
                    : i18n.noData;
            }
            if (text && i18n.zeroText) {
                text.textContent = finishedCount > 0 ? i18n.zeroText : (i18n.noDataHint || text.textContent);
            }
        }
        if (!empty && chart) chart.resize();
    }

    function clearChart() {
        if (!chart) return;

        chart.data.labels = [];
        chart.data.datasets.forEach(function(dataset) {
            dataset.data = [];
        });
        chart.options.plugins.title.text = '';
        chart.update();
    }

    function updateChartData(data, mode) {
        if (!chart || !data) return;

        var labels = Object.keys(data);

        if (mode === 'month') {
            labels.sort(function(a, b) {
                return MONTH_ORDER.indexOf(a) - MONTH_ORDER.indexOf(b);
            });
        } else if (mode === 'quarter') {
            labels.sort(function(a, b) {
                return QUARTER_ORDER.indexOf(a) - QUARTER_ORDER.indexOf(b);
            });
        }

        var startUp = [];
        var working = [];
        var delay = [];

        labels.forEach(function(period) {
            var periodData = data[period] || {};
            startUp.push(periodData.averageStartUp || 0);
            working.push(periodData.averageWorking || 0);
            delay.push(periodData.averageDelay || 0);
        });

        chart.config.type = 'bar';
        chart.options.scales.x.stacked = true;
        chart.options.scales.y.stacked = true;

        chart.data.labels = labels;
        chart.data.datasets[0].data = startUp;
        chart.data.datasets[1].data = working;
        chart.data.datasets[2].data = delay;

        chart.options.plugins.title.text = i18n.averagePer + ' ' + i18n[mode] + countSuffix();
        chart.update();
    }

    function updateYearlyChart(data) {
        if (!chart || !data) return;

        var selectedYear = document.getElementById('yearSelect').value;

        chart.config.type = 'bar';
        chart.options.scales.x.stacked = true;
        chart.options.scales.y.stacked = true;

        chart.data.labels = [selectedYear];
        chart.data.datasets[0].data = [data.averageStartUp || 0];
        chart.data.datasets[1].data = [data.averageWorking || 0];
        chart.data.datasets[2].data = [data.averageDelay || 0];

        chart.options.plugins.title.text = i18n.annualAverage + ' (' + selectedYear + ')' + countSuffix();
        chart.update();
    }

    // «· 8 encargos finalizados» junto al título del gráfico
    function countSuffix() {
        return currentCount > 0 && i18n.finishedCount ? ' · ' + i18n.finishedCount.replace(':n', currentCount) : '';
    }

    document.addEventListener('DOMContentLoaded', init);
})();