@extends('layouts.admin')
@php
    $client_keyword = Auth::user()->type == 'client' ? 'client' : '';
    $logo = \App\Models\Utility::get_file('avatars/');
    $logo_tasks = \App\Models\Utility::get_file('tasks/');
    use App\Models\User;
@endphp
@section('page-title')
    {{ __('Milestoneboard') }}
@endsection

<head>
    <link rel="stylesheet" href="{{ asset('assets/css/milestoneboard.css') }}">
</head>
<style>
    .showCompletedProjectGroup{
        display: flex;
        gap: 17px;
    }
    .showCompletedProjects{
        width: 20px;
        height: 20px;
        margin-top: -6px;
    }
    .showCompletedProjects:hover{
        cursor: pointer;
       
    }
    .showCompletedProjectsUnabled{
        filter: grayscale(1);
    }
    .modifiedWidth {
        width: 99.9%;
    }

    .adjustImg {
        width: 40px;
    }

    .calendarAlert {
        font-size: 29px;
        padding-top: 12px !important;
        padding-bottom: 0px !important;
    }

    .adjustTextCalendar {
        padding-top: 10% !important;
        font-size: 12px !important;
    }

    @media screen and(max-width:1200px) and(min-width:1000px) {
        .adjustImg {
            width: 65%;
        }

        .calendarAlert {
            font-size: 25px !important;
            padding-top: 12px !important;
        }

        .adjustTextCalendar {
            padding-top: 15%;
        }
    }
</style>
@section('links')
    @if (isset($project_id) && $project_id != -1)
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('projects.index', $currentWorkspace->slug) }}">{{ __('Projects') }}</a></li>
    
        <li class="breadcrumb-item"><a
                href="{{ route('projects.show', [$currentWorkspace->slug, $project_id]) }}">{{$project_name}}</a>
        </li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('projects.index', $currentWorkspace->slug) }}">{{ __('Projects') }}</a></li>
    @endif
    <li class="breadcrumb-item">{{ __('Milestoneboard') }}</li>
@endsection

@section('action-button')
    <div class="d-flex justify-content-end row1">
    <div id="modal-container" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="text-align: left; width: 800px;">
            <!-- El contenido del modal se cargará aquí -->
        </div>
    </div>
</div>
        @if (isset($currentWorkspace) && $currentWorkspace)
            <div class="col-sm-auto">
                <button style="width: 100%" type="button" class="btn btn-primary addMilestone" data-ajax-popup="true"
                    data-title="{{ __('Milestone order') }}"
                    data-url="{{ route('projects.milestone', [$currentWorkspace->slug, $project_id]) }}"
                    data-toggle="popover" title="{{ __('Create') }}"><i class="fa-solid fa-file-lines me-3"
                        style="color: #ffffff;"></i>
                    {{ __('Create Order Form') }}
                </button>
            </div>
        @endif
    </div>
@endsection
@section('content')
    <div class="row modifiedWidth">
        <div class="col-sm-12">
            <div class="row kanban-wrapper horizontal-scroll-cards" data-toggle="dragula"
                data-containers='{{ json_encode($statusClass) }}' data-handleclass="handleclass">
                @foreach ($stages as $status)
                    <div class="col-3 pe-1" id="{{ 'milestone-list-' . str_replace(' ', '_', $status->id) }}">
                        <div class="card card-list">
                            <div class="card-header">
                                <div class="float-end showCompletedProjectGroup">
                                
                                @if ($status->name === 'Done')
                            @if ($project_id == -1)
                            
                                <img id="toggleCompletedProjectsIcon" src="{{ asset('assets/img/clipboard-check-solid.svg') }}" alt="show completed projects" title="{{ __('Show Completed Projects') }}" class="showCompletedProjects showCompletedProjectsUnabled"/>
                              
                                @endif
                            @endif
                                    <button class="btn-submit btn btn-md btn-primary btn-icon px-1 py-0 " style="height: 19.7px;">
                                        <span class="badge badge-secondary rounded-pill count">
                                            {{ isset($milestones[$status->id]) ? count($milestones[$status->id]) : 0 }}
                                        </span>
                                    </button>
                                </div>
                                <h4 class="mb-0">
                                    {{ __($status->name) }}
                                </h4>
                            </div>
                            <div id="{{ 'milestone-list-' . str_replace(' ', '_', $status->id) }}"
                                data-status="{{ $status->id }}" class="card-body kanban-box">
                                @if (isset($milestones[$status->id]))
                                    @foreach ($milestones[$status->id] as $milestone)
                                        <div class="card" id="{{ $milestone['id'] }}" data-status="{{ $status->id }}"
                                            data-project-id="{{ $milestone['project_id'] }}">
                                            <div class="card-header border-0 pb-0 col-sm-12">
                                                <div class="d-flex">
                                                    <div class="col-sm-9 text-center tooltipCus"
                                                        data-title="{{ __('Milestone') }}">
                                                        <b id="mileTitle">{{ $milestone['title'] }}</b>
                                                    </div>
                                                    <div class="col-sm-2 pt-1 text-center">
                                                        <a href="#" class=" tooltipCus"
                                                            data-title="{{ $milestone['sales']->name }}">
                                                            <img class="img-fluid"
                                                                src="{{ asset('assets/img/salesManager.png') }}"
                                                                alt="logo" />
                                                        </a>

                                                    </div>
                                                </div>
                                                <hr class="border border-2 opacity-50">
                                                <div class="card-header-right col-sm-1 text-end">
                                                    <div class="btn-group card-option">
                                                        @if ($currentWorkspace->permission == 'Owner' || $currentWorkspace->permission == 'Member')
                                                            <button type="button" class="btn dropdown-toggle"
                                                                data-bs-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">
                                                                <i class="feather icon-more-vertical"></i>
                                                            </button>
                                                            <div class="dropdown-menu dropdown-menu-end">
                                                                <a href="#" class="dropdown-item"
                                                                    data-ajax-popup="true" title="{{ __('View') }}"
                                                                    data-title="{{ __('Milestone Details') }}"
                                                                    data-url="{{ route('projects.milestone.show', [$currentWorkspace->slug, $milestone['id']]) }}">
                                                                    <i class="ti ti-eye pr-1"></i>
                                                                    {{ __('View') }}
                                                                </a>
                                                                <a href="#"
   class="dropdown-item milestone-action"
   data-ajax-popup="true"
   title="{{ __('Add Task') }}"
   data-title="{{ __('Add Task') }}"
   data-url="{{ route('tasks.create', [$currentWorkspace->slug, 'project_id'=> $milestone['project_id'], 'milestoneTitle' => $milestone['title'], 'milestone_id' =>$milestone['id']]) }}">
    <i class="fas fa-tasks pr-1"></i> {{ __('Add Task') }}
</a>
                                                                @if (
                                                                    $currentWorkspace->permission == 'Owner' ||
                                                                        ($currentWorkspace->permission == 'Member' && Auth::user()->type == 'user'))
                                                                        <a href="#"
   class="dropdown-item milestone-action"
   data-ajax-popup="true"
   data-size="lg"
   data-toggle="popover"
   title="{{ __('Edit') }}"
   data-title="{{ __('Edit Milestone') }}"
   data-url="{{ route('projects.milestone.edit', [$currentWorkspace->slug, $milestone['id']]) }}">
    <i class="ti ti-edit pr-1"></i>{{ __('Edit') }}
</a>
                                                                    @if (empty($milestone['tasks']))
                                                                        <a href="#" class="dropdown-item bs-pass-para"
                                                                            data-confirm="{{ __('Are You Sure?') }}"
                                                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                            data-confirm-yes="delete-form-{{ $milestone['id'] }}">
                                                                            <i class="ti ti-trash"></i>
                                                                            {{ __('Delete') }}
                                                                        </a>
                                                                        <form id="delete-form-{{ $milestone['id'] }}"
                                                                            action="{{ route('projects.milestone.destroy', [$currentWorkspace->slug, $milestone['id']]) }}"
                                                                            method="POST" style="display: none;">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                        </form>
                                                                    @else
                                                                    <a href="#"
   class="dropdown-item milestone-action bs-pass-para"
   data-confirm="{{ __('Are You Sure?') }}"
   data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
   data-confirm-yes="delete-form-{{ $milestone['id'] }}">
    <i class="ti ti-trash"></i> {{ __('Delete') }}
</a>
                                                                        <form id="delete-form-{{ $milestone['id'] }}"
                                                                            action="{{ route('projects.milestone.destroy', [$currentWorkspace->slug, $milestone['id']]) }}"
                                                                            method="POST" style="display: none;">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                        </form>
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body pt-1">
                                                <div class="row">
                                                    @if ($milestone['tasks'])
                                                        <div class="col-sm-12  p-3"
                                                         >
                                                         @foreach ($milestone['tasks'] as $task)
    <div class="taskList tooltipCus p-target mb-2 col-sm-12 marginText"
    role="button"
         data-task-id="{{ $task['id'] }}"
         data-task-name="{{ $task['name'] }}"
         data-milestone-id="{{ $milestone['id'] }}"
         data-project-id="{{ $milestone['project_id'] }}"
         data-project-name="{{ $milestone['project_name'] }}"
         data-technician-name="{{ $task['technician']->id }}"
         data-url="{{ route('create.timesheet.from.orders', [$currentWorkspace->slug, $project_id]) }}"
         data-ajax-timesheet-popup="true"
         data-title="{{ $task['technician']->name }}">
        @php
            $isLate = strtotime($task['estimated_date']) < strtotime(date('Y-m-d'));
            $dateClass = $isLate ? 'danger' : 'success';
            $icon = $dateClass == 'danger'
                        ? '<i class="ms-2 me-2 fa-solid fa-hourglass-end fa-xs text-' . $dateClass . '"></i>'
                        : '<i class="ms-2 me-2 fa-solid fa-hourglass-start fa-xs p-0 m-0 text-' . $dateClass . '"></i>';
        @endphp
        {!! $icon !!}{{ __($task['name']) }}
    </div>
    @if ($project_id != -1)
        <div class="taskList tooltipCus col-sm-12 text-end"
             data-title="{{ $task['technician']->name }}">
            <a href="#">
            </a>
        </div>
    @endif
@endforeach

                                                            @if ($project_id == -1)
                                                                <div class="col-sm-11 text-end"
                                                                data-title="{{ $task['technician']->name }}">
                                                                    <a href="#">
                                                                        <!-- <img alt="image" class="user-groupTasks"
                                                                            @if ($task['technician']->avatar) src="{{ asset($task['technician']->avatar) }}" @else avatar="{{ $task['technician']->name }}" @endif> -->
                                                                    </a>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <div class="text-muted text-center m-2" style="width: 80%">
                                                            {{ __('No tasks in progress') }}...
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="card mb-0">
                                                    <div class="card-body p-2">
                                                        <div class="row">
                                                            <div class="foot-milestone">
                                                                <div class="col-6 text-center">
                                                                    <div class="text-center tooltipCus"
                                                                        data-title="{{ __('Project') }}">
                                                                        <div>
                                                                            <img class="img-fluid p-1 adjustImg"
                                                                                src="{{ asset('assets/img/' . $milestone['project_type'] . '.png') }}"
                                                                                alt="Project type">
                                                                        </div>
                                                                        <b
                                                                            style="font-size: 12px">{{ $milestone['project_name'] }}</b>
                                                                        <span class="text-muted"
                                                                            data-title="{{ __('Ref. M.O') }}"><b>{{ $milestone['project_ref'] }}</b></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6 text-center tooltipCus"
                                                                    data-title="{{ __('End Date') }}">
                                                                    @if ($milestone['daysleft'] < 1)
                                                                        <i class="fa-solid fa-calendar-check fa-beat-fade m-1 pb-1 fa-2xl calendarAlert"
                                                                            style="color: red;"></i>
                                                                    @elseif($milestone['daysleft'] < 3)
                                                                        <i class="fa-solid fa-calendar-check fa-2xl m-1 calendarAlert"
                                                                            style="color: #db8d33;"></i>
                                                                    @else
                                                                        <i class="fa-solid fa-calendar-check fa-2xl m-1 calendarAlert"
                                                                            style="color: #53b446;"></i>
                                                                    @endif
                                                                    <div class="text-center adjustTextCalendar">
                                                                        <b style="font-size: 12px">
                                                                            {{ \App\Models\Utility::dateFormat($milestone['end_date']) }}</b>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="empty-container" data-placeholder="Empty"></span>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('css-page')
    <link rel="stylesheet" href="{{ asset('assets/custom/css/dropzone.min.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('assets/custom/js/dragula.min.js') }}"></script>
    @if ($milestones != null)
        @push('scripts')
            <!-- <script>
                ! function(a) {
                    "use strict";

                    var t = function() {
                        this.$body = a("body");
                    };

                    t.prototype.init = function() {
                        a('[data-toggle="dragula"]').each(function() {
                            var containers = a(this).data("containers");
                            var containersArray = [];

                            if (containers && containers.length) {
                                for (var i = 0; i < containers.length; i++) {
                                    var container = a("#" + containers[i] + " .kanban-box")[0];
                                    if (container) {
                                        containersArray.push(container);
                                    } else {
                                        console.error('Contenedor no encontrado:', containers[i]);
                                    }
                                }
                            } else {
                                containersArray = [a(this)[0]];
                            }
                            var handleClass = a(this).data("handleclass");
                            dragula(containersArray, {
                                moves: function(el, container, handle) {

                                    return el.classList.contains('card');
                                }
                            }).on('drop', handleDrop);
                        });
                    };
    function handleDrop(el, target, source, sibling) {
    var sort = [];
    a(target).find(".card").each(function(key) {
        var cardId = a(this).attr('id');
        if (cardId) {
            console.log('Card ID at index', key, ':', cardId);
            sort.push(cardId);
        } else {
            console.warn('Card at index', key, 'does not have an ID');
        }
    });
    
    // Obtenemos el cardId del elemento que se acaba de mover
    var cardId = a(el).attr('id');
    var oldStatus = a(source).data('status');
    var newStatus = a(target).data('status');
    var project_id = a(el).data('project-id');
    var milestoneTitle = a(el).find('#mileTitle').text(); // Obtenemos el título del milestone
    
    if (oldStatus == 1 && newStatus == 2) {
        console.log('De por hacer a in progress');
        console.log('Card ID:', cardId, 'Old status:', oldStatus, 'New status:', newStatus, 'Project ID:', project_id, 'Milestone Title:', milestoneTitle);

        // Se dispara la misma acción que al hacer clic en "Add Task on Timesheet"
        var url = '{{ route('tasks.create', $currentWorkspace->slug) }}' + '?project_id=' + project_id + '&milestoneTitle=' + milestoneTitle  + '&milestone_id=' + cardId;
        var title = '{{ __('Create New Task') }}';
        var modalId = 'commonModal';

        $("#" + modalId + " .modal-title").html(title);
        $.ajax({
            url: url,
            dataType: 'html',
            success: function(data) {
                $('#' + modalId + ' .body').html(data);
                $("#" + modalId).modal('show');
                commonLoader();
                loadConfirm();
            }
        });
    }
    updateTaskCount(source);
    updateTaskCount(target);

    a.ajax({
        url: '{{ route('milestone.update.order', [$currentWorkspace->slug, $milestone['project_id']]) }}',
        type: 'POST',
        data: {
            id: cardId,  // Se envía el cardId obtenido
            sort: sort,
            new_status: newStatus,
            old_status: oldStatus,
            project_id: project_id
        },
        success: function(data) {
            console.log('AJAX success');
        },
        error: function(xhr, status, error) {
            console.error('Error al actualizar el orden:', error);
        }
    });
}

                    function updateTaskCount(container) {
                        var parentCardList = a(container).parents('.card-list');
                        var count = a(container).children('.card').length;
                        parentCardList.find('.count').text(count);
                    }

                    a.Dragula = new t;
                    a.Dragula.Constructor = t;

                }(window.jQuery);

                ! function(a) {
                    "use strict";
                    a.Dragula.init();
                }(window.jQuery);
            </script> -->


            <script>
    ! function(a) {
        "use strict";

        var t = function() {
            this.$body = a("body");
        };

        t.prototype.init = function() {
            a('[data-toggle="dragula"]').each(function() {
                var containers = a(this).data("containers");
                var containersArray = [];

                if (containers && containers.length) {
                    for (var i = 0; i < containers.length; i++) {
                        var container = a("#" + containers[i] + " .kanban-box")[0];
                        if (container) {
                            containersArray.push(container);
                        } else {
                            console.error('Contenedor no encontrado:', containers[i]);
                        }
                    }
                } else {
                    containersArray = [a(this)[0]];
                }
                var handleClass = a(this).data("handleclass");

                // Inicializamos dragula y agregamos eventos 'drag' y 'drop'
                dragula(containersArray, {
                    moves: function(el, container, handle) {
                        return el.classList.contains('card');
                    }
                })
                // Al iniciar el drag, almacenamos el contenedor de origen y el índice original
                .on('drag', function(el, source) {
                    a(el).data('originContainer', source);
                    a(el).data('originalIndex', a(source).children('.card').index(a(el)));
                })
                // Al soltar el elemento se ejecuta el handleDrop
                .on('drop', handleDrop);
            });
        };

        function handleDrop(el, target, source, sibling) {
            // Obtenemos el nuevo orden de los elementos en el contenedor destino
            var sort = [];
            a(target).find(".card").each(function(key) {
                var cardId = a(this).attr('id');
                if (cardId) {
                    console.log('Card ID at index', key, ':', cardId);
                    sort.push(cardId);
                } else {
                    console.warn('Card at index', key, 'does not have an ID');
                }
            });

            // Obtenemos información necesaria
            var cardId = a(el).attr('id');
            // Utilizamos .data() para obtener el status; sin embargo, al mover la tarjeta actualizaremos el atributo
            var oldStatus = a(source).data('status');
            var newStatus = a(target).data('status');
            var project_id = a(el).data('project-id');
            var milestoneTitle = a(el).find('#mileTitle').text(); // Título del milestone

            // Definir las transiciones permitidas
            const allowedTransitions = {
    4: [3],    // oldStatus 4 solo puede ir a 3
    3: [2, 4], // oldStatus 3 solo puede ir a 2 o 4
    2: [3],    // oldStatus 2 solo puede ir a 3
    1: [2]     // oldStatus 1 solo puede ir a 2
};

// Verificar si el movimiento es válido
if (!(allowedTransitions[oldStatus] && allowedTransitions[oldStatus].includes(newStatus))) {
    console.log(`Movimiento no permitido: No se puede mover un elemento de status ${oldStatus} a status ${newStatus}.`);

    // Se recupera el contenedor de origen y la posición original que se almacenaron en el evento "drag"
    var originContainer = a(el).data('originContainer');
    var originalIndex = a(el).data('originalIndex');

    // Asegurarse de que el contenedor de origen aún existe
    var $origin = a(originContainer);
    if ($origin.length === 0) {
        console.error("El contenedor de origen ya no existe en el DOM.");
        return;
    }

    // Remover el elemento del contenedor destino correctamente
    a(el).detach(); // Mejor que .remove(), evita errores de referencia

    // Obtener todas las tarjetas dentro del contenedor original
    var $cards = $origin.children('.card');

    // Reinsertamos en la posición original
    if ($cards.length > 0 && originalIndex < $cards.length) {
        a(el).insertBefore($cards.eq(originalIndex));
    } else {
        $origin.append(el);
    }

    // Limpiar referencias de origen para evitar anidamientos
    a(el).data('originContainer', null);
    a(el).data('originalIndex', null);

    // Actualizamos el contador de tareas de ambos contenedores
    updateTaskCount(source);
    updateTaskCount(target);

    // No se ejecuta la llamada AJAX ya que se ha cancelado el cambio
    return;
}


            // Si se permite el movimiento y es de status 1 a 2, se dispara la acción de "Add Task"
            if (oldStatus == 1 && newStatus >= 2) {
                console.log('De por hacer a in progress');

                var url = '{{ route('tasks.create', $currentWorkspace->slug) }}' + '?project_id=' + project_id + '&milestoneTitle=' + milestoneTitle  + '&milestone_id=' + cardId;
                var title = '{{ __('Create New Task') }}';
                var modalId = 'commonModal';

                $("#" + modalId + " .modal-title").html(title);
                $.ajax({
                    url: url,
                    dataType: 'html',
                    success: function(data) {
                        $('#' + modalId + ' .body').html(data);
                        $("#" + modalId).modal('show');
                        commonLoader();
                        loadConfirm();
                    }
                });
            }

            // Actualizamos los contadores de tareas en los contenedores de origen y destino
            updateTaskCount(source);
            updateTaskCount(target);

            // *** Actualización dinámica del status en el elemento ***
            // Esto asegura que, si se mueve la tarjeta y cambia su status,
            // el atributo data-status se actualiza y el toggle lo detecta correctamente.
            a(el).attr('data-status', newStatus);

            // Se realiza la llamada AJAX para actualizar el orden y el estado en el servidor
            a.ajax({
                url: '{{ route('milestone.update.order', [$currentWorkspace->slug, $milestone['project_id']]) }}',
                type: 'POST',
                data: {
                    id: cardId,  // Se envía el cardId obtenido
                    sort: sort,
                    new_status: newStatus,
                    old_status: oldStatus,
                    project_id: project_id
                },
                success: function(data) {
                    console.log('AJAX success');
                },
                error: function(xhr, status, error) {
                    console.error('Error al actualizar el orden:', error);
                }
            });
        }

        function updateTaskCount(container) {
            var parentCardList = a(container).parents('.card-list');
            var count = a(container).children('.card').length;
            parentCardList.find('.count').text(count);
        }

        a.Dragula = new t;
        a.Dragula.Constructor = t;

    }(window.jQuery);

    ! function(a) {
        "use strict";
        a.Dragula.init();
    }(window.jQuery);
</script>
@if ($project_id == -1)
<!-- Script encargado de mostrar/ocultar los proyectos completado -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    let showCompleted = false; // Variable global para rastrear la visibilidad de proyectos completados

    // Inicializa: Oculta grupos de milestones cuyo TODOS elementos tengan status 4
    initializeCompletedProjects();

    // Configura el listener para el toggle
    const toggleIcon = document.getElementById('toggleCompletedProjectsIcon');
    if (toggleIcon) {
        toggleIcon.addEventListener('click', function() {
            showCompleted = !showCompleted;
            toggleCompletedProjects(showCompleted);
            this.classList.toggle('showCompletedProjectsUnabled', !showCompleted);
            this.title = showCompleted ? "{{ __('Hide Completed Projects') }}" : "{{ __('Show Completed Projects') }}";
        });
    }

    function initializeCompletedProjects() {
        const milestones = document.querySelectorAll('.card[data-project-id]');
        const projectMap = new Map();

        milestones.forEach(milestone => {
            const projectId = milestone.dataset.projectId;
            if (!projectMap.has(projectId)) {
                projectMap.set(projectId, []);
            }
            projectMap.get(projectId).push(milestone);
        });

        projectMap.forEach((milestones, projectId) => {
            const allInStatus4 = milestones.every(m => parseInt(m.dataset.status) === 4);
            if (allInStatus4) {
                milestones.forEach(m => m.style.display = 'none');
            }
        });
    }

    function toggleCompletedProjects(shouldShow) {
        const milestones = document.querySelectorAll('.card[data-project-id]');
        const projectMap = new Map();

        milestones.forEach(milestone => {
            const projectId = milestone.dataset.projectId;
            if (!projectMap.has(projectId)) {
                projectMap.set(projectId, []);
            }
            projectMap.get(projectId).push(milestone);
        });

        projectMap.forEach((milestones, projectId) => {
            const allInStatus4 = milestones.every(m => parseInt(m.dataset.status) === 4);
            milestones.forEach(m => m.style.display = (allInStatus4 && !shouldShow) ? 'none' : 'block');
        });
    }

    function checkAndUpdateProjectVisibility(el) {
        const projectId = el.dataset.projectId;
        const projectMilestones = document.querySelectorAll(`.card[data-project-id='${projectId}']`);
        const allInStatus4 = Array.from(projectMilestones).every(m => parseInt(m.dataset.status) === 4);

        if (allInStatus4 && !showCompleted) {
            projectMilestones.forEach(m => m.style.display = 'none');
        }
    }

    // Agrega un MutationObserver para detectar cambios en data-status y actualizar dinámicamente
    function observeMilestoneStatusChanges() {
        const milestoneCards = document.querySelectorAll('.card[data-project-id]');
        milestoneCards.forEach(card => {
            const observer = new MutationObserver(mutations => {
                mutations.forEach(mutation => {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'data-status') {
                        checkAndUpdateProjectVisibility(card);
                    }
                });
            });
            observer.observe(card, {
                attributes: true,
                attributeFilter: ['data-status']
            });
        });
    }

    observeMilestoneStatusChanges();
});
</script>

@endif
<!-- Script encargado de la acción de "Add Task on Timesheet" al hacer clic en una tarea (se desactiva si el milestone está en status 4) -->
<script>
    // Espera a que el DOM esté completamente cargado
    document.addEventListener('DOMContentLoaded', function() {
        
        // Selecciona todos los elementos con la clase .taskList
        const tasks = document.querySelectorAll('.taskList');

        tasks.forEach(task => {
            task.addEventListener('click', function() {
                // Obtén el milestone asociado a la tarea
                const milestone = this.closest('.card');
                const milestoneStatus = milestone.getAttribute('data-status');

                // Verifica si el milestone está en status 4
                if (milestoneStatus === '4' || milestoneStatus === '3') {
                    console.log('El milestone está en status 3 o 4, no se ejecutará la acción.');
                    return; // Detiene la ejecución del código si el status es 4
                }

                // Obtiene los valores de los atributos data
                const taskData = {
                    task_id: this.getAttribute('data-task-id'),
                    milestone_id: this.getAttribute('data-milestone-id'),
                    project_id: this.getAttribute('data-project-id'),
                    user_id: this.getAttribute('data-technician-name'),
                    date : new Date().toISOString().split('T')[0],
                };

                // Muestra los datos en la consola del navegador
                console.log(taskData);
                $.ajax({
                    url : '{{ route('create.timesheet.from.orders', [$currentWorkspace->slug, $project_id]) }}',
                    type: 'GET',
                    data: taskData,
                    success: function(data) {
                        console.log('AJAX success'); // Verificar contenido
                        $('#modal-container .modal-content').html(data);
                        var myModal = new bootstrap.Modal(document.getElementById('modal-container'));
                        myModal.show();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error al actualizar el orden:', error);
                    }
                });
            });
        });
    });
</script>
<!-- Script encargado de mostrar/ocultar las opciones cuando el estado del proyecto esta en 4 (en Hecho) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Actualiza la visibilidad de las opciones de acción según el status
    updateMilestoneActions();

    // Agrega un MutationObserver para detectar cambios en data-status y actualizar dinámicamente
    function observeMilestoneStatusChanges() {
        const milestoneCards = document.querySelectorAll('.card[data-project-id]');
        milestoneCards.forEach(card => {
            const observer = new MutationObserver(mutations => {
                mutations.forEach(mutation => {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'data-status') {
                        updateMilestoneActions();
                    }
                });
            });
            observer.observe(card, {
                attributes: true,
                attributeFilter: ['data-status']
            });
        });
    }

    function updateMilestoneActions() {
        const milestoneCards = document.querySelectorAll('.card[data-project-id]');
        milestoneCards.forEach(card => {
            const status = parseInt(card.dataset.status);
            const actionItems = card.querySelectorAll('.milestone-action');
            if (status === 4) {
                actionItems.forEach(item => item.style.display = 'none');
            } else {
                actionItems.forEach(item => item.style.display = '');
            }
        });
    }

    observeMilestoneStatusChanges();
});
</script>
        @endpush
    @endif
