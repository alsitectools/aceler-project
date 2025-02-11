<head>
    <link rel="stylesheet" href="{{ asset('assets/css/milestone.css') }}">
</head>
@php
    $logo = \App\Models\Utility::get_file('users-avatar/');
    $logo_tasks = \App\Models\Utility::get_file('tasks/');
@endphp
<div class="modal-body">
    @if ($currentWorkspace && $milestone)
        <div class="p-2">
            <div class="row">
                <div class="col-md-12 mb-4">
                    <h2 class="mb-3">{{ $milestone->title }}</h2>
                </div>

                <div class="col-md-12 d-flex">
                    <p class="form-control-label text-muted col-md-2"><b>{{ __('Requested by') }} </p>
                    <img class="fix_img"
                        @if ($salesManager->avatar) src="{{ asset($salesManager->avatar) }}" @else avatar="{{ $salesManager->name }}" @endif>
                    {{ $salesManager->name }}
                </div>
                <div class="col-md-12 d-flex">
                    <p class="form-control-label text-muted col-md-2"><b>{{ __('Last updated') }} </p>
                    <i class="fa-solid fa-calendar-check m-1 fa-xl" style="color: #54b5ae;"></i>
                    {{ $milestone->updated_at->diffForHumans() }}
                </div>
                <div class="row">
                    <!-- Etiqueta de la descripción -->
                    <div class="col-md-2">
                        <p class="form-control-label text-muted"><b>{{ __('Description') }}:</b></p>
                    </div>
                    <div class="col-md-10">
                        <!-- Descripción -->
                        <p class="mb-3">{{ $milestone->summary }}</p>

                        <!-- Archivos -->
                        <div class="divFilesMilestoneShow flex-wrap align-items-center">
                            @foreach ($milestone->files as $file)
                                @php
                                    $extension = pathinfo($file->name, PATHINFO_EXTENSION);
                                    $iconPath = file_exists(public_path('assets/iconFilesTypes/' . $extension . '.png'))
                                        ? 'assets/iconFilesTypes/' . $extension . '.png'
                                        : 'assets/iconFilesTypes/default.png';
                                @endphp

                                <div class="fileShowMilestone me-1">
                                    <div style="display: flex;align-items: center;">
                                            <img class="mt-1" src="{{ asset($iconPath) }}"
                                                alt="{{ $extension }} icon" style="width: 20px; height: 25px;" />
 
                                            <span class="file-nameMilestoneShow ms-1">{{ $file->name }}</span>
                                            <br />
                                            <a onclick="downloadMilestoneFile({{ $milestone->project_id}},'{{$milestone->title}}','{{ $file->name }}', {{ $file->id }})" class="buttonFiles"
                                                style="margin-left: 5px;margin-right: 5px;">
                                                <i class="ti ti-download" style="color:white"></i>
                                            </a>
                                            <small class="text-muted">
                                                ({{ $file->file_size }})
                                            </small>   
                                        
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <hr>
                <div class="mt-3">
                    <a href="#" id="toggleTasks">
                        <h3 class="text-primary">Tareas <!--<i id="toggleIcon" class="fa fa-chevron-down"></i>--></h3>
                    </a>
                    <!-- Contenedor de las tareas (inicialmente oculto) -->
                    <div id="taskListContainer" class="mt-4">
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
                                            '"></i>';
                                @endphp
                                {!! $icon !!} {{ __($task->task_name ?? $task) }}
                            </div>
                        @endforeach
                    </div>
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
                                    {{ __("It's looking like you may have taken a wrong turn. Don't worry... it happens to the best of us. Here's a little tip that might help you get back on track.") }}
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
<script>
    function downloadMilestoneFile(idProject, milestoneTitle, file, fileId) {
        const downloadUrl = '<?php echo url('projects/milestone/download-file'); ?>';
        $.ajax({
            url: downloadUrl,
            method: 'POST',
            data: {
                "idProject": idProject,
                "milestoneTitle": milestoneTitle,
                "fileName": file,
                "fileId": fileId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    console.log("Download URL: ", response.file_url);

                    // Crear un enlace temporal para descargar el archivo
                    let downloadLink = document.createElement("a");
                    downloadLink.href = response.file_url;
                    downloadLink.target = "_blank";
                    downloadLink.download = file; // Nombre del archivo
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
    // $(document).ready(function() {
    //     const toggleButton = $('#toggleTasks');
    //     const taskListContainer = $('#taskListContainer');
    //     const toggleIcon = $('#toggleIcon');

    //     toggleButton.on('click', function(event) {
    //         event.preventDefault();

    //         if (taskListContainer.is(':visible')) {
    //             taskListContainer.hide();
    //             toggleIcon.removeClass('fa-chevron-up').addClass(
    //                 'fa-chevron-down');
    //         } else {
    //             taskListContainer.show();
    //             toggleIcon.removeClass('fa-chevron-down').addClass(
    //                 'fa-chevron-up');
    //         }
    //     });
    // });
</script>
