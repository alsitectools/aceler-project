<head>
    <link rel="stylesheet" href="{{ asset('assets/css/milestone.css') }}">
</head>
@php
    $logo = \App\Models\Utility::get_file('users-avatar/');
    $logo_tasks = \App\Models\Utility::get_file('tasks/');
@endphp
<style>
    .ctr {
        display: flex;
        align-content: center;
        align-items: center;
    }

    .buttonFiles {
        background-color: #aa182c;
    }

    .buttonFiles:hover,
    .buttonFiles:focus,
    .buttonFiles:active {
        background-color: #8b0f23 !important;
    }
</style>
<div class="modal-body">
    @if ($currentWorkspace && $milestone)

        <div class="mt-3 mb-5">
            <div class="row">
                <div class="form-group col-md-12">
                    <fieldset class="custom-fieldset ctr"
                        style="display: flex; justify-content: space-between; align-items: center;">
                        <legend class="custom-legend">{{ __('Name') }}:</legend>
                        <h2 class="ps-2" style="font-size: 24px;">{{ $milestone->title }}</h2>
                        @if ($milestone->status == 3)
                            <label class="statusBadge bg-warning ">{{ __('For Review') }}</label>
                        @elseif ($milestone->status == 4)
                            <label class="bg-success statusBadge">{{ __('Finished') }}</label>
                        @else
                            <label class="statusBadge {{ $milestone->status == 1 ? 'bg-info' : 'bg-secondary' }}">
                                {{ $milestone->status == 1 ? __('To Do') : __('Ongoing') }}
                            </label>
                        @endif
                    </fieldset>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-8">
                    <fieldset class="custom-fieldset ctr">
                        <legend class="custom-legend">{{ __('Requested by') }}:</legend>
                        <h5 class="pt-2 ps-2"> {{ $salesManager->name }}</h5>
                        <img class="imgName" style="margin-left: 10px; width: 35px !important;"
                            @if ($salesManager->avatar) src="{{ asset($salesManager->avatar) }}"
                    @else
                    avatar="{{ $salesManager->name }}" @endif>

                    </fieldset>
                </div>
                <div class="form-group col-md-4">
                    <fieldset style="padding: 2px 16px" class="custom-fieldset ctr">
                        <legend class="custom-legend">{{ __('Assigned to') }}</legend>
                        <h5 class="ps-2 pt-2">
                            @if (isset($assignedToUser) && $assignedToUser->name)
                                {{ $assignedToUser->name }}
                                <img class="imgName" style="margin-left: 10px; width: 35px !important;"
                                    @if ($assignedToUser->avatar) src="{{ asset($assignedToUser->avatar) }}"
                    @else
                    avatar="{{ $assignedToUser->name }}" @endif>
                            @else
                                None
                            @endif
                        </h5>

                    </fieldset>
                </div>


            </div>
            <div class="row">
                <div class="form-group col-md-8">
                    <fieldset class="custom-fieldset ctr">
                        <legend class="custom-legend">{{ __('Project') }}:</legend>
                        <h5 class="pt-2 ps-2"> {{ $project->name }}</h5>
                    </fieldset>
                </div>
                <div class="form-group col-md-4">
                    <fieldset class="custom-fieldset ctr">
                        <legend class="custom-legend">{{ __('Workspace') }}:</legend>
                        <h5 class="pt-2 ps-2"> {{ $delegation_name }}</h5>
                    </fieldset>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-md-8">
                    <fieldset class="custom-fieldset ctr">
                        <legend class="custom-legend">{{ __('Description') }}:</legend>
                        <div class="pt-2 ps-2" style="white-space: pre-wrap; word-wrap: break-word;">{{ $milestone->summary }}</div>
                    </fieldset>
                </div>
                {{-- {test} --}}
                <div class="form-group col-md-4">
                    <fieldset class="custom-fieldset ctr">
                        <legend class="custom-legend">{{ __('Expected delivery date') }}:</legend>
                        <h5 class="ps-2 pt-2"> {{ $milestone->planned_end_date }}:</h5>
                    </fieldset>
                </div>

                {{-- endTest --}}
            </div>
            <div class="row">
                <div class="form-group col-md-4">
                    <fieldset class="custom-fieldset ctr">
                        <legend class="custom-legend">{{ __('Tasks') }}:</legend>
                        <div class="mt-3" id="taskListContainer">
                            @if (!empty($milestone->showMilestonetasks() && count($milestone->showMilestonetasks()) > 0))
                                @foreach ($milestone->showMilestonetasks() as $task)
                                    <div class="taskList p-target mb-2 col-sm-12 marginText">
                                        @php
                                            $isLate = strtotime($task->estimated_date ?? '') < strtotime(date('Y-m-d'));
                                            $dateClass = $isLate ? 'danger' : 'success';
                                            $icon = $isLate
                                                ? '<i class="ms-2 me-2 fa-solid fa-hourglass-end fa-xs text-' .
                                                    $dateClass .
                                                    '"></i>'
                                                : '<i class="ms-2 me-2 fa-solid fa-hourglass-start fa-xs text-' .
                                                    $dateClass .
                                                '"></i>'; @endphp {!! $icon !!} {{ __($task->task_name ?? $task) }}
                                    </div>
                                @endforeach
                            @else
                                <p class="ps-2 m-1 text-muted">
                                    {{ __('No tasks for this milestone.') }}</p>
                            @endif
                        </div>
                    </fieldset>
                </div>
                <div class="form-group col-md-8">
                    <fieldset class="custom-fieldset ctr pb-2">
                        <legend class="custom-legend">{{ __('Files') }}:</legend>
                        <div class="custom-file-container mt-3">
                            @if (!empty($milestoneFiles) && count($milestoneFiles) > 0)
                                @foreach ($milestoneFiles as $file)
                                    <div class="custom-file">
                                        <img src="{{ asset('assets/iconFilesTypes/' . $file->extension . '.png') }}"
                                            alt="{{ $file->extension }} icon" class="styleIconFiles">
                                        <p class="file-name">{{ $file->name }}</p>
                                        <a onclick="downloadFile({{ $project->id }}, '{{ $milestone->title }}', '{{ $file->file }}')"
                                            class="buttonFiles btn btn-sm">
                                            <i class="ti ti-download" style="color:white"></i>
                                        </a>
                                    </div>
                                @endforeach
                            @else
                                <p class="ps-2 m-1 text-muted">
                                    {{ __('No files uploaded for this milestone.') }}
                                </p>
                            @endif
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>
    @else
        <div class="container mt-5">
            <div class="card">
                <div class="card-body p-4">
                    <div class="page-error">
                        <div class="page-inner">
                            <h1>404</h1>
                            <div class="page-description">
                                {{ __('Page Not Found') }}
                            </div>
                            <div class="page-search">
                                <p class="text-muted mt-3">
                                    {{ __("It's looking like you may have taken a wrong turn. Don't worry... it happens to
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    the best of us. Here's a little tip that might help you get back on track.") }}
                                </p>
                                <div class="mt-3">
                                    <a class="btn-return-home badge-blue" href="{{ route('home') }}"><i
                                            class="fas fa-reply"></i> {{ __('Return Home') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
<script>
    function downloadFile(idProject, titleMilestone, file) {

        const downloadUrl = "{{ route('project.downloadFile') }}";
        $.ajax({
            url: downloadUrl,
            method: 'POST',
            data: {
                "idProject": idProject,
                "milestoneTitle": titleMilestone,
                "fileName": file,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    console.log("Download URL: ", response.file_url);

                    // Crear un enlace temporal para descargar el archivo
                    let downloadLink = document.createElement("a");
                    downloadLink.href = response.file_url;
                    downloadLink.target = "_blank";
                    downloadLink.download = file;
                    document.body.appendChild(downloadLink);
                    downloadLink.click();
                    document.body.removeChild(downloadLink);
                } else {
                    alert("Error: File not found.");
                }
            },
            error: function(xhr) {
                alert("An error occurred while downloading the file.");
                console.error(xhr.responseText);
            }
        });
    }
</script>
