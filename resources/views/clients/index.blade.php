@extends('layouts.admin')

@section('page-title')
    {{ __('Clients') }}
@endsection
@section('links')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>

    <li class="breadcrumb-item"> {{ __('Clients') }}</li>
@endsection

@php
    $currentWorkspace = Auth::user()->currentWorkspace;
    $logo = \App\Models\Utility::get_file('users-avatar/');
@endphp

@section('action-button')
    @if (\Auth::user()->type == 'admin')
        <a href="#" class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="md"
            data-title="{{ __('Add_Client') }}" data-url="{{ route('clients.create', $currentWorkspace->slug) }}"
            data-toggle="tooltip" title="{{ __('Add Client') }}">
            <i class="ti ti-plus"></i>
        </a>
    @endif
@endsection

@section('content')
    <div class="row">
        @foreach ($clients as $client)
            <div class="col-xl-3 col-lg-4 col-sm-6">
                <div class="card   text-center">

                    <div class="card-body">
                    <img class="theme-avtar"
                                @if (Auth::user()->avatar) 
                                    src="{{ url('storage/app/public/' . Auth::user()->avatar) }}" 
                                @else 
                                    avatar="{{ Auth::user()->name }}" 
                                @endif
                                alt="{{ Auth::user()->name }}">
                        <h4 class="mt-2">{{ $client->name }}</h4>
                        <small>{{ $client->email }}</small>
                    </div>
                </div>
            </div>
        @endforeach
        @if (!$currentWorkspace)
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
    </div>

    <!-- [ sample-page ] end -->
    </div>
@endsection

@push('scripts')
@endpush
