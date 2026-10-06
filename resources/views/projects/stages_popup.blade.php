<style>
    .fontSize14px {
        font-size: 14px !important;
    }

    .marginLeft57 {
        /* margin-left: 57% !important; */
    }
</style>
<div class="container-fluid py-2">

    @if (session('success'))
        <div class="alert alert-success py-2" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger py-2" role="alert">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger py-2" role="alert">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('projects.stages.store', [$currentWorkspace->slug, $project->id]) }}"
        class="mb-3 p-3 border rounded-3">
        @csrf
        <label for="new-stage-name" class="form-label mb-2 fw-semibold">{{ __('New phase') }}</label>
        <div class="input-group input-group-lg">
            <input id="new-stage-name" type="text" name="name" class="fontSize14px form-control"
                placeholder="{{ __('Enter phase name') }}" autocomplete="off" autofocus required>
            <button type="submit" class="fontSize14px btn btn-primary px-4">{{ __('Add') }}</button>
        </div>
    </form>

    @if ($stages->count())
        <div class="d-flex flex-column gap-2">
            @foreach ($stages as $stage)
                <div class="border rounded-3 p-3">
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light text-dark border">#{{ $loop->iteration }}</span>
                            <small class="text-muted">{{ __('Phase') }}</small>
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-lg-row align-items-lg-center gap-2">
                        <form method="POST"
                            action="{{ route('projects.stages.update', [$currentWorkspace->slug, $project->id, $stage->id]) }}"
                            class="d-flex align-items-center gap-2 w-100">
                            @csrf
                            <label for="stage-name-{{ $stage->id }}"
                                class="visually-hidden">{{ __('Phase name') }}</label>
                            <input id="stage-name-{{ $stage->id }}" type="text" name="name"
                                class="form-control" value="{{ $stage->name }}" required>
                            <button type="submit"
                                class="btn btn-primary text-nowrap">{{ __('Save changes') }}</button>
                        </form>

                        <form method="POST"
                            action="{{ route('projects.stages.destroy', [$currentWorkspace->slug, $project->id, $stage->id]) }}"
                            onsubmit="return confirm('{{ __('Delete phase') }}: {{ addslashes($stage->name) }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger text-nowrap"
                                aria-label="{{ __('Delete') }} {{ $stage->name }}">
                                <i class="ti ti-trash me-1"></i>{{ __('Delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="border rounded-3 p-4 text-center">
            <div class="mb-2">
                <i class="ti ti-layout-kanban fs-3 text-muted"></i>
            </div>
            <p class="text-muted mb-1">{{ __('No stages configured for this project.') }}</p>
            <small class="text-muted">{{ __('Create your first stage using the form above.') }}</small>
        </div>
    @endif
</div>

<script>
    (function() {
        const modal = document.getElementById('commonModal');
        if (!modal) {
            return;
        }

        const header = modal.querySelector('.modal-header');
        const closeButton = header ? header.querySelector('.btn-close') : null;

        if (!header || !closeButton) {
            return;
        }

        const existingBadge = header.querySelector('.stages-count-badge');
        if (existingBadge) {
            existingBadge.remove();
        }

        const badge = document.createElement('span');
        badge.className = 'marginLeft57 badge bg-primary rounded-pill px-3 py-2 me-2 stages-count-badge';
        badge.textContent = '{{ $stages->count() }} ' + @json(__('phases'));
        header.insertBefore(badge, closeButton);

        const clearBadgeOnClose = function() {
            const removableBadge = header.querySelector('.stages-count-badge');
            if (removableBadge) {
                removableBadge.remove();
            }
            modal.removeEventListener('hidden.bs.modal', clearBadgeOnClose);
        };

        modal.addEventListener('hidden.bs.modal', clearBadgeOnClose);
    })();
</script>
