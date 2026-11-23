{{--
    Fila compacta de proyecto (listado de Proyectos y Mis proyectos).
    Parámetros:
      $project, $project_type
      $projectUrl      URL al abrir el proyecto
      $workspaceSlug   (opcional) añade la clase workspace-<slug> para el filtro de Mis proyectos
      $statusUrl       (opcional) URL de cambio de estado guardada en el badge (Mis proyectos)
      $showWorkspace   (opcional) muestra el nombre del workspace en la línea de datos
      $showActions     (opcional) unirse/salir y menú ⋯ (listado de Proyectos)
    Las clases All / <estado> / type-N y data-project-name las usan Isotope y los filtros.
--}}
@php
    $typeModel = $project_type[$project->type - 1] ?? null;
    $typeName = $typeModel->name ?? '';
    $statusMap = [
        'Finished' => ['Finished', 'bg-success'],
        'Ongoing' => ['Ongoing', 'bg-secondary'],
    ];
    [$statusKey, $statusCls] = $statusMap[$project->status] ?? ['OnHold', 'bg-warning'];
    $initials = function ($name) {
        $parts = preg_split('/\s+/', trim((string) $name)) ?: [];
        $ini = mb_substr($parts[0] ?? '', 0, 1) . mb_substr($parts[1] ?? '', 0, 1);
        return mb_strtoupper($ini !== '' ? $ini : '?');
    };
    $users = $project->users ?? collect();
    $maxAvatars = 3;
@endphp
<div onclick="if (!event.target.closest('#deleteFormContainer, .dropdown-menu, .join-leave-btn, .project-status-badge')) { redirectToCurrentProject('{{ $projectUrl }}'); }"
    data-project-name="{{ mb_strtolower($project->name) }}"
    data-updated="{{ optional($project->updated_at)->timestamp ?? 0 }}"
    class="hover card pl-row All {{ $project->status }} type-{{ $project->type }} {{ $project->delegation ? 'del-' . \Illuminate\Support\Str::slug($project->ref_delegation ?: 'x') : '' }} {{ !empty($workspaceSlug) ? 'workspace-' . $workspaceSlug : '' }}">

    <img class="pl-type-icon" src="{{ asset('assets/img/types/' . $typeName . '.svg') }}" alt="{{ __($typeName) }}">

    <div class="pl-main">
        <div class="pl-title" title="{{ $project->name }}">{{ $project->name }}</div>
        <div class="pl-meta">
            <span class="pl-type pl-type-{{ $project->type }}">{{ __($typeName) }}</span>
            @if (!empty($project->ref_mo))
                <span class="pl-sep">·</span>
                <span class="pl-mo" title="{{ __('Reference M.O') }}">MO {{ $project->ref_mo }}</span>
            @endif
            @if ($project->delegation)
                <span class="pl-sep">·</span>
                <span class="pl-del" title="{{ __('Delegation Name') }}">
                    {{ \Illuminate\Support\Str::title($project->delegation->delegation_name) }}
                    · {{ \Illuminate\Support\Str::upper($project->ref_delegation) }}
                </span>
            @endif
            @if (!empty($showWorkspace) && $project->workspaceData)
                <span class="pl-sep">·</span>
                <span class="pl-del"><i class="fa-solid fa-layer-group"></i> {{ $project->workspaceData->display_name }}</span>
            @endif
            <span class="pl-sep">·</span>
            <span class="pl-upd">{{ __('Last updated') }} {{ $project->updated_at->diffForHumans() }}</span>
        </div>
    </div>

    <div class="pl-avatars">
        @foreach ($users->take($maxAvatars) as $i => $user)
            <span class="pl-av pl-av-{{ $i % 5 }}" title="{{ $user->name }}">
                <span class="pl-av-ini">{{ $initials($user->name) }}</span>
                @if ($user->avatar)
                    <img src="{{ asset($user->avatar) }}" alt="" onerror="this.remove()">
                @endif
            </span>
        @endforeach
        @if ($users->count() > $maxAvatars)
            <span class="pl-av pl-av-more" title="{{ $users->slice($maxAvatars)->pluck('name')->implode(', ') }}">+{{ $users->count() - $maxAvatars }}</span>
        @endif
    </div>

    <div class="pl-status">
        <span class="badge {{ $statusCls }} project-status-badge" title="{{ __('Status') }}"
            data-project-id="{{ $project->id }}" data-current-status="{{ $statusKey }}"
            @if (!empty($statusUrl)) data-status-url="{{ $statusUrl }}" @endif
            onclick="event.stopPropagation(); openProjectStatusModal(this);">{{ __($statusKey) }}</span>
    </div>

    <div class="pl-actions">
        @if (!empty($showActions))
            @auth('web')
                @if ($project->users->contains('id', Auth::id()))
                    @if ((int) $project->created_by !== (int) Auth::user()->id)
                        <button type="button" class="join-leave-btn join-leave-btn--leave"
                            title="{{ __('Leave project') }}"
                            onclick="event.stopPropagation(); document.getElementById('leave-project-{{ $project->id }}').submit();">
                            <span class="jl-stack">
                                <i class="fa-solid fa-user"></i>
                                <span class="jl-badge"><i class="fa-solid fa-check"></i></span>
                            </span>
                        </button>
                        <form id="leave-project-{{ $project->id }}"
                            action="{{ route('projects.leave', [$currentWorkspace->slug, $project->id]) }}"
                            method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endif
                @else
                    <button type="button" class="join-leave-btn join-leave-btn--join"
                        title="{{ __('Join project') }}"
                        onclick="event.stopPropagation(); document.getElementById('join-project-{{ $project->id }}').submit();">
                        <span class="jl-stack">
                            <i class="fa-solid fa-user"></i>
                            <span class="jl-badge"><i class="fa-solid fa-plus"></i></span>
                        </span>
                    </button>
                    <form id="join-project-{{ $project->id }}"
                        action="{{ route('projects.join', [$currentWorkspace->slug, $project->id]) }}"
                        method="POST" style="display: none;">
                        @csrf
                    </form>
                @endif

                @if ($project->is_active && $project->created_by == Auth::user()->id)
                    <div class="btn-group">
                        <button type="button" class="pl-menu-btn" onclick="event.stopPropagation();"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                            title="{{ __('Action') }}">
                            <i class="fa-solid fa-ellipsis-h"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="#" id="deleteFormContainer"
                                class="dropdown-item text-danger delete-popup bs-pass-para"
                                data-confirm="{{ __('Are You Sure?') }}"
                                data-text="{{ trans('This action can not be undone. Do you want to continue?') }}"
                                data-confirm-yes="delete-form-{{ $project->id }}">
                                <i class="ti ti-trash"></i>
                                <span>{{ __('Delete') }}</span>
                            </a>
                            <form id="delete-form-{{ $project->id }}"
                                action="{{ route('projects.destroy', [$currentWorkspace->slug, $project->id]) }}"
                                method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div>
                @endif
            @endauth
        @endif
    </div>
</div>
