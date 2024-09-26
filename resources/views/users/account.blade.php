@extends('layouts.admin')

@section('page-title')
    {{ __('User Profile') }}
@endsection
@section('links')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"> {{ __('User Profile') }}</li>
@endsection
@php
    $logo = \App\Models\Utility::get_file('avatars/');
@endphp
@section('content')
    <div class="row">
        <div class="col-xl-3">
            <div class="card sticky-top">
                <div class="list-group list-group-flush" id="useradd-sidenav">
                    <a href="#v-pills-home" class="list-group-item list-group-item-action border-0">{{ __('Account') }}
                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                    </a>

                    <a href="#v-pills-profile"
                        class="list-group-item list-group-item-action border-0">{{ __('Add another workspace') }}
                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-xl-9">
            <div id="v-pills-home" class="card">
                <div class="card-header">
                    <h5>{{ __('Avatar') }}</h5>
                </div>
                @php
                    $workspace = $currentWorkspace ? $currentWorkspace->id : 0;
                    $user_id = $user ? $user->id : 0;
                @endphp
                <div class="card-body">
                    <form method="post"
                        action="@auth('web'){{ route('update.account', [$workspace, $user_id]) }}@elseauth{{ route('client.update.account', [$workspace, $user_id]) }}@endauth"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-lg-4 avatar-centrado">
                                <div class="form-group">
                                    <img @if ($user->avatar) src="{{ asset($logo . $user->avatar) }}" @else avatar="{{ $user->name }}" @endif
                                        id="myAvatar" alt="user-image" class="rounded-circle img-thumbnail">
                                    {{-- <div class="choose-file">
                                        <label for="avatar">
                                            <div class=" bg-primary "><i
                                                    class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                            </div>
                                            <input type="file" class="form-control choose_file_custom" name="avatar"
                                                id="avatar" data-filename="avatar-logo" style="display: none;">
                                        </label>
                                        @error('avatar')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div> --}}
                                </div>
                                {{-- <small
                                    class="text-muted text-center">{{ __('Please upload a valid image file. Size of image should not be more than 2MB.') }}</small> --}}
                            </div>
                            <div class="col-lg-8">
                                <div class="d-flex">
                                    <h1> {{ $user->name }}</h1>
                                </div>
                                <div class="d-flex mt-4">
                                    <i class="fa-regular fa-envelope fa-xl mt-4 me-3"></i>
                                    <div style="display: flex; flex-direction: column;" class="col-12">
                                        <strong for="name" class="form-label ">{{ __('Email') }}</strong>
                                        {{ $user->email }}
                                    </div>
                                </div>
                                <div class="d-flex mt-4">
                                    <i class="fa-regular fa-user fa-xl mt-4 me-3"></i>
                                    <div style="display: flex; flex-direction: column;" class="col-12">
                                        <strong for="name" class="form-label ">{{ __('Job title') }}</strong>
                                        {{ $user->jobTitle }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="row mt-4">
                            <div class=" row">
                                <div class="text-end">
                                    <button type="submit" class="btn-submit btn btn-primary col-sm-auto col-12">
                                        {{ __('Save Changes') }}
                                    </button>
                                </div>
                            </div>
                        </div> --}}
                    </form>
                    @if ($user->avatar != '')
                        <form
                            action="@auth('web'){{ route('delete.avatar') }}@elseauth{{ route('client.delete.avatar') }}@endauth"
                            method="post" id="delete_avatar">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endif
                </div>
            </div>

            <div class="card" id="v-pills-profile">
                <div class="card-header">
                    <h5>{{ __('Add another workspace') }}</h5>
                </div>
                <div class="card-body">
                    <div class="col-12 d-flex">
                        <div class="col-4">
                            <div class="d-flex mt-4">
                                <h1><i class="ti ti-users text-success card-icon-text-space m-2"></i>
                                </h1>
                                <div style="display: flex; flex-direction: column;" class="col-8">
                                    <strong for="name" class="form-label mb-3">{{ __('Current workspace') }}</strong>
                                    {{ $currentWorkspace->name }}
                                </div>
                            </div>
                        </div>
                        <div class="col-8 mt-4 ms-5">
                            <a href="#" class="d-flex">
                                <h1 class="mt-2 me-1 d-inline"><i class="bi bi-info-circle" style="color: #FFD43B;"></i>
                                </h1>
                                <p class="text-muted infoWorkspace">
                                    {{ __('The selected workspaces indicate which ones you belong to. You can also select others to be displayed in the list of your workspaces.') }}
                                </p>
                            </a>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex mt-4">
                            <h1><i class="ti ti-users text-primary card-icon-text-space m-2"></i></h1>
                            <div style="display: flex; flex-direction: column; width: 100%;">
                                <strong for="name" class="form-label mb-4">{{ __('New workspace') }}</strong>
                                <div class="container">
                                    <div class="row">
                                        @foreach ($workspaces as $workspace)
                                            <div class="col-md-4 mb-2">
                                                <div class="form-check form-switch">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" role="switch"
                                                            id="workspaceCheckbox-{{ $workspace->id }}"
                                                            onchange="addWorkspace({{ $workspace->id }})"
                                                            @if (in_array($workspace->id, $anotherWorkspaces)) checked @endif>
                                                        {{ $workspace->name }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->
    @endsection
    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script type="text/javascript">
            $('#avatar').change(function() {

                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#myAvatar').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);

            });

            function addWorkspace(workspaceId) {
                $.ajax({
                    url: '{{ route('addWorkspace', ':id') }}'.replace(':id', workspaceId),
                    type: 'GET',
                    success: function(response) {
                        location.reload();
                    },
                    error: function(response) {
                        console.error("Error al actualizar el estado del workspace");
                    }
                });
            }
        </script>
        <script>
            $(document).on('click', '.list-group-item', function() {
                $('.list-group-item').removeClass('active');
                $('.list-group-item').removeClass('text-primary');
                setTimeout(() => {
                    $(this).addClass('active').removeClass('text-primary');
                }, 10);
            });

            var type = window.location.hash.substr(1);
            $('.list-group-item').removeClass('active');
            $('.list-group-item').removeClass('text-primary');
            if (type != '') {
                $('a[href="#' + type + '"]').addClass('active').removeClass('text-primary');
            } else {
                $('.list-group-item:eq(0)').addClass('active').removeClass('text-primary');
            }

            var scrollSpy = new bootstrap.ScrollSpy(document.body, {
                target: '#useradd-sidenav',
                offset: 300
            })
        </script>
    @endpush
    <style>
        .icon {
            width: 25px;
            height: 23px;
            margin-right: 10px;
        }
    </style>
