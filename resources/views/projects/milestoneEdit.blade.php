<head>
    <link rel="stylesheet" href="{{ asset('assets/css/milestone.css') }}">
    <script src="{{ asset('assets/js/milestone_uploadfiles.js') }}" defer></script>
</head>
@if ($milestone && $currentWorkspace)
    <form method="post" action="{{ route('projects.milestone.update', [$currentWorkspace->slug, $milestone->id]) }}"
        enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
            <!-- Información general del hito -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="milestone-title" class="col-form-label">{{ __('Milestone Title') }}</label>
                        <input type="text" class="form-control form-control-light" id="milestone-title"
                            placeholder="{{ __('Enter Title') }}" value="{{ $milestone->title }}" name="title"
                            required disabled>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="start_date" class="col-form-label">{{ __('Created date') }}</label>
                        <input type="date" class="form-control form-control-light date" id="start_date"
                            name="start_date" value="{{ $milestone->start_date }}" disabled>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="end_date" class="col-form-label">{{ __('Estimated date') }}</label>
                        <input onclick="this.showPicker()" type="date" class="form-control form-control-light date"
                            id="end_date" name="end_date" value="{{ $milestone->end_date }}" required>
                    </div>
                </div>
                <div class="form-group col-md-12">
                    <label for="task-summary" class="col-form-label">{{ __('Description') }}</label>
                    <textarea class="form-control form-control-light" id="task-summary" rows="3" name="summary">{{ $milestone->summary }}</textarea>
                </div>
            </div>
            <!-- Archivos adjuntos existentes -->
            <div class="form-group col-md-12">
                <label class="col-form-label">{{ __('Attached files') }}</label>
                <div class="form-group browser-file">
                    <div id="file-list">
                        @foreach ($milestone->files as $file)
                            @php
                                $extension = pathinfo($file->name, PATHINFO_EXTENSION);
                                $iconPath = file_exists(public_path('assets/iconFilesTypes/' . $extension . '.png'))
                                    ? 'assets/iconFilesTypes/' . $extension . '.png'
                                    : 'assets/iconFilesTypes/default.png';
                            @endphp
                            <div class="file exist d-flex align-items-center mt-2" data-file-id="{{ $file->id }}">
                                <img src="{{ asset($iconPath) }}" alt="{{ $extension }} icon"
                                    style="width: 20px; height: 25px;">
                                <div class="file-name ms-2">{{ $file->name }} <small
                                        class="text-muted">({{ $file->file_size }})</small></div>
                                <button class="btn btn-danger btn-sm ms-2 btn-delete-file"
                                    data-url="{{ route('milestone.destroy.file', [$currentWorkspace->slug, $milestone->id, $file->id, $milestone->project_id]) }}">
                                    <i class="fa-solid fa-trash-alt"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div id="hidden-file-inputs" style="display: none;"></div>
            </div>
            <!-- Selección de nuevos archivos -->
            <div class="form-group col-md-12 text-start">
                <input type="file" id="file-upload" class="form-control" multiple style="display: none;">
                <label for="file-upload" class="btn btn-primary">
                    <i class="fa-solid fa-paperclip"></i> {{ __('Attach new files') }}
                </label>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
            <input type="submit" value="{{ __('Save Changes') }}" class="btn btn-primary">
        </div>
    </form>
@else
    <div class="container mt-5">
        <div class="card">
            <div class="card-body p-4">
                <h1>404</h1>
                <p>{{ __('Page Not Found') }}</p>
            </div>
        </div>
    </div>
@endif
<script>
    const assetBasePath = "{{ asset('assets/iconFilesTypes') }}/";
</script>
