@extends('layouts.admin')

@section('page-title')
    {{ __('Dashboard') }}
@endsection
@php
    $client_keyword = Auth::user()->getGuard() == 'client' ? 'client.' : '';
@endphp
<style>
    .pro-status {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    @media screen and (max-width: 1200px) and (min-width:1000px){
        .taskRowWidth{
            width: 32% !important;
        }
        .colWidthTask{
            width: 49% !important;
        }
    }
</style>

@section('content')
    <section class="section">
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
                    <div class="row">
                        <div class="col-xl-4 col-md-6 col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="col-sm-12 d-flex">
                                        <div class="col-sm-6">
                                            <div class="theme-avtar bg-success">
                                                <i class="fa-solid fa-diagram-project bg-success text-white"></i>
                                            </div>
                                            <p class="text-muted text-sm"></p>
                                            <h6 class="">{{ __('Projects') }}</h6>
                                            <h3 class="mb-0">{{ $totalProject }} <span
                                                    class="text-success text-sm"></span></h3>
                                        </div>
                                        <div class="col-sm-6 text-center pro-status">
                                        <div class="col-auto m-1 pr-1">
                                                <span
                                                    class="badge rounded-pill bg-warning d-inline">{{ __('OnHold') }}</span>
                                                <h3 class="text-center d-inline">{{ 1 }}
                                                </h3>
                                            </div>
                                            <div class="col-auto m-1 pr-1">
                                                <span
                                                    class="badge rounded-pill bg-secondary d-inline">{{ __('Ongoing') }}</span>
                                                <h3 class="text-center d-inline">{{ 5 }} </h3>

                                            </div>
                                            <div class="col-auto m-1 pr-1">
                                                <span
                                                    class="badge rounded-pill bg-success d-inline">{{ __('Finished') }}</span>
                                                <h3 class="d-inline">{{ 2 }} </h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6 col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="theme-avtar" style="background-color: #B197FC !important;">
                                        <i class="fa-solid fa-file-lines fa-xl text-white" style="color: #B197FC;"></i>
                                    </div>
                                    <p class="text-muted text-sm"></p>
                                    <h6 class="">{{ __('Milestones') }}</h6>
                                    <h3 class="mb-0">{{ $totalMilestones }} <span class="text-success text-sm"></span>
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6 col-sm-6 taskRowWidth">
                            <div class="card">
                                <div class="card-body">
                                    <div class="theme-avtar bg-info">
                                        <i class="fas fa-tasks bg-info text-white"></i>
                                    </div>
                                    <p class="text-muted text-sm "></p>
                                    <h6 class="">{{ __('Tasks') }}</h6>
                                    <h3 class="mb-0">{{ $totalTask }} <span class="text-success text-sm"></span></h3>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="theme-avtar bg-danger">
                                        <i class="fa-solid fa-user-tie bg-danger text-white"></i>
                                    </div>
                                    <p class="text-muted text-sm"></p>
                                    <h6 class="">{{ __('Sales managers') }}</h6>
                                    <h3 class="mb-0">{{ $totalSales }} <span class="text-success text-sm"></span></h3>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="theme-avtar bg-primary">
                                        <i class="fa-solid fa-helmet-safety bg-primary text-white"></i>
                                    </div>
                                    <p class="text-muted text-sm"></p>
                                    <h6 class="">{{ __('Technicians') }}</h6>
                                    <h3 class="mb-0">{{ $totalTechni }} <span class="text-success text-sm"></span>
                                    </h3>
                                </div>
                            </div>
                        </div>


                        <div class="col-lg-6 col-md-6 colWidthTask">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-9">
                                            <h5 class="">
                                                {{ __('Tasks') }}
                                            </h5>
                                        </div>
                                        <div class="col-auto d-flex justify-content-end">
                                            <div class="">
                                                <small><b>{{ $completeTask }}</b>
                                                    {{ __('Tasks completed out of') }}
                                                    {{ $totalTask }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-centered table-hover mb-0 animated">
                                            <tbody>
                                                @foreach ($tasks as $task)
                                                    <tr>
                                                        <td>
                                                            {{-- <div class=" my-1"><a
                                                                    href="{{ route('projects.task.board', [$currentWorkspace->slug, $task->project_id]) }}"
                                                                    class="text-body">{{ $taskTypes[$task->type_id]['name'] }}</a>
                                                            </div> --}}
                                                            @php
                                                                $isLate =
                                                                    strtotime($task->estimated_date) <
                                                                    strtotime(date('Y-m-d'));
                                                                $dateClass = $isLate ? 'danger' : 'success';
                                                                $formattedDate = date(
                                                                    'Y-m-d',
                                                                    strtotime($task->estimated_date),
                                                                );
                                                                $due_date =
                                                                    '<span class="text-' .
                                                                    $dateClass .
                                                                    '">' .
                                                                    $formattedDate .
                                                                    '</span>';
                                                                $icon =
                                                                    '<i class="fa-solid fa-calendar-check text-' .
                                                                    $dateClass .
                                                                    '"></i>';
                                                            @endphp

                                                            <span class="text-muted">
                                                                {!! $icon !!}
                                                                {!! $due_date !!}
                                                            </span>

                                                        </td>
                                                        <td>
                                                            <span class="text-muted">{{ __('Project') }}</span>
                                                            <div class=" mt-1 font-weight-normal">
                                                                {{ $task->project->name }}</div>
                                                        </td>
                                                        <td>
                                                            <span class="text-muted">{{ __('Assigned to') }}</span>
                                                            <div class="mt-1 font-weight-normal">
                                                                @foreach ($task->users() as $user)
                                                                    <span
                                                                        class="badge p-2 px-2 rounded bg-secondary">{{ $user->name }}</span>
                                                                @endforeach
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
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
