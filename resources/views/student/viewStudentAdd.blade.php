@php
    include public_path('functions.php');
@endphp

@extends('main')

@section('content')
    @if ($action == 'add')
<div class="container">
        @if (false)
    <form id="formAdd"  class="form-horizontal rowgap" action="{{route('saveStudent')}}" method="post" enctype="multipart/form-data">
    <fieldset>
    @csrf

        @else
    <form id="formAdd"  class="form-horizontal rowgap" action="" method="post" enctype="multipart/form-data" onsubmit="event.preventDefault(); backgroundPostFormAndAlert(this, '{{route('saveStudent')}}'); return false;">
    <fieldset>
        @csrf
        <input type="hidden" name="isAjax" value="1">
        @endif

        <!--<input type="hidden" name="act" value="add">-->
        <input type="hidden" name="act" value="">
        <input type="hidden" name="filled" value="1">
        <input type="hidden" name="id_jd" value="">
        <div class="row bg-curve mt-0">
            <div class="col-12 text-center">
                <a class="btn btn-sm btn-outline-danger float-left" type="reset" onclick="formInputFieldReset(document.getElementById('formAdd'));">Reset</a>
                <strong>New Student</strong>
                <button id="myButton" name="myButton" class="btn btn-sm btn-primary float-right" type="submit">Submit</button>
            </div>
        </div>
        <!-- Nav tabs -->
        <div class="divLDEANavTabs">
          <ul class="nav nav-tabs mt-2 text-center">
            <li class="nav-item min80">
                <a class="nav-link active" data-toggle="tab" href="#tabPaneAddInfo1">General</a>
            </li>
            <li class="nav-item min80">
                <a class="nav-link " data-toggle="tab" href="#tabPaneAddInfo2">Other</a>
            </li>
          </ul>
        </div>


        <!-- Tab-content and tab-panes -->
        <div class="tab-content bg-white shadow" style="min-height:10rem">
            <div class="tab-pane container-fluid pt-2 active" id="tabPaneAddInfo1">
                <div class="row">
        @php $edStatus = 0; @endphp
                <div class="form-group col-md-3">
                    <label class="control-label" for="edStatus">Status</label>
                    <div class="">
        @php $entries = $listEnumValues['student']['edStatus']; @endphp
                        <select id="edStatus" name="edStatus" class="form-control" data-live-search="true" data-style="dropdownborder" >
                            <option value="">Select</option>
        @for ($k=0; $k < count($entries); $k++)
            @php $selected = ''; @endphp
            @if ($edStatus === $k) @php $selected = 'selected'; @endphp @endif
            @php $temp = escapeHtml($entries[$k]); @endphp
                                <option value="{{$k}}" {{$selected}}>{{$temp}}</option>
        @endfor                            
                        </select>
                    </div>
                </div>

        @php $tdLeaveStartDate = null; @endphp
                <div class="form-group col-md-3">
                    <label class="control-label" for="tdLeaveStartDate">Leave Start Date</label>
                    <div class="">
                        <input id="tdLeaveStartDate" name="tdLeaveStartDate" type="date" placeholder="" value="{{$tdLeaveStartDate}}" class="form-control" >
                    </div>
                </div>

        @php $sName = null; @endphp
                <div class="form-group col-md-3">
                    <label class="control-label" for="sName">Name</label>
                    <div class="">
                        <input id="sName" name="sName" type="text" placeholder="" value="{{$sName}}" class="form-control" maxlength="250" >
                    </div>
                </div>

        @php $erType = null; @endphp
                <div class="form-group col-md-3">
                    <label class="control-label" for="erType">Type *</label>
                    <div class="">
                        <div>
                            <input type="radio" id="erType0" name="erType" value="0"/>
                            <label for="erType">Penalty</label>&emsp;
                            <input type="radio" id="erType1" name="erType" value="1"/>
                            <label for="erType">Reward</label>&emsp;
                        </div>
                    </div>
                </div>

        @php $iAmount = 0; @endphp
                <div class="form-group col-md-3">
                    <label class="control-label" for="iAmount">Amount</label>
                    <div class="">
                        <input id="iAmount" name="iAmount" type="number" placeholder="" value="{{$iAmount}}" class="form-control" >
                    </div>
                </div>

    
        @php 
            $entries = $listEnumValues['student']['sdlZone']; 
            $datalist = "<datalist id=''>";
        @endphp

        @for ($k=0; $k < count($entries); $k++)
            @php
            $temp = escapeHtml($entries[$k]);
            $datalist .= "
                            <option value='{{$temp}}'>{{$temp}}</option>";
            @endphp
        @endfor

        @php
        $datalist .= "
                        </datalist>";
        @endphp
        @php $sdlZone = null; @endphp
                <div class="form-group col-md-3">
                    <label class="control-label" for="sdlZone">Zone</label>
                    <div class="">
                    <input list="" id="sdlZone" name="sdlZone" type="text" placeholder="" class="form-control" maxlength="64" >{!! $datalist !!}
                    </div>
                </div>

        @php $sDescription = null; @endphp
                <div class="form-group col-md-6">
                    <label class="control-label" for="sDescription">Description</label>
                    <div class="">
                        <textarea id="sDescription" name="sDescription" type="text" placeholder="Pass in Quill function" value="{{$sDescription}}" class="form-control" cols="40" rows="5" maxlength="1000" ></textarea>
                    </div>
                </div>

        @php $fileAttachment = null; @endphp
                <div class="form-group col-md-3">
                    <label class="control-label" for="fileAttachment">Attachment</label>
                    <div class="">
                        <input id="fileAttachment" name="fileAttachment" type="file" placeholder="" class="form-control-file" >
                    </div>
                </div>

        @php $picScreenshot = null; @endphp
                <div class="form-group col-md-3">
                    <label class="control-label" for="picScreenshot">Pic Screenshot</label>
                    <div class="">
                        <input id="picScreenshot" name="picScreenshot" type="file" placeholder="" class="form-control-file" >
                    </div>
                </div>

        @php $xdActivityType = 0; @endphp
                <div class="form-group col-md-3">
                    <label class="control-label" for="xdActivityType">Activity Type</label>
                    <div class="">
        @php $entries = $listEnumValues['student']['xdActivityType']; @endphp
                        <select id="xdActivityType" name="xdActivityType" class="form-control" data-live-search="true" data-style="dropdownborder" >
                            <option value="">Select</option>
        @for ($k=0; $k < count($entries); $k++)
            @php $selected = ''; @endphp
            @if ($edStatus === $k) @php $selected = 'selected'; @endphp @endif
            @php $temp = escapeHtml($entries[$k]); @endphp
                                <option value="{{$k}}" {{$selected}}>{{$temp}}</option>
        @endfor                            
                        </select>
                    </div>
                </div>
              </div> <!-- row -->
            </div> <!-- Tab Pane -->
            <div class="tab-pane container-fluid pt-2 " id="tabPaneAddInfo2">
                <div class="row">
        @php $idd_blog = null; @endphp
                <div class="form-group col-md-3">
                    <label class="control-label" for="idd_blog">Blog</label>
                    <div class="">
        @php $entries = $listEnumValues['student']['blog']; @endphp
                        <select id="idd_blog" name="idd_blog" class="selectpicker form-control" data-live-search="true" data-style="dropdownborder" >
                            <option value="">Select</option>
        @for ($k=0; $k < count($entries); $k++)
            @php $selected = ''; @endphp
            @if ($idd_blog === $k) @php $selected = 'selected'; @endphp @endif
            @php $temp = escapeHtml($entries[$k]); @endphp
                            <option value="{{$k}}" {{$selected}}>{{$temp}}</option>
        @endfor
                        </select>
                    </div>
                </div>

        @php $fJdMatch = null; @endphp
                <div class="form-group col-md-3">
                    <label class="control-label" for="fJdMatch">Jd Match</label>
                    <div class="">
                        <input id="fJdMatch" name="fJdMatch" type="number" placeholder="" value="{{$fJdMatch}}" step="any" class="form-control" >
                    </div>
                </div>

        @php $sLinkIntroVideo = null; @endphp
                <div class="form-group col-md-3">
                    <label class="control-label" for="sLinkIntroVideo">Link Intro Video</label>
                    <div class="">
                        <input id="sLinkIntroVideo" name="sLinkIntroVideo" type="text" placeholder="" value="{{$sLinkIntroVideo}}" class="form-control" maxlength="250" >
                    </div>
                </div>

        @php $tdtIvTime = null; @endphp
                <div class="form-group col-md-3">
                    <label class="control-label" for="tdtIvTime">Iv Time</label>
                    <div class="">
                        <input id="tdtIvTime" name="tdtIvTime" type="datetime-local" placeholder="" value="{{$tdtIvTime}}" class="form-control" >
                    </div>
                </div>

        @php $jsonMobile_poc = null; @endphp
                <div class="form-group col-md-3">
                    <label class="control-label" for="jsonMobile_poc">Mobile poc</label>
                    <div class="">
                        <input id="jsonMobile_poc" name="jsonMobile_poc" type="hidden" value="" class="form-control" maxlength="250" >
                        <input id="jsonMobile_poc_display" type="text" placeholder="Click to fill number" class="form-control" onclick="showModalToEnterMobileGroup('jsonMobile_poc')" readonly >
                    </div>
                </div>

        @php $add_faq = null; @endphp
                <div class="form-group col-md-3">
                    <label class="control-label" for="add_faq">Faq</label>
                    <div class="">
        @php $entries = $listEnumValues['student']['faq']; @endphp
                        <select id="add_faq" name="add_faq[]" class="selectpicker form-control" data-live-search="true" data-style="dropdownborder">
                            <option value="">Select</option>
        @for ($k=0; $k < count($entries); $k++)
            @php $temp = escapeHtml($entries[$k]); @endphp
                            <option value="{{$k}}">{{$temp}}</option>
        @endfor
                        </select>

                    </div>
                </div>

        @php $xcCalling = 0; @endphp
                <div class="form-group col-md-3">
                    <label class="control-label" for="xcCalling">Calling</label>
                    <div class="">

        @php $entries = $listEnumValues['student']['xcCalling']; @endphp
                        <select id="xcCalling" name="xcCalling[]" class="form-control selectpicker" data-live-search="true" data-style="dropdownborder" >
                            <option value="">Select</option>
        @for ($k=0; $k < count($entries); $k++)
            @php 
                $selected = ($xcCalling === $entries[$k]) ? 'selected' : '';
                $temp = escapeHtml($entries[$k]);
            @endphp    
                            <option value="{{$k}}" {{$selected}}>{{$temp}}</option>
        @endfor
                        </select>
                    </div>
                </div>

        @php $xrIs_GiveResult = 1; @endphp
                <div class="form-group col-md-3">
                    <label class="control-label" for="xrIs_GiveResult">Is Give Result</label>
                    <div class="">
                        <div>
                            <input type="radio" id="xrIs_GiveResult0" name="xrIs_GiveResult" value="0"/>
                            <label for="xrIs_GiveResult">No</label>&emsp;
                            <input type="radio" id="xrIs_GiveResult1" name="xrIs_GiveResult" value="1"/>
                            <label for="xrIs_GiveResult">Yes</label>&emsp;
                        </div>
                    </div>
                </div>

        @php $mdd_subject = null; @endphp
                <div class="form-group col-md-3">
                    <label class="control-label" for="mdd_subject">Quote</label>
                    <div class="">

        @php $entries = $listEnumValues['student']['quote']; @endphp
                        <input type="hidden" value="null" name="mdd_subject">
                        <select id="mdd_subject" name="mdd_subject[]" class="selectpicker form-control" data-live-search="true" data-style="dropdownborder">
                            <option value="">Select</option>
        @for ($k=0; $k < count($entries); $k++)
            @php
                $selected = '';
                $temp = escapeHtml($entries[$k]);
            @endphp
                            <option value="{{$entries[$k]}}">{{$temp}}</option>
        @endfor
                        </select>
                    </div>
                </div>

        @php $mcLevels = 0; @endphp
                <div class="form-group col-md-3">
                    <label class="control-label" for="mcLevels">Levels</label>
                    <div class="">

        @php $entries = $listEnumValues['student']['mcLevels']; @endphp
                        <select id="mcLevels" name="mcLevels[]" class="form-control selectpicker" data-live-search="true" data-style="dropdownborder">
                            <option value="">Select</option>";
        @for ($k=0; $k < count($entries); $k++)  
            @php 
                $selected = ($mcLevels === $entries[$k]) ? 'selected' : '';
                $temp = escapeHtml($entries[$k]);
            @endphp    
                            <option value="{{$k}}" {{$selected}}>{{$temp}}</option>
        @endfor
                        </select>
                    </div>
                </div>
              </div> <!-- row-->
            </div> <!-- tab-pane-->
        </div> <!-- tab-content-->
            <div class="row">
                <div class="col-md-12 text-center">
                    <button id="myButton1" name="myButton" class="btn btn-md btn-primary" type="submit">Submit</button>
                </div>
            </div>
    </fieldset>
    </form> 
</div>
    @endif
@endsection
