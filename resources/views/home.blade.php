@extends('layouts.admin')

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
    /* General Reset */
    .carousel {
        display: flex;
        position: relative;
        width: 100%;
        height: 650px;
        overflow: hidden;
        border: 2px solid #ccc;
        border-radius: 10px;
        background-color: #fff;
    }

.carousel-slide {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    transition: flex 0.5s ease-in-out, transform 0.5s ease-in-out;
    cursor: pointer;
    overflow: hidden;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    color: white;
}

.carousel-slide h2 {
    font-size: 20px;
    color: white;
}

.carousel-slide p {
    display: none;
    font-size: 1rem;
    margin: 0;
    color: #ccc;
}

/* Active Slide Styling */
.carousel-slide.active {
    flex: 7;
    color: #fff;
    background-color: rgb(0 0 0 / 70%);
}

.carousel-slide.active p {
    display: block;
}

.carousel-slide:first-child.active h2{
    position: absolute;
    left: 5%;
    top: 5%;
}

/* Style for the middle slide when active */
.carousel-slide:nth-child(2).active h2 {
    position: absolute;
    left: 20%;
    top: 5%;
    transform: translateX(-50%);
}

/* Style for the last slide when active */
.carousel-slide:last-child.active h2{
    position: absolute;
    left: 30%;
    top: 5%;
}
/* Inactive Slide Styling */
.carousel-slide:not(.active) {
    flex: 1;
    background-color: rgb(0 0 0 / 45%);
    box-shadow: 5px 0px 5px 0px rgb(0 0 0 / 45%);
    -webkit-box-shadow: 5px 0px 5px 0px rgb(0 0 0 / 45%);
    -moz-box-shadow: 5px 0px 5px 0px rgb(0 0 0 / 45%);
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

                <div>
                <div class="carousel">
                    <div class="carousel-slide" data-index="1" data-image="assets/img/backgroundTutorial/img projects large size.png">
                        <h2>Proyecto</h2>
                        @include('tutorial.projectTutorial')
                    </div>
                    <div class="carousel-slide" data-index="2" data-image="assets/img/backgroundTutorial/img milestones large size.png">
                        <h2>Hoja de encargo</h2>
                        @include('tutorial.milestoneTutorial')
                    </div>
                    <div class="carousel-slide" data-index="3" data-image="assets/img/backgroundTutorial/img task large size.png">
                        <h2>Tareas</h2>
                        @include('tutorial.taskTutorial')
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
<script>
document.addEventListener("DOMContentLoaded", () => {
    const slides = document.querySelectorAll(".carousel-slide");
    const carousel = document.querySelector(".carousel");

    // Set the default background image (custom image)
    const defaultImageUrl = "assets/img/backgroundTutorial/img mix large size.png";
    carousel.style.backgroundImage = `url('${defaultImageUrl}')`;

    // Add event listeners to slides
    slides.forEach((slide) => {
        slide.addEventListener("click", () => {
            // Remove active class from all slides
            slides.forEach((s) => s.classList.remove("active"));

            // Add active class to the clicked slide
            slide.classList.add("active");

            // Update the background image to the clicked slide's image
            const imageUrl = slide.getAttribute("data-image");
            carousel.style.backgroundImage = `url('${imageUrl}')`;
        });
    });
});
</script>