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
    margin-bottom:20px ;
}
.tabs{
    height: 100%;
    width: 32%;
    background-color: F7F8F9;
    /* border: 1px solid magenta; */
    /* background-color: magenta; */
    /* border: 1px solid red; */
    border-radius: 15px;
    filter: drop-shadow(0px 1px 3px #dbdbdb);
}

.ctr{
    display: flex;
    align-items: center;
    
  
}
.tabIcon{
    margin-left: 30px;
    width: 50px;
    height: 50px;
    background-color: #AA182C;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 16px;
    filter: drop-shadow(0px 1px 3px rgba(0, 0, 0, 0.2));
}

.icons{
    width: 35px;
    height: 32px;
    filter: invert(1);
}
.projectIcon{
    background-color: #8dd656;
}
.milestoneIcon{
    background-color:rgb(174 154 247);
}
.taskIcon{
    background-color: #72c8d4;
}
.tabTexts{
    margin-left: 20px;
    font-size: 22px;
    font-weight: 800;
}
.tabNumCounter{
    background-color: white;
    filter: drop-shadow(3px 2px 0px rgba(0, 0, 0, 0.4));
    display: flex;
    align-items: center;
    width: 28px;
    height: 30px;
    justify-content: center;
    border-radius: 7px;
    filter: box-shadow(0px 2px 8px #c2c2c2);
}
.tutorialPartContainer{
    width: 100%;
    height: 600px;
    /* border: 1px solid black; */
    justify-content: space-between;
}
.tutorialPartCard{
    border: 1px solid #DFDEDE;
    display: flex;
    align-items: center;
    flex-direction: column;
    border-radius: 18px;
    width: 32%;
    height: 570px;
    /* border: 1px solid red; */
    background-position: center; 
    background-repeat: no-repeat; 
    background-size: cover; 
    box-shadow: inset 0px 8px 4px rgba(0, 0, 0, 0.3),0px 4px 10px rgba(0, 0, 0, 0.5); ; 
    
}
.tutorialPartCardTitle{
    box-shadow: inset 0px 8px 4px rgba(0, 0, 0, 0.3); 
    width: 251px;
    height: 102px;
    background-color: white;
    position: relative;
    top: 0;
    /* left: 24.5%; */
    border-bottom-left-radius: 20px;
    border-bottom-right-radius: 20px;
    display: flex;
    align-content: center;
    justify-content: center;
    align-items: center;
    font-weight: bold;
}
.tutorialPartCardTitle>h2{
    color: #980200;
    font-size: x-large;
    font-weight: 800;
}
.tutorialPartCardContent{
    background-color: #0000004d;
    height: 150px;
    width: 350px;
    position: relative;
    /* left: 13.5%; */
    top: 18%;
    border: 1px solid black;
    display: flex;
    border-radius: 13px;
    justify-content: center;
    align-items: center;
    border-radius: 13px;
    box-shadow: 0px 8px 2px rgb(0 0 0 / 22%);
}
.tutorialPartCardContent>span{
    color: white;
    text-align: center;
    padding: 10px;
    font-size: medium;
}
.projectTutorials{
    background-image: url("{{ asset('assets/img/backgroundTutorialProject.png') }}");
}
.milestoneTutorials{
    background-image: url("{{ asset('assets/img/backgroundTutorialTasksMilestones.png') }}");
}
.tasksTutorials{
    
    background-image: url("{{ asset('assets/img/backgroundTutorialTasks.png') }}");  
}
.stickyComercialTec{
    width:97%;
    height: 100px;
    background-color: F8FAF9;
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    position: absolute;
    bottom: 0;

}
.dropDownCT{
    background-color: #F8FAF9;
    box-shadow: inset 0px 8px 2px rgb(0 0 0 / 22%);
    /* border: 1px solid red; */
    border-top-left-radius: 23px;
    border-top-right-radius: 23px;
    transition: height 0.5s ease-out, transform 0.5s ease-out;
}
.dropDownCT.show {
    height: 469px;
    transform: translateY(0);
}

.dropDownCT.hide {
    height: 0;
    transform: translateY(100%);
}
.dropdownHeaders{
    display: flex;
    align-content: center;
    align-items: center;
    justify-content: center;
    height: 100%;
    position: relative;
}
.dropdownHeaders>span{
    font-size: 20px;
    font-weight: 600;
    margin-left: 10px;
}
.comercial{
    width: 40%;
    height: 50px;
    /* background-color: #AA182C; */
}
.technicians{
    width: 40%;
    height: 50px;
    /* background-color: yellow; */
}
.comercialTecIcons{
    height: 23px;
    width: 34px;
    display: inline-flex;
}
.dropdownArrow{
    /* transform: rotate(0deg); */
    transition: transform 0.3s ease-out, top 0.3s ease-out;
    height: 45px;
    width: 34px;
    position: absolute;
    right: 26px;
    /* top: -2px; */

    transform: rotate(180deg);
    top: 13px;
}
.dropdownArrow.rotated {
    /* transform: rotate(180deg);
    top: 13px; */

    transform: rotate(0deg);
    top: -2px;
}
.dropdownContent {
    height: 0;
    overflow: hidden;
    transition: height 0.3s ease-out;
}
.dropdownContent.show {
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
.dropdownContent.show::-webkit-scrollbar {
    width: 0px;
    background: transparent; /* Opcional: para asegurarse de que el área del scrollbar no sea visible */
}
.shown {
    transform: translateY(-470px);
}


.comercialAndTechnicians {
    background-color: #F8FAF9;
    margin-top: 20px;
    height: 95px;
    width: 85%;
    border-radius: 9px;
    box-shadow: 0px 0px 5px rgb(0 0 0 / 22%);
    display: flex;
    align-items: center;
    justify-content: flex-start; /* Ensure alignment */
    gap: 20px; /* Add consistent spacing */
    padding: 16 15px; /* Add padding for content separation */
}
.ppcontainer {
    flex-shrink: 0; /* Prevent resizing of profile picture container */
}
.profilePicture {
    width: 60px;
    height: 60px;
    background-color: red;
    border-radius: 100%;
    margin-left: 12px;
}
.textContent {
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    flex-grow: 1; /* Allow the text content to take remaining space */
    text-align: center;
}
.fullName {
    font-size: larger;
    font-weight: 600;
    margin-bottom: 5px; /* Adjust spacing */
    white-space: nowrap; /* Prevent text from breaking */
    overflow: hidden; /* Ensure overflow is hidden */
    text-overflow: ellipsis; /* Add ellipsis for long names */
}
.emailName {
    font-size: large;
    color: #949494;
    white-space: nowrap; /* Prevent text from breaking */
    overflow: hidden; /* Ensure overflow is hidden */
    text-overflow: ellipsis; /* Add ellipsis for long emails */
}
.statusContainer{
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    margin-left: 11%;
    height: 100%;
    width: 27%;
    justify-content: space-evenly;

}
.status{
    /* background-color: aqua; */
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
.hold{
    background-color: #e06c71;
}
.progressstat{
    background-color: #d3d3d3;
}
.ended{
    background-color: #c9edb9;
}
.statusNum{
    color:black
}
.statusNumContainer{
    background-color: white;
    border-radius: 100%;
    padding-left: 5px;
    padding-right: 5px;
}
.statusText{
    padding: 2px;
}
.testContent{
    background-color: yellow;
}
.hiddenTuto{
    display: none;
}
@media screen and (min-width:1440px) and (max-width: 1490px){
    /* *{
        border: 3px solid green;
    } */
    .status{
        width: 95%;
    }
    .stickyComercialTec{
        height: 0px;
    }
}
.filter-input {
    width: 80%;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
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
            <div class="row" onclick="resetCards(event)">
                <div class="col-lg-12 col-md-12">
                    <!-- <div class="row"> -->
                        
                        <div class="summary">
                            <div class="tabs ctr">
                            <div class="tabIcon projectIcon" >
                                <img class="icons" src="{{asset('assets/custom/libs/@fontawesome/fontawesome-free/svgs/solid/project-diagram.svg')}}" alt="logo" />

                                </div>
                                <div class="tabTexts">
                                    {{__('Projects')}}
                                </div>
                                <div class="tabTexts tabNumCounter">
                                    <span>
                                    {{ $totalProject  ?? 0}}
                                    </span>
                                    
                                </div>
                                <div class="statusContainer">
                                    <div class="status hold ctr">
                                        <span class="statusText">{{__('OnHold')}}</span>
                                        <div class="statusNumContainer">
                                        <span class="statusNum">{{$projectProcess['OnHold'] ?? 0}}</span>
                                        </div>
                                    </div>
                                    <div class="status progressstat ctr">
                                        <span class="statusText">{{__('Ongoing')}}</span>
                                        <div class="statusNumContainer">
                                        <span class="statusNum">{{$projectProcess['Ongoing']?? 0}}</span>
                                        </div>
                                    </div>
                                    <div class="status ended ctr">
                                        <span class="statusText">{{__('Finished')}}</span>
                                        <div class="statusNumContainer">
                                        <span class="statusNum">{{$projectProcess['Finished']?? 0}}</span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="tabs ctr">
                            <div class="tabIcon milestoneIcon" >
                            <img class="icons" src="{{asset('assets/custom/libs/@fontawesome/fontawesome-free/svgs/solid/file-alt.svg')}}" alt="logo" />
                            </div>
                                <div class="tabTexts">
                                {{__('Milestones')}}
                                </div>
                                <div class="tabTexts tabNumCounter">
                                    <span>
                                    {{ $totalMilestones  ?? 0}}
                                    </span>
                                    
                                </div>
                            </div>
                            <div class="tabs ctr">
                                <div class="tabIcon taskIcon" >
                                <img class="icons" src="{{asset('assets/custom/libs/@fontawesome/fontawesome-free/svgs/solid/tasks.svg')}}" alt="logo" />

                                </div>
                                <div class="tabTexts">
                                {{__('Tasks')}}
                                </div>
                                <div class="tabTexts tabNumCounter">
                                    <span>
                                    {{ $totalTask  ?? 0}}
                                    </span>
                                    
                                </div>
                            </div>
                        </div>
                       
                <div>
                <!-- //Nuevo Carrusel Marc  -->
                <div class="tutorialPartContainer ctr" >
                    <div class="projectTutorials tutorialPartCard" id="projectTutorial" onclick="expandCard(this, event)">
                        <div class="tutorialPartCardTitle">
                            <h2>{{__('Projects')}}</h2>
                        </div>
                        <div class="tutorialPartCardContent initial-content">
                        
                            <span id="defaultTextProject">{{__('Learn how to create, delete and visualize projects of any type')}}</span>
                        </div>
                        <div class="hiddenTuto" id="hiddenTutoProject">@include('tutorial.projectTutorial')</div>
                    </div>
                    <div class="milestoneTutorials tutorialPartCard" id="milestoneTutorials" onclick="expandCard(this, event)">
                    <div class="tutorialPartCardTitle">
                    <h2>{{__('Milestones')}}</h2>
                    </div>
                    <div class="tutorialPartCardContent initial-content">
                  
                    <span id="defaultTextMilestone">{{__('Learn how to create, delete and edit job sheets, as well as view their current status')}}</span>
                    </div>
                    <div class="hiddenTuto" id="hiddenTutoMilestone">@include('tutorial.milestoneTutorial')</div>
                    </div>
                    <div class="tasksTutorials tutorialPartCard" id="tasksTutorials" onclick="expandCard(this, event)">
                    <div class="tutorialPartCardTitle">
                    <h2>{{__('Tasks')}}</h2>
                    </div>
                    <div class="tutorialPartCardContent initial-content">
                    <span id="defaultTextTask">{{__('Learn how to input the hours dedicated to each task')}}</span>
                    </div>
                    <div class="hiddenTuto" id="hiddenTutoTask">@include('tutorial.taskTutorial')</div>

                    </div>
                </div>
                <div class="stickyComercialTec">
                    <div class="comercial dropDownCT">
                        <div class="dropdownHeaders" id="headerCom" onclick="toggleContentCom()">
                            <img src="{{asset('assets/img/salesManager.png')}}" class="comercialTecIcons"/>
                            <span>{{__('Sales managers')}}</span>
                            <img src="{{asset('assets/images/sort-down-solid.svg')}}" class="dropdownArrow"/>
                        </div>
                        <div class="dropdownContent" id="contentCom">
                            <input type="text" class="filter-input" id="filterComerciales" placeholder="Filtrar comerciales..." oninput="filterList('filterComerciales', 'contentCom')">
                            <!-- ejemplo comercial  -->
                            <!-- <div class="comercialAndTechnicians">
                                <div class="ppcontainer">
                                    <div class="profilePicture"></div>
                                </div>
                                <div class="textContent">
                                    <span class="fullName">Pedro Perez Martinez</span>
                                    <span class="emailName">pedropeMartinez@alsina.com</span>
                                </div>
                            </div> -->
                            <!-- final ejemplo comercial  -->
                            <!-- Info real comercial  -->
                            @forEach($comerciales as $comercial)
                                <div class="comercialAndTechnicians">
                                    <div class="ppcontainer">
                                        <img alt="{{ $comercial->name }}" class="profilePicture"
                                            @if ($comercial->avatar) src="{{ asset( $comercial->avatar) }}" @else avatar="{{ $comercial->name }}" @endif>
                                    </div>
                                    <div class="textContent">
                                        <span class="fullName">{{$comercial->name}}</span>
                                        <span class="emailName">{{$comercial->email}}</span>
                                    </div>
                                </div> 
                            @endforEach
                            <!-- final info real comercial  -->
                        </div>
                    </div>
                    <div class="technicians dropDownCT">
                        <div class="dropdownHeaders" id="headerTec" onclick="toggleContentTec()">
                            <img src="{{asset('assets/img/technicians.png')}}" class="comercialTecIcons"/>
                            <span>{{__('Technicians')}}</span>
                            <img src="{{asset('assets/images/sort-down-solid.svg')}}" class="dropdownArrow"/>
                        </div>
                        <div class="dropdownContent" id="contentTec">
                            <input type="text" class="filter-input" id="filterTechnicians" placeholder="Filtrar técnicos/as..." oninput="filterList('filterTechnicians', 'contentTec')">
                            @forEach($technicians as $technician)
                                <div class="comercialAndTechnicians">
                                    <div class="ppcontainer">
                                        <img alt="{{ $technician->name }}" class="profilePicture"
                                            @if ($technician->avatar) src="{{ asset( $technician->avatar) }}" @else avatar="{{ $technician->name }}" @endif>
                                    </div>
                                    <div class="textContent">
                                        <span class="fullName">{{$technician->name}}</span>
                                        <span class="emailName">{{$technician->email}}</span>
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
<script>
function toggleContentCom() {
    const contentCom = document.getElementById("contentCom");
    const comercial = document.querySelector(".comercial");
    const arrow = document.querySelector("#headerCom .dropdownArrow");
    contentCom.classList.toggle("show");
    contentCom.classList.toggle("hide");
    comercial.classList.toggle("shown");
    arrow.classList.toggle("rotated");
    console.log(contentCom.classList.contains("show") ? "desplegado" : "contraido");
}

function toggleContentTec() {
    const contentTec = document.getElementById("contentTec");
    const technicians = document.querySelector(".technicians");
    const arrow = document.querySelector("#headerTec .dropdownArrow");
    contentTec.classList.toggle("show");
    contentTec.classList.toggle("hide");
    technicians.classList.toggle("shown");
    arrow.classList.toggle("rotated");
    console.log(contentTec.classList.contains("show") ? "desplegado" : "contraido");
}

function expandCard(clickedCard, event) {
    event.stopPropagation(); // Prevent the click event from propagating to the container
    const cards = document.querySelectorAll('.tutorialPartCard');
    const titles = document.querySelectorAll('.tutorialPartCardTitle');

    cards.forEach(card => {
        card.style.transition = 'width 0.5s ease-in-out'; // Add transition for width change
        const hiddenTuto = card.querySelector('.hiddenTuto');
        const defaultText = card.querySelector('span[id^="defaultText"]');
        const initialContent = card.querySelector('.tutorialPartCardContent.initial-content');
        if (card === clickedCard) {
            card.style.width = '80%';
            if (hiddenTuto) hiddenTuto.style.display = 'flex';
            if (defaultText) defaultText.style.display = 'none';
            if (initialContent) initialContent.style.display = 'none';
        } else {
            card.style.width = '10%';
            if (hiddenTuto) hiddenTuto.style.display = 'none';
            if (defaultText) defaultText.style.display = 'none';
            if (initialContent) initialContent.style.display = 'none';
        }
    });

    titles.forEach(title => {
        const parentCard = title.parentElement;
        title.style.transition = 'width 0.5s ease-in-out'; // Add transition for width change
        if (parentCard.style.width === '10%') {
            title.style.width = '100%';
            title.style.borderTopLeftRadius = '10px';
            title.style.borderTopRightRadius = '10px';
            title.style.borderBottomRightRadius = '0px';
            title.style.borderBottomLeftRadius = '0px';
            title.style.textAlign = 'center';
        } else {
            title.style.width = '';
            title.style.borderRadius = '';
        }
    });

    // Display the hiddenTuto content based on the card's ID
    const hiddenTutoProject = document.getElementById('hiddenTutoProject');
    const hiddenTutoMilestone = document.getElementById('hiddenTutoMilestone');
    const hiddenTutoTask = document.getElementById('hiddenTutoTask');

    hiddenTutoProject.style.display = 'none';
    hiddenTutoMilestone.style.display = 'none';
    hiddenTutoTask.style.display = 'none';

    switch (clickedCard.id) {
        case 'projectTutorial':
            hiddenTutoProject.style.display = 'flex';
            break;
        case 'milestoneTutorials':
            hiddenTutoMilestone.style.display = 'flex';
            break;
        case 'tasksTutorials':
            hiddenTutoTask.style.display = 'flex';
            break;
        default:
            break;
    }
}

function resetCards(event) {
    const cards = document.querySelectorAll('.tutorialPartCard');
    const initialContents = document.querySelectorAll('.initial-content');
    const titles = document.querySelectorAll('.tutorialPartCardTitle');

    cards.forEach(card => {
        card.style.width = '32%';
        // Remove dynamically added content
        const dynamicContent = card.querySelector('.tutorialPartCardContent:not(.initial-content)');
        if (dynamicContent) {
            dynamicContent.remove();
        }
        const hiddenTuto = card.querySelector('.hiddenTuto');
        const defaultText = card.querySelector('span[id^="defaultText"]');
        if (hiddenTuto) hiddenTuto.style.display = 'none';
        if (defaultText) defaultText.style.display = 'flex';
    });

    initialContents.forEach(content => {
        const parentCard = content.parentElement;
        if (parentCard.style.width === '32%') {
            content.style.display = 'flex';
        } else {
            content.style.display = 'none';
        }
    });

    titles.forEach(title => {
        title.style.width = '';
        title.style.borderRadius = '';
        title.style.textAlign = '';
    });
}

document.addEventListener("DOMContentLoaded", () => {
    const slides = document.querySelectorAll(".carousel-slide");
    const carousel = document.querySelector(".carousel");

    // Set the default background image (custom image)
    const defaultImageUrl = "{{ asset('assets/img/backgroundTutorial/img mix large size.png') }}";
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

function filterList(inputId, containerId) {
    const input = document.getElementById(inputId);
    const filter = input.value.toLowerCase();
    const container = document.getElementById(containerId);
    const items = container.getElementsByClassName('comercialAndTechnicians');

    for (let i = 0; i < items.length; i++) {
        const name = items[i].getElementsByClassName('fullName')[0];
        const email = items[i].getElementsByClassName('emailName')[0];
        const emailPrefix = email.innerHTML.split('@')[0].toLowerCase();
        if (filter === "" || name.innerHTML.toLowerCase().indexOf(filter) > -1 || emailPrefix.indexOf(filter) > -1) {
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