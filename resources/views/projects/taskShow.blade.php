@php
    $logo = \App\Models\Utility::get_file('avatars/');
    $logo_tasks = \App\Models\Utility::get_file('tasks/');
@endphp
<style>
    .firstDivIconTexts{
        padding-top: 8%;
        display: flex;
        padding-left: 11%;
    }
    .divIconTexts{
        display: flex;
        padding-left: 11%;
        padding-top: 5%;
    }
    .divIconModified{
        display: flex;
        padding-top: 5%;
        padding-left: 12%;
    }
    .customIconsStlye{
        color: #aa182c;
        font-size: 25px;
    }
    .textStyle{
        font-size: 16px;
        padding-left: 15%;
    }
    .textStyleModified{
        font-size: 16px;
        padding-left: 14%;
    }
    .userImg{
        width: 8%;
        margin-left: 14%;
        margin-top: -2px !important;
    }
    .textUserName{
        padding-left: 2%;
    }
    .resumeDivBackground{
        display: flex;
        width: 90%;
        height: 25%;
        border-radius: 10px;
        margin-left: 5%;
        margin-top: 5%;
        box-shadow: 0 0 5px 1px rgb(0 0 0 / 31%);
        -webkit-box-shadow: 0 0 5px 1px rgb(0 0 0 / 31%);
        -moz-box-shadow: 0 0 5px 1px rgb(0 0 0 / 31%);
    }
    .divInsideRightResume{
        margin-top: 7%;
        padding-left: 3%;
        display: flex;
    }
    .divInsideLeftResume{
        margin-top: 7%;
        display: flex;
        padding-left: 5%;
    }
    .iconsResume{
        font-size: 45px;
        color: #AA182C;
    }
    .textResume{
        padding-left: 5px;
    }
    .subtitleResume{
        margin-top: -16px;
        font-size: 12px;
        color: grey;
    }
    .divResumeAjustText{
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .subtitleResumeRight{
        padding-left: 8px;
        margin-top: -16px;
        font-size: 12px;
        color: grey;
    }
</style>
<div >
    @if ($taskDetail['workspace'] && $taskDetail['task'])
        <div >
            <div >
                <div class="firstDivIconTexts">
                    <i class="fa-solid fa-list-check customIconsStlye" ></i>
                    <p class="textStyle">
                        {{ $taskDetail['task_name'] }}
                    </p>
                </div>

                <div class="divIconTexts">
                    <i class="fa-solid fa-diagram-project customIconsStlye" ></i>
                    <p class="textStyleModified">
                        {{ $taskDetail['project']->name }}
                    </p>
                </div>

                <div class="divIconModified">
                    <i class="fa-solid fa-file-lines customIconsStlye"></i>
                    <p class="textStyle">
                        {{ $taskDetail['milestone'] }}
                    </p>
                </div>
                
            </div>
            <div>
                <div class="divIconModified">
                    <i class="fa-regular fa-calendar-days customIconsStlye" ></i>
                    <p class="textStyleModified">{{ \App\Models\Utility::dateFormat($taskDetail['task']->start_date) }}</p>
                </div>
                <div class="divIconTexts">
                    <i class="fa-solid fa-helmet-safety customIconsStlye" ></i>
                    <img 
                        @if ($taskDetail['assign_to']->avatar) src="{{ asset($logo . $taskDetail['assign_to']) }}" 
                        @else avatar="{{ $taskDetail['assign_to']->name }}" @endif
                        class="rounded-circle mt-1 userImg">
                    <span class="textUserName">{{ $taskDetail['assign_to']->name }}</span>
                </div>
            </div>
        </div>
        <div class="resumeDivBackground">
            <div class="divInsideLeftResume">
                <i class="fa-regular fa-calendar iconsResume" ></i>
                <div class="divResumeAjustText">
                    <p class="textResume">{{ $taskDetail['start_of_week'] }} - {{ $taskDetail['end_of_week'] }}</p>
                    <p class="subtitleResume">Periodo de la semana</p>
                </div>
            </div>
            <div class="divInsideRightResume">
                <i class="fa-solid fa-clock-rotate-left iconsResume" ></i>
                <div class="divResumeAjustText">
                    <p class="textResume">{{ $taskDetail['total_time_this_week'] }}</p>
                    <p class="subtitleResumeRight">Total Horas</p>
                </div>
            </div>  
        </div>
    @else
        <div >
            <div >
                <div >
                    <div>
                        <div >
                            <h1>404</h1>
                            <div>
                                {{ __('Page Not Found') }}
                            </div>
                            <div >
                                <p >
                                    {{ __("It's looking like you may have taken a wrong turn. Don't worry... it happens to the best of us. Here's a little tip that might help you get back on track.") }}
                                </p>
                                <div>
                                    <a  href="{{ route('home') }}">
                                    <i ></i> {{ __('Return Home') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    /*
    Not working and is not needed
    (function() {
        const d_week = new Datepicker(document.querySelector('.datepicker2'), {
            buttonClass: 'btn',
            todayBtn: true,
            clearBtn: true,
            format: 'yyyy-mm-dd',
        });
    })();*/
</script>
