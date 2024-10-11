@php
    include public_path('functions.php');
    include public_path('userconfig.php');
@endphp

@extends('main')

@section('content')
    @if ($action == 'detail')
        @php
            $id = $object->id;
            $edStatus = $listEnumValues['student']['edStatus'][$object->edStatus];
            $tdLeaveStartDate = datetimeToNiceShortDate($object->tdLeaveStartDate );
            $sName = escapeHtml($object -> sName );
            $erType = $listEnumValues['student']['erType'][$object->erType];
            $iAmount = $object -> iAmount ;
            $sdlZone = escapeHtml($object -> sdlZone );
            $sDescription = escpeHtmlQuill($object -> sDescription , 1000);
            $fileAttachment = $object -> fileAttachment;
            $picScreenshot = $object -> picScreenshot;
            $xdActivityType = $listEnumValues['student']['xdActivityType'][$object->xdActivityType];
            $url = "";
            $id_jd = ($object -> id_jd  != null) ? "<a class=\"\" href=\"{{$url}}\" target=\"_blank\">ok</a>" : '';
            $url = "";
            $idd_blog = ($object -> idd_blog  != null) ? "<a class=\"\" href=\"{{$url}}\" target=\"_blank\">{{$object -> idd_blog}}</a>" : '';
            $fJdMatch = $object -> fJdMatch ;
            $tdtIvTime = datetimeToNiceDatetime($object -> tdtIvTime );
            $jsonMobile_poc = getCallingLink($object -> jsonMobile_poc );
            $add_faq = escapeHtml($object -> add_faq );
            $xcCalling = $listEnumValues['student']['xcCalling'][$object -> xcCalling];
            $xrIs_GiveResult = $listEnumValues['student']['xrIs_GiveResult'][$object -> xrIs_GiveResult];
            $mdd_subject = $object -> mdd_subject;
            $mcLevels = $listEnumValues['student']['mcLevels'][$object -> mcLevels];
            $xrIvBy = $object -> xrIvBy;
            $tCreationTime = utcTimestampToDefaultDatetime($object -> tCreationTime );
            $tLastUpdate = utcTimestampToDefaultDatetime($object -> tLastUpdate );
            $sShowSuccess = "SUCCESS: Ok";
        @endphp
<div class="container">
    <div class="row justify-content-between">
        <div class="col-auto">
            <ul class="nav nav-tabs big border-0 mx-1">
                <li class="nav-item">
                    <a class="nav-link active border-0 arrow">Student</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link border-0 bg-white"><b>{{$object->sName}}</b></a>
                </li>
            </ul>
        </div>
        <div class="col-auto">

        </div>
    </div>
    <div class="row">
         <div class="col-md-2 col-0 d-none d-md-block">
            here related data buttons will be displayed
         </div>
         <div class="col-md-10 col-12">
            <!--Nav tabs -->
            <div class="">
                <ul class="nav nav-tabs mt-2 text-center">
                    <li class="nav-item min80">
                        <a class="nav-link active" data-toggle="tab" href="#tabPaneDetailInfo1">General</a>
                    </li>
                    <li class="nav-item min80">
                        <a class="nav-link " data-toggle="tab" href="#tabPaneDetailInfo2">Other</a>
                    </li>
                    <li class="nav-item min80">
                        <a class="nav-link" data-toggle="tab" href="#tabPaneDetailAction" id="navItemDetailAction">Actions</a>
                    </li>
                </ul>
            </div>
            <!-- Tab panes -->
            <div class="tab-content bg-white shadow" style="min-height:10rem">
                <div class="tab-pane container-fluid pt-2 active" id="tabPaneDetailInfo1">
                    <div class="" id="divDetailInfo1"></div>
                </div>
                <div class="tab-pane container-fluid pt-2 " id="tabPaneDetailInfo2">
                    <div class="" id="divDetailInfo2"></div>
                </div>
                <div class="tab-pane container-fluid pt-2 " id="tabPaneDetailAction">
                    <div class="" id="divDetailInfo3"></div>
                </div>
            </div>
         </div>
    </div>
</div>

<script>
    //prepare arrays of objects {column, value, size(optional)} for tabs
    var arrDataDetailInfo1 = [
        {"column": "Status", "value": `{{$edStatus}}`},
        {"column": "Leave Start Date", "value": `{{$tdLeaveStartDate}}`},
        {"column": "Name", "value": `{{$sName}}`},
        {"column": "Type", "value": `{{$erType}}`},
        {"column": "Amount", "value": `{{$iAmount}}`},
        {"column": "Zone", "value": `{{$sdlZone}}`},
        {"column": "Description", "value": `{{$sDescription}}`, "size": `12`},
        {"column": "Attachment", "value": `{{$fileAttachment}}`},
        {"column": "Pic Screenshot", "value": `{{$picScreenshot}}`},
        {"column": "Activity Type", "value": `{{$xdActivityType}}`},
        {"column": "JD", "value": `{{$id_jd}}`},
    ];
    var arrDataDetailInfo2 = [
        {"column": "Blog", "value": `{{$idd_blog}}`},
        {"column": "Jd Match", "value": `{{$fJdMatch}}`},
        {"column": "Iv Time", "value": `{{$tdtIvTime}}`},
        {"column": "Mobile poc", "value": `{{$jsonMobile_poc}}`},
        {"column": "Faq", "value": `{{$add_faq}}`},
        {"column": "Calling", "value": `{{$xcCalling}}`},
        {"column": "Is Give Result", "value": `{{$xrIs_GiveResult}}`},
        {"column": "Quote", "value": `{{$mdd_subject}}`},
        {"column": "Levels", "value": `{{$mcLevels}}`},
        {"column": "Iv By", "value": `{{$xrIvBy}}`},
        {"column": "Created On", "value": `{{$tCreationTime}}`},
        {"column": "Updated On", "value": `{{$tLastUpdate}}`},
        {"column": "ID", "value": `{{$id}}`}
    ];
    
    function drawDetailViewFields(sDivIdName, sData, sStyle="singleFieldCard")
    {
        var count = sData.length;
    
        response = ``;
        if ((sStyle == "singleFieldCard") || (sStyle == "multiFieldCard"))
        {
             response += `<div class="row">`;
        }
        for (i = 0; i < count; i++)
        {
            if (sData[i].size !== undefined ) colSize = parseInt(sData[i].size);
            else colSize = 4;
    
            if (sStyle == "rowColCol")
            {
                response += `<div class="row"><div class="col-3 small-heading">${sData[i].column}</div><div class="col-1 small-heading">:</div><div class="col-8">${sData[i].value}</div></div>`;
            }
            else // Single or multi
            {
                response += `
                <div class="col-md-${colSize} detailField p-2">
                    <div class="detailField pl-3 py-2 shadow-sm rounded h-100">
                        <label class="detailFieldLabel">${sData[i].column}</label><div class="detailFieldValue" style=\"white-space: pre-wrap\">${sData[i].value}</div>
                    </div>
                </div>`;
            }
            //console.log(colSize);
        }
        if ((sStyle == "singleFieldCard") || (sStyle == "multiFieldCard"))
        {
             response += `</div> <!-- row for all fields -->`;
        }
        //response += `</div>`
        document.getElementById(sDivIdName).innerHTML=response;
    }
 
    drawDetailViewFields('divDetailInfo1', arrDataDetailInfo1);
    drawDetailViewFields('divDetailInfo2', arrDataDetailInfo2);
    document.addEventListener( 'DOMContentLoaded', loadDetailViewDesign);//If detail view loads as new page
    if (document.readyState == 'complete') loadDetailViewDesign();//If detail view loads as modal
    function loadDetailViewDesign()
    {
        //showAlert(`{{$sShowSuccess}}`);
    }

</script>

    @endif
@endsection
