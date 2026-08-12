@extends('layouts.admin')
@php
    $client_keyword = Auth::user()->type == 'client' ? 'client' : '';
    $logo = \App\Models\Utility::get_file('avatars/');
    $logo_tasks = \App\Models\Utility::get_file('tasks/');
    use App\Models\User;
@endphp
@section('page-title')
    {{ __('My Order Forms Board') }}
@endsection

<head>
    <link rel="stylesheet" href="{{ asset('assets/css/milestoneboard.css') }}">
</head>
<style>
    .showCompletedProjectGroup {
        display: flex;
        gap: 17px;
    }

    .showCompletedProjects {
        width: 20px;
        height: 20px;
        margin-top: -6px;
    }

    .showCompletedProjects:hover {
        cursor: pointer;

    }

    .showCompletedProjectsUnabled {
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

    .toastNegation {
        z-index: 30;
        position: absolute;
        top: 100px;
        bottom: auto;
        right: 10px;
        display: flex;
        text-align: center;
        align-content: center;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }

    .fixedHeight {
        min-height: 600px;
        max-height: 600px;
        height: 600px;
        overflow: hidden;
        overflow-y: auto;
        scrollbar-color: #aa182c #ffff0000;
        scrollbar-width: thin;
        /* position: relative; */
    }

    .fixedHeight::-webkit-scrollbar {
        width: 8px;
        height: 10px;
        /* Ancho del scrollbar */
    }

    /* Fondo del scrollbar */
    .fixedHeight::-webkit-scrollbar-track {
        background: #ffffff;
        /* Color del fondo */
        border-radius: 4px;
        /* Bordes redondeados */
    }

    /* Parte deslizable del scrollbar */
    .fixedHeight::-webkit-scrollbar-thumb {
        background: #AA182C;
        /* Color del scrollbar */
        border-radius: 4px;
        height: 10px;
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

    .notAsignedMilestone {
        border: 3px solid #a62330 !important;
    }

    .legendIcon {
        width: 20px;
        margin-left: 8px;
        cursor: pointer;
        transition: transform 0.1s ease-in-out;
    }

    .legend {
        opacity: 0;
        background-color: #f9fbfa;
        border: 2px solid transparent;
        border-radius: 15px;
        width: 394px;
        height: 735px;
        position: absolute;
        top: 4.5%;
        left: 21rem;
        display: flex;
        z-index: 3;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        filter: drop-shadow(1px 1px 5px #b1b1b1);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        pointer-events: none;
    }
    .legend-close {
        position: absolute;
        top: 10px;
        right: 14px;
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #666;
        line-height: 1;
        padding: 0;
        z-index: 4;
        pointer-events: auto;
    }

    .legend.visible {
        pointer-events: auto;

    }


    .colorExample {
        width: 20px;
        height: 20px;
        border-radius: 100%;
        position: absolute;
        right: 2rem;
    }

    i.lIcons {
        margin-right: 0.9rem !important;
        color: black;
        margin-top: -6px !important;
    }

    .colorText {
        position: absolute;
        left: 2.5rem;
    }

    .lEntry {
        border-radius: 10px;
        display: flex;
        width: 92%;
        height: 3rem;
        margin-bottom: 10px;
        align-items: center;
        justify-content: space-around;
        align-content: center;
        font-size: 13px;
        font-weight: 600;
    }



    .lDetail {
        border: 2px solid gray;
        gap: 47px;
        text-align: center;
        height: 7rem;
        background-color: #80808030;
        color: #6b6b6b;
    }

    .lTitle {
        position: absolute;
        top: 1rem;
        font-size: 20px;
        font-weight: 600;
        color: black;
    }

    .lastBreadCrumb {
        /* background-color: #AA182C !important; */
        /* width: 80%; */
        max-width: 700px;
        overflow: hidden;
        text-wrap: nowrap;
        text-overflow: ellipsis;
    }

    .kanban-box .dash-micon {
        margin-right: 0;
        margin-bottom: 16px;
        height: 80px;
        width: 80px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
    }
    .kanban-box .dash-micon i {
        font-size: 48px;
        color: #adb5bd;
    }
    .empty-state-text {
        font-size: 20px;
        color: #d1d1d1;
    }
    .filtered-empty-state {
        display: none;
    }
</style>
@section('links')
    @if (isset($project_id) && $project_id != -1)
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('projects.index', $currentWorkspace->slug) }}">{{ __('Projects') }}</a>
        </li>

        <li class="breadcrumb-item lastBreadCrumb"><a
                href="{{ route('projects.show', [$currentWorkspace->slug, $project_id]) }}">{{ $project_name }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
        <li class="breadcrumb-item"><a
                href="{{ route('projects.index', $currentWorkspace->slug) }}">{{ __('Projects') }}</a></li>
    @endif
    <li class="breadcrumb-item">{{ __('My Order Forms Board') }}</li>
    <img class="legendIcon" src="{{ asset('assets/img/questionCircle.svg') }}" />
    <div class="legend">
        <button type="button" class="legend-close" onclick="this.closest('.legend').style.opacity='0'; this.closest('.legend').style.pointerEvents='none';">&times;</button>
        <span class="lTitle">{{ __('Color legend') }}</span>
        <hr style="backgroundColor: #e0e1e1; width: 100%; height: 2px;">
        <div class="lEntry" style="border:2px solid #000000 ;height: 5rem !important; text-align: center; "
            style="display: flex; ">
            <span>{{ __('Comparison with the delivery date desired by the manager') }}</span>
            <i class="fa-solid fa-calendar-check fa-2xl m-1 calendarAlert lIcons"></i>
        </div>
        <div class="lEntry" style="border:2px solid #000000 ;height: 5rem !important; text-align: center;">
            <span>{{ __('Comparison with the estimated delivery date') }}</span>
            <i class="ms-2 me-2 fa-solid fa-hourglass-start lIcons" style=" font-size:22px;"></i>
        </div>
        <hr style="backgroundColor: #e0e1e1; width: 100%; height: 2px;">
        <div class="lEntry" style="border:2px solid #000000 ;  border-left: 12px solid black;">
            <span class="colorText">{{ __('Undelivered, still on schedule') }}</span>
            <div class="colorExample" style="background-color: #000000; border:1.5px solid #000000 ;"></div>
        </div>
        <div class="lEntry" style="border:2px solid #db8d33;  border-left: 12px solid #db8d33;">
            <span class="colorText">{{ __('Undelivered,out of date') }}</span>
            <div class="colorExample" style="background-color: #db8d33; border:1.5px solid #db8d33"></div>
        </div>
        <div class="lEntry" style="border:2px solid #53b446;  border-left: 12px solid #53b446;">
            <span class="colorText">{{ __('Delivered on schedule') }}</span>
            <div class="colorExample" style="background-color: #53b446; border:1.5px solid #53b446"></div>
        </div>

        <div class="lEntry" style="border:2px solid #ff0000;  border-left: 12px solid #ff0000;">
            <span class="colorText">{{ __('Delivered, out of date') }}</span>
            <div class="colorExample" style="background-color: #ff0000; border:1.5px solid #ff0000"></div>
        </div>
        <div class="lEntry" style="border:2px solid #a62330; gap:47px; border-left: 12px solid #a62330;">
            <span class="colorText">{{ __('Order pending of assignment') }}</span>
            <div class="colorExample" style="background-color: #a62330; border:1.5px solid #a62330; "></div>
        </div>
        <div class="lEntry lDetail">
            <span>{{ __('In this section you will find only the job sheets assigned to you or created by you.') }}</span>
        </div>
    </div>
@endsection

@section('action-button')
    <div class="d-flex justify-content-end row1">
        <div id="modal-container" class="modal fade" tabindex="-1" role="dialog" data-bs-backdrop="static">
            <div class="modal-dialog" role="document">
                <div class="modal-content" style="text-align: left; width: 800px;">
                    <!-- El contenido del modal se cargará aquí -->
                </div>
            </div>
        </div>
        @if (isset($currentWorkspace) && $currentWorkspace)
            @include('projects.partials.milestone_filter_popUp', ['filtersPopupMode' => 'my_board'])
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
                                        {{-- @if ($project_id == -1)
                                            <img id="toggleCompletedProjectsIcon"
                                                src="{{ asset('assets/img/clipboard-check-solid.svg') }}"
                                                alt="show completed projects" title="{{ __('Show Completed Projects') }}"
                                                class="showCompletedProjects showCompletedProjectsUnabled" />
                                        @endif --}}
                                    @endif
                                    <button class="btn-submit btn btn-md btn-primary btn-icon px-1 py-0 "
                                        style="height: 19.7px;">
                                        <span class="badge badge-secondary rounded-pill count">
                                            {{ isset($milestones[$status->id]) ? count($milestones[$status->id]) : 0 }}
                                        </span>
                                    </button>
                                </div>
                                <h4 class="mb-0">
                                    @switch($status->id)
                                        @case(1)
                                            <i class="ti ti-clipboard-list me-1" style="color: #aa182c;"></i>
                                            @break
                                        @case(2)
                                            <i class="ti ti-activity me-1" style="color: #aa182c;"></i>
                                            @break
                                        @case(3)
                                            <i class="ti ti-eye me-1" style="color: #aa182c;"></i>
                                            @break
                                        @case(4)
                                            <i class="ti ti-circle-check me-1" style="color: #aa182c;"></i>
                                            @break
                                    @endswitch
                                    {{ __($status->name) }}
                                </h4>
                            </div>
                            <div id="{{ 'milestone-list-' . str_replace(' ', '_', $status->id) }}"
                                data-status="{{ $status->id }}" class="card-body kanban-box fixedHeight">
                                @php
                                    // Evitar duplicados
                                    $renderedMilestonesIds = $renderedMilestonesIds ?? [];
                                @endphp

                                {{-- ========================= --}}
                                {{--   MILESTONES DEL USUARIO   --}}
                                {{-- ========================= --}}
                                @if (isset($milestones[$status->id]))
                                    @foreach ($milestones[$status->id] as $milestone)
                                        {{-- Registrar ID para evitar duplicados --}}
                                        @php $renderedMilestonesIds[] = $milestone['id']; @endphp

                                        @include('projects.partials.milestone_card', [
                                            'milestone' => $milestone,
                                            'status' => $status,
                                            'currentWorkspace' => $currentWorkspace,
                                            'project_id' => $project_id,
                                            'extraClass' => '',
                                            'inlineStyle' => '',
                                            'ownerShip' => 'yes',
                                        ])
                                    @endforeach
                                @endif

                                @php
                                    $msCountLocal = isset($milestones[$status->id]) ? count($milestones[$status->id]) : 0;
                                @endphp
                                <div class="noNotificationsContainer" style="margin-top: -20px; {{ $msCountLocal > 0 ? 'display: none;' : '' }}">
                                    <span class="dash-micon">
                                        <i class="fa-solid fa-file-lines"></i>
                                    </span>
                                    <small class="empty-state-text">{{ __('No order forms yet') }}</small>
                                    <span style="font-size: 11px; color: #999; margin-top: 4px;">{{ __('Drag an order form here') }}</span>
                                </div>

                                <div class="noNotificationsContainer filtered-empty-state" style="margin-top: -20px; display: none;">
                                    <span class="dash-micon">
                                        <i class="fa-solid fa-eye-slash"></i>
                                    </span>
                                    <small class="empty-state-text">{{ __('Hidden order forms') }}</small>
                                    <span style="font-size: 11px; color: #999; margin-top: 4px;">{{ __('Apply filters to view them') }}</span>
                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@include('projects.file_preview')

@push('css-page')
    <link rel="stylesheet" href="{{ asset('assets/custom/css/dropzone.min.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('assets/custom/js/dragula.min.js') }}"></script>
    @if ($milestones != null)
        @push('scripts')



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
                                        if (handle && handle.closest && handle.closest('.milestone-dropdown-toggle')) {
                                            return false;
                                        }
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
                        // El título vive en el atributo data-milestone-title del card o en .milestone-title
                        var milestoneTitle = a(el).attr('data-milestone-title') || a(el).find('.milestone-title').text() || '';
                        console.log("=== DEBUG DRAG AND DROP ===");
                        console.log("Card ID:", cardId);
                        console.log("Project ID:", project_id);
                        console.log("Milestone Title:", milestoneTitle);
                        console.log("Old Status:", oldStatus);
                        console.log("New Status:", newStatus);
                        console.log("El completo:", el);
                        console.log("=== FIN DEBUG ===");

                        // Definir las transiciones permitidas
                        const allowedTransitions = {
                            4: [3], // oldStatus 4 solo puede ir a 3
                            3: [2, 4], // oldStatus 3 solo puede ir a 2 o 4
                            2: [3], // oldStatus 2 solo puede ir a 3
                            1: [2] // oldStatus 1 solo puede ir a 2
                        };

                        // Verificar si el movimiento es válido
                        if (!(allowedTransitions[oldStatus] && allowedTransitions[oldStatus].includes(newStatus))) {
                            console.log(
                                `Movimiento no permitido: No se puede mover un elemento de status ${oldStatus} a status ${newStatus}.`
                            );

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


                        // Si se permite el movimiento y es de status 1 a 2, mostrar directamente el popup de crear tarea
                        if (oldStatus == 1 && newStatus >= 2) {

                            console.log('De por hacer a in progress');

                            var modalId = 'commonModal';

                            // cardId = el id del milestone (en tu HTML id="{{ $milestone['id'] }}")
                            var cardId = a(el).attr('id');

                            // slug del workspace REAL de esa card (en my board es crítico)
                            var wsSlug = a(el).data('workspace-slug') || a(el).attr('data-workspace-slug');

                            var project_id = a(el).data('project-id');

                            // título robusto
                            var milestoneTitle =
                                a(el).data('milestone-title') ||
                                a(el).attr('data-milestone-title') ||
                                a(el).find('.milestone-title').text() ||
                                'Sin título';

                            if (!wsSlug || !cardId || !project_id) {
                                console.error('Faltan datos:', {
                                    wsSlug,
                                    cardId,
                                    project_id,
                                    milestoneTitle
                                });
                                alert(
                                    'Error: no se pudo determinar workspace/proyecto/milestone. Revisa data-workspace-slug en la card.'
                                );
                                return;
                            }

                            // -----------------------------
                            // 1) Abrir modal ASSIGN
                            // -----------------------------
                            var assignUrlTemplate = "{{ route('projects.milestone.assign', ['__SLUG__', ':id']) }}";
                            var assignUrl = assignUrlTemplate
                                .replace('__SLUG__', encodeURIComponent(wsSlug))
                                .replace(':id', cardId);

                            var assignTitle = "{{ __('Assign Milestone') }}";
                            $("#" + modalId + " .modal-title").html(assignTitle);

                            $.ajax({
                                url: assignUrl,
                                dataType: 'html',
                                success: function(assignData) {

                                    var modalEl = document.getElementById(modalId);
                                    var modal = bootstrap.Modal.getOrCreateInstance(modalEl, {
                                        backdrop: 'static',
                                        keyboard: false
                                    });

                                    // Reset modal dialog
                                    var modalDialog = modalEl.querySelector('.modal-dialog');
                                    if (modalDialog) {
                                        modalDialog.className = 'modal-dialog';
                                        modalDialog.removeAttribute('style');
                                    }

                                    $('#' + modalId + ' .body').html(assignData);

                                    // Marcar que viene de drag&drop
                                    $('#asignMilestoneForm').attr('data-from-status-change', 'true');

                                    // Guardar contexto (por si lo necesitas dentro del form)
                                    $('#' + modalId).data('ws-slug', wsSlug);
                                    $('#' + modalId).data('milestone-id', cardId);
                                    $('#' + modalId).data('project-id', project_id);

                                    modal.show();

                                    // -----------------------------------
                                    // 2) Cuando se asigne -> abrir CREATE TASK
                                    // -----------------------------------
                                    document.addEventListener('milestoneAssigned', function showTaskModal() {

                                        // (recalcular el título por si cambió algo)
                                        var $milestoneCard = a("#" + cardId);
                                        var retrievedTitle =
                                            $milestoneCard.data('milestone-title') ||
                                            $milestoneCard.attr('data-milestone-title') ||
                                            $milestoneCard.find('.milestone-title').text() ||
                                            milestoneTitle ||
                                            'Sin título';

                                        var createTaskUrlTemplate =
                                            "{{ route('tasks.create', ['__SLUG__']) }}";
                                        var createTaskUrl = createTaskUrlTemplate
                                            .replace('__SLUG__', encodeURIComponent(wsSlug)) +
                                            '?project_id=' + encodeURIComponent(project_id) +
                                            '&milestoneTitle=' + encodeURIComponent(retrievedTitle) +
                                            '&milestone_id=' + encodeURIComponent(cardId) +
                                            '&fromMyMilestoneBoard=true';

                                        var createTaskTitle = "{{ __('Create New Task') }}";
                                        $("#" + modalId + " .modal-title").html(createTaskTitle);

                                        $.ajax({
                                            url: createTaskUrl,
                                            dataType: 'html',
                                            success: function(taskData) {

                                                var modalEl = document.getElementById(modalId);
                                                var modal = bootstrap.Modal.getOrCreateInstance(
                                                    modalEl, {
                                                        backdrop: 'static',
                                                        keyboard: false
                                                    });

                                                var modalDialog = modalEl.querySelector(
                                                    '.modal-dialog');
                                                if (modalDialog) {
                                                    modalDialog.className = 'modal-dialog';
                                                    modalDialog.removeAttribute('style');
                                                }

                                                $('#' + modalId + ' .body').html(taskData);
                                                modal.show();

                                                commonLoader();
                                                loadConfirm();
                                            },
                                            error: function(xhr, status, error) {
                                                console.error(
                                                    'Error al cargar el formulario de tarea:',
                                                    error);
                                                console.error('Response:', xhr.responseText);
                                                alert(
                                                    'Error al cargar el formulario de creación de tarea'
                                                );
                                            }
                                        });

                                    }, {
                                        once: true
                                    });

                                    commonLoader();
                                    loadConfirm();
                                },
                                error: function(xhr, status, error) {
                                    console.error('Error al cargar el modal de asignación:', error);
                                    console.error('Response:', xhr.responseText);
                                    alert('Error al cargar el modal de asignación');
                                }
                            });

                            // IMPORTANTÍSIMO:
                            // NO abras aquí el modal de crear tarea.
                            // Solo debe abrirse cuando se dispare "milestoneAssigned".
                            return;
                        }


                        // Si se permite el movimiento y es de status 3 a 4, se genera una notificación

                        if (oldStatus == 3 && newStatus == 4) {
                            console.log('Generando notificacion de milestone completado');
                            let msg = milestoneTitle;
                            let ntipe = 3;
                            if (!msg) return;

                            fetch("{{ route('notifications.add') }}", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/json",
                                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                    },
                                    body: JSON.stringify({
                                        workspace_id: {{ $currentWorkspace->id }},
                                        msg: msg,
                                        ntipe: ntipe,
                                        milestoneAssignedTo: -2
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        let notificationList = document.querySelector('.limited');
                                        let newNotification = document.createElement('div');
                                        newNotification.classList.add('notificationSTL');
                                        const spanMsg = document.createElement('span');
                                        spanMsg.className = 'textRepo';
                                        spanMsg.textContent = data.data.msg;
                                        const spanType = document.createElement('span');
                                        spanType.className = 'textRepo';
                                        spanType.textContent = data.data.type;
                                        const btn = document.createElement('button');
                                        btn.type = 'button';
                                        btn.className = 'btn-close repoIcon';
                                        btn.setAttribute('aria-label', 'Close');
                                        newNotification.appendChild(spanMsg);
                                        newNotification.appendChild(spanType);
                                        newNotification.appendChild(btn);
                                        notificationList.prepend(newNotification);
                                    }
                                })
                                .catch(error => console.error("Error al agregar notificación:", error));
                        }
                        if (oldStatus == 3 && newStatus == 2) {
                            console.log("Milestone 3 -> 2: abrir modal de motivo (NO borrar puntuaciones aquí).");

                            const modal = document.getElementById('statusChangeModal');
                            if (!modal) {
                                console.error('No existe #statusChangeModal en la vista.');
                                return false;
                            }

                            // Guardar datos para el submit
                            modal.dataset.milestoneId = cardId;
                            modal.dataset.projectId = project_id;
                            modal.dataset.oldStatus = oldStatus;
                            modal.dataset.newStatus = newStatus;
                            modal.dataset.sort = JSON.stringify(sort);

                            // Para saber si se guardó o canceló
                            modal.dataset.saved = '0';

                            // Limpiar textarea
                            document.getElementById('statusChangeComment').value = '';

                            // Mostrar modal
                            bootstrap.Modal.getOrCreateInstance(modal, {
                                backdrop: 'static',
                                keyboard: false
                            }).show();

                            // Cortar para que NO ejecute el AJAX global de abajo
                            return false;
                        }

                        if (oldStatus == 2 && newStatus == 3) {
                            /////////////INICIO status 2 a 3///////////////////////////
                            var milestoneRequBy = a(el).find('#milestoneReqName').attr('data-technician-id');
                            var projectName = a(el).find('#milestoneReqName').attr('data-project-name');
                            var milestonetId = a(el).find('#milestoneReqName').attr('data-milestone-id');
                            console.log("El id del milestone es")
                            console.log(milestonetId);



                            $.ajax({
                                url: '{{ route('projects.milestone.checkTaskHours', [$currentWorkspace->slug, $milestone['id']]) }}',
                                type: 'GET',
                                data: {
                                    id: milestonetId
                                },
                                success: function(data) {
                                    if (data.all_exist) {
                                        console.log('Todas las tareas tienen timesheets.');

                                        console.log("lo ha solicitado:");
                                        console.log(milestoneRequBy)
                                        console.log('Generando notificacion de milestone completado');
                                        console.log('Titulo: ' +
                                            milestoneTitle)
                                        let msg = milestoneTitle + ' en el proyecto ' + projectName;
                                        let ntipe = 5;
                                        if (msg) {
                                            //AQUI FALTA MILESTONEID
                                            fetch("{{ route('notifications.add') }}", {
                                                    method: "POST",
                                                    headers: {
                                                        "Content-Type": "application/json",
                                                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                                    },
                                                    body: JSON.stringify({
                                                        workspace_id: {{ $currentWorkspace->id }},
                                                        msg: msg,
                                                        ntipe: ntipe,
                                                        milestoneAssignedTo: milestoneRequBy,
                                                        milestone_id: milestonetId,
                                                    })
                                                })
                                                .then(response => response.json())
                                                .then(data => {
                                                    if (data.success) {
                                                        let notificationList = document.querySelector('.limited');
                                                        let newNotification = document.createElement('div');
                                                        newNotification.classList.add('notificationSTL');
                                                        const spanMsg = document.createElement('span');
                                                        spanMsg.className = 'textRepo';
                                                        spanMsg.textContent = data.data.msg;
                                                        const spanType = document.createElement('span');
                                                        spanType.className = 'textRepo';
                                                        spanType.textContent = data.data.type;
                                                        const btn = document.createElement('button');
                                                        btn.type = 'button';
                                                        btn.className = 'btn-close repoIcon';
                                                        btn.setAttribute('aria-label', 'Close');
                                                        newNotification.appendChild(spanMsg);
                                                        newNotification.appendChild(spanType);
                                                        newNotification.appendChild(btn);
                                                        notificationList.prepend(newNotification);
                                                    }
                                                })
                                                .catch(error => console.error("Error al agregar notificación:", error));
                                        }
                                        ///// CHECK IF THE MILESTONE HAS A DRAWING TASK /////
                                        // ✅ Verificar si el milestone tiene tareas con type_id = 1 antes de mostrar el popup
                                        $.ajax({
                                            url: '{{ route('projects.milestone.hasDrawingTask', [$currentWorkspace->slug, 0]) }}'
                                                .replace('/0', '/' + milestonetId),
                                            type: 'GET',
                                            success: function(response) {
                                                const hasDrawingTask = response.has_drawing_task;

                                                if (!hasDrawingTask) {
                                                    console.log(
                                                        "⏩ El milestone no tiene tareas type_id = 1 — no se muestra popup de revisión."
                                                    );
                                                    return;
                                                }

                                                console.log(
                                                    "✅ El milestone tiene tareas type_id = 1 — mostrando popup de revisión."
                                                );

                                                // === Mostrar popup de revisión ===
                                                var popupUrl =
                                                    '{{ route('projects.milestone.review', [$currentWorkspace->slug, ':id']) }}'
                                                    .replace(':id', milestonetId);

                                                $("#" + modalId + " .modal-title").html(
                                                    "{{ __('Revisión de hoja de encargo') }}");

                                                $.ajax({
                                                    url: popupUrl,
                                                    data: {
                                                        milestone_id: milestonetId,
                                                        project_id: projectId,
                                                    },
                                                    success: function(response) {
                                                        $("#" + modalId + " .body").html(
                                                            response);
                                                        $("#" + modalId).modal({
                                                            backdrop: 'static',
                                                            keyboard: false
                                                        });
                                                        $("#" + modalId).modal('show');
                                                        // 🧩 Guardamos datos para revertir si el popup se cancela
                                                        $("#" + modalId).data('milestone-id',
                                                            milestonetId);
                                                        $("#" + modalId).data('previous-status',
                                                            2
                                                        ); // Volverá a "In Progress" si se cierra
                                                        $("#" + modalId).data(
                                                            'previous-container', source);

                                                    },
                                                    error: function(xhr) {
                                                        console.error(
                                                            "❌ Error al cargar el popup de revisión:",
                                                            xhr.responseText);
                                                    }
                                                });
                                            },
                                            error: function(xhr, status, error) {
                                                console.error('⚠️ Error al comprobar tareas del milestone:',
                                                    error);
                                            }
                                        });
                                        ///// END CHECK IF THE MILESTONE HAS A DRAWING TASK /////
                                    } else {
                                        console.log('No todas las tareas tienen timesheets.');

                                        $.ajax({
                                            url: '{{ route('milestone.update.order', [$currentWorkspace->slug, $milestone['project_id']]) }}',
                                            type: 'POST',
                                            data: {
                                                id: milestonetId, // Se envía el milestone ID
                                                sort: sort,
                                                new_status: 2,
                                                old_status: 300, // Estado temporal
                                                project_id: project_id
                                            },
                                            success: function(response) {
                                                console.log('Cambio aplicado correctamente');

                                                // Actualizar el DOM: mover el elemento al contenedor correspondiente
                                                const milestoneCard = document.querySelector(
                                                    `.card[id='${milestonetId}']`);
                                                if (milestoneCard) {
                                                    // Actualizar el atributo data-status (si ya lo tienes definido en el HTML)
                                                    milestoneCard.setAttribute('data-status', 2);

                                                    // Mover el elemento al contenedor correspondiente (buscando por data-status)
                                                    const newContainer = document.querySelector(
                                                        `.kanban-box[data-status='2']`);
                                                    if (newContainer) {
                                                        newContainer.appendChild(milestoneCard);
                                                    }

                                                    // Actualizar contadores de tareas
                                                    updateTaskCount(source);
                                                    updateTaskCount(newContainer);
                                                    updateTaskCount(target);
                                                }

                                                const toastMessage = data.has_tasks ?
                                                    "{{ __('Todas las tareas tienen que tener horas imputadas') }}" :
                                                    "{{ __('No se puede mover un encargo sin tareas') }}";

                                                // Crear el toast dinámicamente
                                                const toastHTML = `
                        <div aria-live="polite" aria-atomic="true"
                             class="toast align-items-center text-white bg-primary border-0 toastNegation"
                             role="alert" id="successToast" data-bs-autohide="true" data-bs-delay="2000">
                            <div class="d-flex">
                                <div class="toast-body">
                                    ${toastMessage}
                                </div>
                            </div>
                        </div>
                    `;

                                                // Buscar el elemento con data-title="Hoja de encargo" y añadir el toast encima
                                                const targetElement = document.querySelector(
                                                    '[data-title="Hoja de encargo"]');
                                                const existingToast = document.getElementById(
                                                    'successToast');
                                                if (existingToast) {
                                                    existingToast.remove();
                                                }

                                                if (targetElement) {
                                                    // Inserta el toast justo antes del targetElement
                                                    $(targetElement).before(toastHTML);
                                                } else {
                                                    // Fallback: insertar al final del body si no hay target
                                                    document.body.insertAdjacentHTML('beforeend',
                                                        toastHTML);
                                                }

                                                // Inicializa y muestra el toast con Bootstrap
                                                const toastElement = document.getElementById(
                                                    'successToast');
                                                if (toastElement) {
                                                    const toast = new bootstrap.Toast(toastElement);
                                                    toast.show();
                                                }
                                            },
                                            error: function(xhr, status, error) {
                                                console.error('Error al actualizar el orden:', error);
                                            }
                                        });
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error('Error al verificar las horas de tarea:', error);
                                }
                            });








                            /////////////FINAL status 2 a 3///////////////////////////

                            // === Mostrar popup personalizado cuando milestone pasa a estado 3 ===
                            var milestoneId = a(el).find('#milestoneReqName').attr('data-milestone-id');
                            var milestoneTitle = a(el).attr('data-milestone-title') || a(el).find('.milestone-title').text() || '';
                            var projectId = a(el).data('project-id');
                            var modalId = 'commonModal';

                            // Construimos la URL de la vista que quieres mostrar (tú la defines en tu controlador)
                            // var popupUrl = '{{ route('projects.milestone.review', [$currentWorkspace->slug, ':id']) }}'.replace(
                            //     ':id', milestoneId);

                            // // Abrimos el modal
                            // $("#" + modalId + " .modal-title").html("Revisión del hito");
                            // $.ajax({
                            //     url: popupUrl,
                            //     data: {
                            //         milestone_id: milestoneId,
                            //         project_id: projectId,
                            //     },
                            //     success: function(response) {
                            //         $("#" + modalId + " .body").html(response);
                            //         $("#" + modalId).modal({
                            //             backdrop: 'static',
                            //             keyboard: false
                            //         });
                            //         $("#" + modalId).modal('show');
                            //     },
                            //     error: function(xhr) {
                            //         console.error("Error al cargar el popup de revisión:", xhr.responseText);
                            //     }
                            // });

                        } // ⭐ Cuando un milestone pasa de status 4 → 3 borrar finalization_date
                        if (oldStatus == 4 && newStatus == 3) {

                            $.ajax({
                                url: "{{ route('projects.milestone.clearFinalizationDate', [$currentWorkspace->slug, ':id']) }}"
                                    .replace(':id', cardId),
                                type: "POST",
                                headers: {
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                },
                                success: function(response) {
                                    console.log("Finalization date cleared!");
                                },
                                error: function(xhr) {
                                    console.error("Error clearing finalization date");
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
                                id: cardId, // Se envía el cardId obtenido
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
                        var allCards = a(container).children('.card');
                        var totalCount = allCards.length;
                        var visibleCount = allCards.filter(function() {
                            return a(this).css('display') !== 'none';
                        }).length;
                        parentCardList.find('.count').text(visibleCount);
                        var emptyState = a(container).find('.noNotificationsContainer').first();
                        if (emptyState.length) {
                            if (totalCount > 0) {
                                emptyState.hide();
                            } else {
                                emptyState.show();
                            }
                        }
                        var status = a(container).data('status');
                        if (status == 4 && totalCount != visibleCount) {
                            console.warn('[COUNTER] Columna Hecho: total=' + totalCount + ' visible=' + visibleCount);
                            allCards.each(function() {
                                if (a(this).css('display') === 'none') {
                                    console.warn('[COUNTER] Card oculta:', a(this).attr('id'), 'data-project-id:', a(this).data('project-id'));
                                }
                            });
                        }
                    }

                    a.Dragula = new t;
                    a.Dragula.Constructor = t;

                }(window.jQuery);

                ! function(a) {
                    "use strict";
                    a.Dragula.init();
                }(window.jQuery);
            </script>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const modalEl = document.getElementById('commonModal');

                    // Se ejecuta cuando el modal se cierra (por cancelar o por la X)
                    modalEl.addEventListener('hidden.bs.modal', function() {
                        const milestoneId = $(this).data('milestone-id');
                        const previousStatus = $(this).data('previous-status');
                        const previousContainer = $(this).data('previous-container');

                        // Limpiamos los datos guardados
                        $(this).removeData('milestone-id');
                        $(this).removeData('previous-status');
                        $(this).removeData('previous-container');

                        // Si no hay datos guardados, no hacemos nada
                        if (!milestoneId || !previousStatus) return;

                        console.log(`🔄 Revirtiendo milestone ${milestoneId} al estado ${previousStatus}`);

                        // Buscamos la tarjeta del milestone y la movemos al contenedor anterior
                        const $milestoneCard = $(`.card[id='${milestoneId}']`);
                        const $oldContainer = $(`.kanban-box[data-status='${previousStatus}']`);

                        if ($milestoneCard.length && $oldContainer.length) {
                            $oldContainer.append($milestoneCard);
                            $milestoneCard.attr('data-status', previousStatus);
                        }

                        // ✅ Actualizamos en el servidor el cambio de vuelta
                        $.ajax({
                            url: '{{ route('milestone.update.order', [$currentWorkspace->slug, $project_id]) }}',
                            type: 'POST',
                            data: {
                                id: milestoneId,
                                sort: [], // no importa el orden en este caso
                                new_status: previousStatus,
                                old_status: 3,
                                project_id: $milestoneCard.data('project-id')
                            },
                            success: function() {
                                console.log(`✅ Milestone ${milestoneId} revertido correctamente`);
                            },
                            error: function(err) {
                                console.error('❌ Error al revertir milestone:', err);
                            }
                        });
                    });
                });
            </script>
            @if ($project_id == -1)
                <!-- Script encargado de mostrar/ocultar los proyectos completado -->
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        let showCompleted = false; // Variable global para rastrear la visibilidad de proyectos completados

                        // Sincronizar con el estado del filtro modal (si existe)
                        if (window.milestoneBoardFilters) {
                            showCompleted = window.milestoneBoardFilters.showCompleted;
                        }

                        // Inicializa: Oculta grupos de milestones cuyo TODOS elementos tengan status 4
                        initializeCompletedProjects();

                        // Configura el listener para el toggle
                        const toggleIcon = document.getElementById('toggleCompletedProjectsIcon');
                        if (toggleIcon) {
                            toggleIcon.addEventListener('click', function() {
                                showCompleted = !showCompleted;
                                toggleCompletedProjects(showCompleted);
                                if (window.milestoneBoardFilters) {
                                    window.milestoneBoardFilters.showCompleted = showCompleted;
                                    if (window.applyMilestoneFilters) window.applyMilestoneFilters();
                                }
                                this.classList.toggle('showCompletedProjectsUnabled', !showCompleted);
                                this.title = showCompleted ? "{{ __('Hide Completed Projects') }}" :
                                    "{{ __('Show Completed Projects') }}";
                            });
                        }

                        function initializeCompletedProjects() {
                            if (window.milestoneBoardFilters && window.milestoneBoardFilters.showCompleted) return;
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
                                milestones.forEach(m => {
                                    m.style.display = (allInStatus4 && !shouldShow) ? 'none' : 'block';
                                    m.style.border = (allInStatus4 && shouldShow) ? '3px solid #15b500' :
                                        'none';
                                });
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
                                        if (mutation.type === 'attributes' && mutation.attributeName ===
                                            'data-status') {
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
                    const tasks = document.querySelectorAll('.milestone-task, .taskList');

                    tasks.forEach(task => {
                        // Verifica si el técnico asignado es el usuario actual
                        const technicianId = task.getAttribute('data-technician-name');
                        const currentUserId = "{{ Auth::id() }}";

                        if (technicianId === currentUserId) {
                            task.addEventListener('click', function() {
                                // El resto del código del evento click se mantiene igual
                                const milestone = this.closest('.card');
                                const milestoneStatus = milestone.getAttribute('data-status');

                                if (milestoneStatus === '4' || milestoneStatus === '3') {
                                    console.log(
                                        'El milestone está en status 3 o 4, no se ejecutará la acción.');
                                    return;
                                }

                                const taskData = {
                                    task_id: this.getAttribute('data-task-id'),
                                    milestone_id: this.getAttribute('data-milestone-id'),
                                    project_id: this.getAttribute('data-project-id'),
                                    user_id: this.getAttribute('data-technician-name'),
                                    date: new Date().toISOString().split('T')[0],
                                };

                                $.ajax({
                                    url: '{{ route('create.timesheet.from.orders', [$currentWorkspace->slug, $project_id]) }}',
                                    type: 'GET',
                                    data: taskData,
                                    success: function(data) {
                                        $('#modal-container .modal-content').html(data).css({
                                            'text-align': 'left',
                                            'width': '800px'
                                        });
                                        var myModal = bootstrap.Modal.getOrCreateInstance(
                                            document
                                            .getElementById('modal-container'));
                                        myModal.show();
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('Error al actualizar el orden:', error);
                                    }
                                });
                            });
                        } else {
                            // Desactiva el evento click si el técnico asignado no es el usuario actual
                            task.addEventListener('click', function(event) {
                                event.stopPropagation();
                                event.preventDefault();
                            });
                            // Añade el estilo de cursor not-allowed
                            task.style.cursor = 'not-allowed';
                        }
                    });
                });
            </script>
            <!-- Script encargado de mostrar/ocultar la leyenda de colores -->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const legendIcon = document.querySelector('.legendIcon');
                    const legend = document.querySelector('.legend');

                    // Configuración inicial
                    legend.style.opacity = '0';
                    legend.style.transform = 'translateY(-10px)';
                    legend.style.visibility = 'hidden';

                    legendIcon.addEventListener('click', function() {
                        const isVisible = legend.style.visibility === 'visible';

                        if (isVisible) {
                            legend.style.opacity = '0';
                            legend.style.transform = 'translateY(-10px)';
                            setTimeout(() => {
                                legend.style.visibility = 'hidden';
                            }, 300); // Coincide con la duración de la transición
                        } else {
                            legend.style.visibility = 'visible';
                            legend.style.opacity = '1';
                            legend.style.transform = 'translateY(0)';
                        }

                        // Agregar animación de click al ícono
                        this.style.transform = 'scale(0.9)';
                        setTimeout(() => {
                            this.style.transform = 'scale(1)';
                        }, 100);
                    });
                });
            </script>

            <!-- Modal para cambio de status de review (3) a in progress (2) -->
            <div class="modal fade" id="statusChangeModal" tabindex="-1" role="dialog"
                aria-labelledby="statusChangeModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="statusChangeModalLabel">{{ __('Return to In progress') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <div class="form-group">
                                <label for="statusChangeComment">{{ __('Notes / Reason') }}</label>
                                <textarea class="form-control" id="statusChangeComment" rows="4"
                                    placeholder="{{ __('Enter any notes or reason for starting this milestone...') }}"></textarea>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                {{ __('Cancel') }}
                            </button>
                            <button type="button" class="btn btn-primary" onclick="submitStatusChange()">
                                {{ __('Save') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                function submitStatusChange() {
                    const modal = document.getElementById('statusChangeModal');

                    const milestoneId = modal.dataset.milestoneId;
                    const projectId = modal.dataset.projectId;
                    const oldStatus = modal.dataset.oldStatus; // 3
                    const newStatus = modal.dataset.newStatus; // 2
                    const sortData = modal.dataset.sort;
                    const comment = (document.getElementById('statusChangeComment').value || '').trim();

                    // Obligatorio
                    if (!comment) {
                        alert("Debes indicar el motivo.");
                        return;
                    }

                    // Marcar como guardado (para que no haga reload por cancelar)
                    modal.dataset.saved = '1';

                    // 1) Actualizar status en servidor
                    $.ajax({
                        url: '{{ route('milestone.update.order', [$currentWorkspace->slug, ':projectId']) }}'
                            .replace(':projectId', projectId),
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            id: milestoneId,
                            old_status: oldStatus,
                            new_status: newStatus,
                            sort: JSON.parse(sortData || '[]'),
                            project_id: projectId,
                            status_change_comment: comment
                        },
                        success: function() {

                            // 2) Borrar puntuaciones SOLO si se confirmó 3->2
                            if (String(oldStatus) === '3' && String(newStatus) === '2') {
                                console.log("Confirmado 3->2: eliminando puntuaciones...");

                                $.ajax({
                                    url: '{{ route('projects.milestone.deletePuntuaciones', [$currentWorkspace->slug, ':id']) }}'
                                        .replace(':id', milestoneId),
                                    type: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    complete: function() {
                                        // 3) Siempre recargar al final (haya o no error borrando)
                                        location.reload();
                                    }
                                });

                            } else {
                                location.reload();
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error al actualizar estado:', error);
                            alert('Error updating milestone status');
                        }
                    });

                    bootstrap.Modal.getOrCreateInstance(modal).hide();
                }

                // Cancelar => reload para volver a ver el estado correcto (y NO borrar puntuaciones)
                document.addEventListener('DOMContentLoaded', function() {
                    const modal = document.getElementById('statusChangeModal');
                    if (!modal) return;

                    modal.dataset.saved = '0';

                    modal.addEventListener('hidden.bs.modal', function() {
                        if (modal.dataset.saved !== '1') {
                            // cancel / x
                            location.reload();
                        }
                        modal.dataset.saved = '0';
                    });
                });
            </script>

            @include('projects.partials.task_review_modal')

        @endpush
    @endif
