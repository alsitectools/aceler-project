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
            <div class="card sticky-top" style="top:30px">
                <div class="list-group list-group-flush" id="useradd-sidenav">
                    <a href="#v-pills-home" class="list-group-item list-group-item-action border-0">{{ __('Account') }} <div
                            class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                </div>
            </div>
        </div>
        <div class="col-xl-9">
            <div id="v-pills-home" class="card ">
                <div class="card-header">
                    <h5>{{ __('Account') }}</h5>
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
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <img @if ($user->avatar) src="{{ asset($logo . $user->avatar) }}" @else avatar="{{ $user->name }}" @endif
                                        id="myAvatar" alt="user-image" class="rounded-circle img-thumbnail img_hight w-25">

                                    <div class="choose-file d-flex">
                                        <label for="avatar" style="width: 200px;">
                                            <div class=" bg-primary"> <i
                                                    class="ti ti-upload px-1"></i>{{ __('Choose file here') }}     </div>
                                            <input type="file" class="form-control choose_file_custom" name="avatar"
                                                id="avatar" data-filename="avatar-logo">
                                        </label>
                                        @error('avatar')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        @if ($user->avatar != '')
                                            <div>
                                                <a href="#"
                                                    class=" action-btn btn-danger  btn btn-sm  mb-1 d-inline-flex align-items-center bs-pass-para"
                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                    data-confirm-yes="delete_avatar"><i class="ti ti-trash text-white"></i>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <small
                                    class="text-muted">{{ __('Please upload a valid image file. Size of image should not be more than 2MB.') }}</small>
                            </div>
                            <div class="col-lg-6">

                                <div class="col-12 d-flex">
                                    <h1> {{ $user->name }}</h1>
                                </div>
                                <div class="col-6 d-flex mt-4">
                                    <i class="fa-regular fa-envelope fa-xl mt-4 me-3"></i>
                                    <div style="display: flex; flex-direction: column;" class="col-12">
                                        <strong for="name" class="form-label ">{{ __('Email') }}</strong>
                                        {{ $user->email }}
                                    </div>
                                </div>
                                <div class="col-6 d-flex mt-4">
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
                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
@endsection
@push('scripts')
    <script type="text/javascript">
        $('#avatar').change(function() {

            let reader = new FileReader();
            reader.onload = (e) => {
                $('#myAvatar').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);

        });
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
