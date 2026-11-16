{{--
    Barra del listado de proyectos + pestañas de estado (Proyectos y Mis proyectos).
    Parámetros: $projects, $showCreate (opcional), $showWorkspace (opcional).
    Los filtros los gestiona projects/partials/project_list_script.blade.php:
      [data-facet] + [data-value] = clase que debe tener la fila (.pl-row) para pasar el filtro.
--}}
@php
    $plTypes = [1 => 'Jobsite', 3 => 'I+D Project', 4 => 'Office', 2 => 'Innovation', 5 => 'I+D Development'];
    $plDelegations = collect($projects ?? [])
        ->filter(fn($p) => $p->delegation)
        ->map(fn($p) => [
            'value' => 'del-' . \Illuminate\Support\Str::slug($p->ref_delegation ?: 'x'),
            'name' => \Illuminate\Support\Str::title($p->delegation->delegation_name),
            'code' => \Illuminate\Support\Str::upper($p->ref_delegation),
        ])
        ->unique('value')
        ->sortBy('name')
        ->values();
    $plWorkspaces = !empty($showWorkspace)
        ? collect($projects ?? [])->map(fn($p) => $p->workspaceData)->filter()->unique('id')->sortBy('display_name')->values()
        : collect();
    $hasProjects = isset($projects) && $projects && !$projects->isEmpty();
@endphp

<div class="pl-toolbar">
    <div id="searchInputProjects" data-popper-placement="bottom-start">
        <form class="form-inline mr-auto mb-0 formSearch">
            <div class="search-element">
                <input type="text" class="input" id="searchInput"
                    placeholder="{{ __('Search by name or M.O reference') }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="input-icon" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                        clip-rule="evenodd" />
                </svg>
                <div class="search-backdrop"></div>
            </div>
        </form>
    </div>

    @if ($hasProjects)
        {{-- Tipo --}}
        <div class="dropdown pl-facet" data-facet="type" data-label="{{ __('Type') }}">
            <button type="button" class="pl-tool-btn pl-facet-btn" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="pl-facet-label">{{ __('Type') }}</span>
                <i class="fa-solid fa-chevron-down pl-sort-caret"></i>
                <span class="pl-facet-x" role="button" aria-label="{{ __('Clear') }}"><i class="fa-solid fa-xmark"></i></span>
            </button>
            <ul class="dropdown-menu pl-menu">
                @foreach ($plTypes as $typeId => $typeName)
                    <li>
                        <button type="button" class="dropdown-item" data-value="type-{{ $typeId }}" data-text="{{ __($typeName) }}">
                            <i class="fa-solid fa-check pl-ck"></i>
                            <img src="{{ asset('assets/img/types/' . $typeName . '.svg') }}" alt="">
                            {{ __($typeName) }}
                            <span class="pl-n"></span>
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Delegación --}}
        @if ($plDelegations->count() > 0)
            <div class="dropdown pl-facet" data-facet="del" data-label="{{ __('Delegation') }}">
                <button type="button" class="pl-tool-btn pl-facet-btn" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="pl-facet-label">{{ __('Delegation') }}</span>
                    <i class="fa-solid fa-chevron-down pl-sort-caret"></i>
                    <span class="pl-facet-x" role="button" aria-label="{{ __('Clear') }}"><i class="fa-solid fa-xmark"></i></span>
                </button>
                <ul class="dropdown-menu pl-menu">
                    @foreach ($plDelegations as $del)
                        <li>
                            <button type="button" class="dropdown-item" data-value="{{ $del['value'] }}" data-text="{{ $del['name'] }}">
                                <i class="fa-solid fa-check pl-ck"></i>
                                {{ $del['name'] }} <small class="pl-code">{{ $del['code'] }}</small>
                                <span class="pl-n"></span>
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Espacio de trabajo (Mis proyectos) --}}
        @if ($plWorkspaces->count() > 1)
            <div class="dropdown pl-facet" data-facet="ws" data-label="{{ __('Workspace') }}">
                <button type="button" class="pl-tool-btn pl-facet-btn" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="pl-facet-label">{{ __('Workspace') }}</span>
                    <i class="fa-solid fa-chevron-down pl-sort-caret"></i>
                    <span class="pl-facet-x" role="button" aria-label="{{ __('Clear') }}"><i class="fa-solid fa-xmark"></i></span>
                </button>
                <ul class="dropdown-menu pl-menu">
                    @foreach ($plWorkspaces as $ws)
                        <li>
                            <button type="button" class="dropdown-item" data-value="workspace-{{ $ws->slug }}" data-text="{{ $ws->display_name }}">
                                <i class="fa-solid fa-check pl-ck"></i>
                                {{ $ws->display_name }}
                                <span class="pl-n"></span>
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Ordenar (solo icono) --}}
        <div class="dropdown pl-sort">
            <button type="button" id="btnSortProjects" class="pl-tool-btn pl-icon-btn"
                data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('Sort by') }}">
                <i class="fa-solid fa-arrow-down-short-wide pl-sort-icon"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end pl-sort-menu pl-menu">
                <li><button type="button" class="dropdown-item" data-sort="recent"><i class="fa-solid fa-check pl-ck"></i>{{ __('Most recent') }}</button></li>
                <li><button type="button" class="dropdown-item" data-sort="name-asc"><i class="fa-solid fa-check pl-ck"></i>{{ __('Name') }} A–Z</button></li>
                <li><button type="button" class="dropdown-item" data-sort="name-desc"><i class="fa-solid fa-check pl-ck"></i>{{ __('Name') }} Z–A</button></li>
            </ul>
        </div>
    @endif

    @if (!empty($showCreate))
        @auth('web')
            <button type="button" class="pl-new-btn" data-ajax-popup="true"
                data-title="{{ __('Create New Project') }}" data-url="{{ route('projects.create', $currentWorkspace->slug) }}">
                <i class="fa-solid fa-plus"></i>
                <span>{{ __('New') }}</span>
            </button>
        @endauth
    @endif
</div>

@if ($hasProjects)
    <div class="pl-tabs" data-facet="status" role="tablist">
        <button type="button" class="pl-tab active" data-value="*">{{ __('All statuses') }} <span class="pl-n"></span></button>
        <button type="button" class="pl-tab" data-value="Ongoing"><span class="pl-dot pl-dot-ongoing"></span>{{ __('Ongoing') }} <span class="pl-n"></span></button>
        <button type="button" class="pl-tab" data-value="OnHold"><span class="pl-dot pl-dot-onhold"></span>{{ __('OnHold') }} <span class="pl-n"></span></button>
        <button type="button" class="pl-tab" data-value="Finished"><span class="pl-dot pl-dot-finished"></span>{{ __('Finished') }} <span class="pl-n"></span></button>
    </div>
@endif
