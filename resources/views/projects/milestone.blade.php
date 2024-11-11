@php
    $user = Auth::user();
    $actionUrl =
        $project_id == -1
            ? route('projects.milestone.store', [$currentWorkspace->slug, 'PLACEHOLDER'])
            : route('projects.milestone.store', [$currentWorkspace->slug, $project->id]);
@endphp
<style>
    .disabled {
        color: black !important;
        background-color: #6c757d !important;
    }

    .modal-dialog {
        max-width: 60%;
    }

    #projects_list,
    #ref_mo_list,
    #clipo_list {
        max-height: 245px;
        overflow-y: auto;
        position: absolute;
        width: 95%;
        -webkit-box-shadow: 0px 5px 5px -2px #bcbcbc;
        box-shadow: -3px 4px 5px -2px #bcbcbc;

    }

    .stylelist:hover {
        background-color: #aa182c;
        font-weight: bold;
        color: rgb(255, 255, 255);

    }

    .form-control:focus-visible {
        outline: none;
    }

    .form-control:focus {
        border-color: transparent !important;
    }

    #ref_mo_list::-webkit-scrollbar,
    #clipo_list::-webkit-scrollbar {
        width: 0;
        background: transparent;
    }


    .accordion-light .accordion-item {
        border-radius: 0.25rem !important;
        background-color: #f8f9fa;
        border: 1px solid #ced4da;
    }

    .accordion-light .accordion-button {
        background-color: #f8f9fa;
        color: #495057;
        height: calc(1.5em + 0.75rem + 2px);
        padding: 0.375rem 0.75rem;
        line-height: 1.5;
        border-radius: 0.25rem;
    }

    .accordion-light .accordion-button:not(.collapsed) {
        box-shadow: none;
    }
</style>

@if ($currentWorkspace)
    <div class="modal-body">
        <!-- Toast Notification -->
        <div aria-live="polite" aria-atomic="true"
            class="position-fixed d-flex justify-content-end  top-25 start-50 translate-middle-y" style="z-index: 1080;">
            <div class="toast align-items-center text-white bg-primary border-0" role="alert" aria-live="assertive"
                aria-atomic="true" id="successToast">
                <div class="d-flex">
                    <div class="toast-body" id="toastMessage">
                    </div>
                </div>
            </div>
        </div>
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="milestone-tab" data-bs-toggle="tab" href="#milestone" role="tab"
                    aria-controls="milestone" aria-selected="true">
                    <i class="fa-solid fa-file-lines me-2"></i> {{ __('Create Milestone') }}
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="project-tab" data-bs-toggle="tab" href="#projectForm" role="tab"
                    aria-controls="project" aria-selected="false">
                    <i class="fa-solid fa-diagram-project me-2"></i> {{ __('Create New Project') }}
                </a>
            </li>
        </ul>
        <div class="tab-content mt-3" id="myTabContent">
            <!-- Milestone Form -->
            <div class="tab-pane fade show active" id="milestone" role="tabpanel" aria-labelledby="milestone-tab">
                <form id="milestone-form" method="POST" action="{{ $actionUrl }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                @if (isset($project_id) && $project_id == -1)
                                    <div id="project">
                                        <label class="col-form-label">{{ __('Search project') }}</label>
                                        <input type="text" class="form-control" id="searchProject"
                                            placeholder="{{ __('Name or reference M.O') }}">
                                        <input id="projectId" name="project_id" style="display: none">
                                        <div class="list-group" id="projects_list"></div>

                                    </div>
                                @else
                                    <input class="form-control" type="text" value="{{ $project->name }}" disabled>
                                    <input class="form-control" type="text" id="project_id" name="project_id"
                                        value="{{ $project->id }}" autocomplete="off" style="display: none;">
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-form-label">{{ __('MO') }}</label>
                                <input type="text" class="form-control form-control-light" id="milestone_mo"
                                    placeholder="{{ __('MO') }}" name="ref_mo" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-form-label">{{ __('Title') }}</label>
                                <input type="text" class="form-control form-control-light" id="milestone-title"
                                    placeholder="{{ __('Title') }}" name="title" required>
                            </div>
                        </div>
                        <div class="col-md-6" id="sales_manager">
                            <label class="col-form-label">{{ __('Search Sales Manager') }}</label>
                            <input type="text" class="form-control" name="sales_manager" id="searchSalesManager"
                                placeholder="{{ __('Name of Sales Manager') }}"
                                value="{{ Auth::user()->type == 'client' ? Auth::user()->name : '' }}">
                            <div class="list-group" id="sales_manager_list"></div>

                        </div>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="toggleFormSwitch">
                        <label class="form-check-label"
                            for="toggleFormSwitch">{{ __('Only in case it is necessary to to carry out a project with a visa.') }}</label>
                    </div>
                    <!-- Contenedor del formulario adicional -->
                    <div id="additionalForm" class="collapse mt-3">
                        <div class="card card-body">
                            <div class="mb-3">
                                <label for="input1"
                                    class="form-label">{{ __('Name of the company that will install the system') }}:</label>
                                <input type="text" class="form-control" name="company" id="company"
                                    placeholder="Ingrese valor">
                            </div>
                            <div class="mb-3">
                                <label for="input2"
                                    class="form-label">{{ __('Name of prime contractor') }}:</label>
                                <input type="text" class="form-control" name="contractor" id="contractor"
                                    placeholder="Ingrese valor">
                            </div>
                            <div class="mb-3">
                                <label for="input2"
                                    class="form-label">{{ __('Address of prime contractor') }}:</label>
                                <input type="text" class="form-control" name="contractorAdress"
                                    id="contractorAdress" placeholder="Ingrese valor">
                            </div>
                            <div class="mb-3">
                                <label for="input2" class="form-label">{{ __('Adress jobsite') }}:</label>
                                <input type="text" class="form-control" name="jobsiteAdress" id="jobsiteAdress"
                                    placeholder="Ingrese valor">
                            </div>
                            <p class="mb-3">
                                <b>{{ __('Note: In order to carry out the project it is necessary to send the quotation of the formwork and falsework system, and the complete assembly drawings and geometrical definition of the structure.') }}
                                </b>
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">{{ __('Created date') }}</label>
                                <input type="date" class="form-control form-control-light date" id="start_date"
                                    name="start_date" value="<?php echo date('Y-m-d'); ?>" autocomplete="off" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">{{ __('Desired delivery date') }}</label>
                                <input onclick="this.showPicker()" type="date"
                                    class="form-control form-control-light date" id="end_date" value=""
                                    placeholder="{{ __('Date') }}" name="end_date" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="task-summary" class="col-form-label">{{ __('Description') }}</label>
                        <textarea class="form-control form-control-light" id="task-summary" rows="10" name="summary"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <input type="submit" value="{{ __('Save Changes') }}" class="btn btn-primary">
                    </div>
                </form>
            </div>
            <!-- New Project Form -->
            <div class="tab-pane fade" id="projectForm" role="tabpanel" aria-labelledby="project-tab">
                <form class="" id="projectForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label class="col-form-label">{{ __('Project type') }}</label>
                                <select class="form-control form-control-light" name="project_type" id="project_type"
                                    required="">
                                    <option selected disabled>{{ __('Choose one') }}</option>
                                    @foreach ($project_type as $type)
                                        <option style="background-color:white; color:black;"
                                            value="{{ $type->id }}" data-type="{{ $type->name }}">
                                            {{ __($type->name) }} </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6" id="ref_mo"
                                style="display: none; position: relative;">
                                <label for="search_mo" class="col-form-label">{{ __('Search MO') }}</label>
                                <input type="text" class="form-control" name="ref_mo" id="searchMo"
                                    placeholder="{{ __('Reference') }}">
                                <div class="list-group" id="ref_mo_list"></div>

                            </div>

                            <div class="form-group col-md-6" id="clipo"
                                style="display: none; position: relative">
                                <label for="clipo" class="col-form-label">{{ __('Search client') }}</label>
                                <input class="form-control" type="text" name="clipo" id="searchClipo"
                                    placeholder="{{ __('Clipo') }}">
                                <div class="list-group" id="clipo_list"></div>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="projectname" class="col-form-label">{{ __('Name') }}</label>
                                <input class="form-control" type="text" id="projectname" name="name" required
                                    placeholder="{{ __('Project Name') }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <input type="submit" value="{{ __('Add New project') }}" class="btn btn-primary">
                    </div>
                </form>
            </div>

        </div>

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
                                <a class="btn-return-home badge-blue" href="{{ route('home') }}">
                                    <i class="fas fa-reply"></i> {{ __('Return Home') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<script>
    $(document).ready(function() {
        $('#toggleFormSwitch').change(function() {
            if ($(this).is(':checked')) {
                $('#additionalForm').collapse('show');
            } else {
                $('#additionalForm').collapse('hide');
            }
        });
    });
</script>
<script>
    const projects = @json($projects);
    const currentWorkspaceSlug = '{{ $currentWorkspace->slug }}'; // Asegúrate de que esta variable esté en el contexto
    const searchMoUrl = "{{ route('search-mo-json', '__slug') }}".replace('__slug', currentWorkspaceSlug);
    const searchClipoUrl = "{{ route('search-clipo-json', '__slug') }}".replace('__slug', currentWorkspaceSlug);
    const searchProjectsUrl = "{{ route('search-project-json', '__slug') }}".replace('__slug', currentWorkspaceSlug);
    const searchSalesManagerUrl = "{{ route('search-sales-json', '__slug') }}".replace('__slug', currentWorkspaceSlug);
</script>

<script src="{{ asset('assets/js/create_project.js') }}"></script>
<script>
    $(document).ready(function() {

        $('#projectForm').on('submit', function(event) {
            event.preventDefault();

            const data = {
                project_type: $('#project_type').val(),
                name: $('#projectname').val(),
                ref_mo: $('#searchMo').val(),
                clipo: $('#searchClipo').val(),
                isReload: false
            };

            const slug = "{{ $currentWorkspace->slug }}";
            const url = "{{ route('project.milestone.store', ['slug' => 'slug']) }}";
            const finalUrl = url.replace('slug', slug);

            $.ajax({
                url: finalUrl,
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(data),
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    // Limpiar campos
                    $('#projectname').val("");
                    $('#searchMo').val("");
                    $('#searchClipo').val("");

                    msg = '{{ __('Project Created Successfully!') }}'
                    // Mostrar el mensaje en el *toast*
                    $('#toastMessage').text(msg);

                    const toast = new bootstrap.Toast(document.getElementById(
                        'successToast'), {
                        delay: 2000
                    });
                    toast.show();

                },
                error: function(xhr, status, error) {
                    console.error('Error:', xhr.responseText);
                    $('#toastMessage').text('An error occurred.');
                    const toast = new bootstrap.Toast(document.getElementById(
                        'successToast'), {
                        delay: 2000
                    });
                    toast.show();
                }

            });
        });
    });
</script>


<script>
    $(document).ready(function() {
        $('#project_id').on('change', function() {
            var selectedProjectId = $(this).val();
            var currentWorkspaceSlug = "{{ $currentWorkspace->slug }}";

            var actionUrl =
                `{{ route('projects.milestone.store', [$currentWorkspace->slug, 'PLACEHOLDER']) }}`;
            actionUrl = actionUrl.replace('PLACEHOLDER', selectedProjectId);

            $('#milestone-form').attr('action', actionUrl);
        });
    });
</script>
