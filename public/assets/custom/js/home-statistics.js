(function () {
    'use strict';

    var MONTH_ORDER = ["January", "February", "March", "April", "May", "June", "July", "August", "September",
        "October", "November", "December"
    ];
    var QUARTER_ORDER = ["Q1", "Q2", "Q3", "Q4"];

    var averageTimes = window.averageTimes || {};
    var i18n = window.statisticsI18n || {};
    var currentView = 'monthly';
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
                        backgroundColor: 'rgba(211, 211, 211, 0.8)',
                        hidden: false
                    },
                    {
                        label: i18n.onTime,
                        data: [],
                        backgroundColor: 'rgba(201, 237, 185, 0.8)',
                        hidden: false
                    },
                    {
                        label: i18n.delay,
                        data: [],
                        backgroundColor: 'rgba(224, 108, 113, 0.8)',
                        hidden: false
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: {
                            generateLabels: function(ch) {
                                var labels = Chart.defaults.plugins.legend.labels.generateLabels(ch);

                                labels.push({
                                    text: i18n.showValues,
                                    fillStyle: 'black',
                                    strokeStyle: 'black',
                                    hidden: !ch.options.plugins.datalabels.display,
                                    datasetIndex: -1
                                });

                                return labels;
                            }
                        },
                        onClick: function(e, legendItem, legend) {
                            if (legendItem.datasetIndex === -1) {
                                var currentDisplay = legend.chart.options.plugins.datalabels.display;
                                legend.chart.options.plugins.datalabels.display = !currentDisplay;

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
                        display: true,
                        color: 'black',
                        font: {
                            weight: 'bold',
                            size: 12
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
                        borderRadius: 8
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

        var empty = false;
        if (view === 'monthly') {
            empty = Object.keys(data.months || {}).length === 0;
        } else if (view === 'quarterly') {
            empty = Object.keys(data.quarters || {}).length === 0;
        } else if (view === 'yearly') {
            var y = data.yearly || {};
            empty = !(y.averageStartUp || y.averageWorking || y.averageDelay);
        }

        toggleEmptyState(empty);
        if (empty) {
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

    function toggleEmptyState(empty) {
        var canvas = document.getElementById('myChart');
        var message = document.getElementById('chartEmptyMessage');

        if (canvas) canvas.style.display = empty ? 'none' : 'block';
        if (message) message.style.display = empty ? 'flex' : 'none';
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

        chart.options.plugins.title.text = i18n.averagePer + ' ' + i18n[mode];
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

        chart.options.plugins.title.text = i18n.annualAverage + ' (' + selectedYear + ')';
        chart.update();
    }

    document.addEventListener('DOMContentLoaded', init);
})();