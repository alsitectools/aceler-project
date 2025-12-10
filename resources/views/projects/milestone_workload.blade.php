<style>
    .filterbutton {
        background-color: #AA182C;
        color: white;
    }

    .filterbutton:hover {
        background-color: #ac2c3d;

    }

    .assigned-milestone-list::-webkit-scrollbar {
        width: 8px;
        height: 10px;
    }

    .assigned-milestone-list::-webkit-scrollbar-thumb {
        background: #AA182C;
        /* Color del scrollbar */
        border-radius: 4px;
        height: 10px;
    }
</style>
<div class="modal-body">
    <div class="form-group" style="display:none;">
        <label class="col-form-label">{{ __('Select user') }}</label>
        <input type="text" class="form-control" id="workload-search-input" placeholder="{{ __('Search') }}"
            name="workload-search-input" value="" autocomplete="off">
        <div id="workload-user-list" aria-label="User list" class="dropdown-menu"
            style="width: 100% !important; display: none;">
            @foreach ($users as $user)
                <div class="option list-group-item list-group-item-action stylelist ps-3"
                    collected-data-id="{{ $user->id }}" data-workspace="{{ $user->currant_workspace }}"
                    style="padding: 8px; cursor: pointer;">
                    {{ $user->name }}
                </div>
            @endforeach
            <input type="text" name="workload_user_id" id="workload_user_id" style="display: none;" value="">
            <input type="text" name="workload_user_workspace" id="workload_user_workspace" style="display: none;"
                value="">F
        </div>
    </div>

    <div class="form-group mt-3">
        <label class="col-form-label">{{ __('Unassigned Milestones') }}</label>
        <div id="milestones-list"
            style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; border-radius: 4px; padding: 8px;">
            @forelse ($milestonesSinAssignar as $milestone)
                <div style="padding: 8px; border-bottom: 1px solid #eee;">
                    <span style="font-weight: 600; color: #333;">{{ $milestone->title }}</span>
                    <span style="margin-left: 10px; color: #666;">({{ $milestone->project->name }})</span>
                </div>
            @empty
                <div style="padding: 8px; color: #999; text-align: center;">
                    {{ __('No unassigned milestones') }}
                </div>
            @endforelse
        </div>
    </div>

    <div class="form-group mt-3">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 0px;">
            <label class="col-form-label" style="margin: 0;">{{ __('Assigned Milestones') }}</label>
            <i class="fa-solid fa-filter" id="filter-toggle-btn"
                style="font-size: 14px; color: #999; cursor: pointer; transition: color 0.3s; padding:2px; border-radius:5px;"></i>
        </div>

        <!-- Input de filtro (oculto por defecto) -->
        <input type="text" class="form-control" id="assigned-filter-input"
            placeholder="{{ __('Filter by user name...') }}" style="display: none; margin-bottom: 10px;">

        <div id="assigned-milestones-list"
            style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; border-radius: 4px; padding: 8px;"
            class="assigned-milestone-list">
            @forelse ($milestonesAgrupados as $userId => $data)
                <div class="assigned-milestone-item" data-user-name="{{ strtolower($data['user']->name) }}"
                    style="padding: 12px; border-bottom: 1px solid #eee; background-color: #f9f9f9; margin-bottom: 8px; border-radius: 4px;">
                    <!-- Encabezado del usuario -->
                    <div style="display: flex; align-items: center; margin-bottom: 8px;">
                        @if ($data['user']->avatar)
                            <img class="fix_img" src="{{ asset($data['user']->avatar) }}"
                                alt="{{ $data['user']->name }}"
                                style="width: 32px; height: 32px; border-radius: 50%; margin-right: 10px; object-fit: cover;">
                        @else
                            <div
                                style="width: 32px; height: 32px; border-radius: 50%; background-color: #ddd; margin-right: 10px; display: flex; align-items: center; justify-content: center; color: #666; font-weight: bold;">
                                {{ substr($data['user']->name, 0, 1) }}
                            </div>
                        @endif
                        <span style="font-weight: 600; color: #333; flex-grow: 1;">{{ $data['user']->name }}</span>
                    </div>

                    <!-- Contadores clickeables -->
                    <div style="display: flex; gap: 10px; margin-top: 8px;">
                        @if ($data['count_1'] > 0)
                            <button type="button" class="btn btn-sm btn-outline-warning toggle-status-btn"
                                data-user-id="{{ $userId }}" data-status="1"
                                style="font-size: 12px; padding: 4px 8px;">
                                {{ __('To Do') }}: <strong>{{ $data['count_1'] }}</strong>
                            </button>
                        @endif

                        @if ($data['count_2'] > 0)
                            <button type="button" class="btn btn-sm btn-outline-info toggle-status-btn"
                                data-user-id="{{ $userId }}" data-status="2"
                                style="font-size: 12px; padding: 4px 8px;">
                                {{ __('In Progress') }}: <strong>{{ $data['count_2'] }}</strong>
                            </button>
                        @endif
                    </div>

                    <!-- Lista desplegable de milestones (oculta por defecto) -->
                    <div class="milestone-list" data-user-id="{{ $userId }}"
                        style="display: none; margin-top: 8px; padding: 8px; background-color: white; border-radius: 4px; border-left: 3px solid #aa182c;">

                        <!-- Milestones status 1 -->
                        <div class="status-1-list" style="display: none;">
                            <h6 style="color: #ffc107; margin-bottom: 6px; font-size: 12px; font-weight: 600;">
                                {{ __('To Do') }}
                            </h6>
                            @foreach ($data['status_1'] as $milestone)
                                <div style="padding: 4px 0; font-size: 12px; color: #666;">
                                    <i class="fa-solid fa-circle-small" style="color: #ffc107; margin-right: 4px;"></i>
                                    {{ $milestone->title }}
                                    <span style="color: #999;">({{ $milestone->project->name }})</span>
                                </div>
                            @endforeach
                        </div>

                        <!-- Milestones status 2 -->
                        <div class="status-2-list" style="display: none;">
                            <h6 style="color: #17a2b8; margin-bottom: 6px; font-size: 12px; font-weight: 600;">
                                {{ __('In Progress') }}
                            </h6>
                            @foreach ($data['status_2'] as $milestone)
                                <div style="padding: 4px 0; font-size: 12px; color: #666;">
                                    <i class="fa-solid fa-circle-small" style="color: #17a2b8; margin-right: 4px;"></i>
                                    {{ $milestone->title }}
                                    <span style="color: #999;">({{ $milestone->project->name }})</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <div style="padding: 8px; color: #999; text-align: center;">
                    {{ __('No assigned milestones') }}
                </div>
            @endforelse
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
    <button type="button" class="btn btn-primary" style="display: none"
        id="workload-consult-btn">{{ __('Consultar') }}</button>
</div>

<script>
    var searchInputWorkload = document.getElementById('workload-search-input');
    var optionsListWorkload = document.getElementById('workload-user-list');
    var optionsWorkload = optionsListWorkload.getElementsByClassName('option');
    var hiddenInputWorkloadId = document.getElementById('workload_user_id');
    var hiddenInputWorkloadWorkspace = document.getElementById('workload_user_workspace');

    // Muestra el menú al hacer clic en el input
    searchInputWorkload.addEventListener('click', function(event) {
        event.stopPropagation();
        optionsListWorkload.style.display = 'block';
    });

    // Filtra las opciones conforme se escribe
    searchInputWorkload.addEventListener('input', function() {
        const filter = searchInputWorkload.value.toLowerCase();
        let hasVisibleOption = false;
        for (let i = 0; i < optionsWorkload.length; i++) {
            const option = optionsWorkload[i];
            const text = option.innerText.toLowerCase();
            if (text.includes(filter)) {
                option.style.display = 'block';
                hasVisibleOption = true;
            } else {
                option.style.display = 'none';
            }
        }
        optionsListWorkload.style.display = hasVisibleOption ? 'block' : 'none';
    });

    // Asigna el valor seleccionado y oculta el menú
    for (let i = 0; i < optionsWorkload.length; i++) {
        optionsWorkload[i].addEventListener('click', function() {
            const selectedUserId = this.getAttribute('collected-data-id');
            const selectedWorkspace = this.getAttribute('data-workspace');
            searchInputWorkload.value = this.innerText;
            hiddenInputWorkloadId.value = selectedUserId;
            hiddenInputWorkloadWorkspace.value = selectedWorkspace;
            optionsListWorkload.style.display = 'none';
        });
    }

    // Cierra el menú si se hace clic fuera del contenedor
    document.addEventListener('click', function(event) {
        if (!event.target.closest('#workload-user-list') && !event.target.closest('#workload-search-input')) {
            optionsListWorkload.style.display = 'none';
        }
    });

    // Botón Consultar
    document.getElementById('workload-consult-btn').addEventListener('click', function() {
        const inputValue = document.getElementById('workload-search-input').value;
        const userId = document.getElementById('workload_user_id').value;
        const workspace = document.getElementById('workload_user_workspace').value;
        console.log('Usuario: ' + inputValue + ', ID: ' + userId + ', Workspace: ' + workspace);
    });

    // ============== FILTRO DE ASSIGNED MILESTONES ==============
    var filterToggleBtn = document.getElementById('filter-toggle-btn');
    var assignedFilterInput = document.getElementById('assigned-filter-input');

    // Toggle del botón de filtro
    filterToggleBtn.addEventListener('click', function() {
        const isVisible = assignedFilterInput.style.display !== 'none';
        assignedFilterInput.style.display = isVisible ? 'none' : 'block';

        // Cambiar color del icono
        if (isVisible) {
            // Si estaba visible, lo ocultamos - icono gris
            filterToggleBtn.style.color = '#999';

        } else {
            // Si estaba oculto, lo mostramos - icono verde
            filterToggleBtn.style.color = '#28a745';

            assignedFilterInput.focus();
        }

        // Si ocultamos el filtro, limpiarlo
        if (isVisible) {
            assignedFilterInput.value = '';
            filterMilestoneItems('');
        }
    });

    // Filtrar items cuando se escribe en el input
    assignedFilterInput.addEventListener('input', function() {
        const filterValue = this.value.toLowerCase();
        filterMilestoneItems(filterValue);
    });

    // Función para filtrar los elementos de milestones asignados
    function filterMilestoneItems(filterValue) {
        const items = document.querySelectorAll('.assigned-milestone-item');
        let hasVisibleItems = false;

        items.forEach(item => {
            const userName = item.getAttribute('data-user-name');
            if (userName.includes(filterValue)) {
                item.style.display = 'block';
                hasVisibleItems = true;
            } else {
                item.style.display = 'none';
            }
        });

        // Buscar o crear mensaje de sin resultados
        const assignedList = document.getElementById('assigned-milestones-list');
        let noResultsMsg = assignedList.querySelector('.no-filter-results');

        if (!hasVisibleItems && filterValue) {
            // Si no hay resultados y hay filtro activo
            if (!noResultsMsg) {
                noResultsMsg = document.createElement('div');
                noResultsMsg.className = 'no-filter-results';
                noResultsMsg.style.padding = '8px';
                noResultsMsg.style.color = '#999';
                noResultsMsg.style.textAlign = 'center';
                noResultsMsg.style.fontStyle = 'italic';
                noResultsMsg.textContent = '{{ __('No matches found') }}';
                assignedList.appendChild(noResultsMsg);
            }
        } else if (noResultsMsg) {
            // Si hay resultados o no hay filtro, eliminar el mensaje
            noResultsMsg.remove();
        }
    }

    // Manejo de los botones de estado para desplegar milestones asignados
    document.querySelectorAll('.toggle-status-btn').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            const status = this.getAttribute('data-status');
            const milestoneList = document.querySelector('.milestone-list[data-user-id="' + userId +
                '"]');

            if (milestoneList) {
                const status1List = milestoneList.querySelector('.status-1-list');
                const status2List = milestoneList.querySelector('.status-2-list');

                if (status === '1') {
                    // Toggle de To Do
                    const isVisible = status1List.style.display !== 'none';
                    status1List.style.display = isVisible ? 'none' : 'block';
                } else if (status === '2') {
                    // Toggle de In Progress
                    const isVisible = status2List.style.display !== 'none';
                    status2List.style.display = isVisible ? 'none' : 'block';
                }

                // Mostrar la lista contenedora si al menos una sección está visible
                const status1Visible = status1List && status1List.style.display !== 'none';
                const status2Visible = status2List && status2List.style.display !== 'none';
                milestoneList.style.display = (status1Visible || status2Visible) ? 'block' : 'none';
            }
        });
    });
</script>
