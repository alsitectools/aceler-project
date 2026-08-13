@extends('layouts.admin')
@php
    $client_keyword = Auth::user()->type == 'client' ? 'client' : '';
    $logo = \App\Models\Utility::get_file('avatars/');
    $logo_tasks = \App\Models\Utility::get_file('tasks/');
    use App\Models\User;
@endphp
@section('page-title')
    {{ __('Order Forms Board') }}
@endsection

<head>
    <link rel="stylesheet" href="{{ asset('assets/css/milestoneboard.css') }}">
</head>
<style>
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
    <li class="breadcrumb-item">{{ __('Order Forms Board') }}</li>
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
            <span>{{ __('In this section you will find the job sheets you have requested, those assigned to you, those on which you have performed tasks and also those not yet assigned.') }}</span>
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
            @include('projects.partials.milestone_filter_popUp', ['filtersPopupMode' => 'standard'])
            <div class="col-sm-auto" style="margin-right: 20px;">
                <button style="width: 100%" type="button" class="btn btn-primary addMilestone" data-ajax-popup="true"
                    data-title="{{ __('Check Workloads') }}"
                    data-url="{{ route('projects.milestone.workload', [$currentWorkspace->slug, $project_id]) }}"
                    data-toggle="popover" title="{{ __('Create') }}"><i class="fa-solid fa-briefcase me-3"
                        style="color: #ffffff;"></i>
                    {{ __('Check Workloads') }}
                </button>
            </div>
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
    @include('saver.saver', ['letters' => 'ESPERE...', 'overlayId' => 'espera-overlay'])
    <div class="row modifiedWidth">
        <div class="col-sm-12">
            <div class="row kanban-wrapper horizontal-scroll-cards" data-toggle="dragula"
                data-containers='{{ json_encode($statusClass) }}' data-handleclass="handleclass">
                @foreach ($stages as $status)
                    <div class="col-3 pe-1" id="{{ 'milestone-list-' . str_replace(' ', '_', $status->id) }}">
                        <div class="card card-list">
                            <div class="card-header">
                                <div class="float-end">
                                    <button class="btn-submit btn btn-md btn-primary btn-icon px-1 py-0 "
                                        style="height: 19.7px; margin-top:2px">
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
                                        {{-- @dump($milestone) --}}
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


                                {{-- ============================================ --}}
                                {{--   MILESTONES DEL RESTO DEL WORKSPACE          --}}
                                {{-- ============================================ --}}
                                @if (isset($milestonesUsers[$status->id]))
                                    @foreach ($milestonesUsers[$status->id] as $milestone)
                                        {{-- Saltar si ya se pintó arriba --}}
                                        @continue(in_array($milestone['id'], $renderedMilestonesIds))

                                        @include('projects.partials.milestone_card', [
                                            'milestone' => $milestone,
                                            'status' => $status,
                                            'currentWorkspace' => $currentWorkspace,
                                            'project_id' => $project_id,
                                            'extraClass' => 'other-user-milestone',
                                            'inlineStyle' => '',
                                            'ownerShip' => 'no',
                                        ])
                                    @endforeach
                                @endif

                                @php
                                    $msCount = (isset($milestones[$status->id]) ? count($milestones[$status->id]) : 0)
                                             + (isset($milestonesUsers[$status->id]) ? count($milestonesUsers[$status->id]) : 0);
                                @endphp
                                <div class="noNotificationsContainer" style="margin-top: -20px; {{ $msCount > 0 ? 'display: none;' : '' }}">
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
                            console.log('Card ID:', cardId, 'Old status:', oldStatus, 'New status:', newStatus, 'Project ID:',
                                project_id, 'Milestone Title:', milestoneTitle);

                            // Se dispara la misma acción que al hacer clic en "Add Task on Timesheet"
                            var url = '{{ route('tasks.create', $currentWorkspace->slug) }}' + '?project_id=' + project_id +
                                '&milestoneTitle=' + milestoneTitle + '&milestone_id=' + cardId;
                            var title = '{{ __('Create New Task') }}';
                            var modalId = 'commonModal';

                            $("#" + modalId + " .modal-title").html(title);
                            $.ajax({
                                url: url,
                                dataType: 'html',
                                success: function(data) {
                                    var modalEl = document.getElementById(modalId);
                                    var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                                    // Reset modal dialog
                                    var modalDialog = modalEl.querySelector('.modal-dialog');
                                    if (modalDialog) {
                                        modalDialog.className = 'modal-dialog';
                                        modalDialog.removeAttribute('style');
                                    }
                                    $('#' + modalId + ' .body').html(data);
                                    modal.show();
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

                        console.log("el completo ");
                        console.log(el)
                        console.log("Milestone Title (obtenido): " + milestoneTitle);

                        // Guardar el título tanto en data como en atributo HTML para persistencia
                        a(el).data('milestoneTitle', milestoneTitle);
                        a(el).attr('data-milestone-title', milestoneTitle);

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


                        // Si se permite el movimiento y es de status 1 a 2, se dispara primero el popup de asignación
                        if (oldStatus == 1 && newStatus >= 2) {
                            console.log('De por hacer a in progress');

                            // Modificamos la construcción de la URL para asegurar la ruta correcta
                            var assignUrl = '{{ route('projects.milestone.assign', [$currentWorkspace->slug, ':id']) }}'.replace(
                                ':id', cardId);
                            var assignTitle = '{{ __('Assign Milestone') }}';
                            var modalId = 'commonModal';

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
                                    // Marcamos el formulario para saber que viene del cambio de estado
                                    $('#asignMilestoneForm').attr('data-from-status-change', 'true');

                                    // Guardar contexto para revertir si se cierra sin guardar
                                    $('#' + modalId).data('assign-flow', true);
                                    $('#' + modalId).data('assign-saved', false);
                                    $('#' + modalId).data('milestone-id', cardId);
                                    $('#' + modalId).data('previous-status', oldStatus);
                                    $('#' + modalId).data('previous-container', source);
                                    $('#' + modalId).data('original-index', a(el).data('originalIndex'));
                                    $('#' + modalId).data('moved-el-id', cardId); // por si quieres asegurar

                                    modal.show();


                                    // Escuchar el evento solo si se disparó desde el form
                                    document.addEventListener('milestoneAssigned', function showTaskModal() {
                                        // Si la card ya tiene tareas, NO abrir "Create New Task":
                                        // solo se persiste el paso a "en curso" y se recarga el board
                                        var $milestoneCardEl = a("#" + cardId);
                                        var hasTasks = $milestoneCardEl.find('.milestone-task').length > 0;

                                        if (hasTasks) {
                                            var orderUrl = '{{ route('milestone.update.order', [$currentWorkspace->slug, ':projectId']) }}'
                                                .replace(':projectId', project_id);
                                            var sort = [];
                                            var container = $milestoneCardEl.closest('.card-list');
                                            container.find('.card').each(function(key) {
                                                var cid = a(this).attr('id');
                                                if (cid) {
                                                    sort.push(cid);
                                                }
                                            });
                                            a.ajax({
                                                url: orderUrl,
                                                type: 'POST',
                                                data: {
                                                    id: cardId,
                                                    sort: sort,
                                                    new_status: 2,
                                                    old_status: 1,
                                                    project_id: project_id
                                                },
                                                success: function() {
                                                    location.reload();
                                                }
                                            });
                                            return;
                                        }

                                        // Mostrar el loader de texto grande "ESPERE" durante el delay
                                        var esperaOverlay = document.getElementById('espera-overlay');
                                        if (esperaOverlay) {
                                            esperaOverlay.style.display = 'flex';
                                            document.body.style.overflow = 'hidden';
                                        }

                                        setTimeout(() => {


                                            document.removeEventListener('milestoneAssigned',
                                                showTaskModal);

                                            // Recuperar el título del milestone del elemento
                                            var $milestoneCard = a("#" + cardId);
                                            var retrievedTitle = $milestoneCard.data('milestoneTitle');

                                            // Si no tenemos el título guardado en data, intentar desde atributo HTML
                                            if (!retrievedTitle) {
                                                retrievedTitle = $milestoneCard.attr(
                                                    'data-milestone-title');
                                            }

                                            // Si no tenemos el título desde atributo, intentar obtenerlo directamente
                                            if (!retrievedTitle) {
                                                retrievedTitle = $milestoneCard.find('.milestone-title')
                                                    .text();
                                            }
                                            if (!retrievedTitle) {
                                                retrievedTitle = milestoneTitle || 'Sin título';
                                            }

                                            console.log('Título recuperado para crear tarea:',
                                                retrievedTitle);

                                            var createTaskUrl =
                                                '{{ route('tasks.create', $currentWorkspace->slug) }}' +
                                                '?project_id=' + project_id +
                                                '&milestoneTitle=' + encodeURIComponent(
                                                    retrievedTitle) +
                                                '&milestone_id=' + cardId +
                                                '&fromMilestoneBoard=true';
                                            var createTaskTitle = '{{ __('Create New Task') }}';

                                            console.log('URL de creación de tarea:', createTaskUrl);

                                            $("#" + modalId + " .modal-title").html(createTaskTitle);
                                            $.ajax({
                                                url: createTaskUrl,
                                                dataType: 'html',
                                                success: function(taskData) {
                                                    var modalEl = document.getElementById(
                                                        modalId);
                                                    var modal = bootstrap.Modal
                                                        .getOrCreateInstance(modalEl, {
                                                            backdrop: 'static',
                                                            keyboard: false
                                                        });
                                                    // Reset modal dialog
                                                    var modalDialog = modalEl.querySelector(
                                                        '.modal-dialog');
                                                    if (modalDialog) {
                                                        modalDialog.className =
                                                            'modal-dialog';
                                                        modalDialog.removeAttribute(
                                                            'style');
                                                    }
                                                    $('#' + modalId + ' .body').html(
                                                        taskData);
                                                    if (esperaOverlay) {
                                                        esperaOverlay.style.display = 'none';
                                                        document.body.style.overflow = 'auto';
                                                    }
                                                    modal.show();
                                                    commonLoader();
                                                    loadConfirm();
                                                },
                                                error: function(xhr, status, error) {
                                                    console.error(
                                                        'Error al cargar el formulario de creación de tarea:',
                                                        error);
                                                    if (esperaOverlay) {
                                                        esperaOverlay.style.display = 'none';
                                                        document.body.style.overflow = 'auto';
                                                    }
                                                }
                                            });
                                        }, 2000);
                                    }, {
                                        once: true
                                    });

                                    commonLoader();
                                    loadConfirm();
                                },
                                error: function(xhr, status, error) {
                                    console.error('Error al cargar el modal de asignación:', error);
                                }
                            });
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
                                        const span1 = document.createElement('span');
                                        span1.className = 'textRepo';
                                        span1.textContent = data.data.msg;
                                        const span2 = document.createElement('span');
                                        span2.className = 'textRepo';
                                        span2.textContent = data.data.type;
                                        const btn = document.createElement('button');
                                        btn.type = 'button';
                                        btn.className = 'btn-close repoIcon';
                                        btn.setAttribute('aria-label', 'Close');
                                        newNotification.appendChild(span1);
                                        newNotification.appendChild(span2);
                                        newNotification.appendChild(btn);
                                    }
                                })
                                .catch(error => console.error("Error al agregar notificación:", error));
                        }
                        if (oldStatus == 3 && newStatus == 2) {
                            console.log("El milestone pasa de review a en curso — mostrando modal de comentario...");
                            // Guardar los datos del milestone en el modal para usarlos después
                            document.getElementById('statusChangeModal').dataset.milestoneId = cardId;
                            document.getElementById('statusChangeModal').dataset.slug = '{{ $currentWorkspace->slug }}';
                            document.getElementById('statusChangeModal').dataset.oldStatus = oldStatus;
                            document.getElementById('statusChangeModal').dataset.newStatus = newStatus;
                            document.getElementById('statusChangeModal').dataset.projectId = project_id;
                            document.getElementById('statusChangeModal').dataset.sort = JSON.stringify(sort);

                            // Limpiar el textarea
                            document.getElementById('statusChangeComment').value = '';

                            // Mostrar el modal
                            var statusModalEl = document.getElementById('statusChangeModal');
                            var statusModal = bootstrap.Modal.getOrCreateInstance(statusModalEl);
                            statusModal.show();

                            // Prevenir que se haga la actualización aquí, se hará después
                            // Retornar de la función completa para no ejecutar el AJAX global
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
                                                        var modalEl = document.getElementById(
                                                            modalId);
                                                        var modal = bootstrap.Modal
                                                            .getOrCreateInstance(modalEl, {
                                                                backdrop: 'static',
                                                                keyboard: false
                                                            });
                                                        // Reset modal dialog
                                                        var modalDialog = modalEl.querySelector(
                                                            '.modal-dialog');
                                                        if (modalDialog) {
                                                            modalDialog.className =
                                                                'modal-dialog';
                                                            modalDialog.removeAttribute(
                                                                'style');
                                                        }
                                                        $("#" + modalId + " .body").html(
                                                            response);
                                                        modal.show();
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
                    if (!modalEl) return;

                    // Se ejecuta cuando el modal se cierra (por cancelar o por la X)
                    modalEl.addEventListener('hidden.bs.modal', function() {

                        // ============================
                        // ✅ PASO 2: revertir si ASSIGN se canceló (1->2)
                        // ============================
                        const isAssignFlow = $(this).data('assign-flow') === true;
                        const assignSaved = $(this).data('assign-saved') === true;

                        if (isAssignFlow && !assignSaved) {

                            const milestoneId = $(this).data('milestone-id');
                            const previousStatus = $(this).data('previous-status'); // debería ser 1
                            const previousContainer = $(this).data('previous-container'); // source
                            const originalIndex = $(this).data('original-index'); // índice original

                            // Limpiar flags de assign (para que no se repita)
                            $(this).removeData('assign-flow');
                            $(this).removeData('assign-saved');

                            console.log(
                                `↩️ ASSIGN cancelado. Revirtiendo milestone ${milestoneId} al estado ${previousStatus}`
                            );

                            // Revertir DOM a la posición original
                            const $milestoneCard = $(`.card[id='${milestoneId}']`);
                            const $origin = $(previousContainer);

                            if ($milestoneCard.length && $origin.length) {

                                // insertar en la misma posición si existe
                                const $cards = $origin.children('.card');

                                $milestoneCard.detach();

                                if ($cards.length > 0 && originalIndex != null && originalIndex < $cards.length) {
                                    $milestoneCard.insertBefore($cards.eq(originalIndex));
                                } else {
                                    $origin.append($milestoneCard);
                                }

                                $milestoneCard.attr('data-status', previousStatus);

                                // actualizar contadores
                                updateTaskCount(previousContainer);
                                const targetContainer = document.querySelector(`.kanban-box[data-status='2']`);
                                if (targetContainer) updateTaskCount(targetContainer);
                            }

                            // Revertir en backend a status 1
                            $.ajax({
                                url: '{{ route('milestone.update.order', [$currentWorkspace->slug, $project_id]) }}',
                                type: 'POST',
                                data: {
                                    id: milestoneId,
                                    sort: [],
                                    new_status: previousStatus, // 1
                                    old_status: 2, // intentó ir a 2
                                    project_id: $milestoneCard.data('project-id')
                                },
                                complete: function() {
                                    // nada; no recargamos aquí para evitar parpadeos
                                }
                            });

                            // ✅ Limpieza visual del modal (igual que ya hacías)
                            const $modalDialog = $(this).find('.modal-dialog');
                            $modalDialog.attr('class', 'modal-dialog').removeAttr('style');
                            $(this).find('.modal-content').removeAttr('style');
                            $(this).find('.dropdown-menu').removeAttr('style');
                            $(this).find('.modal-title').empty();
                            $(this).find('.body').empty();

                            // 🔴 MUY IMPORTANTE: salir para que NO ejecute el revert de otros flujos
                            return;
                        }

                        // ============================
                        // ✅ TU LÓGICA EXISTENTE (revert por cancelación del popup de review, etc.)
                        // ============================
                        const milestoneId = $(this).data('milestone-id');
                        const previousStatus = $(this).data('previous-status');
                        const previousContainer = $(this).data('previous-container');

                        // Limpiamos los datos guardados
                        $(this).removeData('milestone-id');
                        $(this).removeData('previous-status');
                        $(this).removeData('previous-container');

                        // ✅ Limpiar todas las clases de tamaño del modal y restablecer a la base
                        const $modalDialog = $(this).find('.modal-dialog');
                        $modalDialog.attr('class', 'modal-dialog').removeAttr('style');

                        // ✅ Limpiar estilos inline del modal-content
                        $(this).find('.modal-content').removeAttr('style');

                        // ✅ Remover estilos inline específicos del dropdown
                        $(this).find('.dropdown-menu').removeAttr('style');

                        // ✅ Limpiar el título del modal
                        $(this).find('.modal-title').empty();

                        // ✅ Limpiar el contenido del modal body después de cerrar
                        $(this).find('.body').empty();

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

            <script>
                // Limpieza del modal-container cuando se cierra
                document.addEventListener('DOMContentLoaded', function() {
                    const modalContainerEl = document.getElementById('modal-container');
                    if (modalContainerEl) {
                        modalContainerEl.addEventListener('hidden.bs.modal', function() {
                            const $modal = $(this);
                            // ✅ Resetear clases del modal-dialog
                            $modal.find('.modal-dialog').attr('class', 'modal-dialog').removeAttr('style');
                            // ✅ Limpiar estilos inline del modal-content y restaurar los por defecto (800px)
                            $modal.find('.modal-content').css({
                                'text-align': 'left',
                                'width': '800px'
                            }).empty();
                        });
                    }
                });
            </script>
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
                            if (task.classList.contains('task-inactive')) {
                                task.style.cursor = 'not-allowed';
                            }
                            task.addEventListener('click', function() {
                                // El resto del código del evento click se mantiene igual
                                const milestone = this.closest('.card');
                                const milestoneStatus = milestone.getAttribute('data-status');

                                if (milestoneStatus === '4' || milestoneStatus === '3') {
                                    console.log(
                                        'El milestone está en status 3 o 4, no se ejecutará la acción.');
                                    return;
                                }

                                if (this.classList.contains('task-inactive')) {
                                    // Tareas "por hacer" (tipo 3): no se permite imputar horas
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
                                        var modalEl = document.getElementById(
                                            'modal-container');
                                        // Reset modal dialog
                                        var modalDialog = modalEl.querySelector(
                                            '.modal-dialog');
                                        if (modalDialog) {
                                            modalDialog.className = 'modal-dialog';
                                            modalDialog.removeAttribute('style');
                                        }
                                        $('#modal-container .modal-content').html(data).css({
                                            'text-align': 'left',
                                            'width': '800px'
                                        });
                                        var myModal = bootstrap.Modal.getOrCreateInstance(
                                            modalEl);
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

            <!-- Modal para cambio de status de review a en curso -->
            <div class="modal fade" id="statusChangeModal" tabindex="-1" role="dialog"
                aria-labelledby="statusChangeModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="statusChangeModalLabel">{{ __('Start Milestone - In Progress') }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="statusChangeForm" method="POST" style="display:none;">
                            @csrf
                        </form>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="statusChangeComment">{{ __('Notes / Reason') }}</label>
                                <textarea class="form-control" id="statusChangeComment" name="status_change_comment" rows="4"
                                    placeholder="{{ __('Enter any notes or reason for starting this milestone...') }}"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="button" class="btn btn-primary"
                                onclick="submitStatusChange()">{{ __('Return to In progress') }}</button>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const statusModal = document.getElementById('statusChangeModal');
                    if (!statusModal) return;

                    // Se ejecuta cuando se cierra por Cancelar, X o backdrop
                    statusModal.addEventListener('hidden.bs.modal', function() {
                        // Recargar la página siempre que se cierre el modal
                        location.reload();
                    });
                });
            </script>

            <script>
                function submitStatusChange() {
                    const modal = document.getElementById('statusChangeModal');

                    const milestoneId = modal.dataset.milestoneId;
                    const oldStatus = modal.dataset.oldStatus; // 3
                    const newStatus = modal.dataset.newStatus; // 2
                    const projectId = modal.dataset.projectId;
                    const sortData = modal.dataset.sort;
                    const comment = document.getElementById('statusChangeComment').value;

                    // (Opcional) obligar comentario
                    if (!comment.trim()) {
                        alert("Debes indicar el motivo para volver a In Progress.");
                        return;
                    }

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
                            sort: JSON.parse(sortData),
                            project_id: projectId,
                            status_change_comment: comment
                        },
                        success: function() {

                            // ✅ SOLO si realmente confirmamos 3 -> 2, borramos puntuaciones
                            if (String(oldStatus) === '3' && String(newStatus) === '2') {
                                console.log("El milestone vuelve de estado 3 a 2 — eliminando puntuaciones...");

                                $.ajax({
                                    url: '{{ route('projects.milestone.deletePuntuaciones', [$currentWorkspace->slug, ':id']) }}'
                                        .replace(':id', milestoneId),
                                    type: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    success: function() {
                                        // cuando termina, recargamos
                                        location.reload();
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('Error al eliminar puntuaciones:', error);
                                        // aunque falle el borrado, recarga para reflejar el cambio de status
                                        location.reload();
                                    }
                                });

                            } else {
                                // otros cambios -> recarga normal
                                location.reload();
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error al actualizar estado:', error);
                            alert('{{ __('Error updating milestone status') }}');
                        }
                    });

                    bootstrap.Modal.getOrCreateInstance(modal).hide();
                }
            </script>

            @include('projects.partials.task_review_modal')
        @endpush
    @endif
