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
</style>

<form class="" method="post" action="{{ route('projects.store', [$currentWorkspace->slug]) }}">
    @csrf
    <div class="modal-body">
        <div class="row">
            <div class="form-group col-md-12">
                <label class="col-form-label">{{ __('Project type') }}</label>
                <select class="form-control form-control-light" name="project_type" id="project_type" required>
                    <option selected disabled>{{ __('Choose one') }}</option>
                    @foreach ($project_type as $type)
                        <option style="background-color:white; color:black;" value="{{ $type->id }}"
                            data-type="{{ $type->name }}"> {{ __($type->name) }} </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-6" id="ref_mo" style="display: none;">
                <label for="search_mo" class="col-form-label">{{ __('Search project') }}</label>
                <input type="text" class="form-control" name="ref_mo" id="searchMo"
                    placeholder="{{ __('Reference') }}">
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
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <input type="submit" value="{{ __('Add New project') }}" class="btn btn-primary">
    </div>
</form>
<!-- Script para pasar datos de Blade a JavaScript -->
<script>
    // Asegúrate de que esto esté antes de tu archivo JS
    const projects = @json($projects);
    const currentWorkspaceSlug = '{{ $currentWorkspace->slug }}'; // Asegúrate de que esta variable esté en el contexto
    const searchMoUrl = "{{ route('search-mo-json', '__slug') }}".replace('__slug', currentWorkspaceSlug);
    const searchClipoUrl = "{{ route('search-clipo-json', '__slug') }}".replace('__slug', currentWorkspaceSlug);
</script>
<script src="{{ asset('assets/js/create_project.js') }}"></script>
