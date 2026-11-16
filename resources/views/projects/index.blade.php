@extends('layouts.admin')
@section('page-title')
    {{ __('Projects') }}
@endsection
@section('links')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">{{ __('Home') }}</a>
    </li>
    <li class="breadcrumb-item">{{ __('Projects') }}</li>
@endsection
@php

    $logo = \App\Models\Utility::get_file('avatars/');
@endphp

<head>
    <link rel="stylesheet" href="{{ asset('assets/css/index_projects.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/projects_list.css') }}?v={{ filemtime(public_path('assets/css/projects_list.css')) }}">
</head>
<style>
    .filterTypo {
        text-wrap: nowrap;
        text-align: center
    }

    .filterTypo:hover {
        background-color: rgb(202, 202, 202) !important;
        border-radius: 5px !important;
    }

.join-leave-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        background: none;
        color: #666;
        width: 34px;
        height: 34px;
        padding: 0;
        flex-shrink: 0;
        line-height: 1;
        cursor: pointer;
        border-radius: 50%;
        transition: color 0.15s ease, background 0.15s ease;
        margin-left: -14px;
        margin-top: 6px;
    }

    .join-leave-btn i {
        font-size: 16px;
        line-height: 1;
    }

    .jl-stack {
        position: relative;
        display: inline-flex;
    }

    .jl-stack .fa-user {
        font-size: 22px;
        line-height: 1;
    }

    .join-leave-btn .jl-badge {
        position: absolute;
        right: -5px;
        bottom: -7px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .join-leave-btn .jl-badge i {
        font-size: 9px;
        line-height: 1;
        color: inherit;
        -webkit-text-stroke: 0.6px currentColor;
    }

    .join-leave-btn:hover {
        color: #aa182c;
    }

    .join-leave-btn--leave {
        color: #666;
    }

    .join-leave-btn--leave:hover {
        color: #aa182c;
    }

    .join-leave-btn--join:hover {
        color: #1e7d32;
    }

    .participantsSection {
        width: 20% !important;
    }

    .filterSection {
        display: flex;
        background-color: red;
        width: 96% !important;
        padding: 20px;
        box-shadow: 0 6px 30px rgba(182, 186, 203, 0.3);
        background-color: #ffffff;
        border-radius: 10px;
    }


    .divStatus {
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }

    #eac-container-searchInput {
        display: none;
        position: absolute;
        z-index: 9999;
        top: 100%;
        left: 0;
        width: 100%;
    }


    .hover:hover {
        cursor: pointer;
    }

    /* no project icon section*/
    #gears {
        height: 370px;
        width: 842px;
        z-index: 3;



    }


    /* colors */
    .gear-outer.large {
        fill: #d5d5d5;
    }

    .gear-outer.small {
        fill: #d5d5d5;
    }

    .gear-inner {
        fill: #f7f9f9;
    }


    /* ------------------------------
    animation keyframes
------------------------------ */
    /* large gear rotation */
    @keyframes spin-clockwise {
        0% {
            transform: rotate(0);
        }

        100% {
            transform: rotate(2600deg);
        }
    }


    /* small gears rotation */
    @keyframes spin-counter-clockwise {
        0% {
            transform: rotate(0);
        }

        100% {
            transform: rotate(-2600deg);
        }
    }


    /* ------------------------------
    gear animation
------------------------------ */
    .gear-outer {
        transform-box: fill-box;
        transform-origin: center center;
        animation: spin-clockwise 50s infinite linear;
    }

    .gear-outer.small {
        animation: spin-counter-clockwise 50s infinite linear;
    }

    .noProjectText {
        color: #d5d5d5;
        -webkit-text-stroke: 0.5px #c7c7c7;
    }

    /* en no project icon section */


    .createBut {
        display: flex !important;
        align-items: center;
        justify-content: center;
        width: 25%;
    }

    .custom {
        /* box-shadow: none !important; */
        background-color: #ffffff !important;
        box-shadow: 0 6px 30px rgba(182, 186, 203, 0.3);
        width: 95%;
        /* background-color: #f7f9f9 !important; */
    }

    .formSearch {
        width: 100%;
    }



    @media screen and (max-width:1441px) and (min-width:900px) {

        /* * {
            border: 1px dashed green !important;
        } */

        .participantsSection {
            width: 30% !important;
        }

        .filterTypo {
            font-size: 11px !important;
            text-wrap: nowrap !important;
        }

        .projectsContainer {
            width: 58.33333% !important;
        }

        .rightColumn {
            width: 41.66667% !important;
        }

        .createBut {
            height: 90%;
        }

        .innerButtonText {
            font-size: 13px !important;
        }

        .responsiveButton {
            font-size: 12px !important;
        }
    }

    @media (min-width: 768px) {

        .createBut {
            width: 30% !important;
        }

    }
</style>

<style>
    .active-filters,
    .status-filter {
        display: flex;
        padding: 2%;
        background-color: #f6f6f6;
        width: 100%;
    }

    .type-filter {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding: 2%;
        background-color: #f6f6f6;
        width: 100%;
    }

    .type-filter > div {
        flex: 1;
    }

    .status-indicator {
        width: 15px;
        height: 15px;
        border-radius: 50%;
        display: inline-block;
    }

    .btn.active {
        transition: background-color 0.3s ease, color 0.3s ease, transform 0.2s ease;
        background-color: white !important;
        color: #454545 !important;
        transform: scale(1.05);
    }

    .disp-btn {
        display: flex;
        align-items: center;
        width: 100%;
        text-align: left !important;
    }

    .btn-active-filters {
        position: relative;
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 180px;
        background-color: #eeeeee !important;
        border: none;
        border-radius: 10px;
        padding: 12px 16px;
        margin: 0;
        text-decoration: none;
        padding-right: 28px;
        color: #454545;
    }

    .btn-active-filters img {
        flex-shrink: 0;
    }

    .active-filter-item {
        position: relative;
    }

    .active-filter-item .btn-active-filters {
        pointer-events: none;
    }

    .active-filter-remove {
        position: absolute;
        top: 6px;
        right: 10px;
        width: 22px;
        height: 22px;
        border: none;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #aa182c;
        ;
        font-size: 14px;
        line-height: 1;
        cursor: pointer;
        transition: transform 0.15s ease, background-color 0.15s ease;
    }

    .active-filter-remove:hover {
        background-color: #aa182c;
        color: white;
        transform: scale(1.08);
    }

    .legendIcon {
        width: 20px;
        margin-left: 8px;
        cursor: pointer;
        transition: transform 0.1s ease-in-out;
    }

    #eac-container-searchInput {
        max-height: 100px !important;
        overflow-y: auto !important;
        background-color: white !important;
        border: 1px solid #cdcdcd;
        border-radius: 10px;
        padding: 0px 10px !important;
    }

    .easy-autocomplete-container ul {
        background-color: rgba(255, 255, 255, 0) !important;
        border: none !important;
        border-radius: none !important;
        padding: 0px !important;
    }

    .delegationTag {
        background-color: #be0202 !important;
    }

    .delegationName {
        background-color: #392c34 !important;
    }

</style>


@section('content')
    <section class="section">
        <div class="row">
            @if ($projects && $currentWorkspace)
                <div class="col-12 projectsContainer pl-full">
                    @include('projects.partials.project_toolbar', ['showCreate' => true])
                    <div class="pl-list-box">
                        <div class="pl-list-scroll" id="plListScroll">
                    <div class="grid filters-content">
                        @if ($projects->isEmpty())
                            <!-- seccion para cuando no hay proyectos -->
                            <div class="col-md-2 project-type text-center m-2"
                                style="display: flex; align-items: center; justify-content: center; width:100%">

                                <svg version="1.1" id="gears" xmlns="http://www.w3.org/2000/svg"
                                    xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 250 250"
                                    style="enable-background:new 0 0 250 250;" xml:space="preserve">
                                    <g>
                                        <path class="gear-outer large"
                                            d="M145.6,134.6l10.6,5.3c3.5,2.1,4.6,5.1,3.1,9c-1.9,5.4-6.2,12.3-13.1,20.6c-2.7,3.1-5.8,3.6-9.4,1.6l-9.4-5.3 c-5,4.4-10.6,7.7-16.8,
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    10v10.6c0,1.7-0.6,3.2-1.9,4.7c-1.2,1.5-2.7,2.3-4.4,2.5c-7.9,1.5-15.8,1.5-23.7,0c-1.9-0.2-3.4-1-4.5-2.5 c-1.1-1.5-1.7-3-1.7-4.7v-10.6c-6.4-2.3-12.2-5.6-17.2-10l-9,5.3c-3.5,2.1-6.7,1.6-9.4-1.6c-6.7-8.1-11-15-13.1-20.6
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  c-1.5-3.9-0.4-7,3.1-9l10.3-5.3c-1.2-6.7-1.2-13.2,0-19.7l-10.3-5.3c-3.7-2.1-4.8-5.1-3.1-9C27.8,95,32.2,88.1,38.9,80 c2.5-3.1,5.6-3.6,
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  9.4-1.6l9,5c5.2-4.4,10.9-7.6,17.2-9.7V63.2c0-1.9,0.6-3.5,1.7-4.8c1.1-1.4,2.7-2.1,4.5-2.3 c7.9-1.5,15.8-1.5,23.7,0c0.9,0.2,1.5,0.4,2.3,0.8c0.8,0.4,1.4,1.1,2,1.7c0.6,0.6,1.1,1.4,1.4,2.2c0.3,0.8,0.5,1.7,0.5,2.5v10.6
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  c6.2,2.3,11.9,5.5,16.8,9.7l9.4-5c3.5-2.3,6.7-1.8,9.4,1.6c6.7,7.9,11,14.8,13.1,20.6c1.5,4,0.4,7-3.1,9l-10.6,5.3 C146.8,121.4,146.8,127.9,145.6,134.6z" />
                                        <circle class="gear-inner" cx="92.5" cy="124.8" r="23.1" />
                                        <path class="gear-outer small"
                                            d="M185.1,105l-2.5,4.4c-1,1.9-2.6,2.5-4.7,1.9c-3.7-1.5-7.1-3.4-10-5.9c-1.7-1.5-2-3.1-0.9-5l2.5-4.4
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                c-2.1-2.5-3.7-5.3-5-8.4h-5c-2.3,0-3.5-1.1-3.7-3.4c-0.8-3.7-0.8-7.6,0-11.5c0.2-2.1,1.5-3.1,3.7-3.1h5c1.2-3.1,2.9-5.9,5-8.4
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                l-2.5-4.7c-1-1.9-0.7-3.4,0.9-4.7c2.9-2.5,6.2-4.5,10-5.9c2.1-0.8,3.6-0.3,4.7,1.6l2.5,4.7c3.3-0.6,6.7-0.6,10,0l2.5-4.7
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          c1-1.9,2.6-2.4,4.7-1.6c3.7,1.2,7.1,3.2,10,5.9c1.7,1.2,2,2.8,0.9,4.7l-2.5,4.7c2.1,2.5,3.7,5.3,5,8.4h5c2.3,0,3.5,1,3.7,
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          3.1 c0.8,4,0.8,7.8,0,11.5c-0.2,2.3-1.5,3.4-3.7,3.4h-5c-1.2,3.1-2.9,5.9-5,8.4l2.5,4.4c1,2.1,0.7,3.7-0.9,5c-2.9,2.5-6.2,4.5-10,5.9
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           c-2.1,0.6-3.6,0-4.7-1.9L195,105C191.7,105.6,188.4,105.6,185.1,105z" />
                                        <circle class="gear-inner" cx="190" cy="78.4" r="11.5" />
                                        <path class="gear-outer small"
                                            d="M185.1,198.2l-2.5,4.4c-1,1.9-2.6,2.5-4.7,1.9c-3.7-1.5-7.1-3.4-10-5.9c-1.7-1.5-2-3.1-0.9-5l2.5-4.4 c-2.1-2.5-3.7-5.3-5-8.4h-5c-2.3,0-3.5-1.1-3.7-3.4c-0.8-3.7-0.8-7.6,0-11.5c0.2-2.1,1.5-3.1,3.7-3.1h5c1.2-3.1,2.9-6,5-8.7
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             l-2.5-4.4c-1-1.9-0.7-3.5,0.9-5c2.9-2.5,6.2-4.4,10-5.6c2.1-0.8,3.6-0.3,4.7,1.6l2.5,4.7c3.3-0.6,6.7-0.6,10,0l2.5-4.7
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              c1-1.9,2.6-2.4,4.7-1.6c3.7,1.2,7.1,3.1,10,5.6c1.7,1.5,2,3.1,0.9,5l-2.5,4.4c2.1,2.7,3.7,5.6,5,8.7h5c2.3,0,3.5,1,3.7,3.1
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 c0.8,4,0.8,7.8,0,11.5c-0.2,2.3-1.5,3.4-3.7,3.4h-5c-1.2,3.1-2.9,5.9-5,8.4l2.5,4.4c1,1.9,0.7,3.5-0.9,5c-2.9,2.5-6.2,4.5-10,5.9
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    c-2.1,0.6-3.6,0-4.7-1.9l-2.5-4.4C191.7,198.8,188.4,198.8,185.1,198.2z" />
                                        <circle class="gear-inner" cx="190.1" cy="171.6" r="11.5" />
                                    </g>
                                </svg>
                                <h1 class="noProjectText">{{ __('You still have no projects') }}</h1>

                            </div>
                            <div class="col-md-9">
                                <div class="card-header pt-3 pb-1 d-flex p-3">

                                    <div class="card-header-right">
                                        <div class="btn-group card-option">
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- fin seccion para cuando no hay proyectos -->
                        @else
                            @foreach ($projects as $project)
                                @include('projects.partials.project_row', [
                                    'projectUrl' => route('projects.show', [$currentWorkspace->slug, $project->id]),
                                    'showActions' => true,
                                ])
                            @endforeach
                        @endif
                    </div>
                        </div>
                        @if (!$projects->isEmpty())
                            <div class="pl-list-foot">
                                <span class="pl-list-shown" data-tpl="{{ __('Showing :count projects', ['count' => '__N__']) }}"></span>
                                <button type="button" class="pl-link pl-to-top"><i class="fa-solid fa-arrow-up"></i> {{ __('Back to top') }}</button>
                            </div>
                        @endif
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
    </section>
@endsection

<script>
    function redirectToCurrentProject(url) {
        window.location.href = url;
    }
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
@if (isset($currentWorkspace) && $currentWorkspace)
    <script src="{{ asset('assets/custom/js/jquery.easy-autocomplete.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('searchInput');
            const divContainer = document.getElementById('searchInputProjects');

            // Usar un observador de mutaciones para detectar la aparición del contenedor de autocompletar
            const observer = new MutationObserver(function(mutationsList) {
                for (const mutation of mutationsList) {
                    if (mutation.addedNodes) {
                        mutation.addedNodes.forEach((node) => {
                            if (node.id === 'eac-container-searchInput') {
                                node.style.display = 'none';
                                observer.disconnect();
                            }
                        });
                    }
                }
            });
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });

            input.addEventListener('input', function() {
                const autocompleteContainer = document.getElementById('eac-container-searchInput');
                if (autocompleteContainer) {
                    if (this.value.trim().length > 0) {
                        autocompleteContainer.style.display = 'block';
                        autocompleteContainer.style.textAlign = 'left';
                        autocompleteContainer.style.width = '77%';
                        autocompleteContainer.style.maxHeight = '1px';
                        // divContainer.classList.add('dropdown-menu');
                    } else {
                        autocompleteContainer.style.display = 'none';
                        divContainer.classList.remove('dropdown-menu');
                    }
                }
            });
        });
    </script>
    <script>
        jQuery(document).ready(function($) {
            var options = {
                adjustWidth: false,
                url: function(phrase) {
                    return "{{ route('search.json', $currentWorkspace->slug) }}/" + encodeURIComponent(
                        phrase);
                },
                categories: [{
                    listLocation: "Projects",
                    // header: "{{ __('Projects') }}" //Para mostrar el título "Projects" en la lista de autocompletado
                }],
                getValue: "text",
                template: {
                    type: "links",
                    fields: {
                        link: "link"
                    }
                },
            };
            $(".search-element input").easyAutocomplete(options);
        });
    </script>
@endif

<!-- Modal: cambiar estado del proyecto desde la burbuja -->
<div class="modal fade" id="projectStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Status') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-secondary ps-option"
                        data-status="Ongoing">{{ __('Ongoing') }}</button>
                    <button type="button" class="btn btn-outline-warning ps-option"
                        data-status="OnHold">{{ __('OnHold') }}</button>
                    <button type="button" class="btn btn-outline-success ps-option"
                        data-status="Finished">{{ __('Finished') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('assets/custom/js/isotope.pkgd.min.js') }}"></script>
    @include('projects.partials.project_list_script')
    <script>
        // After the section loads or changes
        $(document).ready(function() {
            // Re-initialize tooltips
            $('.legendIcon.tooltipCus').tooltip({
                placement: 'auto',
                title: function() {
                    return $(this).data('title');
                }
            });
        });
    </script>
    <script>
        (function() {
            var statusUrlTpl = '{{ route('projects.status.update', [$currentWorkspace->slug, '__ID__']) }}';
            var statusMeta = {
                Ongoing: { label: @json(__('Ongoing')), cls: 'bg-secondary' },
                OnHold: { label: @json(__('OnHold')), cls: 'bg-warning' },
                Finished: { label: @json(__('Finished')), cls: 'bg-success' }
            };

            window.openProjectStatusModal = function(el) {
                window._psProjectId = el.getAttribute('data-project-id');
                window._psCurrent = el.getAttribute('data-current-status');
                window._psBadge = el;
                document.querySelectorAll('#projectStatusModal .ps-option').forEach(function(btn) {
                    var isCurrent = btn.getAttribute('data-status') === window._psCurrent;
                    btn.disabled = isCurrent;
                    btn.classList.toggle('fw-bold', isCurrent);
                });
                bootstrap.Modal.getOrCreateInstance(document.getElementById('projectStatusModal')).show();
            };

            document.addEventListener('click', function(e) {
                var btn = e.target.closest('#projectStatusModal .ps-option');
                if (!btn || btn.disabled) return;

                var newStatus = btn.getAttribute('data-status');
                var projectId = window._psProjectId;

                fetch(statusUrlTpl.replace('__ID__', projectId), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ status: newStatus })
                })
                .then(function(r) {
                    return r.json().then(function(data) {
                        return { ok: r.ok, data: data };
                    });
                })
                .then(function(res) {
                    if (!res.ok || !res.data.success) {
                        alert(res.data.message || 'Error updating status');
                        return;
                    }
                    var badge = window._psBadge;
                    var meta = statusMeta[res.data.status];
                    badge.classList.remove('bg-success', 'bg-secondary', 'bg-warning');
                    badge.classList.add(meta.cls);
                    badge.textContent = meta.label;
                    badge.setAttribute('data-current-status', res.data.status);

                    var card = badge.closest('.card');
                    if (card) {
                        card.classList.remove(window._psCurrent);
                        card.classList.add(res.data.status);
                    }
                    if (window._projGrid) {
                        if (window.plRefresh) { window.plRefresh(); } else { window._projGrid.isotope('layout'); }
                    }
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('projectStatusModal')).hide();
                })
                .catch(function(err) {
                    console.error(err);
                    alert('Error updating status');
                });
            });
        })();
    </script>
@endpush
