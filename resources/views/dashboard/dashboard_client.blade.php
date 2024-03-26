@extends('layouts.client')
@section('main-content')

<div class="breadcrumb">
    <h1>{{ __('translate.Dashboard') }}</h1>

</div>

<div class="separator-breadcrumb border-top"></div>

<div id="section_Dashboard_client">

    <!-- ICON BG -->
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                <div class="card-body text-center">
                    <i class="i-Dropbox"></i>
                    <div class="content">
                        <p class="text-muted mt-2 mb-0">{{ __('translate.Projects') }}</p>
                        <p class="text-primary text-24 line-height-1 mb-2">{{$count_projects}}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                <div class="card-body text-center">
                    <i class="i-Check"></i>
                    <div class="content">
                        <p class="text-muted mt-2 mb-0">{{ __('translate.Tasks') }}</p>
                        <p class="text-primary text-24 line-height-1 mb-2">{{$count_tasks}}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-sm-12">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="card-title">{{ __('translate.Projects_by_Status') }}</div>
                    <div id="echartProject"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-sm-12">
            <div class="card o-hidden mb-4">
                <div class="card-header d-flex align-items-center border-0">
                    <h3 class="float-left card-title m-0">{{ __('translate.Latest_Projects') }}</h3>
                </div>

                <div class="">
                    <div class="table-responsive">
                        <table id="user_table" class="table  text-center">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('translate.Project') }}</th>
                                    <th scope="col">{{ __('translate.Status') }}</th>
                                    <th scope="col">{{ __('translate.Progress') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($latest_projects as $latest_project)
                                <tr>
                                    <td>{{$latest_project->title}}</td>
                                    <td>
                                        @if($latest_project->status == 'completed')
                                        <span class="badge badge-success m-2">{{ __('translate.Completed') }}</span>
                                        @elseif($latest_project->status == 'not_started')
                                        <span class="badge badge-warning m-2">{{ __('translate.Not_Started') }}</span>
                                        @elseif($latest_project->status == 'progress')
                                        <span class="badge badge-primary m-2">{{ __('translate.In_Progress') }}</span>
                                        @elseif($latest_project->status == 'cancelled')
                                        <span class="badge badge-danger m-2">{{ __('translate.Cancelled') }}</span>
                                        @elseif($latest_project->status == 'hold')
                                        <span class="badge badge-secondary m-2">{{ __('translate.On_Hold') }}</span>
                                        @endif
                                    </td>
                                    <td>{{$latest_project->project_progress}} %</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>



</div>
@endsection


@section('page-js')
<script src="{{asset('assets/js/vendor/echarts.min.js')}}"></script>
<script src="{{asset('assets/js/echart.options.min.js')}}"></script>


<script>
    // Chart Project by status
         let echartElemProject = document.getElementById('echartProject');
        if (echartElemProject) {
            let echartProject = echarts.init(echartElemProject);
            echartProject.setOption({
                // color: ["#6D28D9", "#8B5CF6", "#A78BFA", "#C4B5FD", "#7C3AED"],
                color: ["#003f5c", "#58508d", "#bc5090", "#ff6361", "#ffa600"],
    
                tooltip: {
                    show: true,
                    backgroundColor: 'rgba(0, 0, 0, .8)'
                },
    
                series: [{
                        type: 'pie',
                        radius: '60%',
                        center: ['50%', '50%'],
                        data:[
                            @foreach($project_status as $key => $value) {
                                value:@json($value) , name:@json($key),
                            },
                            @endforeach
                        
                        ],
                        itemStyle: {
                            emphasis: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: 'rgba(0, 0, 0, 0.5)'
                            }
                        }
                    }
    
                ]
            });
            $(window).on('resize', function() {
                setTimeout(() => {
                    echartProject.resize();
                }, 500);
            });
        }
    
    
</script>

@endsection