@extends('layouts.admin')
<!-- @include('tutorial.projectTutorial') -->
@php
    $client_keyword = Auth::user()->getGuard() == 'client' ? 'client.' : '';

@endphp


<style>
    .pro-status {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    @media screen and (max-width: 1200px) and (min-width:1000px) {
        .taskRowWidth {
            width: 32% !important;
        }

        .colWidthTask {
            width: 49% !important;
        }
    }

    /* /New Styling/ */
    .summary {
        display: flex;
        justify-content: space-between;
        width: 100%;
        margin-top: 10px;
        /* border: 1px solid black; */
        height: 120px;
        align-content: center;
        align-items: center;
        margin-bottom: 20px;
    }

    .tabs {
        height: 100%;
        width: 32%;
        background-color: white;
        border-radius: 15px;
        box-shadow: 0 6px 30px rgba(182, 186, 203, 0.3);
    }

    .ctr {
        display: flex;
        align-items: center;


    }

    .titleTecAndCom {
        padding-left: 10px;
        font-size: 16px;
    }

    .tabIcon {
        margin-left: 30px;
        width: 50px;
        height: 50px;
        background-color: #AA182C;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 10px;
        filter: drop-shadow(0px 1px 3px rgba(0, 0, 0, 0.2));
    }

    .icons {
        width: 35px;
        height: 32px;
        filter: invert(1);
    }

    .projectIcon {
        background-color: #8dd656;
    }

    .milestoneIcon {
        background-color: rgb(174 154 247);
    }

    .taskIcon {
        background-color: #72c8d4;
    }

    .tabTexts {
        margin-left: 20px;
        font-size: 22px;
        font-weight: 800;
    }

    .tabNumCounter {
        background-color: white;
        display: flex;
        align-items: center;
        width: 40px;
        height: 35px;
        justify-content: center;
        border-radius: 7px;
        box-shadow: 2px 2px 5px 0px rgb(0 0 0 / 30%);
    }



    .stickyComercialTec {
        width: 97%;
        height: 100px;
        background-color: F8FAF9;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        position: absolute;
        bottom: 0;

    }

    .dropDownCT {
        background-color: #F8FAF9;
        box-shadow: inset 0px 8px 2px rgb(0 0 0 / 22%);
        /* border: 1px solid red; */
        border-top-left-radius: 23px;
        border-top-right-radius: 23px;
    }

    .dropdownHeaders {
        display: flex;
        align-content: center;
        align-items: center;
        justify-content: center;
        height: 100%;
        position: relative;
    }

    .dropdownHeaders>span {
        font-size: 20px;
        font-weight: 600;
        margin-left: 10px;
    }

    .comercial {
        width: 40%;
        height: 50px;
        /* background-color: #AA182C; */
    }

    .technicians {
        width: 40%;
        height: 50px;
        /* background-color: yellow; */
    }

    .comercialTecIcons {
        height: 23px;
        width: 34px;
        display: inline-flex;
        margin-bottom: 4px;
    }

    .dropdownContent {
        height: 469px;
        display: flex;
        flex-direction: column;
        align-content: center;
        align-items: center;
        background-color: #f8faf9;
        border-top: 3px solid #c2c3c2;
        border-right: 3px solid #c2c3c2;
        border-left: 3px solid #c2c3c2;
        overflow: scroll;
    }

    .dropdownContent::-webkit-scrollbar {
        width: 0px;
        background: transparent;
        /* Opcional: para asegurarse de que el área del scrollbar no sea visible */
    }

    .comercialAndTechnicians {
        background-color: #F8FAF9;
        margin-top: 20px;
        height: 60px;
        width: 99%;
        border-radius: 9px;
        box-shadow: 0px 0px 5px rgb(0 0 0 / 22%);
        display: flex;
        align-items: center;
        justify-content: flex-start;
        /* Ensure alignment */
        gap: 20px;
        /* Add consistent spacing */
        padding: 16 15px;
        /* Add padding for content separation */
    }

    .ppcontainer {
        flex-shrink: 0;
        /* Prevent resizing of profile picture container */
    }

    .profilePicture {
        width: 40px;
        height: 40px;
        background-color: red;
        border-radius: 100%;
        margin-left: 12px;
    }

    .textContent {
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        flex-grow: 1;
        /* Allow the text content to take remaining space */
        text-align: center;
    }

    .fullName {
        font-size: larger;
        font-weight: 600;
    }

    .emailName {
        font-size: large;
        color: #949494;
        white-space: nowrap;
    }

    .statusContainer {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        margin-left: 11%;
        height: 100%;
        width: 27%;
        justify-content: center;
        gap: 8px;

    }

    .status {
        height: 28px;
        color: black;
        font-size: 12px;
        width: 77%;
        border-radius: 10px;
        text-align: center;
        display: flex;
        justify-content: space-evenly;
        align-items: center;
        padding: 1px;
        box-shadow: 1px 2px 1px 0px #00000061;

    }

    .hold {
        background-color: #e06c71;
    }

    .progressstat {
        background-color: #d3d3d3;
    }

    .ended {
        background-color: #c9edb9;
    }

    .statusNum {
        color: black
    }

    .statusNumContainer {
        background-color: white;
        border-radius: 100%;
        padding-left: 5px;
        padding-right: 5px;
    }

    .statusText {
        padding: 2px;
    }

    .testContent {
        background-color: yellow;
    }

    .hiddenTuto {
        display: none;
    }

    .modifiedDivTecAndCom {
        max-height: 400px;
        /* Asegura que el div tenga un límite de altura */
        overflow-y: auto;
        /* Permite el desplazamiento vertical si hay demasiado contenido */
        overflow-x: hidden;
        /* Evita desplazamiento horizontal */
        height: auto;
        /* Se ajusta automáticamente sin forzar una altura fija */
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 10px;
        margin-bottom: 20px;
    }

    .divStatisticsButtons {
        margin-bottom: 10px;
        display: flex;
        flex-direction: row;
        justify-content: flex-start;
        align-items: center;
    }

    .formControlModified {
        cursor: pointer;
        width: 10% !important;
        margin-right: 1%;
        margin-left: 1%;
    }

    .marginRight1 {
        margin-right: 1%;
    }

    .yearListDiv {
        position: absolute;
        background: white;
        border: 1px solid rgb(204, 204, 204);
        width: 10%;
        left: 30px;
        z-index: 1000;
        top: 23%;
        border-radius: 10px;
        display: none;
    }

    .yearOption:hover {
        background-color: #AA182C;
        color: white;
        border-radius: 8px;
    }

    @media screen and (min-width:1440px) and (max-width: 1490px) {

        /* *{
        border: 3px solid green;
    } */
        .status {
            width: 95%;
        }

        .stickyComercialTec {
            height: 0px;
        }
    }

    .filter-input {
        width: 97%;
        padding: 10px;
        margin: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        margin-bottom: 0;
    }

    .milestonesTextSpan {
        font-size: 13px;
        font-weight: 800;
        margin-left: 9px;
    }

    .displayFlexAlignCenter {
        display: flex;
        align-content: center;
        align-items: center;
    }

    .alignArrowSelect {
        position: absolute;
        left: 10%;
        top: 102px;
    }
</style>

@section('content')
    <section class="section" style="margin-top: -30px;">
        @if (Auth::user()->type == 'admin')
            <div class="row">
                <div class="col-12">
                    @if (empty(env('PUSHER_APP_ID')) ||
                            empty(env('PUSHER_APP_KEY')) ||
                            empty(env('PUSHER_APP_SECRET')) ||
                            empty(env('PUSHER_APP_CLUSTER')))
                        <div class="alert alert-warning"><i class="fas fa-warning"></i>
                            {{ __('Please Add Pusher Detail in Setting Page ') }}<u><a
                                    href="{{ route('settings.index') }}">{{ __('here') }}</a></u></div>
                    @endif
                    @if (empty(env('MAIL_DRIVER')) ||
                            empty(env('MAIL_HOST')) ||
                            empty(env('MAIL_PORT')) ||
                            empty(env('MAIL_USERNAME')) ||
                            empty(env('MAIL_PASSWORD')) ||
                            empty(env('MAIL_PASSWORD')))
                        <div class="alert alert-warning"><i class="fas fa-warning"></i>
                            {{ __('Please Add Mail Details in Setting Page ') }} <u><a
                                    href="{{ route('settings.index') }}">{{ __('here') }}</a></u></div>
                    @endif
                </div>
                <div class="col-lg-7 col-md-7 col-sm-7">
                    <div class="row">

                        <div class="col-lg-4 col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="theme-avtar bg-info">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <p class="text-muted text-sm mt-4 mb-2">
                                        {{ __('Paid User') }} : <strong>{{ $totalPaidUsers }}</strong></p>
                                    <h6 class="mb-3">{{ __('Total Users') }}</h6>
                                    <h3 class="mb-0">{{ $totalUsers }} <span class="text-success text-sm"></span></h3>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="theme-avtar bg-success">
                                        <i class="fas fa-cash-register"></i>
                                    </div>
                                    <p class="text-muted text-sm mt-4 mb-2">

                                        {{ __('Order Amount') }} :
                                        <strong>{{ (env('CURRENCY_SYMBOL') != '' ? env('CURRENCY_SYMBOL') : '$') . $totalOrderAmount }}</strong>
                                    </p>
                                    <h6 class="mb-3">{{ __('Total Orders') }}</h6>
                                    <h3 class="mb-0">{{ $totalOrders }} <span class="text-success text-sm"></span></h3>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <div class="card">
                                <div class="card-body total_plan">
                                    <div class="theme-avtar bg-danger">
                                        <i class="fas fa-trophy"></i>
                                    </div>
                                    <p class="text-muted text-sm mt-4 mb-2">
                                        {{ __('Most purchase plan') }} : <strong>
                                            @if ($mostPlans)
                                                {{ $mostPlans->name }}
                                            @else
                                                -
                                            @endif
                                        </strong>
                                    </p>
                                    <h6 class="mb-3">{{ __('Total Plans') }}</h6>
                                    <h3 class="mb-0">{{ $totalPlans }} <span class="text-success text-sm"></span></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 col-md-5 col-sm-5">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-10">
                                    <h5>{{ __('Recent Orders') }}</h5>
                                </div>
                                <div class=" col-2"><small class="text-end"></small></div>
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- <div id="task-area-chart"></div> --}}
                        </div>
                    </div>
                </div>
            </div>
        @elseif($currentWorkspace)
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <!-- <div class="row"> -->
                    <div class="page-header-title">
                        <h4 class="m-b-10">{{ __('Resume of') }} {{ $currentWorkspace->name }}</h4>
                    </div>
                    <div class="summary">
                        <div class="tabs ctr">
                            <div class="tabIcon projectIcon">
                                <img class="icons"
                                    src="{{ asset('assets/custom/libs/@fontawesome/fontawesome-free/svgs/solid/project-diagram.svg') }}"
                                    alt="logo" />

                            </div>
                            <div class="tabTexts">
                                {{ __('Projects') }}
                            </div>
                            <div class="tabTexts tabNumCounter">
                                <span>
                                    {{ $totalProject ?? 0 }}
                                </span>

                            </div>
                            <div class="statusContainer">
                                <div class="status hold ctr">
                                    <span class="statusText">{{ __('OnHold') }}</span>
                                    <div class="statusNumContainer">
                                        <span class="statusNum">{{ $projectProcess['OnHold'] ?? 0 }}</span>
                                    </div>
                                </div>
                                <div class="status progressstat ctr">
                                    <span class="statusText">{{ __('Ongoing') }}</span>
                                    <div class="statusNumContainer">
                                        <span class="statusNum">{{ $projectProcess['Ongoing'] ?? 0 }}</span>
                                    </div>
                                </div>
                                <div class="status ended ctr">
                                    <span class="statusText">{{ __('Finished') }}</span>
                                    <div class="statusNumContainer">
                                        <span class="statusNum">{{ $projectProcess['Finished'] ?? 0 }}</span>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="tabs ctr">
                            <div class="tabIcon milestoneIcon">
                                <img class="icons"
                                    src="{{ asset('assets/custom/libs/@fontawesome/fontawesome-free/svgs/solid/file-alt.svg') }}"
                                    alt="logo" />
                            </div>
                            <div class="tabTexts">
                                {{ __('Milestones') }}
                            </div>
                            <div>
                                <div class="displayFlexAlignCenter">
                                    <div class="tabTexts tabNumCounter" style="margin-bottom: 2%;">
                                        <span>
                                            {{ $totalWorkspaceMilestones ?? 0 }}
                                        </span>
                                    </div>
                                    <span class="milestonesTextSpan">{{ $currentWorkspace->name }}</span>
                                </div>
                                <div class="displayFlexAlignCenter">
                                    <div class="tabTexts tabNumCounter">
                                        <span>
                                            {{ $totalMilestones ?? 0 }}
                                        </span>
                                    </div>
                                    <span class="milestonesTextSpan">{{ __('Your milestones') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="tabs ctr">
                            <div class="tabIcon taskIcon">
                                <img class="icons"
                                    src="{{ asset('assets/custom/libs/@fontawesome/fontawesome-free/svgs/solid/tasks.svg') }}"
                                    alt="logo" />

                            </div>
                            <div class="tabTexts">
                                {{ __('Your tasks') }}
                            </div>
                            <div class="tabTexts tabNumCounter">
                                <span>
                                    {{ $totalTask ?? 0 }}
                                </span>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card min-h">
                            <div class="card-header">
                                Statistics
                            </div>
                            <div class="card-body p-3">
                                <div class="divStatisticsButtons">
                                    <div>
                                        <input type="hidden" id="yearSelect" name="yearSelect"
                                            value="{{ collect($averageTimesKeys)->sortDesc()->first() }}">
                                        <i class="fa-solid fa-chevron-down alignArrowSelect"></i>
                                    </div>
                                    <div class="formControlModified form-control" id="yearDropdown"
                                        style="cursor: pointer;">
                                        <span
                                            id="yearDisplay">{{ collect($averageTimesKeys)->sortDesc()->first() }}</span>
                                    </div>

                                    <!-- Lista de años como opciones (Oculto inicialmente) -->
                                    <div id="yearList" class="yearListDiv">
                                        @foreach (collect($averageTimesKeys)->sortDesc() as $year)
                                            <div class="yearOption" data-year="{{ $year }}"
                                                style="padding: 5px; cursor: pointer;">{{ $year }}</div>
                                        @endforeach
                                    </div>

                                    <button onclick="updateChart('monthly')"
                                        class="marginRight1 btn btn-primary">{{ __('Monthly') }}
                                    </button>
                                    <button onclick="updateChart('quarterly')"
                                        class="marginRight1 btn btn-primary">{{ __('Quarterly') }}</button>
                                    <button onclick="updateChart('yearly')"
                                        class="btn btn-primary">{{ __('Yearly') }}</button>
                                </div>
                                <canvas id="myChart" style="height: 400px; width:100%"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card min-h">
                                <div class="card-header">
                                    <img src="{{ asset('assets/img/salesManager.png') }}" class="comercialTecIcons" />
                                    <span class="titleTecAndCom">{{ __('Sales managers') }}</span>
                                </div>
                                <div class="modifiedDivTecAndCom top-10-scroll" id="contentCom">
                                    <input type="text" class="filter-input" id="filterComerciales"
                                        placeholder="{{ __('Filter sales managers') }}"
                                        oninput="filterList('filterComerciales', 'contentCom')" />

                                    @foreach ($comerciales as $comercial)
                                        <div class="comercialAndTechnicians">
                                            <div class="ppcontainer">
                                                <img alt="{{ $comercial->name }}" class="profilePicture"
                                                    @if ($comercial->avatar) src="{{ asset($comercial->avatar) }}" @else avatar="{{ $comercial->name }}" @endif>
                                            </div>
                                            <div class="textContent">
                                                <span class="fullName">{{ $comercial->name }}</span>
                                                <span class="emailName">{{ $comercial->email }}</span>
                                            </div>
                                        </div>
                                    @endforEach
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card min-h">
                                <div class="card-header">
                                    <img src="{{ asset('assets/img/technicians.png') }}" class="comercialTecIcons" />
                                    <span class="titleTecAndCom">{{ __('Technicians') }}</span>
                                </div>
                                <input type="text" class="filter-input" id="filterTechnicians"
                                    placeholder="{{ __('Filter technicians') }}"
                                    oninput="filterList('filterTechnicians', 'contentTec')" />
                                <div class="modifiedDivTecAndCom top-10-scroll" id="contentTec">

                                    @foreach ($technicians as $technician)
                                        <div class="comercialAndTechnicians">
                                            <div class="ppcontainer">
                                                <img alt="{{ $technician->name }}" class="profilePicture"
                                                    @if ($technician->avatar) src="{{ asset($technician->avatar) }}" @else avatar="{{ $technician->name }}" @endif>
                                            </div>
                                            <div class="textContent">
                                                <span class="fullName">{{ $technician->name }}</span>
                                                <span class="emailName">{{ $technician->email }}</span>
                                            </div>
                                        </div>
                                    @endforEach

                                </div>
                            </div>
                        </div>
                    @else
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card mb-0 mt-3 text-center text-white bg-info">
                                    <div class="card-body">
                                        <h5 class="card-title mb-0">
                                            {{ __('There is no active Workspace. Please create Workspace from right side menu.') }}
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>
        @endif
    </section>
@endsection
@push('scripts')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="{{ asset('assets/custom/libs/nicescroll/jquery.nicescroll.min.js') }} "></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

    <script>
        $(document).ready(function() {
            if ($(".modifiedDivTecAndCom").length) {
                $(".modifiedDivTecAndCom").css({
                    "max-height": 300
                }).niceScroll();
            }

            $("#yearDropdown").click(function() {
                $("#yearList").toggle();
            });

            // Cuando se selecciona un año, actualiza el input y la vista actual sin cambiar la modalidad
            $(".yearOption").click(function() {
                let selectedYear = $(this).data("year");

                $("#yearSelect").val(selectedYear); // Actualiza el input oculto
                $("#yearDisplay").text(selectedYear); // Muestra el año seleccionado
                $("#yearList").hide(); // Oculta la lista de años

                updateYear(); // Actualiza la gráfica sin cambiar la vista
            });

            // Ocultar la lista si se hace clic fuera de ella
            $(document).click(function(event) {
                if (!$(event.target).closest("#yearDropdown, #yearList").length) {
                    $("#yearList").hide();
                }
            });
        });
    </script>
    <script>
        // all average data 
        var averageTimes = @json($averageTimes);
        let selectedYear = document.getElementById('yearSelect').value;
        //updateChartData(averageTimes[selectedYear]); // Inicializa con el primer año

        function updateYear() {
            let selectedYear = $("#yearSelect").val();

            if (!averageTimes[selectedYear]) {
                console.log(`No hay datos para el año ${selectedYear}`);
                return;
            }

            // Mantiene la vista activa cuando cambia el año
            updateChart(currentView);
        }

        function updateChart(view) {
            let selectedYear = $("#yearSelect").val();

            if (!averageTimes[selectedYear]) {
                console.log(`No hay datos para el año ${selectedYear}`);
                return;
            }

            let data = averageTimes[selectedYear];

            // Mantiene la vista seleccionada
            currentView = view;

            if (view === 'monthly') {
                updateChartData(data.months, "{{ __('Month') }}");
            } else if (view === 'quarterly') {
                updateChartData(data.quarters, "{{ __('Quarter') }}");
            } else if (view === 'yearly') {
                updateYearlyChart(data.yearly);
            }
        }

        function updateYearlyChart(data) {
            if (!window.chart) {
                console.log("Error: El gráfico aún no ha sido inicializado.");
                return;
            }

            if (!data) {
                console.log("No hay datos disponibles para la vista anual.");
                return;
            }

            let selectedYear = $("#yearSelect").val(); // Obtener el año seleccionado

            let labels = [selectedYear]; // Mostrar el año actual en el eje X
            let tiempo_inicio = [data.averageStartUp || 0];
            let tiempo_bueno = [data.averageWorking || 0];
            let retraso = [data.averageDelay || 0];

            // Mantener las barras apiladas
            window.chart.config.type = 'bar';
            window.chart.options.scales.x.stacked = true;
            window.chart.options.scales.y.stacked = true;

            window.chart.data.labels = labels;
            window.chart.data.datasets[0].data = tiempo_inicio;
            window.chart.data.datasets[1].data = tiempo_bueno;
            window.chart.data.datasets[2].data = retraso;

            window.chart.options.plugins.title.text = `{{ __('Annual average') }} (${selectedYear})`;
            window.chart.update();
        }


        function updateChartData(data, labelType) {
            if (!window.chart) {
                console.log("Error: El gráfico aún no ha sido inicializado.");
                return;
            }

            if (!data) {
                console.log("No hay datos disponibles para la vista seleccionada.");
                return;
            }

            // Ordenar etiquetas correctamente
            const monthOrder = ["January", "February", "March", "April", "May", "June", "July", "August", "September",
                "October", "November", "December"
            ];
            const quarterOrder = ["Q1", "Q2", "Q3", "Q4"];

            let labels = Object.keys(data);

            if (labelType === "Meses") {
                labels.sort((a, b) => monthOrder.indexOf(a) - monthOrder.indexOf(b));
            } else if (labelType === "Trimestres") {
                labels.sort((a, b) => quarterOrder.indexOf(a) - quarterOrder.indexOf(b));
            }

            let tiempo_inicio = [];
            let tiempo_bueno = [];
            let retraso = [];

            labels.forEach(periodo => {
                let periodoData = data[periodo] || {};
                tiempo_inicio.push(periodoData.averageStartUp || 0);
                tiempo_bueno.push(periodoData.averageWorking || 0);
                retraso.push(periodoData.averageDelay || 0);
            });

            window.chart.config.type = 'bar';
            window.chart.options.scales.x.stacked = true;
            window.chart.options.scales.y.stacked = true;

            window.chart.data.labels = labels;
            window.chart.data.datasets[0].data = tiempo_inicio;
            window.chart.data.datasets[1].data = tiempo_bueno;
            window.chart.data.datasets[2].data = retraso;

            window.chart.options.plugins.title.text = `{{ __('Average per') }} ${labelType}`;
            window.chart.update();
        }

        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('myChart').getContext('2d');

            window.chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                            label: "{{ __('Starting time') }}",
                            data: [],
                            backgroundColor: 'rgba(211, 211, 211, 0.8)',
                            hidden: false
                        },
                        {
                            label: "{{ __('On time') }}",
                            data: [],
                            backgroundColor: 'rgba(201, 237, 185, 0.8)',
                            hidden: false
                        },
                        {
                            label: "{{ __('Delay') }}",
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
                                generateLabels: function(chart) {
                                    let labels = Chart.defaults.plugins.legend.labels.generateLabels(
                                        chart);

                                    labels.push({
                                        text: "{{ __('Show Values') }}",
                                        fillStyle: 'black',
                                        strokeStyle: 'black',
                                        hidden: !chart.options.plugins.datalabels.display,
                                        datasetIndex: -1
                                    });

                                    return labels;
                                }
                            },
                            onClick: function(e, legendItem, legend) {
                                if (legendItem.datasetIndex === -1) {
                                    let currentDisplay = legend.chart.options.plugins.datalabels
                                        .display;
                                    legend.chart.options.plugins.datalabels.display = !currentDisplay;

                                    legend.options.labels.generateLabels(legend.chart);
                                    legend.chart.update();
                                } else {
                                    let dataset = legend.chart.data.datasets[legendItem.datasetIndex];
                                    dataset.hidden = !dataset.hidden;
                                    legend.chart.update();
                                }
                            }
                        },
                        title: {
                            display: true,
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
                                text: 'Días'
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

            let selectedYear = document.getElementById('yearSelect').value;
            updateChart('monthly');
        });
    </script>
    <script>
        function filterList(inputId, containerId) {
            const input = document.getElementById(inputId);
            const filter = input.value.toLowerCase();
            const container = document.getElementById(containerId);
            const items = container.getElementsByClassName('comercialAndTechnicians');

            for (let i = 0; i < items.length; i++) {
                const name = items[i].getElementsByClassName('fullName')[0];
                const email = items[i].getElementsByClassName('emailName')[0];
                const emailPrefix = email.innerHTML.split('@')[0].toLowerCase();
                if (filter === "" || name.innerHTML.toLowerCase().indexOf(filter) > -1 || emailPrefix.indexOf(filter) > -
                    1) {
                    items[i].style.display = "";
                } else {
                    items[i].style.display = "none";
                }
            }
        }

        document.getElementById('filterComerciales').addEventListener('input', function() {
            filterList('filterComerciales', 'contentCom');
        });

        document.getElementById('filterTechnicians').addEventListener('input', function() {
            filterList('filterTechnicians', 'contentTec');
        });
    </script>
@endpush
