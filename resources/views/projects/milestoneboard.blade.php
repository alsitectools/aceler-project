@extends('layouts.admin')
@php
    $client_keyword = Auth::user()->type == 'client' ? 'client.' : '';
    $logo = \App\Models\Utility::get_file('avatars/');
    $logo_tasks = \App\Models\Utility::get_file('tasks/');
    use App\Models\User;
    use App\Models\Milestone;
    use App\Models\Task;
    // dd($milestones);
@endphp
@section('page-title')
    {{ __('messages.Milestone_Board') }}
@endsection
<style>
    .foot-milestone {
        display: flex !important;
        flex-direction: row;
        flex-wrap: nowrap;
        justify-content: space-around;
        align-items: baseline;
    }

    .taskList {
        display: flex;
        flex-direction: column;
        flex-wrap: nowrap;
        align-content: space-around;
        align-items: flex-start;
    }

    .statusDate {
        display: flex;
        flex-wrap: nowrap;
        align-items: center;
        flex-direction: column;
        margin: 5%;
    }

    .p-target {
        padding-left: 10%;
    }
</style>
@section('links')
    @if (isset($project_id) && $project_id != '-1')
        <li class="breadcrumb-item"><a
                href="{{ route('projects.show', [$currentWorkspace->slug, $project_id]) }}">{{ __('Project Details') }}</a>
        </li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    @endif
    <li class="breadcrumb-item">{{ __('messages.Milestone_Board') }}</li>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="row kanban-wrapper horizontal-scroll-cards" data-toggle="dragula"
                data-containers='{{ json_encode($statusClass) }}' data-handleclass="handleclass">
                @foreach ($stages as $status)
                    <div class="col-3" id="{{ 'milestone-list-' . str_replace(' ', '_', $status->id) }}">
                        <div class="card card-list">
                            <div class="card-header">
                                <div class="float-end">
                                    <button class="btn-submit btn btn-md btn-primary btn-icon px-1 py-0">
                                        <span class="badge badge-secondary rounded-pill count">
                                            {{ isset($milestones[$status->id]) ? count($milestones[$status->id]) : 0 }}
                                        </span>
                                    </button>
                                </div>
                                <h4 class="mb-0">
                                    {{ __($status->name != 'Todo' ? $status->name : 'Por hacer') }}
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
                                                    <div class="col-sm-9 text-center"
                                                        title="{{ __('dictionary.Milestone') }}">
                                                        <b>{{ $milestone['title'] }}</b>
                                                    </div>
                                                    <div class="col-sm-2 pt-1 text-center" title="{{ $sales->name }}">
                                                        <i class="fa-solid fa-user-tie fa-xl"
                                                            style="color: #CFCECE; padding-top: 25%;"></i>
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
                                                                    data-ajax-popup="true"
                                                                    title="{{ __('messages.View') }}"
                                                                    data-title="{{ __('Milestone Details') }}"
                                                                    data-url="{{ route('projects.milestone.show', [$currentWorkspace->slug, $milestone['id']]) }}">
                                                                    <i class="ti ti-eye pr-1"></i>
                                                                    {{ __('messages.View') }}
                                                                </a>
                                                                @if (
                                                                    $currentWorkspace->permission == 'Owner' ||
                                                                        ($currentWorkspace->permission == 'Member' && Auth::user()->type == 'user'))
                                                                    <a href="#" class="dropdown-item"
                                                                        data-ajax-popup="true" data-size="lg"
                                                                        data-toggle="popover" title="{{ __('Edit') }}"
                                                                        data-title="{{ __('dictionary.Edit_milestone') }}"
                                                                        data-url="{{ route('projects.milestone.edit', [$currentWorkspace->slug, $milestone['id']]) }}">
                                                                        <i class="ti ti-edit pr-1">{{ __('Edit') }}</i>
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    @if ($milestone['tasks'])
                                                        <div class="col-sm-12" title="{{ __('messages.Tasks') }}">
                                                            @foreach ($milestone['tasks'] as $task)
                                                                <div class="taskList p-target col-sm-12">
                                                                  
                                                                    <p><i class="fa-solid fa-thumbtack m-1"
                                                                        style="color: #CFCECE"></i> {{ $task['name'] }} </p>

                                                                </div>
                                                            @endforeach
                                                            <div class="col-sm-12"
                                                                title="{{ $milestone['technician']->name }}">
                                                                <i class="fa-solid fa-helmet-safety fa-xl m-1"
                                                                    style="color: #CFCECE; padding-left: 78%; padding-top: 5%;"></i>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="text-muted text-center">
                                                            {{ 'No hay tareas en curso...' }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="card mb-0 mt-3">
                                                    <div class="card-body p-3">
                                                        <div class="row">
                                                            <div class="foot-milestone">
                                                                <div class="col-6 text-center">
                                                                    <div class="text-center" title="{{ __('Project') }}">
                                                                        <div>
                                                                            <img class="img-fluid p-1" width="40px"
                                                                                src="{{ asset('assets/img/' . $milestone['project_type'] . '.png') }}"
                                                                                alt="Project type">
                                                                        </div>
                                                                        <b
                                                                            style="font-size: 12px">{{ $milestone['project_name'] }}</b>
                                                                        <span class="text-muted" data-toggle="tooltip"
                                                                            title="Referencia MO"><b>{{ $milestone['project_ref'] }}</b></span>

                                                                    </div>
                                                                </div>
                                                                <div class="col-6 text-center"
                                                                    title="{{ __('dictionary.End_Date') }}">
                                                                    @if ($milestone['daysleft'] < 1)
                                                                        <i class="fa-solid fa-calendar-check fa-beat-fade m-1 pb-1 fa-2xl"
                                                                            style="color: red;"></i>
                                                                    @elseif($milestone['daysleft'] < 3)
                                                                        <i class="fa-solid fa-calendar-check fa-2xl m-1"
                                                                            style="color: #db8d33;"></i>
                                                                    @else
                                                                        <i class="fa-solid fa-calendar-check fa-2xl m-1"
                                                                            style="color: #53b446;"></i>
                                                                    @endif
                                                                    <div class="text-center"
                                                                        style="padding-top: 15%; font-size: 12px;">
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
@if ($currentWorkspace && $client_keyword != 'client.')
    @push('scripts')
        <script src="{{ asset('assets/custom/js/dragula.min.js') }}"></script>
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

                        // Depuración
                        console.log('Containers data:', containers);

                        // Convertir el array de contenedores en elementos DOM
                        if (containers && containers.length) {
                            for (var i = 0; i < containers.length; i++) {
                                var container = a("#" + containers[i] + " .kanban-box")[0];
                                if (container) {
                                    containersArray.push(container);
                                    console.log('Container found:', containers[i]);
                                } else {
                                    console.error('Contenedor no encontrado:', containers[i]);
                                }
                            }
                        } else {
                            containersArray = [a(this)[0]];
                        }

                        var handleClass = a(this).data("handleclass");

                        // Inicializar Dragula
                        dragula(containersArray, {
                            moves: function(el, container, handle) {
                                // Verificar si el elemento tiene la clase 'card' (es decir, solo se pueden mover las tarjetas)
                                return el.classList.contains('card');
                            }
                        }).on('drop', handleDrop);
                    });
                };

                function handleDrop(el, target, source, sibling) {
                    console.log('Element dropped:', el);
                    console.log('Target container:', target);
                    console.log('Source container:', source);
                    console.log('Sibling element:', sibling);

                    var sort = [];
                    a(target).find(".card").each(function(key) {
                        var cardId = a(this).attr('id');
                        if (cardId) {
                            console.log('Card ID at index', key, ':', cardId); // Verificar el ID de cada tarjeta
                            sort.push(cardId); // Solo agrega el ID si está definido
                        } else {
                            console.warn('Card at index', key, 'does not have an ID');
                        }
                    });

                    var id = el.id;
                    var oldStatus = a(source).data('status');
                    var newStatus = a(target).data('status');
                    var project_id = a(el).data('project-id');

                    console.log('Sorting order:', sort);
                    console.log('Task ID:', id);
                    console.log('Old status:', oldStatus);
                    console.log('New status:', newStatus);
                    console.log('Project ID:', project_id);

                    // Actualizar conteo de tareas en cada contenedor
                    updateTaskCount(source);
                    updateTaskCount(target);

                    // Enviar la solicitud AJAX para actualizar el orden
                    a.ajax({
                        url: '{{ route('milestone.update.order', [$currentWorkspace->slug, $milestone['project_id']]) }}',
                        type: 'POST',
                        data: {
                            id: id,
                            sort: sort,
                            new_status: newStatus,
                            old_status: oldStatus,
                            project_id: project_id
                        },
                        success: function(data) {
                            console.log('AJAX success:');
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
        <script>
            $(document).on('click', '#form-comment button', function(e) {
                var comment = $.trim($("#form-comment textarea[name='comment']").val());
                if (comment != '') {
                    $.ajax({
                        url: $("#form-comment").data('action'),
                        data: {
                            comment: comment
                        },
                        type: 'POST',
                        success: function(data) {
                            data = JSON.parse(data);

                            var avatar = (data.user.avatar) ? "src='{{ $logo }}/" + data.user
                                .avatar + "'" : "avatar='" + data.user.name + "'";
                            var html = "<li class='media border-bottom mb-3'>" +
                                "                    <img class='mr-3 avatar-sm rounded-circle img-thumbnail hight_img ' width='60' " +
                                avatar + " alt='" + data.user.name + "'>" +
                                "                    <div class='media-body mb-2'>" +
                                "                    <div class='float-left'>" +
                                "                        <h5 class='mt-0 mb-1 form-control-label'>" +
                                data.user.name + "</h5>" +
                                "                        " + data.comment +
                                "                           </div>" +
                                "                           <div class='text-end'>" +
                                "                               <a href='#' class='delete-icon action-btn btn-danger  btn btn-sm d-inline-flex align-items-center delete-comment' data-url='" +
                                data.deleteUrl + "'>" +
                                "                                   <i class='ti ti-trash'></i>" +
                                "                               </a>" +
                                "                           </div>" +
                                "                    </div>" +
                                "                </li>";

                            $("#task-comments").prepend(html);
                            LetterAvatar.transform();
                            $("#form-comment textarea[name='comment']").val('');
                            show_toastr('{{ __('Success') }}', '{{ __('Comment Added Successfully!') }}',
                                'success');
                        },
                        error: function(data) {
                            show_toastr('{{ __('Error') }}', '{{ __('Some Thing Is Wrong!') }}',
                                'error');
                        }
                    });
                } else {
                    show_toastr('{{ __('Error') }}', '{{ __('Please write comment!') }}', 'error');
                }
            });
            $(document).on("click", ".delete-comment", function() {
                if (confirm('{{ __('Are you sure ?') }}')) {
                    var btn = $(this);
                    $.ajax({
                        url: $(this).attr('data-url'),
                        type: 'DELETE',
                        dataType: 'JSON',
                        success: function(data) {
                            show_toastr('{{ __('Success') }}',
                                '{{ __('Comment Deleted Successfully!') }}', 'success');
                            btn.closest('.media').remove();
                        },
                        error: function(data) {
                            data = data.responseJSON;
                            if (data.message) {
                                show_toastr('{{ __('Error') }}', data.message, 'error');
                            } else {
                                show_toastr('{{ __('Error') }}', '{{ __('Some Thing Is Wrong!') }}',
                                    'error');
                            }
                        }
                    });
                }
            });
            $(document).on('click', '#form-subtask button', function(e) {
                e.preventDefault();

                var name = $.trim($("#form-subtask input[name=name]").val());
                var due_date = $.trim($("#form-subtask input[name=due_date]").val());
                if (name == '' || due_date == '') {
                    show_toastr('{{ __('Error') }}', '{{ __('Please enter fields!') }}', 'error');
                    return false;
                }

                $.ajax({
                    url: $("#form-subtask").data('action'),
                    type: 'POST',
                    data: {
                        name: name,
                        due_date: due_date,
                    },
                    dataType: 'JSON',
                    success: function(data) {
                        show_toastr('{{ __('Success') }}', '{{ __('Sub Task Added Successfully!') }}',
                            'success');

                        var html = '<li class="list-group-item py-3">' +
                            '    <div class="form-check form-switch d-inline-block">' +
                            '        <input type="checkbox" class="form-check-input" name="option" id="option' +
                            data.id + '" value="' + data.id + '" data-url="' + data.updateUrl + '">' +
                            '        <label class="custom-control-label form-control-label" for="option' +
                            data.id + '">' + data.name + '</label>' +
                            '    </div>' +
                            '    <div class="text-end">' +
                            '        <a href="#" class=" action-btn btn-danger  btn btn-sm d-inline-flex align-items-center delete-comment delete-icon delete-subtask" data-url="' +
                            data.deleteUrl + '">' +
                            '            <i class="ti ti-trash"></i>' +
                            '        </a>' +
                            '    </div>' +
                            '</li>';

                        $("#subtasks").prepend(html);
                        $("#form-subtask input[name=name]").val('');
                        $("#form-subtask input[name=due_date]").val('');
                        $("#form-subtask").collapse('toggle');
                    },
                    error: function(data) {
                        data = data.responseJSON;
                        if (data.message) {
                            show_toastr('{{ __('Error') }}', data.message, 'error');
                            $('#file-error').text(data.errors.file[0]).show();
                        } else {
                            show_toastr('{{ __('Error') }}', '{{ __('Some Thing Is Wrong!') }}',
                                'error');
                        }
                    }
                });
            });
            $(document).on("change", "#subtasks input[type=checkbox]", function() {
                $.ajax({
                    url: $(this).attr('data-url'),
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(data) {
                        show_toastr('{{ __('Success') }}', '{{ __('Subtask Updated Successfully!') }}',
                            'success');
                        // console.log(data);
                    },
                    error: function(data) {
                        data = data.responseJSON;
                        if (data.message) {
                            show_toastr('{{ __('Error') }}', data.message, 'error');
                        } else {
                            show_toastr('{{ __('Error') }}', '{{ __('Some Thing Is Wrong!') }}',
                                'error');
                        }
                    }
                });
            });
            $(document).on("click", ".delete-subtask", function() {
                // if (confirm('{{ __('Are you sure ?') }}')) {
                var btn = $(this);
                $.ajax({
                    url: $(this).attr('data-url'),
                    type: 'DELETE',
                    dataType: 'JSON',
                    success: function(data) {
                        show_toastr('{{ __('Success') }}', '{{ __('Subtask Deleted Successfully!') }}',
                            'success');
                        btn.closest('.list-group-item').remove();
                    },
                    error: function(data) {
                        data = data.responseJSON;
                        if (data.message) {
                            show_toastr('{{ __('Error') }}', data.message, 'error');
                        } else {
                            show_toastr('{{ __('Error') }}', '{{ __('Some Thing Is Wrong!') }}',
                                'error');
                        }
                    }
                });
                // }
            });
            // $("#form-file").submit(function(e){
            $(document).on('submit', '#form-file', function(e) {
                e.preventDefault();

                $.ajax({
                    url: $("#form-file").data('url'),
                    type: 'POST',
                    data: new FormData(this),
                    dataType: 'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(data) {
                        show_toastr('{{ __('Success') }}', '{{ __('File Upload Successfully!') }}',
                            'success');
                        // console.log(data);
                        var delLink = '';

                        if (data.deleteUrl.length > 0) {
                            delLink =
                                "<a href='#' class=' action-btn btn-danger  btn btn-sm d-inline-flex align-items-center  delete-icon delete-comment-file'  data-url='" +
                                data.deleteUrl + "'>" +
                                "                                        <i class='ti ti-trash'></i>" +
                                "                                    </a>";
                        }

                        function getImageView(data) {
                            var fileExtension = data.extension.toLowerCase();

                            if (['.jpg', '.jpeg', '.png', '.gif', '.bmp'].includes(fileExtension)) {
                                return "{{ $logo_tasks }}/" + data.file;
                            } else {
                                return "{{ $logo_tasks }}/" + 'sample-file.png';
                            }
                        }
                        var html = "<div class='card mb-1 shadow-none border'>" +
                            "   <div class='card-body p-3'>" +
                            "       <div class='row align-items-center'>" +
                            "           <div class='col-auto'>" +
                            "               <div class='avatar-sm'>" +
                            "                   <span class='avatar-title rounded text-uppercase'>" +
                            "                       <img class='preview_img_size' src='" + getImageView(
                                data) + "'>" +
                            "                   </span>" +
                            "               </div>" +
                            "           </div>" +
                            "           <div class='col pl-0'>" +
                            "               <a href='#' class='text-muted form-control-label'>" + data
                            .name + "</a>" +
                            "               <p class='mb-0'>" + data.file_size + "</p>" +
                            "           </div>" +
                            "           <div class='col-auto'>" +
                            "               <a download href='{{ $logo_tasks }}/" + data.file +
                            "' class='edit-icon action-btn btn-primary btn btn-sm d-inline-flex align-items-center'>" +
                            "                   <i class='ti ti-download'></i>" +
                            "               </a>" +
                            "               <a href='{{ $logo_tasks }}/" + data.file +
                            "' class='edit-icon action-btn btn-secondary btn btn-sm d-inline-flex align-items-center mx-1'>" +
                            "                   <i class='ti ti-crosshair text-white'></i>" +
                            "               </a>" +
                            delLink +
                            "           </div>" +
                            "       </div>" +
                            "   </div>" +
                            "</div>";
                        $("#comments-file").prepend(html);
                    },
                    error: function(data) {
                        data = data.responseJSON;

                        if (data) {
                            show_toastr('{{ __('Error') }}',
                                'File type and size must be match with Storage setting.', 'error');
                            //show_toastr('{{ __('Error') }}', data.message, 'error');
                            $('#file-error').text(data.errors.file[0]).show();
                        } else {
                            show_toastr('{{ __('Error') }}',
                                'File type and size must be match with Storage setting.', 'error');
                        }
                    }
                });
            });
            $(document).on("click", ".delete-comment-file", function() {
                if (confirm('{{ __('Are you sure ?') }}')) {
                    var btn = $(this);
                    $.ajax({
                        url: $(this).attr('data-url'),
                        type: 'DELETE',
                        dataType: 'JSON',
                        success: function(data) {
                            show_toastr('{{ __('Success') }}', '{{ __('File Deleted Successfully!') }}',
                                'success');
                            btn.closest('.border').remove();
                        },
                        error: function(data) {
                            data = data.responseJSON;
                            if (data.message) {
                                show_toastr('{{ __('Error') }}', data.message, 'error');
                            } else {
                                show_toastr('{{ __('Error') }}', '{{ __('Some Thing Is Wrong!') }}',
                                    'error');
                            }
                        }
                    });
                }
            });
        </script>
    @endpush
@endif
<style type="text/css">
    .hight_img {
        max-width: 30px !important;
        max-height: 30px !important;
    }
</style>
