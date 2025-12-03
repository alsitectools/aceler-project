<!-- Estilos personalizados para esta vista -->
<style>
    #ref_mo_list,
    #clipo_list {
        max-height: 230px;
        overflow-y: auto;
        position: absolute;
        width: 45%;
        -webkit-box-shadow: 0px 5px 5px -2px #bcbcbc;
        box-shadow: 0px 5px 5px -2px #bcbcbc;
    }

    .stylelist:hover {
        background-color: #aa182c;
        font-weight: bold;
        color: rgb(255, 255, 255);
    }

    #ref_mo_list::-webkit-scrollbar,
    #clipo_list::-webkit-scrollbar {
        width: 0;
        background: transparent;
    }

    .modal-dialog {
        max-width: 60%;
        /* Set modal width to 60% */
    }

    .delegationSelect {
        max-height: 300px !important;
    }
</style>

<!-- Formulario para crear un nuevo proyecto -->
<form id="new-project-form" method="post" action="{{ route('projects.store', [$currentWorkspace->slug]) }}">
    @csrf
    <div class="modal-body">
        <div class="row">
            <div class="form-group col-md-12">
                <label class="col-form-label">{{ __('Project type') }}</label>
                <select class="form-control form-control-light" name="project_type" id="project_type" required>
                    <option selected disabled>{{ __('Choose one') }}</option>
                    @foreach ($project_type as $type)
                        <option style="background-color:white; color:black;" value="{{ $type->id }}"
                            data-type="{{ $type->name }}">
                            {{ __($type->name) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-6" id="ref_mo" style="display: none;">
                <label for="search_mo" class="col-form-label">M.O</label>
                <input type="text" class="form-control" name="ref_mo" id="searchMo" placeholder="Masterobras">
                <div class="list-group" id="ref_mo_list"></div>
            </div>
            <div class="form-group col-md-6" id="clipo" style="display: none;">
                <label for="searchClipo" class="col-form-label">{{ __('Clipo') }}</label>
                <input class="form-control" type="text" name="clipo" id="searchClipo"
                    placeholder="{{ __('Clipo') }}">
                <div class="list-group" style="display: none;" id="clipo_list"></div>
            </div>
            <div class="form-group col-md-12">
                <label for="projectname" class="col-form-label">{{ __('Name') }}</label>
                <input class="form-control" type="text" id="projectname" name="name" required
                    placeholder="{{ __('Project Name') }}">
            </div>
            <div class="form-group col-md-12" id="delegacion" style="display: none;">
                <label for="delegacionInput" class="col-form-label">Delegación</label>
                <input type="text" class="form-control" id="delegacionInput"
                    placeholder="{{ __('Project delegation') }}" autocomplete="off">
                <input type="hidden" name="delegacion" id="delegacionHidden">
                <div class="list-group" id="delegacionList"
                    style="max-height: 220px; overflow-y: auto; display: none; position: absolute; width: 96%; z-index: 1000;">
                    @foreach ($project_delegation->sortBy('delegation_name') as $delegation)
                        <a href="#" class="list-group-item list-group-item-action stylelist"
                            data-id="{{ $delegation->id }}" data-name="{{ $delegation->delegation_name }}">
                            {{ $delegation->delegation_name }} - {{ $delegation->id }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- <div class="form-group col-md-12">
                <label for="milestone-title" class="col-form-label">{{ __('Title') }}</label>
                <input type="text" class="form-control form-control-light" id="milestone-title"
                    placeholder="{{ __('Title') }}" name="title" required>
            </div> --}}
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <input type="submit" value="{{ __('Add New project') }}" class="btn btn-primary">
    </div>
</form>

<!-- Pasando variables de Blade a JavaScript -->
<script>
    const projects = @json($projects);
    const currentWorkspaceSlug = '{{ $currentWorkspace->slug }}';
    const searchMoUrl = "{{ route('search-mo-json', '__slug') }}".replace('__slug', currentWorkspaceSlug);
    const searchClipoUrl = "{{ route('search-clipo-json', '__slug') }}".replace('__slug', currentWorkspaceSlug);
</script>
<!-- Incluimos el archivo JS de create_project si es necesario -->
<script src="{{ asset('assets/js/create_project.js') }}"></script>

<!-- Función de notificación y manejo del submit -->
<script>
    // Función asíncrona para generar la notificación antes del submit
    async function displayNotificationProject() {
        console.log('Generando notificacion de milestone completado');
        // Usamos el valor del campo projectname para el mensaje
        let projectName = document.getElementById('projectname').value;
        let msg = projectName;
        let ntipe = 1;
        if (!msg) return; // Si no hay mensaje, no se realiza nada

        try {
            const response = await fetch("{{ route('notifications.add') }}", {
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
            });
            const data = await response.json();
            if (data.success) {
                let notificationList = document.querySelector('.limited');
                // Si el contenedor de notificaciones no existe, se puede crear o ignorar
                if (notificationList) {
                    let newNotification = document.createElement('div');
                    newNotification.classList.add('notificationSTL');
                    newNotification.innerHTML = `
                        <span class="textRepo">${data.data.msg}</span>
                        <span class="textRepo">${data.data.type}</span>
                        <button type="button" class="btn-close repoIcon" aria-label="Close"></button>
                    `;
                    notificationList.prepend(newNotification);
                }
            }
        } catch (error) {
            console.error("Error al agregar notificación:", error);
        }
    }

    // Interceptamos el submit del formulario para ejecutar la notificación primero
    document.getElementById('new-project-form').addEventListener('submit', async function(event) {
        event.preventDefault(); // Prevenir el envío inmediato
        await displayNotificationProject(); // Esperar a que se complete la notificación
        this.submit(); // Luego se envía el formulario (puedes optar por usar AJAX si lo prefieres)
    });

    // Add event listener to capitalize the first letter of the project name
    document.getElementById('projectname').addEventListener('input', function() {
        let value = this.value;
        if (value.length > 0) {
            this.value = value.charAt(0).toUpperCase() + value.slice(1);
        }
    });

    // // Add event listener to capitalize the first letter of the milestone title
    // document.getElementById('milestone-title').addEventListener('input', function() {
    //     let value = this.value;
    //     if (value.length > 0) {
    //         this.value = value.charAt(0).toUpperCase() + value.slice(1);
    //     }
    // });
</script>
<script>
    const projectTypeSelect = document.getElementById('project_type');
    const delegacionField = document.getElementById('delegacion');
    const delegacionInput = document.getElementById('delegacionInput');
    const delegacionList = document.getElementById('delegacionList');
    const delegacionHidden = document.getElementById('delegacionHidden');

    projectTypeSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const selectedText = selectedOption.getAttribute('data-type');

        if (selectedText && selectedText.toLowerCase() !== 'jobsite') {
            delegacionField.style.display = 'block';
        } else {
            delegacionField.style.display = 'none';
            delegacionInput.value = '';
            delegacionHidden.value = '';
        }
    });

    // Filtrado de delegaciones
    delegacionInput.addEventListener('input', function() {
        const searchText = this.value.toLowerCase();
        const items = delegacionList.getElementsByTagName('a');

        delegacionList.style.display = 'block';

        Array.from(items).forEach(item => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(searchText) ? 'block' : 'none';
        });
    });

    // Selección de delegación
    delegacionList.addEventListener('click', function(e) {
        if (e.target.tagName === 'A') {
            e.preventDefault();
            delegacionInput.value = e.target.getAttribute('data-name');
            delegacionHidden.value = e.target.getAttribute('data-id');
            delegacionList.style.display = 'none';
        }
    });

    // Ocultar lista cuando se hace clic fuera
    document.addEventListener('click', function(e) {
        if (!delegacionInput.contains(e.target) && !delegacionList.contains(e.target)) {
            delegacionList.style.display = 'none';
        }
    });

    // Mostrar lista al hacer focus en el input
    delegacionInput.addEventListener('focus', function() {
        delegacionList.style.display = 'block';
        // Mostrar todos los elementos de la lista
        const items = delegacionList.getElementsByTagName('a');
        Array.from(items).forEach(item => {
            item.style.display = 'block';
        });
    });
</script>
