@extends('layouts.master')
@section('main-content')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/styles/vendor/vue-slider-component.css')}}">


@endsection

@section('main-content')

<div class="breadcrumb">
    <h1>{{ __('translate.Project_List') }}</h1>
    <ul>
        <li>{{ __('translate.Project') }}</li>
        <li>{{ __('translate.Project_details') }}</li>
    </ul>
</div>
<div class="separator-breadcrumb border-top"></div>

<!-- content goes here -->

<section class="ul-product-detail__tab" id="section_details_Project">
    <div class="row">
        <div class="col-lg-12 col-md-12 mt-4">
            <div class="card mt-2 mb-4 ">
                <div class="card-body">
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">

                            <a class="nav-item nav-link active show" id="nav-discussions-tab" data-toggle="tab"
                                href="#nav-discussions" role="tab" aria-controls="nav-discussions"
                                aria-selected="false">{{ __('translate.Discussions') }}</a>

                            <a class="nav-item nav-link" id="nav-issues-tab" data-toggle="tab" href="#nav-issues"
                                role="tab" aria-controls="nav-issues"
                                aria-selected="false">{{ __('translate.Issues') }}</a>

                            <a class="nav-item nav-link" id="nav-documents-tab" data-toggle="tab" href="#nav-documents"
                                role="tab" aria-controls="nav-documents"
                                aria-selected="false">{{ __('translate.Documents') }}</a>

                            <a class="nav-item nav-link" id="nav-tasks-tab" data-toggle="tab" href="#nav-tasks"
                                role="tab" aria-controls="nav-tasks"
                                aria-selected="false">{{ __('translate.Tasks') }}</a>

                        </div>
                    </nav>
                    <div class="tab-content ul-tab__content p-3" id="nav-tabContent">

                        {{-- Discussions --}}
                        <div class="tab-pane fade active show" id="nav-discussions" role="tabpanel"
                            aria-labelledby="nav-discussions-tab">

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="text-left">
                                        <div class="text-left bg-transparent">
                                            <a class="btn btn-primary btn-md m-2" @click="New_Discussion"><i
                                                    class="i-Add text-white mr-2"></i>{{ __('translate.Add_Discussion') }}</a>
                                        </div>
                                        <div class="table-responsive">
                                            <table id="ul-contact-list" class="display table data_datatable"
                                                >
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('translate.Created_by') }}</th>
                                                        <th>{{ __('translate.Message') }}</th>
                                                        <th>{{ __('translate.Date') }}</th>
                                                        <th>{{ __('translate.Action') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($discussions as $discussion)
                                                    <tr>
                                                        <td>{{$discussion->User->username}}</td>
                                                        <td>{{$discussion->message}}</td>
                                                        <td>{{$discussion->created_at}}</td>
                                                        <td>
                                                            <a @click="Remove_Discussion( {{ $discussion->id}})"
                                                                class="ul-link-action text-danger mr-1"
                                                                data-toggle="tooltip" data-placement="top"
                                                                title="Delete">
                                                                <i class="i-Close-Window"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>

                                            </table>
                                        </div>
                                    </div>

                                    <!-- Modal Add  Discussion -->
                                    <div class="modal fade" id="Discussion_Modal" tabindex="-1" role="dialog"
                                        aria-labelledby="Discussion_Modal" aria-hidden="true">
                                        <div class="modal-dialog modal-md" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ __('translate.Create') }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">

                                                    <form @submit.prevent="Create_Discussion()">
                                                        <div class="row">

                                                            <div class="col-md-12">
                                                                <label for="message"
                                                                    class="ul-form__label">{{ __('translate.Message') }}
                                                                    <span class="field_required">*</span></label>
                                                                <textarea type="text" v-model="discussion.message"
                                                                    class="form-control" name="message" id="message"
                                                                    placeholder="{{ __('translate.Enter_Message') }}"></textarea>

                                                                <span class="error"
                                                                    v-if="errors_Discussion && errors_Discussion.message">
                                                                    @{{ errors_Discussion.message[0] }}
                                                                </span>
                                                            </div>

                                                        </div>


                                                        <div class="row mt-3">

                                                            <div class="col-md-6">
                                                                <button type="submit" class="btn btn-primary"
                                                                    :disabled="Submit_Processing_Discussion">
                                                                    {{ __('translate.Submit') }}
                                                                </button>
                                                                <div v-once class="typo__p"
                                                                    v-if="Submit_Processing_Discussion">
                                                                    <div class="spinner spinner-primary mt-3"></div>
                                                                </div>
                                                            </div>
                                                        </div>


                                                    </form>

                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Issues --}}
                        <div class="tab-pane fade" id="nav-issues" role="tabpanel" aria-labelledby="nav-issues-tab">

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="text-left">
                                        <div class="text-left bg-transparent">
                                            <a class="btn btn-primary btn-md m-2" @click="New_Issue"><i
                                                    class="i-Add text-white mr-2"></i>
                                                {{ __('translate.New_Issue') }}</a>
                                        </div>
                                        <div class="table-responsive">
                                            <table id="ul-contact-list" class="display table data_datatable"
                                                >
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('translate.Title') }}</th>
                                                        <th>{{ __('translate.Date') }}</th>
                                                        <th>{{ __('translate.Attachment') }}</th>
                                                        <th>{{ __('translate.Comment') }}</th>
                                                        <th>{{ __('translate.Label') }}</th>
                                                        <th>{{ __('translate.Status') }}</th>
                                                        <th>{{ __('translate.Action') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($issues as $issue)
                                                    <tr>
                                                        <td>{{$issue->title}}</td>
                                                        <td>{{$issue->created_at}}</td>
                                                        <td>
                                                            @if($issue->attachment)
                                                            <span><a href="{{ asset('assets/images/projetcs/issues/'.$issue->attachment) }}"
                                                                    target="_blank">
                                                                    {{$issue->attachment}}</a>
                                                            </span>
                                                            @else
                                                            <span>__</span>
                                                            @endif

                                                        </td>
                                                        <td>{{$issue->comment}}</td>
                                                        <td>{{$issue->label}}</td>
                                                        <td>
                                                            @if($issue->status =='pending')
                                                            <span
                                                                class="badge badge-warning m-2">{{ __('translate.Pending') }}</span>
                                                            @else
                                                            <span
                                                                class="badge badge-success m-2">{{ __('translate.Solved') }}</span>
                                                            @endif
                                                        </td>
                                                        <td>

                                                            <a @click="Edit_Issue( {{ $issue}})"
                                                                class="ul-link-action text-success"
                                                                data-toggle="tooltip" data-placement="top" title="Edit">
                                                                <i class="i-Edit"></i>
                                                            </a>
                                                            <a @click="Remove_Issue( {{ $issue->id}})"
                                                                class="ul-link-action text-danger mr-1"
                                                                data-toggle="tooltip" data-placement="top"
                                                                title="Delete">
                                                                <i class="i-Close-Window"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>

                                            </table>
                                        </div>
                                    </div>

                                    <!-- Modal Add  Issue -->
                                    <div class="modal fade" id="Issue_Modal" tabindex="-1" role="dialog"
                                        aria-labelledby="Issue_Modal" aria-hidden="true">
                                        <div class="modal-dialog modal-md" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 v-if="Edit_mode_Issue" class="modal-title">
                                                        {{ __('translate.Edit') }}</h5>
                                                    <h5 v-else class="modal-title">{{ __('translate.Create') }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">

                                                    <form
                                                        @submit.prevent="Edit_mode_Issue?Update_Issue():Create_Issue()">
                                                        <div class="row">

                                                            <div class="col-md-12">
                                                                <label for="title"
                                                                    class="ul-form__label">{{ __('translate.Title') }}
                                                                    <span class="field_required">*</span></label>
                                                                <input type="text" v-model="issue.title"
                                                                    class="form-control" name="title" id="title"
                                                                    placeholder="{{ __('translate.Enter_title') }}">
                                                                <span class="error"
                                                                    v-if="errors_Issue && errors_Issue.title">
                                                                    @{{ errors_Issue.title[0] }}
                                                                </span>
                                                            </div>

                                                            <div class="col-md-12">
                                                                <label
                                                                    class="ul-form__label">{{ __('translate.Labels') }}
                                                                </label>
                                                                <v-select @input="Selected_Label_type"
                                                                    placeholder="{{ __('translate.Select_Label') }}"
                                                                    v-model="issue.label" :reduce="label => label.value"
                                                                    :options="
                                                                                [
                                                                                    {label: 'Feature', value: 'feature'},
                                                                                    {label: 'Bug', value: 'bug'},
                                                                                    {label: 'Invalid', value: 'invalid'},
                                                                                    {label: 'Help wanted', value: 'help'},
                                                                                    {label: 'Question', value: 'question'},
                                                                                    {label: 'Internship', value: 'internship'},
                                                                                    {label: 'Apprenticeship', value: 'apprenticeship'},
                                                                                    {label: 'Seasonal', value: 'seasonal'},
                                                                                ]">
                                                                </v-select>
                                                            </div>

                                                            <div class="col-md-12">
                                                                <label for="comment"
                                                                    class="ul-form__label">{{ __('translate.Comment') }}
                                                                    <span class="field_required">*</span></label>
                                                                <textarea type="text" v-model="issue.comment"
                                                                    class="form-control" name="comment" id="comment"
                                                                    placeholder="{{ __('translate.Please_provide_any_details') }}"></textarea>

                                                                <span class="error"
                                                                    v-if="errors_Issue && errors_Issue.comment">
                                                                    @{{ errors_Issue.comment[0] }}
                                                                </span>
                                                            </div>

                                                            <div class="col-md-12">
                                                                <label for="attachment"
                                                                    class="ul-form__label">{{ __('translate.Attachment') }}</label>
                                                                <input name="attachment" @change="change_Attachment"
                                                                    type="file" class="form-control mb-3"
                                                                    id="attachment">
                                                                <span class="error"
                                                                    v-if="errors_Issue && errors_Issue.attachment">
                                                                    @{{ errors_Issue.attachment[0] }}
                                                                </span>
                                                            </div>

                                                            <div class="col-md-12" v-if="Edit_mode_Issue">
                                                                <label
                                                                    class="ul-form__label">{{ __('translate.Status') }}
                                                                </label>
                                                                <v-select @input="Selected_Status"
                                                                    placeholder="{{ __('translate.Select_status') }}"
                                                                    v-model="issue.status"
                                                                    :reduce="label => label.value" :options="
                                                                                [
                                                                                    {label: 'Pending', value: 'pending'},
                                                                                    {label: 'Solved', value: 'solved'},
                                                                                ]">
                                                                </v-select>
                                                            </div>

                                                        </div>


                                                        <div class="row mt-3">

                                                            <div class="col-md-6">
                                                                <button type="submit" class="btn btn-primary"
                                                                    :disabled="Submit_Processing_Issue">
                                                                    {{ __('translate.Submit') }}
                                                                </button>
                                                                <div v-once class="typo__p"
                                                                    v-if="Submit_Processing_Issue">
                                                                    <div class="spinner spinner-primary mt-3"></div>
                                                                </div>
                                                            </div>
                                                        </div>


                                                    </form>

                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Document --}}
                        <div class="tab-pane fade" id="nav-documents" role="tabpanel"
                            aria-labelledby="nav-documents-tab">

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="text-left">
                                        <div class="text-left bg-transparent">
                                            <a class="btn btn-primary btn-md m-2" @click="New_Document"><i
                                                    class="i-Add text-white mr-2"></i>
                                                {{ __('translate.New_Document') }}</a>
                                        </div>
                                        <div class="table-responsive">
                                            <table id="ul-contact-list" class="display table data_datatable"
                                                >
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('translate.Title') }}</th>
                                                        <th>{{ __('translate.Description') }}</th>
                                                        <th>{{ __('translate.Attachment') }}</th>
                                                        <th>{{ __('translate.Created_At') }}</th>
                                                        <th>{{ __('translate.Action') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($documents as $document)
                                                    <tr>
                                                        <td>{{$document->title}}</td>
                                                        <td>{{$document->description}}</td>
                                                        <td>
                                                            @if($document->attachment)
                                                            <span><a href="{{ asset('assets/images/projetcs/documents/'.$document->attachment) }}"
                                                                    target="_blank">
                                                                    {{$document->attachment}}</a>
                                                            </span>
                                                            @else
                                                            <span>__</span>
                                                            @endif

                                                        </td>

                                                        <td>{{$document->created_at}}</td>
                                                        <td>
                                                            <a @click="Remove_Document( {{ $document->id}})"
                                                                class="ul-link-action text-danger mr-1"
                                                                data-toggle="tooltip" data-placement="top"
                                                                title="Delete">
                                                                <i class="i-Close-Window"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>

                                            </table>
                                        </div>
                                    </div>

                                    <!-- Modal Add Document -->
                                    <div class="modal fade" id="Document_Modal" tabindex="-1" role="dialog"
                                        aria-labelledby="Document_Modal" aria-hidden="true">
                                        <div class="modal-dialog modal-md" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ __('translate.Create') }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">

                                                    <form @submit.prevent="Create_Document()">
                                                        <div class="row">

                                                            <div class="col-md-12">
                                                                <label for="title"
                                                                    class="ul-form__label">{{ __('translate.Title') }}
                                                                    <span class="field_required">*</span></label>
                                                                <input type="text" v-model="document.title"
                                                                    class="form-control" name="title" id="title"
                                                                    placeholder="{{ __('translate.Enter_title') }}">
                                                                <span class="error"
                                                                    v-if="errors_document && errors_document.title">
                                                                    @{{ errors_document.title[0] }}
                                                                </span>
                                                            </div>

                                                            <div class="col-md-12">
                                                                <label for="description"
                                                                    class="ul-form__label">{{ __('translate.Please_provide_any_details') }}
                                                                </label>
                                                                <textarea type="text" v-model="document.description"
                                                                    class="form-control" name="description"
                                                                    id="description"
                                                                    placeholder="{{ __('translate.Please_provide_any_details') }}"></textarea>

                                                            </div>

                                                            <div class="col-md-12">
                                                                <label for="attachment"
                                                                    class="ul-form__label">{{ __('translate.Attachment') }}</label>
                                                                <input name="attachment" @change="change_Document"
                                                                    type="file" class="form-control mb-3"
                                                                    id="attachment">
                                                                <span class="error"
                                                                    v-if="errors_document && errors_document.attachment">
                                                                    @{{ errors_document.attachment[0] }}
                                                                </span>
                                                            </div>
                                                        </div>


                                                        <div class="row mt-3">

                                                            <div class="col-md-6">
                                                                <button type="submit" class="btn btn-primary"
                                                                    :disabled="Submit_Processing_document">
                                                                    {{ __('translate.Submit') }}
                                                                </button>
                                                                <div v-once class="typo__p"
                                                                    v-if="Submit_Processing_document">
                                                                    <div class="spinner spinner-primary mt-3"></div>
                                                                </div>
                                                            </div>
                                                        </div>


                                                    </form>

                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- tasks --}}
                        <div class="tab-pane fade" id="nav-tasks" role="tabpanel" aria-labelledby="nav-tasks-tab">

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="text-left">
                                        <div class="text-left bg-transparent">
                                            <a class="btn btn-primary btn-md m-2" @click="New_Task"><i
                                                    class="i-Add text-white mr-2"></i>
                                                {{ __('translate.New_Task') }}</a>
                                        </div>
                                        <div class="table-responsive">
                                            <table id="ul-contact-list" class="display table data_datatable"
                                                >
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('translate.Task') }}</th>
                                                        <th>{{ __('translate.Company') }}</th>
                                                        <th>{{ __('translate.Project') }}</th>
                                                        <th>{{ __('translate.Start_Date') }}</th>
                                                        <th>{{ __('translate.Finish_Date') }}</th>
                                                        <th>{{ __('translate.Status') }}</th>
                                                        <th>{{ __('translate.Progress') }}</th>
                                                        <th>{{ __('translate.Action') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($tasks as $task)
                                                    <tr>
                                                        <td>{{$task->title}}</td>
                                                        <td>{{$task->company->name}}</td>
                                                        <td>{{$task->project->title}}</td>
                                                        <td>{{$task->start_date}}</td>
                                                        <td>{{$task->end_date}}</td>
                                                        <td>
                                                            @if($task->status == 'completed')
                                                            <span
                                                                class="badge badge-success m-2">{{ __('translate.Completed') }}</span>
                                                            @elseif($task->status == 'not_started')
                                                            <span
                                                                class="badge badge-warning m-2">{{ __('translate.Not_Started') }}</span>
                                                            @elseif($task->status == 'progress')
                                                            <span
                                                                class="badge badge-primary m-2">{{ __('translate.In_Progress') }}</span>
                                                            @elseif($task->status == 'cancelled')
                                                            <span
                                                                class="badge badge-danger m-2">{{ __('translate.Cancelled') }}</span>
                                                            @elseif($task->status == 'hold')
                                                            <span
                                                                class="badge badge-secondary m-2">{{ __('translate.On_Hold') }}</span>
                                                            @endif
                                                        </td>
                                                        <td>{{$task->task_progress}}% </td>
                                                        <td>
                                                            <a href="/tasks/{{$task->id}}/edit"
                                                                class="ul-link-action text-success"
                                                                data-toggle="tooltip" data-placement="top" title="Edit">
                                                                <i class="i-Edit"></i>
                                                            </a>
                                                            <a @click="Remove_Task( {{ $task->id}})"
                                                                class="ul-link-action text-danger mr-1"
                                                                data-toggle="tooltip" data-placement="top"
                                                                title="Delete">
                                                                <i class="i-Close-Window"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>

                                            </table>
                                        </div>
                                    </div>

                                    <!-- Modal Add Task -->
                                    <div class="modal fade" id="Task_Modal" tabindex="-1" role="dialog"
                                        aria-labelledby="Task_Modal" aria-hidden="true">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ __('translate.Create') }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <!--begin::form-->
                                                    <form @submit.prevent="Create_Task()">
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <label for="title"
                                                                        class="ul-form__label">{{ __('translate.Task_Name') }}
                                                                        <span class="field_required">*</span></label>
                                                                    <input type="text" v-model="task.title"
                                                                        class="form-control" name="title" id="title"
                                                                        placeholder="{{ __('translate.Enter_Task_Title') }}">
                                                                    <span class="error"
                                                                        v-if="errors_Task && errors_Task.title">
                                                                        @{{ errors_Task.title[0] }}
                                                                    </span>
                                                                </div>

                                                                <div class="col-md-4">
                                                                    <label for="start_date"
                                                                        class="ul-form__label">{{ __('translate.Start_Date') }}
                                                                        <span class="field_required">*</span></label>

                                                                    <vuejs-datepicker id="start_date" name="start_date"
                                                                        placeholder="{{ __('translate.Enter_Start_date') }}"
                                                                        v-model="task.start_date"
                                                                        input-class="form-control" format="yyyy-MM-dd"
                                                                        @closed="task.start_date=formatDate(task.start_date)">
                                                                    </vuejs-datepicker>

                                                                    <span class="error"
                                                                        v-if="errors_Task && errors_Task.start_date">
                                                                        @{{ errors_Task.start_date[0] }}
                                                                    </span>
                                                                </div>

                                                                <div class="col-md-4">
                                                                    <label for="end_date"
                                                                        class="ul-form__label">{{ __('translate.Finish_Date') }}
                                                                        <span class="field_required">*</span></label>

                                                                    <vuejs-datepicker id="end_date" name="end_date"
                                                                        placeholder="{{ __('translate.Enter_Finish_date') }}"
                                                                        v-model="task.end_date"
                                                                        input-class="form-control" format="yyyy-MM-dd"
                                                                        @closed="task.end_date=formatDate(task.end_date)">
                                                                    </vuejs-datepicker>

                                                                    <span class="error"
                                                                        v-if="errors_Task && errors_Task.end_date">
                                                                        @{{ errors_Task.end_date[0] }}
                                                                    </span>
                                                                </div>

                                                                <div class="col-md-4">
                                                                    <label
                                                                        class="ul-form__label">{{ __('translate.Company') }}
                                                                        <span class="field_required">*</span></label>
                                                                    <v-select @input="Selected_Company"
                                                                        placeholder="{{ __('translate.Choose_Company') }}"
                                                                        v-model="task.company_id"
                                                                        :reduce="label => label.value"
                                                                        :options="companies.map(companies => ({label: companies.name, value: companies.id}))">
                                                                    </v-select>

                                                                    <span class="error"
                                                                        v-if="errors_Task && errors_Task.company_id">
                                                                        @{{ errors_Task.company_id[0] }}
                                                                    </span>
                                                                </div>

                                                                <div class="col-md-4">
                                                                    <label
                                                                        class="ul-form__label">{{ __('translate.Assigned_Employees') }}
                                                                    </label>
                                                                    <v-select multiple @input="Selected_Team"
                                                                        placeholder="{{ __('translate.Choose_Team') }}"
                                                                        v-model="task.assigned_to"
                                                                        :reduce="label => label.value"
                                                                        :options="employees.map(employees => ({label: employees.username, value: employees.id}))">
                                                                    </v-select>
                                                                </div>

                                                                <div class="col-md-4">
                                                                    <label for="summary"
                                                                        class="ul-form__label">{{ __('translate.Summary') }}
                                                                        <span class="field_required">*</span></label>
                                                                    <input type="text" v-model="task.summary"
                                                                        class="form-control" name="summary" id="summary"
                                                                        placeholder="{{ __('translate.Enter_summary') }}">
                                                                    <span class="error"
                                                                        v-if="errors_Task && errors_Task.summary">
                                                                        @{{ errors_Task.summary[0] }}
                                                                    </span>
                                                                </div>


                                                                <div class="col-md-4">
                                                                    <label
                                                                        class="ul-form__label">{{ __('translate.Priority') }}
                                                                        <span class="field_required">*</span></label>
                                                                    <v-select @input="Selected_Priority"
                                                                        placeholder="{{ __('translate.Select_priority') }}"
                                                                        v-model="task.priority"
                                                                        :reduce="(option) => option.value" :options="
                                                                            [
                                                                                {label: 'Urgent', value: 'urgent'},
                                                                                {label: 'High', value: 'high'},
                                                                                {label: 'Medium', value: 'medium'},
                                                                                {label: 'Low', value: 'low'},
                                                                            ]">
                                                                    </v-select>

                                                                    <span class="error"
                                                                        v-if="errors_Task && errors_Task.priority">
                                                                        @{{ errors_Task.priority[0] }}
                                                                    </span>
                                                                </div>

                                                                <div class="col-md-4">
                                                                    <label
                                                                        class="ul-form__label">{{ __('translate.Status') }}
                                                                        <span class="field_required">*</span></label>
                                                                    <v-select @input="Selected_Status_Task"
                                                                        placeholder="{{ __('translate.Select_status') }}"
                                                                        v-model="task.status"
                                                                        :reduce="(option) => option.value" :options="
                                                                        [
                                                                            {label: 'Not Started', value: 'not_started'},
                                                                            {label: 'In Progress', value: 'progress'},
                                                                            {label: 'Cancelled', value: 'cancelled'},
                                                                            {label: 'On Hold', value: 'hold'},
                                                                            {label: 'Completed', value: 'completed'},
                                                                        ]">
                                                                    </v-select>

                                                                    <span class="error"
                                                                        v-if="errors_Task && errors_Task.status">
                                                                        @{{ errors_Task.status[0] }}
                                                                    </span>
                                                                </div>


                                                                <div class="col-md-4">
                                                                    <label
                                                                        class="ul-form__label">{{ __('translate.Progress') }}</label>
                                                                    <vue-slider v-model="task.task_progress" />
                                                                </div>

                                                                <div class="col-md-12">
                                                                    <label for="description"
                                                                        class="ul-form__label">{{ __('translate.Description') }}</label>
                                                                    <textarea type="text" v-model="task.description"
                                                                        class="form-control" name="description"
                                                                        id="description"
                                                                        placeholder="{{ __('translate.Enter_description') }}"></textarea>
                                                                </div>
                                                            </div>


                                                            <div class="row mt-3">
                                                                <div class="col-lg-6">
                                                                    <button type="submit" class="btn btn-primary"
                                                                        :disabled="Submit_Processing_Task">
                                                                        {{ __('translate.Submit') }}
                                                                    </button>
                                                                    <div v-once class="typo__p"
                                                                        v-if="Submit_Processing_Task">
                                                                        <div class="spinner spinner-primary mt-3"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                    </form>

                                                    <!-- end::form -->

                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>




                    </div>
                </div>
            </div>
        </div>
</section>


@endsection

@section('page-js')
<script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
<script src="{{asset('assets/js/datatables.script.js')}}"></script>
<script src="{{asset('assets/js/vendor/vuejs-datepicker/vuejs-datepicker.min.js')}}"></script>
<script src="{{asset('assets/js/vue-slider-component.min.js')}}"></script>

<script>
    Vue.component('v-select', VueSelect.VueSelect)
    var app = new Vue({
    el: '#section_details_Project',
    components: {
        vuejsDatepicker,
        VueSlider: window['vue-slider-component']
    },
    data: {
        data: new FormData(),
        Submit_Processing_Issue:false,
        Edit_mode_Issue: false,
        errors_Issue:[],

        Submit_Processing_Task:false,
        errors_Task:[],

        Submit_Processing_Discussion:false,
        errors_Discussion:[],

        document_data: new FormData(),
        Submit_Processing_document:false,
        errors_document:[],

        documents:@json($documents),
        document: {
            title: "",
            description: "",
            attachment: "",
        }, 

        project:@json($project),
        discussions:@json($discussions),
        discussion: {
            message: "",
        }, 

        issues:@json($issues),
        issue: {
            title: "",
            comment:"",
            label:"",
            attachment:"",
            status:"",
        }, 

        tooltip:'right',
        companies:@json($companies),
        tasks:@json($tasks),
        employees:[],
        task: {
            title: "",
            description:"",
            summary:"",
            company_id:"",
            start_date:"",
            end_date:"",
            status:"",
            priority:"",
            task_progress:0,
            assigned_to:[],
        }, 
    },
   
   
    methods: {



        //------------------------ Discussions ---------------------------------------------------------------------------------------------\\
       

            //------------------------------ Show Modal (Create Discussion) -------------------------------\\
            New_Discussion() {
                this.reset_Form_discussion();
                $('#Discussion_Modal').modal('show');
            },

            //----------------------------- reset_Form_discussion---------------------------\\
            reset_Form_discussion() {
            this.discussion = {
                id: "",
                message: "",
            };
            this.errors_Discussion = {};
        },

            //------------------------ Create Discussion ---------------------------\\
            Create_Discussion() {
                var self = this;
                self.Submit_Processing_Discussion = true;
                axios.post("/project_discussions", {
                    message: self.discussion.message,
                    project_id: self.project.id,
                }).then(response => {
                        self.Submit_Processing_Discussion = false;
                        window.location.href = '/projects/'+ self.project.id; 
                        toastr.success('{{ __('translate.Created_in_successfully') }}');
                        self.errors_Discussion = {};
                })
                .catch(error => {
                    self.Submit_Processing_Discussion = false;
                    if (error.response.status == 422) {
                        self.errors_Discussion = error.response.data.errors;
                    }
                    toastr.error('{{ __('translate.There_was_something_wronge') }}');
                });
            },


             //--------------------------------- Remove Discussion ---------------------------\\
            Remove_Discussion(id) {

                swal({
                    title: '{{ __('translate.Are_you_sure') }}',
                    text: '{{ __('translate.You_wont_be_able_to_revert_this') }}',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#0CC27E',
                    cancelButtonColor: '#FF586B',
                    confirmButtonText: '{{ __('translate.Yes_delete_it') }}',
                    cancelButtonText: '{{ __('translate.No_cancel') }}',
                    confirmButtonClass: 'btn btn-primary mr-5',
                    cancelButtonClass: 'btn btn-danger',
                    buttonsStyling: false
                }).then(function () {
                        axios
                            .delete("/project_discussions/" + id)
                            .then(() => {
                                location.reload();
                                toastr.success('{{ __('translate.Deleted_in_successfully') }}');

                            })
                            .catch(() => {
                                toastr.error('{{ __('translate.There_was_something_wronge') }}');
                            });
                    });
                },

//--------------------------------------------- Issue-----------------------------------------------------------\\
       

       
            change_Attachment(e){
                let file = e.target.files[0];
                this.issue.attachment = file;
            },

            //------------------------------ Show Modal (Create Issue) -------------------------------\\
            New_Issue() {
                this.reset_Form_Issue();
                this.Edit_mode_Issue = false;
                $('#Issue_Modal').modal('show');
            },

            //------------------------------ Show Modal (Edit Issue) -------------------------------\\
            Edit_Issue(issue) {
                this.Edit_mode_Issue = true;
                this.reset_Form_Issue();
                this.issue = issue;
                this.issue.attachment = "";
                $('#Issue_Modal').modal('show');
            },


              //----------------------------- Reset_Form_Issue---------------------------\\
              reset_Form_Issue() {
                this.issue = {
                    id: "",
                    title: "",
                    comment:"",
                    label:"",
                    attachment:"",
                    status:"",
                };
                this.errors_Issue = {};
            },

            Selected_Status(value) {
                if (value === null) {
                    this.issue.status = "";
                }
            },

            Selected_Label_type(value) {
                if (value === null) {
                    this.issue.label = "";
                }
            },


             //------------------------ Create Issue ---------------------------\\
             Create_Issue() {
                var self = this;
                self.Submit_Processing_Issue = true;
                self.data.append("project_id", self.project.id);
                self.data.append("title", self.issue.title);
                self.data.append("comment", self.issue.comment);
                self.data.append("label", self.issue.label);
                self.data.append("attachment", self.issue.attachment);
                axios
                    .post("/project_issues", self.data)
                    .then(response => {
                        self.Submit_Processing_Issue = false;
                        window.location.href = '/projects/'+ self.project.id; 
                        toastr.success('{{ __('translate.Created_in_successfully') }}');
                        self.errors_Issue = {};
                })
                .catch(error => {
                    self.Submit_Processing_Issue = false;
                    if (error.response.status == 422) {
                        self.errors_Issue = error.response.data.errors;
                    }
                    toastr.error('{{ __('translate.There_was_something_wronge') }}');
                });
            },


             //----------------------- Update_Issue---------------------------\\
             Update_Issue() {
                var self = this;
                self.Submit_Processing_Issue = true;
                self.data.append("project_id", self.project.id);
                self.data.append("title", self.issue.title);
                self.data.append("comment", self.issue.comment);
                self.data.append("label", self.issue.label);
                self.data.append("status", self.issue.status);
                if (self.issue.attachment) {
                    self.data.append("attachment", self.issue.attachment);
                }
                self.data.append("_method", "put");

                axios
                    .post("/project_issues/" + self.issue.id, self.data)
                    .then(response => {
                        self.Submit_Processing_Issue = false;
                        window.location.href = '/projects/'+ self.project.id; 
                        toastr.success('{{ __('translate.Updated_in_successfully') }}');
                        self.errors_Issue = {};
                    })
                    .catch(error => {
                        self.Submit_Processing_Issue = false;
                        if (error.response.status == 422) {
                            self.errors_Issue = error.response.data.errors;
                        }
                        toastr.error('{{ __('translate.There_was_something_wronge') }}');
                    });
            },


         

             //--------------------------------- Remove Issue ---------------------------\\
             Remove_Issue(id) {

                swal({
                    title: '{{ __('translate.Are_you_sure') }}',
                    text: '{{ __('translate.You_wont_be_able_to_revert_this') }}',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#0CC27E',
                    cancelButtonColor: '#FF586B',
                    confirmButtonText: '{{ __('translate.Yes_delete_it') }}',
                    cancelButtonText: '{{ __('translate.No_cancel') }}',
                    confirmButtonClass: 'btn btn-primary mr-5',
                    cancelButtonClass: 'btn btn-danger',
                    buttonsStyling: false
                }).then(function () {
                        axios.delete("/project_issues/" + id)
                            .then(() => {
                                toastr.success('Deleted in successfully');
                                location.reload();

                            })
                            .catch(() => {
                                toastr.danger('There was something wronge');
                            });
                    });
                },


                
//--------------------------------------------- Issue-----------------------------------------------------------\\
       

       
            change_Document(e){
                let file = e.target.files[0];
                this.document.attachment = file;
            },

            //------------------------------ Show Modal (Create Document) -------------------------------\\
            New_Document() {
                this.Reset_Form_Document();
                $('#Document_Modal').modal('show');
            },

              //----------------------------- Reset_Form_Document---------------------------\\
              Reset_Form_Document() {
                this.document = {
                    id: "",
                    title: "",
                    description:"",
                    attachment:"",
                };
                this.errors_document = {};
            },

             //------------------------ Create Document ---------------------------\\
             Create_Document() {
                var self = this;
                self.Submit_Processing_document = true;
                self.document_data.append("project_id", self.project.id);
                self.document_data.append("title", self.document.title);
                self.document_data.append("description", self.document.description);
                self.document_data.append("attachment", self.document.attachment);
                axios
                    .post("/project_documents", self.document_data)
                    .then(response => {
                        self.Submit_Processing_document = false;
                        window.location.href = '/projects/'+ self.project.id; 
                        toastr.success('{{ __('translate.Created_in_successfully') }}');
                        self.errors_document = {};
                })
                .catch(error => {
                    self.Submit_Processing_document = false;
                    if (error.response.status == 422) {
                        self.errors_document = error.response.data.errors;
                    }
                    toastr.error('{{ __('translate.There_was_something_wronge') }}');
                });
            },

         
             //--------------------------------- Remove Document ---------------------------\\
             Remove_Document(id) {

                swal({
                    title: '{{ __('translate.Are_you_sure') }}',
                    text: '{{ __('translate.You_wont_be_able_to_revert_this') }}',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#0CC27E',
                    cancelButtonColor: '#FF586B',
                    confirmButtonText: '{{ __('translate.Yes_delete_it') }}',
                    cancelButtonText: '{{ __('translate.No_cancel') }}',
                    confirmButtonClass: 'btn btn-primary mr-5',
                    cancelButtonClass: 'btn btn-danger',
                    buttonsStyling: false
                }).then(function () {
                        axios.delete("/project_documents/" + id)
                            .then(() => {
                                toastr.success('Deleted in successfully');
                                location.reload();

                            })
                            .catch(() => {
                                toastr.danger('There was something wronge');
                            });
                    });
                },

//------------------------------------- Task ------------------------------------------------------------\\

            New_Task() {
                this.Reset_Form_Task();
                $('#Task_Modal').modal('show');
            },

              //----------------------------- Reset_Form_Task---------------------------\\
              Reset_Form_Task() {
                this.task = {
                    id: "",
                    title: "",
                    description:"",
                    summary:"",
                    company_id:"",
                    start_date:"",
                    end_date:"",
                    status:"",
                    priority:"",
                    task_progress:0,
                    assigned_to:[],
                };
                this.errors_Task = {};
            },

        Selected_Team(value) {
            if (value === null) {
                this.task.assigned_to = [];
            }
        },

     
        Selected_Status_Task(value) {
            if (value === null) {
                this.task.status = "";
            }
        },

        
        Selected_Priority(value) {
            if (value === null) {
                this.task.priority = "";
            }
        },

        Selected_Company(value) {
            if (value === null) {
                this.task.company_id = "";
            }
            this.employees = [];
            this.task.assigned_to = [];
            this.Get_employees_by_company(value);
        },

            
        //---------------------- Get_employees_by_company ------------------------------\\
        
        Get_employees_by_company(value) {
            axios
            .get("/Get_employees_by_company?id=" + value)
            .then(({ data }) => (this.employees = data));
        },

        
        //------------------------ Create Task ---------------------------\\
        Create_Task() {
            var self = this;
            self.Submit_Processing_Task = true;
            axios.post("/tasks", {
                title: self.task.title,
                description: self.task.description,
                summary: self.task.summary,
                project_id: self.project.id,
                company_id: self.task.company_id,
                assigned_to: self.task.assigned_to,
                priority: self.task.priority,
                start_date: self.task.start_date,
                end_date: self.task.end_date,
                status: self.task.status,
                task_progress: self.task.task_progress,
            }).then(response => {
                    self.Submit_Processing_Task = false;
                    window.location.href = '/projects/'+ self.project.id; 
                    toastr.success('{{ __('translate.Created_in_successfully') }}');
                    self.errors_Task = {};
            })
            .catch(error => {
                self.Submit_Processing_Task = false;
                if (error.response.status == 422) {
                    self.errors_Task = error.response.data.errors;
                }
                toastr.error('{{ __('translate.There_was_something_wronge') }}');
            });
        },

        //--------------------------------- Remove Task ---------------------------\\
        Remove_Task(id) {
                swal({
                    title: '{{ __('translate.Are_you_sure') }}',
                    text: '{{ __('translate.You_wont_be_able_to_revert_this') }}',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#0CC27E',
                    cancelButtonColor: '#FF586B',
                    confirmButtonText: '{{ __('translate.Yes_delete_it') }}',
                    cancelButtonText: '{{ __('translate.No_cancel') }}',
                    confirmButtonClass: 'btn btn-primary mr-5',
                    cancelButtonClass: 'btn btn-danger',
                    buttonsStyling: false
                }).then(function () {
                        axios
                            .delete("/tasks/" + id)
                            .then(() => {
                                location.reload();
                                toastr.success('{{ __('translate.Deleted_in_successfully') }}');

                            })
                            .catch(() => {
                                toastr.error('{{ __('translate.There_was_something_wronge') }}');
                            });
                    });
                },



    },
    //-----------------------------Autoload function-------------------
    created () {
    },

})

</script>

<script type="text/javascript">
    $(function () {
      "use strict";

        $('.data_datatable').DataTable( {
            "processing": true, // for show progress bar
            dom: "<'row'<'col-sm-12 col-md-7'lB><'col-sm-12 col-md-5 p-0'f>>rtip",
            oLanguage:
                { 
                sLengthMenu: "_MENU_", 
                sSearch: '',
                sSearchPlaceholder: "Search..."
            },
            buttons: [
                {
                    extend: 'collection',
                    text: 'EXPORT',
                    buttons: [
                        'csv','excel', 'pdf', 'print'
                    ]
            }]
        });

    });
</script>

@endsection