@extends('layouts.master')
@section('main-content')
@section('page-css')
<link rel="stylesheet" href="{{asset('assets/styles/vendor/dragula.css')}}">
<link rel="stylesheet" href="{{asset('assets/styles/vendor/drag.min.css')}}">

@endsection

<div class="breadcrumb">
    <h1>{{ __('translate.Kanban_View') }}</h1>
    <ul>
        <li><a href="/tasks">{{ __('translate.Tasks') }}</a></li>
        <li>{{ __('translate.Kanban_View') }}</li>
    </ul>
</div>

<div class="separator-breadcrumb border-top"></div>
<div class='parent'>
    <div class="board-container">
        <div class="row">
            <div class="container-fluid">
                <div class='wrapper'>
                    {{-- start-task-completed --}}
                    <div class="drag-area completed">
                        <div class="drag-area-card card">
                            <div class="card-header">
                                <div class="card-title">
                                    {{ __('translate.Completed') }}
                                </div>
                            </div>
                            <div class="drag-task card-body" data-column-id="completed" id="completed"
                                data-perfect-scrollbar data-suppress-scroll-x="true">
                                {{-- start item --}}
                                @foreach($tasks_completed as $task_completed)
                                <div class="box card  mb-4" data-id="{{ $task_completed->id }}"
                                    id="{{ $task_completed->id }}">
                                    <div class="card-body">
                                        <div class="d-sm-flex align-item-sm-center flex-sm-nowrap">
                                            <div>
                                                <h6><a href="/tasks/{{$task_completed->id}}">#{{$task_completed->id}}.
                                                        {{$task_completed->title}}</a></h6>
                                                <p class="ul-task-manager__paragraph">{{$task_completed->summary}}</p>
                                                <span class="badge badge-danger">{{ __('translate.Due') }}: <span
                                                        class="font-weight-semibold">{{$task_completed->end_date}}</span></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                {{-- end item --}}

                            </div>
                        </div>
                    </div>
                    {{-- end board completed --}}

                    {{-- start-task-progress --}}
                    <div class="drag-area in_progress">
                        <div class="drag-area-card card">
                            <div class="card-header">
                                <div class="card-title">
                                    {{ __('translate.In_Progress') }}
                                </div>
                            </div>
                            <div class="drag-task card-body" data-perfect-scrollbar data-suppress-scroll-x="true"
                                id="progress" data-column-id="progress">

                                {{-- start item --}}
                                @foreach($tasks_in_progress as $task_in_progress)
                                <div class="box card mb-4" data-id="{{ $task_in_progress->id }}"
                                    id="{{ $task_in_progress->id }}">
                                    <div class="card-body">
                                        <div class="d-sm-flex align-item-sm-center flex-sm-nowrap">
                                            <div>
                                                <h6><a href="/tasks/{{$task_in_progress->id}}">#{{$task_in_progress->id}}.
                                                        {{$task_in_progress->title}}</a></h6>
                                                <p class="ul-task-manager__paragraph">{{$task_in_progress->summary}}</p>
                                                <span class="badge badge-danger">{{ __('translate.Due') }}: <span
                                                        class="font-weight-semibold">{{$task_in_progress->end_date}}</span></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                {{-- end item --}}

                            </div>
                        </div>
                    </div>
                    {{-- end board In_Progress --}}

                    {{-- start-task-Not_Started --}}
                    <div class="drag-area not_started">
                        <div class="drag-area-card card">
                            <div class="card-header">
                                <div class="card-title">
                                    {{ __('translate.Not_Started') }}
                                </div>
                            </div>
                            <div class="drag-task card-body" data-perfect-scrollbar data-suppress-scroll-x="true"
                                id="not_started" data-column-id="not_started">
                                {{-- start item --}}
                                @foreach($tasks_not_started as $task_not_started)
                                <div class="box card mb-4" data-id="{{$task_not_started->id}}"
                                    id="{{$task_not_started->id}}">
                                    <div class="card-body">
                                        <div class="d-sm-flex align-item-sm-center flex-sm-nowrap">
                                            <div>
                                                <h6><a href="/tasks/{{$task_not_started->id}}">#{{$task_not_started->id}}.
                                                        {{$task_not_started->title}}</a></h6>
                                                <p class="ul-task-manager__paragraph">{{$task_not_started->summary}}</p>
                                                <span class="badge badge-danger">{{ __('translate.Due') }}: <span
                                                        class="font-weight-semibold">{{$task_not_started->end_date}}</span></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                {{-- end item --}}

                            </div>
                        </div>
                    </div>
                    {{-- end board Not_Started --}}

                    {{-- start-task-Cancelled --}}
                    <div class="drag-area cancelled">
                        <div class="drag-area-card card">
                            <div class="card-header">
                                <div class="card-title">
                                    {{ __('translate.Cancelled') }}
                                </div>
                            </div>
                            <div class="drag-task card-body" data-perfect-scrollbar data-suppress-scroll-x="true"
                                id="cancelled" data-column-id="cancelled">
                                {{-- start item --}}
                                @foreach($tasks_cancelled as $task_cancelled)
                                <div class="box card mb-4" data-id="{{$task_cancelled->id}}"
                                    id="{{$task_cancelled->id}}">
                                    <div class="card-body">
                                        <div class="d-sm-flex align-item-sm-center flex-sm-nowrap">
                                            <div>
                                                <h6><a href="/tasks/{{$task_cancelled->id}}">#{{$task_cancelled->id}}.
                                                        {{$task_cancelled->title}}</a></h6>
                                                <p class="ul-task-manager__paragraph">{{$task_cancelled->summary}}</p>
                                                <span class="badge badge-danger">{{ __('translate.Due') }}: <span
                                                        class="font-weight-semibold">{{$task_cancelled->end_date}}</span></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                {{-- end item --}}

                            </div>
                        </div>
                    </div>
                    {{-- end board Cancelled --}}

                    {{-- start-task-On_Hold --}}
                    <div class="drag-area hold">
                        <div class="drag-area-card card">
                            <div class="card-header">
                                <div class="card-title">
                                    {{ __('translate.On_Hold') }}
                                </div>
                            </div>
                            <div class="drag-task card-body" data-perfect-scrollbar data-suppress-scroll-x="true"
                                id="hold" data-column-id="hold">
                                {{-- start item --}}
                                @foreach($tasks_hold as $task_hold)
                                <div class="box card mb-4" data-id="{{$task_hold->id}}" id="{{$task_hold->id}}">
                                    <div class="card-body">
                                        <div class="d-sm-flex align-item-sm-center flex-sm-nowrap">
                                            <div>
                                                <h6><a href="/tasks/{{$task_hold->id}}">#{{$task_hold->id}}.
                                                        {{$task_hold->title}}</a></h6>
                                                <p class="ul-task-manager__paragraph">{{$task_hold->summary}}</p>
                                                <span class="badge badge-danger">{{ __('translate.Due') }}: <span
                                                        class="font-weight-semibold">{{$task_hold->end_date}}</span></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                {{-- end item --}}

                            </div>
                        </div>
                    </div>
                    {{-- end board On_Hold --}}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-js')

<script src="{{asset('assets/js/dragula.js')}}"></script>
<script src="{{asset('assets/js/drag.min.js')}}"></script>


<!-- Drag and Drop Plugin -->
<script type="text/javascript">
    $(function () {
      "use strict";

    var containers = [
        document.querySelector('#completed'),
        document.querySelector('#progress'),
        document.querySelector('#not_started'),
        document.querySelector('#cancelled'),
        document.querySelector('#hold')
    ];
    var drake = dragula({
            containers: containers,
            moves: function(el, source, handle, sibling) {
                if (el.classList.contains('move-disable')) {
                    return false;
                }

                return true; // elements are always draggable by default
            },
        })
        .on('drag', function(el) {
            el.className = el.className.replace('ex-moved', '');
        }).on('drop', function(el) {
            el.className += ' ex-moved';
        }).on('over', function(el, container) {
            container.className += ' ex-over';
        }).on('out', function(el, container) {
            container.className = container.className.replace('ex-over', '');
        });
    });

</script>

<script type="text/javascript">
    $(function () {
      "use strict";
      
    drake.on('drop', function(element, target, source, sibling) {
            $children = $("#"+target.id).children();
            var from_Column = $("#"+source.id).data('column-id');
            var new_status = $("#"+target.id).data('column-id');
            var task_id = $("#"+element.id).data('id');
            let _token = $('input[name="_token"]').val();
            $.ajax({
                url: "{{ route('task_change_status') }}",
                type:"POST",
                data:{
                    task_id:task_id,
                    status:new_status,
                    _token: _token
                },
                success:function(response){
                    if(response) {
                        toastr.success('{{ __('translate.Updated_in_successfully') }}');
                    }
                },
                error: function(error) {
                    toastr.error('{{ __('translate.There_was_something_wronge') }}');
                }
            });
        });
    });
                
</script>

@endsection