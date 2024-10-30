@extends('layouts.admin')

@section('page-title')
    {{ __('messages.Company') }}
@endsection

@php
    $logo = \App\Models\Utility::get_file('avatars/');
    $currentWorkspace = Auth::user()->currentWorkspace;
@endphp

@section('links')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>

    <li class="breadcrumb-item"> {{ __('messages.Technicians') }}</li>
@endsection
@section('action-button')
    @auth('web')
        @if (Auth::user()->type == 'admin')
            <a href="#" class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="md" data-title="{{ __('Invite') }}"
                data-url="{{ route('users.invite', $currentWorkspace->slug) }}" data-toggle="tooltip" title="{{ __('Invite') }}">
                <i class="ti ti-plus"></i>
            </a>
        @endif
    @endauth
@endsection
@section('content')
    @if (isset($currentWorkspace) && $currentWorkspace)
        <div class="row">
            @foreach ($users as $user)
                @php($workspace_id = isset($currentWorkspace) && $currentWorkspace ? $currentWorkspace->id : '')
                <div class="col-xl-3 col-lg-4 col-sm-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <div class="avatar">
                            <img class="theme-avtar"
                                @if (Auth::user()->avatar) 
                                    src="{{ url('storage/app/public/' . Auth::user()->avatar) }}" 
                                @else 
                                    avatar="{{ Auth::user()->name }}" 
                                @endif
                                alt="{{ Auth::user()->name }}">
                            </div>
                            <h4 class="mt-2">{{ $user->name }}</h4>
                            <small>{{ $user->email }}</small>

                            <div class=" mb-0 mt-3">
                                <div class=" p-3">
                                    <div class="row px-2">
                                        @if (Auth::user()->type == 'admin')
                                            <div class="col-6 text-start">

                                                <h6 class="mb-0 px-3">{{ $user->countWorkspace() }}</h6>
                                                <p class="text-muted text-sm mb-0">
                                                    {{ trans('messages.Workspaces') }}</p>
                                            </div>
                                            <div
                                                class="col-6 {{ Auth::user()->type == 'admin' ? 'text-end' : 'text-start' }}  ">
                                                <h6 class="mb-0 px-3">{{ $user->countUsers($workspace_id) }}</h6>
                                                <p class="text-muted text-sm mb-0">{{ trans('messages.Users') }}
                                                </p>
                                            </div>
                                            <div class="col-6 text-start mt-2">
                                                <h6 class="mb-0 px-3">{{ $user->countClients($workspace_id) }}
                                                </h6>
                                                <p class="text-muted text-sm mb-0">{{ trans('messages.Clients') }}
                                                </p>
                                            </div>
                                        @endif

                                        <div
                                            class="col-6  {{ Auth::user()->type == 'admin' ? 'text-end mt-2' : 'text-start' }} ">
                                            <h6 class="mb-0 px-3">{{ $user->countProject($workspace_id) }}</h6>
                                            <p class="text-muted text-sm mb-0">{{ __('Projects') }}</p>
                                        </div>

                                        @if (Auth::user()->type == 'admin')
                                            <div class="col-12 text-center Id mt-3">
                                                <a href="#" data-url="{{ route('company.info', $user->id) }}"
                                                    data-size="lg" data-ajax-popup="true" class="btn btn-outline-primary"
                                                    data-title="{{ __('Company Info') }}">{{ trans('messages.AdminHub') }}</a>
                                            </div>
                                        @endif

                                        @if (Auth::user()->type != 'admin')
                                            <div class="col-6 text-end">
                                                <h6 class="mb-0 px-3">{{ $user->countTask($workspace_id) }}</h6>
                                                <p class="text-muted text-sm mb-0">{{ trans('messages.Tasks') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- INVITAR USER
            <div class="col-xl-3 col-lg-4 col-sm-6">
                @auth('web')
                    @if (Auth::user()->type == 'admin')
                        <a href="#" class="btn-addnew-project" data-ajax-popup="true" data-size="md"
                            data-title="{{ __('Invite New User') }}"
                            data-url="{{ route('users.invite', $currentWorkspace->slug) }}">
                            <div class="bg-primary proj-add-icon">
                                <i class="ti ti-plus"></i>
                            </div>
                            <h6 class="mt-4 mb-2">{{ trans('messages.Invite_New_User') }}</h6>
                            <p class="text-muted text-center">{{ trans('messages.Clich_here_to_Invite_New_User') }}
                            </p>
                        </a>
                    @endif
                @endauth
            </div> --}}
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
        </div>
    @endif
    </div>
    <!-- [ sample-page ] end -->
    </div>
@endsection

@push('scripts')
    <script>
        $(".fc-daygrid-event fc-daygrid-block-event fc-h-event fc-event fc-event-draggable fc-event-resizable fc-event-end fc-event-past bg-danger border-danger")
            .click(function() {
                alert("Handler for .click() called.");
            });
    </script>
@endpush
