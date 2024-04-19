@php
    $user = Auth::user();
@endphp
@if ($project && $currentWorkspace)
    <form class="" method="post"
        action="@auth('web'){{ route('projects.milestone.store', [$currentWorkspace->slug, $project->id]) }}@endauth">
        @csrf
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <input type="text" class="form-control form-control-light" id="milestone-title"
                            placeholder="{{ __('Enter Title') }}" name="title" required>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <button class="form-control form-control-light dropdown-toggle text-start" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Seleccionar encargo
                        </button>

                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Menu item</a></li>
                            <li><a class="dropdown-item" href="#">Menu item</a></li>
                            <li><a class="dropdown-item" href="#">Menu item</a></li>
                        </ul>
                    </div>
                </div>

                <div class="form-group col-md-12" style="display:none">
                    <label class="col-form-label">{{ __('Assign To') }}</label>
                    <select class=" multi-select" id="assign_to" name="assign_to[]" data-toggle="select2"
                        multiple="multiple" data-placeholder="{{ __('Select Users ...') }}">
                        <option value="{{ $user->id }}" selected></option>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group ">
                            <label class="form-label">{{ trans('messages.Created_date') }}</label>

                            <div class="input-group date ">
                                <input class="form-control datepicker22" type="text" id="start_date"
                                    name="start_date" value="<?php echo date('Y-m-d'); ?>" autocomplete="off" disabled>
                                <span class="input-group-text">
                                    <i class="feather icon-calendar"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">{{ trans('messages.Desired_delivery_date') }}</label>
                            <div class="input-group date ">
                                <input class="form-control datepicker23" type="text" id="end_date" name="end_date"
                                    autocomplete="off">
                                <span class="input-group-text">
                                    <i class="feather icon-calendar"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="task-summary" class="col-form-label">{{ trans('messages.Summary') }}</label>
                    <textarea class="form-control form-control-light" id="task-summary" rows="3" name="summary"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <input type="submit" value="{{ __('Save Changes') }}" class="btn  btn-primary">
            </div>

    </form>
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
                                <a class="btn-return-home badge-blue" href="{{ route('home') }}"><i
                                        class="fas fa-reply"></i> {{ __('Return Home') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<script>
    (function() {
        var locale = '{{ app()->getLocale() }}';

        const d_week = new Datepicker(document.querySelector('.datepicker22'), {
            locale: locale,
            buttonClass: 'btn',
            todayBtn: true,
            clearBtn: true,
            format: 'yyyy-mm-dd',
        });
    })();
</script>

<script>
    (function() {

        var locale = '{{ app()->getLocale() }}';
        const d_week = new Datepicker(document.querySelector('.datepicker23'), {
            locale: locale,
            buttonClass: 'btn',
            todayBtn: true,
            clearBtn: true,
            format: 'yyyy-mm-dd',
        });
    })();
</script>
