@php
    include public_path('functions.php');
@endphp

@extends('main')

@section('content')
    @if ($action == 'list')
        @php
            viewStudentList($listView, $range, $isDetail, $isAdd, $isEdit, $arrayObjects)
        @endphp
    @elseif ($action == 'add')
        <div class="container">
        @if (true)
            <form id="formAdd"  class="form-horizontal rowgap" action="{{route('addStudent')}}" method="post" enctype="multipart/form-data">
            <fieldset>@csrf
        @else
            <form id="formAdd"  class="form-horizontal rowgap" action="" method="post" enctype="multipart/form-data" onsubmit="backgroundPostFormAndAlert(this, '{$GLOBALS->PATH_TO_CONTROL}student.php');"><fieldset>@csrf
        @endif
        @php
            viewStudentAdd()   
        @endphp  
        </div>

    @elseif ($action == 'detail')
        @php
            viewStudentDetails($isEdit, $object)
        @endphp  
    @elseif ($action == 'edit')
        @php
            $id = $object->sTokenEdit.$object->id
        @endphp
        @if (true)
            <form id="formAdd"  class="form-horizontal rowgap" action="{{route('editStudent', ['id' => $id])}}" method="post" enctype="multipart/form-data">
            <fieldset>@csrf
        @else
            <form id="formAdd"  class="form-horizontal rowgap" action="" method="post" enctype="multipart/form-data" onsubmit="backgroundPostFormAndAlert(this, '{$GLOBALS->PATH_TO_CONTROL}student.php');"><fieldset>@csrf
        @endif
        @php
            //dd("ok123");
            viewStudentEdit($object)
        @endphp         
    @endif
@endsection

<?php

//include_once './public/functions.php';

function ok()
{
    echo "inside ok function";
}
function viewStudentList($listView, $range, $isDetail, $isAdd, $isEdit, &$arrayObjects)
{
    //echo "<pre>"; 
    //print_r($arrayObjects); exit;
    //echo "okay";
    global $sUserType;
    $nav = isset($_REQUEST->nav) ? escapeHtml($_REQUEST->nav) : '';
    $sPageTitle = 'Student List';
    //$output = createHeader(false, $sPageTitle, $nav);
    $arrayObjectsCount = count($arrayObjects);
    $r1 = ''; $r2 = ''; $r3 = ''; $r4 = ''; $all = ''; $deactivated = ''; $deleted = '';
    switch($range)
    {
        case 'r1':
            $r1 = "active";
            break;
        case 'r2':
            $r2 = "active";
            break;
        case 'r3':
            $r3 = "active";
            break;
        case 'r4':
            $r4 = "active";
            break;
        case 'all':
            $all = "active";
            break;
        case 'deactivated':
            $deactivated = "active";
            break;
        case 'deleted':
            $deleted = "active";
            break;
    }
    //There may be some params like id_jd or id_corporate or start/end dates which we want to carry on but some params list act/list/range will be changed so parse params and then append new query params and reuse existing query params
    // $qParams = array();
    // //parse_str($_SERVER->QUERY_STRING, $qParams);
	$qParams = $_REQUEST;
	// if (isset($qParams->modal)) unset($qParams->modal);

    // if (isset($qParams->list)) unset($qParams->list);
    //$qStringNew1 = http_build_query($qParams);
    $sCurrentUrlWithoutList = 'student.php?';

    // unset($qParams->range);
    //$qStringNew2 = http_build_query($qParams);
    $sCurrentUrlWithoutRangeList = 'student.php?';

    // $qParams->act='add';
    // if (isset($qParams->nav)) unset($qParams->nav);
    // $qStringNew3 = http_build_query($qParams);
    $sCurrentUrlForAddWithoutRangeListAct = 'student.php?';

    $output = "
    <div class=\"container-fluid\">";
    $action = isset($_REQUEST->act) ? escapeHtml($_REQUEST->act) : null;
    //Hide NavTabs for Guest and search result list view
    if (($sUserType != 'guest') && ($action != 'search'))
    {
        $output .= "
        <div class=\"text-center\">
            <div class=\"\">
            <ul class=\"nav nav-tabs\">
            <!--
              <li class=\"nav-item\">
                <a class=\"nav-link mt-1 {$r1}\" href=\"{{route('editStudent', ['range' => 'r1'])}}\">Range1</a>
              </li>
              <li class=\"nav-item\">
                <a class=\"nav-link mt-1 {$r2}\" href=\"{$sCurrentUrlWithoutRangeList}&range=r2\">Range2</a>
              </li>
              <li class=\"nav-item\">
                <a class=\"nav-link mt-1 {$r3}\" href=\"{$sCurrentUrlWithoutRangeList}&range=r3\">Range3</a>
              </li>
            -->
              <li class=\"nav-item\">
                <a class=\"nav-link mt-1 {$r4}\" href=\"{{route('editStudent', ['range' => 'r1'])}}\">Recent Updated</a>
              </li>
              <li class=\"nav-item\">
                <a class=\"nav-link mt-1 {$all}\" href=\"{$sCurrentUrlWithoutRangeList}&range=all\">All</a>
              </li>";
/*
        $output .= "
              <li class=\"nav-item\">
                <a class=\"nav-link mt-1 {$deactivated}\" href=\"{$sCurrentUrlWithoutRangeList}&range=deactivated\">Deactivated</a>
              </li>";
*/
        $output .= "
              <li class=\"nav-item\">
                <a class=\"nav-link mt-1 {$deleted}\" href=\"{$sCurrentUrlWithoutRangeList}&range=deleted\">Deleted</a>
              </li>
              <li class=\"nav-item float-right\">
                <span class=\"nav-link mt-1 \">
                    <a class=\"mx-1\" href=\"{$sCurrentUrlWithoutList}&list=card\"><span class=\"fas fa-th-large fa-lg\"></span></a>
                    <a class=\"mx-1\" href=\"{$sCurrentUrlWithoutList}&list=list\"><span class=\"fas fa-list fa-lg\"></span></a>
                    <a class=\"mx-1\" href=\"{$sCurrentUrlWithoutList}&list=table\"><span class=\"fas fa-table fa-lg\"></span></a>
                </span>
              </li>
            </ul>
            </div>
        </div>";
    }
    $output .= "
        <div class=\"row p-1 bg-white justify-content-between\">
            <div class=\"col-auto\">
                <span class=\"bigger\">students</span>&nbsp;<span class=\"badge badge-primary badge-counter float-right small \">{$arrayObjectsCount}</span>
            </div>
";
    if ($isAdd)
    {
        $output .= "
            <div class=\"col-auto\">
                <a class=\"btn btn-sm btn-add\" href=\"{$sCurrentUrlForAddWithoutRangeListAct}&nav=b\"><span class=\"fas fa-plus fa-lg\"></span> New</a>
            </div>
";
    }
    $output .= "
        </div>
    ";
    $sDataTableColumnDefs = '';
    if ($arrayObjectsCount > 0)
    {
        //Before showing entries, show table header if table view
        if ($listView == 'table') //table view
        {
            $output .= "
        <div style=\"overflow-x:auto;\">
        <table class=\"table table-responsive-sm table-sm table-bordered  alternate datatablejs\">
            <thead><tr class=\"thead-light\"><th>Act</th><th>ID- Name</th><th>Status</th><th>Type</th><th>JD</th></tr></thead>
            <tbody>";
            $sDataTableColumnDefs = "columnDefs: [{orderable: false, targets: 0}, {orderable: false, targets: 1}, {orderable: false, targets: 2}, {orderable: false, targets: 3}, {orderable: false, targets: 4}, ],";
        }
        //Start loop to show cards/lines/rows
        $maxCardsInRow = 3;
        $cardWidthInGrid = intval(12/$maxCardsInRow);
        for ($i=0, $cardsInRow=0; $i < $arrayObjectsCount; $i++)
        {
            $sTitle = $arrayObjects[$i]->sName;
            $edStatus = $arrayObjects[$i]->edStatus;
            $erType = $arrayObjects[$i]->erType;
            $id_jd = $arrayObjects[$i]->id_jd;
            $sButtons = "";
            if ($isDetail)
            {
                /*
                $sLinkDetails = CONTROL_ROOT."student.php?act=detail&id={$arrayObjects[$i]->sTokenDetails}{$arrayObjects[$i]->id}";
                $sButtons .= "
                    <a class=\"mr-1\" href=\"{$sLinkDetails}\" target=\"_blank\"><span class=\"fas fa-external-link-alt fa-lg\"></span></a>";
                */
                $sLinkDetailsModal = "student.php?act=detail";
                $sButtons .= "
                    <a class=\"mr-1\" href onclick=\"ajaxAndAlert('modal=1&id={$arrayObjects[$i]->sTokenDetails}{$arrayObjects[$i]->id}', '{$sLinkDetailsModal}')\"><span class=\"fas fa-external-link-alt fa-lg\"></span></a>";
            }

            //In 'with error' list, show the error reason
            $withError = '';

            if ($listView == 'card')
            {
                //Start row at every first card
                if ($cardsInRow == 0)
                {
                    $output .= "
        <div class=\"card-deck\">";
                }
                $output .= "
            <div class=\"card m-1 shadow bg-white rounded-lg col-md-{$cardWidthInGrid}\">
                <div class=\"card-body\">
                    <div class=\"card-title\">
                        <span class=\"fas fa-bookmark fa-lg\">&emsp;</span><b>{$sTitle}</b>{$withError}<span class=\"float-right text-nowrap\">{$sButtons}</span>
                    </div>
                    <div class=\"card-text\">
                        <div class=\"row my-2\"><div class=\"col-3 small-heading\">Status</div><div class=\"col-9\" style=\"white-space: pre-wrap\">{$edStatus}</div></div>
                        <div class=\"row my-2\"><div class=\"col-3 small-heading\">Type</div><div class=\"col-9\" style=\"white-space: pre-wrap\">{$erType}</div></div>
                        <div class=\"row my-2\"><div class=\"col-3 small-heading\">JD</div><div class=\"col-9\" style=\"white-space: pre-wrap\">{$id_jd}</div></div>
                    </div>
                </div>
            </div>";//card
                //Close row at every 3rd card or last card in list
                $cardsInRow++;
                if (($cardsInRow == $maxCardsInRow) || (($i+1) == $arrayObjectsCount))
                {
                    $cardsInRow = 0;
                    $output .= "
        </div>";
                }
            }//End of Card List View

            else if ($listView == 'table') //table view
            {
                $idx = $i+1;
                $output .= "
        <tr><td><span class=\"text-nowrap\">{$sButtons}</span></td><td>{$arrayObjects[$i]->id}- <b>{$sTitle}</b></td><td>{$edStatus}</td><td>{$erType}</td><td>{$id_jd}</td></tr>";
            }//view

            else //list view
            {
                $output .= "
        <div class=\"row border-top small\">
            <div class=\"col-md-2\">{$arrayObjects[$i]->id}- <b>{$sTitle}</b>{$withError}<span class=\"float-right text-nowrap\">{$sButtons}</span></div>
            <div class=\"col-md-2\">{$edStatus}</div>
            <div class=\"col-md-2\">{$erType}</div>
            <div class=\"col-md-2\">{$id_jd}</div>
        </div>";
            }//view
        }//for
        //Close table if table view
        if ($listView == 'table') //table view
        {
            $output .= "
            </tbody>
        </table></div>";
        }
    } else {
        $output .= "
        <div class=\"col-md-12 text-center\">
            <strong>No entries found !</strong>
        </div>";
    }
    $output .= "
    </div>";//Container

    //$mcDatatableOptionsBitmap = DATATABLE_PAGING|DATATABLE_ORDERING|DATATABLE_SEARCHING|DATATABLE_STATESAVE;
    //$output .= createFooter(false, null, false, null, $mcDatatableOptionsBitmap, $sDataTableColumnDefs);
    echo $output;
}


function viewStudentAdd()
{
    //$action = isset($_REQUEST->act) ? escapeHtml($_REQUEST->act) : null;
    //global $sUserType;
    //saveFormOpenTimeInSession();
    $nav = isset($_REQUEST->nav) ? escapeHtml($_REQUEST->nav) : '';
    $sPageTitle = '+Student';
    //$output = createHeader(false, $sPageTitle, $nav);
    //$output = "<div class=\"container border bg-white shadow-lg\">";

    //Check mandatory id_jd values provided in URL otherwise redirect to those pages
    // $temp = isset($_REQUEST->id_jd) ? extractIdFromIdxIdAfterValidate($_REQUEST->id_jd, 'jd') : 0;
    // if ($temp == 0)
    // {
    //     $redirectTo = CONTROL_ROOT.'jd.php?act=list';
    //     header('Location: '.$redirectTo);
    //     exit;
    // }
    // else
    // {
    //     $value = getTableEntryNameById('jd', $temp);
    //     $heading = getNiceColumnName('id_jd');
    //     //$output .= "&nbsp;<b>{$heading}</b>: {$value}&nbsp;";
    //     $output .= "<div class=\"p-2 text-center\">
    //     <b>{$heading}</b>: {$value}</div>";
    // }

    // if(true)
    //     $output = "
    //     <form id=\"formAdd\"  class=\"form-horizontal rowgap\" action=\"\" method=\"post\" enctype=\"multipart/form-data\">
    //     <fieldset>";
    // else
    //     $output = "
    //     <form id=\"formAdd\"  class=\"form-horizontal rowgap\" action=\"\" method=\"post\" enctype=\"multipart/form-data\" onsubmit=\"backgroundPostFormAndAlert(this, '{$GLOBALS->PATH_TO_CONTROL}student.php');\">
    //     <fieldset>
    //         ";
    $output = "
            <input type=\"hidden\" name=\"isAjax\" value=\"1\">
            <!--<input type=\"hidden\" name=\"act\" value=\"add\">-->
            <input type=\"hidden\" name=\"act\" value=\"\">
            <input type=\"hidden\" name=\"filled\" value=\"1\">
";

    $id_jd = isset($_REQUEST->id_jd) ? escapeHtml($_REQUEST->id_jd) : 0;
    if (!empty($id_jd))
    {
        $output .= "
            <input type=\"hidden\" name=\"id_jd\" value=\"{$id_jd}\">";
    }

    $output .= "
            <div class=\"row bg-curve mt-0\">
                <div class=\"col-12 text-center\">
                    <a class=\"btn btn-sm btn-outline-danger float-left\" type=\"reset\" onclick=\"formInputFieldReset(document.getElementById('formAdd'));\">Reset</a>
                    <strong>New Student</strong>
                    <button id=\"myButton\" name=\"myButton\" class=\"btn btn-sm btn-primary float-right\" type=\"submit\">Submit</button>
                </div>
            </div>
";

    $output .= "
        <!-- Nav tabs -->
        <div class=\"divLDEANavTabs\">
          <ul class=\"nav nav-tabs mt-2 text-center\">
            <li class=\"nav-item min80\">
                <a class=\"nav-link active\" data-toggle=\"tab\" href=\"#tabPaneAddInfo1\">General</a>
            </li>
            <li class=\"nav-item min80\">
                <a class=\"nav-link \" data-toggle=\"tab\" href=\"#tabPaneAddInfo2\">Other</a>
            </li>
          </ul>
        </div>
        <!-- Tab-content and tab-panes -->
        <div class=\"tab-content bg-white shadow\" style=\"min-height:10rem\">";
    $output .= "
            <div class=\"tab-pane container-fluid pt-2 active\" id=\"tabPaneAddInfo1\">
                <div class=\"row\">";
    $edStatus = null;
    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"edStatus\">Status</label>
                    <div class=\"\">
";
    //$entries = getEnumValueList('student','edStatus');
    $output .= "
                        <select id=\"edStatus\" name=\"edStatus\" class=\"form-control\" data-live-search=\"true\" data-style=\"dropdownborder\" >
                            <option value=\"\">Select</option>";
    // for ($k=0; $k < count($entries); $k++)
    // {
    //     $selected = '';
    //     if ($edStatus === $k) $selected = 'selected';
    //     $temp = escapeHtml($entries[$k]);
    //     $output .= "
    //                         <option value=\"{$k}\" {$selected}>{$temp}</option>";
    // }
    $output .= "
                        </select>
                    </div>
                </div>";

    $tdLeaveStartDate = null;
    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"tdLeaveStartDate\">Leave Start Date</label>
                    <div class=\"\">
                        <input id=\"tdLeaveStartDate\" name=\"tdLeaveStartDate\" type=\"date\" placeholder=\"\" value=\"{$tdLeaveStartDate}\" class=\"form-control\" >
                    </div>
                </div>";

    $sName = null;
    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"sName\">Name</label>
                    <div class=\"\">
                        <input id=\"sName\" name=\"sName\" type=\"text\" placeholder=\"\" value=\"{$sName}\" class=\"form-control\" maxlength=\"250\" >
                    </div>
                </div>";

    $erType = null;
    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"erType\">Type *</label>
                    <div class=\"\">
                        <div>
                            <input type=\"radio\" id=\"erType0\" name=\"erType\" value=\"0\"/>
                            <label for=\"erType\">Penalty</label>&emsp;
                            <input type=\"radio\" id=\"erType1\" name=\"erType\" value=\"1\"/>
                            <label for=\"erType\">Reward</label>&emsp;
                        </div>
                    </div>
                </div>";

    $iAmount = 0;
    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"iAmount\">Amount</label>
                    <div class=\"\">
                        <input id=\"iAmount\" name=\"iAmount\" type=\"number\" placeholder=\"\" value=\"{$iAmount}\" class=\"form-control\" >
                    </div>
                </div>";

    // Suggestions datalist for sdlZone
    // $sql = "SELECT DISTINCT student.sdlZone FROM student";
    // $entries = querySelect($sql);
    $datalist = "
                        <datalist id=\"datalist_sdlZone\">";
    // for ($k=0; $k < count($entries); $k++)
    // {
    //     $temp = escapeHtml($entries[$k]->sdlZone);
        $datalist .= "
                            <option value=\"\"></option>";
    // }
    $datalist .= "
                        </datalist>";
    $sdlZone = null;
    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"sdlZone\">Zone</label>
                    <div class=\"\">
                    <input list=\"datalist_sdlZone\" id=\"sdlZone\" name=\"sdlZone\" type=\"text\" placeholder=\"\" class=\"form-control\" maxlength=\"64\" >{$datalist}
                    </div>
                </div>";

    $sDescription = null;
    $output .= "
                <div class=\"form-group col-md-6\">
                    <label class=\"control-label\" for=\"sDescription\">Description</label>
                    <div class=\"\">
                        <textarea id=\"sDescription\" name=\"sDescription\" type=\"text\" style=\"display:none\" placeholder=\"Pass in Quill function\" value=\"{$sDescription}\" class=\"form-control\" cols=\"40\" rows=\"5\" maxlength=\"1000\" ></textarea>
                        <div style=\"overflow-y:hidden;\">
                            <div id=\"sDescriptionQuill\" style=\"height:150px;\">{$sDescription}</div>
                        </div>
                    </div>
                </div>";

    $fileAttachment = null;
    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"fileAttachment\">Attachment</label>
                    <div class=\"\">
                        <input id=\"fileAttachment\" name=\"fileAttachment\" type=\"file\" placeholder=\"\" class=\"form-control-file\" >
                    </div>
                </div>";

    $picScreenshot = null;
    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"picScreenshot\">Pic Screenshot</label>
                    <div class=\"\">
                        <input id=\"picScreenshot\" name=\"picScreenshot\" type=\"file\" placeholder=\"\" class=\"form-control-file\" >
                    </div>
                </div>";

    $xdActivityType = null;
    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"xdActivityType\">Activity Type</label>
                    <div class=\"\">
";
    // $entries = getListXmlStringArrayItem('xdActivityType');
    $output .= "
                        <select id=\"xdActivityType\" name=\"xdActivityType\" class=\"form-control\" data-live-search=\"true\" data-style=\"dropdownborder\" >
                            <option value=\"\">Select</option>";
    // for ($k=0; $k < count($entries); $k++)
    // {
    //     $selected = '';
    //     if ($xdActivityType === $k) $selected = 'selected';
    //     $temp = escapeHtml($entries[$k]);
    //     $output .= "
    //                         <option value=\"{$k}\" {$selected}>{$temp}</option>";
    // }
    $output .= "
                        </select>
                    </div>
                </div>";

    $output .= "
              </div> <!-- row -->
            </div> <!-- Tab Pane -->
            <div class=\"tab-pane container-fluid pt-2 \" id=\"tabPaneAddInfo2\">
                <div class=\"row\">";
    $idd_blog = null;
    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"idd_blog\">Blog</label>
                    <div class=\"\">
";
    // $entries = getDropdownRows('blog', null, null, null, null);
    $output .= "
                        <select id=\"idd_blog\" name=\"idd_blog\" class=\"selectpicker form-control\" data-live-search=\"true\" data-style=\"dropdownborder\" >
                            <option value=\"\">Select</option>";
    // $compareUrlId_column = false;
    // //See if any id_* provided for this column in GET params
    // if (isset($_GET->id_blog))
    // {
    //     $id_column = extractIdFromIdxIdAfterValidate(makesafe($_GET->id_blog), 'blog');
    //     if (!empty($id_column)) $compareUrlId_column = true;
    // }
    // for ($k=0; $k < count($entries); $k++)
    // {
    //     $selected = '';
    //     if ($compareUrlId_column && ($id_column==$entries[$k]->id)) $selected = 'selected';
    //     $temp = escapeHtml($entries[$k]->sName);
    //     $output .= "
    //                         <option value=\"{$entries[$k]->sIdx}{$entries[$k]->id}\" {$selected}>{$temp}</option>";
    // }
    $output .= "
                        </select>

                    </div>
                </div>";

    $fJdMatch = null;
    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"fJdMatch\">Jd Match</label>
                    <div class=\"\">
                        <input id=\"fJdMatch\" name=\"fJdMatch\" type=\"number\" placeholder=\"\" value=\"{$fJdMatch}\" step=\"any\" class=\"form-control\" >
                    </div>
                </div>";

    $sLinkIntroVideo = null;
    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"sLinkIntroVideo\">Link Intro Video</label>
                    <div class=\"\">
                        <input id=\"sLinkIntroVideo\" name=\"sLinkIntroVideo\" type=\"text\" placeholder=\"\" value=\"{$sLinkIntroVideo}\" class=\"form-control\" maxlength=\"250\" >
                    </div>
                </div>";

    $tdtIvTime = null;
    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"tdtIvTime\">Iv Time</label>
                    <div class=\"\">
                        <input id=\"tdtIvTime\" name=\"tdtIvTime\" type=\"datetime-local\" placeholder=\"\" value=\"{$tdtIvTime}\" class=\"form-control\" >
                    </div>
                </div>";

    $jsonMobile_poc = null;
    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"jsonMobile_poc\">Mobile poc</label>
                    <div class=\"\">
                        <input id=\"jsonMobile_poc\" name=\"jsonMobile_poc\" type=\"hidden\" value=\"\" class=\"form-control\" maxlength=\"250\" >
                        <input id=\"jsonMobile_poc_display\" type=\"text\" placeholder=\"Click to fill number\" class=\"form-control\" onclick=\"showModalToEnterMobileGroup('jsonMobile_poc')\" readonly >
                    </div>
                </div>";

    $add_faq = null;
    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"add_faq\">Faq</label>
                    <div class=\"\">
";
    // $entries = getDropdownRows('faq', null, null, null, null);
    $output .= "
                        <select id=\"add_faq\" name=\"add_faq[]\" class=\"selectpicker form-control\" data-live-search=\"true\" data-style=\"dropdownborder\" multiple >
                            <option value=\"\">Select</option>";
    // for ($k=0; $k < count($entries); $k++)
    // {
    //     $temp = escapeHtml($entries[$k]->sName);
    //     $output .= "
    //                         <option value=\"{$temp}\">{$temp}</option>";
    // }
    $output .= "
                        </select>

                    </div>
                </div>";

    $xcCalling = 0;
    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"xcCalling\">Calling</label>
                    <div class=\"\">
";
    // $entries = getListXmlStringArrayItem('xcCalling');
    $output .= "
                        <select id=\"xcCalling\" name=\"xcCalling[]\" class=\"form-control selectpicker\" data-live-search=\"true\" data-style=\"dropdownborder\" multiple >
                            <option value=\"\">Select</option>";
    // for ($k=0; $k < count($entries); $k++)
    // {
    //     $selected = (($xcCalling & (1<<$k)) > 0) ? 'selected' : '';
    //     $temp = escapeHtml($entries[$k]);
    //     $output .= "
    //                         <option value=\"{$k}\" {$selected}>{$temp}</option>";
    // }
    $output .= "
                        </select>
                    </div>
                </div>";

    $xrIs_GiveResult = 1;
    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"xrIs_GiveResult\">Is Give Result</label>
                    <div class=\"\">
                        <div>
                            <input type=\"radio\" id=\"xrIs_GiveResult0\" name=\"xrIs_GiveResult\" value=\"0\"/>
                            <label for=\"xrIs_GiveResult\">No</label>&emsp;
                            <input type=\"radio\" id=\"xrIs_GiveResult1\" name=\"xrIs_GiveResult\" value=\"1\"/>
                            <label for=\"xrIs_GiveResult\">Yes</label>&emsp;
                        </div>
                    </div>
                </div>";

    $mdd_subject = null;
    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"mdd_subject\">Quote</label>
                    <div class=\"\">
";
    // $entries = getDropdownRows('quote', null, null, null, null);
    $output .= "
                        <input type=\"hidden\" value=\"null\" name=\"mdd_subject\">
                        <select id=\"mdd_subject\" name=\"mdd_subject[]\" class=\"selectpicker form-control\" data-live-search=\"true\" data-style=\"dropdownborder\" multiple >
                            <option value=\"\">Select</option>";
    // for ($k=0; $k < count($entries); $k++)
    // {
    //     $selected = '';
    //     $temp = escapeHtml($entries[$k]->sName);
    //     $output .= "
    //                         <option value=\"{$entries[$k]->sIdx}{$entries[$k]->id}\">{$temp}</option>";
    // }
    $output .= "
                        </select>

                    </div>
                </div>";

    $mcLevels = 0;
    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"mcLevels\">Levels</label>
                    <div class=\"\">
";
    // $entries = getEnumValueList('student','mcLevels');
    $output .= "
                        <select id=\"mcLevels\" name=\"mcLevels[]\" class=\"form-control selectpicker\" data-live-search=\"true\" data-style=\"dropdownborder\" multiple >
                            <option value=\"\">Select</option>";
    // $mcLevels = 0;//Bitmap
    // for ($k=0; $k < count($entries); $k++)
    // {
    //     $selected = (($mcLevels & (1<<$k)) > 0) ? 'selected' : '';
    //     $temp = escapeHtml($entries[$k]);
    //     $output .= "
    //                         <option value=\"{$k}\" {$selected}>{$temp}</option>";
    // }
    $output .= "
                        </select>
                    </div>
                </div>";

    $xrIvBy = 1;
    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"xrIvBy\">Iv By</label>
                    <div class=\"\">
                        <div>
                            <input type=\"radio\" id=\"xrIvBy0\" name=\"xrIvBy\" value=\"0\"/>
                            <label for=\"xrIvBy\">By Internal</label>&emsp;
                            <input type=\"radio\" id=\"xrIvBy1\" name=\"xrIvBy\" value=\"1\"/>
                            <label for=\"xrIvBy\">By CANGRA</label>&emsp;
                        </div>
                    </div>
                </div>";

    $output .= "
              </div> <!-- row-->
            </div> <!-- tab-pane-->
        </div> <!-- tab-content-->";
    $output .= "
            <div class=\"row\">
                <div class=\"col-md-12 text-center\">
                    <button id=\"myButton1\" name=\"myButton\" class=\"btn btn-md btn-primary\" type=\"submit\">Submit</button>
                </div>
            </div>
        </fieldset>
        </form>
    </div>";
    $sScript = "
<script>
    document.addEventListener( 'DOMContentLoaded', loadAddViewDesign);//If detail view loads as new page
    if (document.readyState == 'complete') loadAddViewDesign();//If detail view loads as modal
    function loadAddViewDesign()
    {

        var sDescriptionPlaceholder = ``;
        quillEditorScript('sDescription', 'sDescriptionQuill', sDescriptionPlaceholder, 1000);
    }
</script>";
    // $output .= createFooter(false, $sScript);
    echo $output;
    // exit;
}

function viewStudentDetails($isEdit, $object)
{
    // echo "<pre>";
    // print_r($object); exit;
    global $sUserType;
    $nav = isset($_REQUEST->nav) ? escapeHtml($_REQUEST->nav) : '';
    //$isAdmin = checkRole(CARE|MANAGEMENT);//isEmployee();
    $sPageTitle = '@ Student';
    $sHeading = 'Student';

    $sTitle = escapeHtml($object->sName);
    // $output = createHeader(false, $sPageTitle, $nav);

    //Display Success box if any Success stored in global Success variable
    // $sSuccess = readAndClearGlobalSuccess(false);
    $sShowSuccess = "";
    // if (!empty($sSuccess))
    //     $sShowSuccess = $sSuccess;

    $output = "
    <div class=\"container-fluid\">
        <div class=\"row justify-content-between\">
            <div class=\"col-auto\">
              <ul class=\"nav nav-tabs big border-0 mx-1\">
                <li class=\"nav-item\">
                    <a class=\"nav-link active border-0 arrow\">{$sHeading}</a>
                </li>
                <li class=\"nav-item\">
                    <a class=\"nav-link border-0 bg-white\"><b>{$sTitle}</b></a>
                </li>
              </ul>
            </div>
            <div class=\"col-auto\">";
    // $output .= getViewEditOptionLogsButtons('student', $object->id, $object->sTokenDetails, $object->sTokenEdit, $object->sIdx, $isEdit, null, true, true);

    $edStatus = $object->edStatus;
    $tdLeaveStartDate = datetimeToNiceShortDate($object->tdLeaveStartDate);
    $sName = escapeHtml($object->sName);
    $erType = $object->erType;
    $iAmount = $object->iAmount;
    $sdlZone = escapeHtml($object->sdlZone);
    $sDescription = escapeHtml($object->sAddress);
    $temp = escapeHtml($object->fileAttachment);
    $fileAttachment = empty($temp) ? '' : "<a href=\"{$temp}\" target=\"_blank\"><span class=\"fas fa-cloud-download-alt fa-lg\"></span> Attachment</a>";
    $temp = escapeHtml($object->picScreenshot);
    $picScreenshot = empty($temp) ? '' : "<a data-fslightbox href=\"{$temp}\"> <!-- <img src=\"{$temp}\" class=\"img-profile rounded-circle\" height=\"80px\" alt=\"Not loaded\"> --> Open</a>";
    $xdActivityType = $object->xdActivityType;
    $url = "jd.php?act=detail";
    $id_jd = ($object->id_jd != null) ? "<a class=\"\" href=\"{$url}\" target=\"_blank\">{$object->sName}</a>" : '';
    $url = "blog.php?act=detail";
    $idd_blog = ($object->idd_blog != null) ? "<a class=\"\" href=\"{$url}\" target=\"_blank\">{$object->idd_blog_sName}</a>" : '';
    $fJdMatch = $object->fJdMatch;
    $temp = null;//escapeHtml($object->sLinkIntroVideo);
    $sLinkIntroVideo = empty($temp) ? '' : "<a class=\"\" href=\"{$temp}\" target=\"_blank\">Link Intro Video</a>";
    $tdtIvTime = datetimeToNiceDatetime($object->tdtIvTime);
    $jsonMobile_poc = getCallingLink($object->jsonMobile_poc);
    $add_faq = escapeHtml($object->add_faq);
    $xcCalling = $object->xcCalling;
    $xrIs_GiveResult = $object->xrIs_GiveResult;
    $mdd_subject = $object->mdd_subject;
    $mcLevels = $object->mcLevels;
    $xrIvBy = $object->xrIvBy;
    $tCreationTime = utcTimestampToDefaultDatetime($object->tCreationTime);
    $tLastUpdate = utcTimestampToDefaultDatetime($object->tLastUpdate);

    $output .= "
            </div><!-- col-auto -->
        </div><!-- row -->
        <div class=\"row\">
         <div class=\"col-md-2 col-0 d-none d-md-block\">";
    $id = $object->id;
    $sIdx = $object->sIdx;
    // $output .= webRelatedListsButtonsSidebar("student", "{$sIdx}{$id}");
    $output .= "
      </div>
      <div class=\"col-md-10 col-12\">";
    $output .= "
        <!--Nav tabs -->
       <div class=\"divLDEANavTabs\">
        <ul class=\"nav nav-tabs mt-2 text-center\">
          <li class=\"nav-item min80\">
            <a class=\"nav-link active\" data-toggle=\"tab\" href=\"#tabPaneDetailInfo1\">General</a>
          </li>
          <li class=\"nav-item min80\">
            <a class=\"nav-link \" data-toggle=\"tab\" href=\"#tabPaneDetailInfo2\">Other</a>
          </li>
          <li class=\"nav-item min80\">
            <a class=\"nav-link\" data-toggle=\"tab\" href=\"#tabPaneDetailAction\" id=\"navItemDetailAction\">Actions</a>
          </li>
        </ul>
        </div>
        <!-- Tab panes -->
        <div class=\"tab-content bg-white shadow\" style=\"min-height:10rem\">
            <div class=\"tab-pane container-fluid pt-2 active\" id=\"tabPaneDetailInfo1\">
                <div class=\"\" id=\"divDetailInfo1\"></div>
            </div>
            <div class=\"tab-pane container-fluid pt-2 \" id=\"tabPaneDetailInfo2\">
                <div class=\"\" id=\"divDetailInfo2\"></div>
            </div>
            <div class=\"tab-pane container-fluid pt-2\" id=\"tabPaneDetailAction\">
              <div class=\"row text-center\"><!-- For action buttons -->";
    // if ($isEdit)
    // {
    //     //Delete or restore and deactivate
    //     $output .= getViewEntryStatusButtons('student', $object->iaEntryStatus, $object->sTokenEdit, $object->id, true);
    // }
    $output .= "
                </div><!-- actions row -->
             </div><!-- Tab Pane -->
        </div><!-- Tab Content -->";
    $output .= "
            </div> <!-- details column -->
        </div> <!-- row  -->
    </div> <!-- \container -->
<script>
    //prepare arrays of objects {column, value, size(optional)} for tabs

    var arrDataDetailInfo1 = [
        {\"column\": \"Status\", \"value\": `{$edStatus}`},
        {\"column\": \"Leave Start Date\", \"value\": `{$tdLeaveStartDate}`},
        {\"column\": \"Name\", \"value\": `{$sName}`},
        {\"column\": \"Type\", \"value\": `{$erType}`},
        {\"column\": \"Amount\", \"value\": `{$iAmount}`},
        {\"column\": \"Zone\", \"value\": `{$sdlZone}`},
        {\"column\": \"Description\", \"value\": `{$sDescription}`, \"size\": `12`},
        {\"column\": \"Attachment\", \"value\": `{$fileAttachment}`},
        {\"column\": \"Pic Screenshot\", \"value\": `{$picScreenshot}`},
        {\"column\": \"Activity Type\", \"value\": `{$xdActivityType}`},
        {\"column\": \"JD\", \"value\": `{$id_jd}`},
    ];
    var arrDataDetailInfo2 = [
        {\"column\": \"Blog\", \"value\": `{$idd_blog}`},
        {\"column\": \"Jd Match\", \"value\": `{$fJdMatch}`},
        {\"column\": \"Link Intro Video\", \"value\": `{$sLinkIntroVideo}`},
        {\"column\": \"Iv Time\", \"value\": `{$tdtIvTime}`},
        {\"column\": \"Mobile poc\", \"value\": `{$jsonMobile_poc}`},
        {\"column\": \"Faq\", \"value\": `{$add_faq}`},
        {\"column\": \"Calling\", \"value\": `{$xcCalling}`},
        {\"column\": \"Is Give Result\", \"value\": `{$xrIs_GiveResult}`},
        {\"column\": \"Quote\", \"value\": `{$mdd_subject}`},
        {\"column\": \"Levels\", \"value\": `{$mcLevels}`},
        {\"column\": \"Iv By\", \"value\": `{$xrIvBy}`},
        {\"column\": \"Created On\", \"value\": `{$tCreationTime}`},
        {\"column\": \"Updated On\", \"value\": `{$tLastUpdate}`},
        {\"column\": \"ID\", \"value\": `{$object->id}`}
    ];

    drawDetailViewFields('divDetailInfo1', arrDataDetailInfo1);
    drawDetailViewFields('divDetailInfo2', arrDataDetailInfo2);
    document.addEventListener( 'DOMContentLoaded', loadDetailViewDesign);//If detail view loads as new page
    if (document.readyState == 'complete') loadDetailViewDesign();//If detail view loads as modal
    function loadDetailViewDesign()
    {
        // showAlert(`{$sShowSuccess}`);
    }

</script>";

    // $output .= createFooter(false, null);
    echo $output;
}

function viewStudentEdit($object)
{
    // echo "<pre>";
    // print_r($object); exit;
    $action = isset($_REQUEST->act) ? escapeHtml($_REQUEST->act) : null;
    global $sUserType;
    saveFormOpenTimeInSession();
    // $nav = isset($_REQUEST->nav) ? escapeHtml($_REQUEST->nav) : '';
    // $modal = isset($_REQUEST->modal) ? intval($_REQUEST->modal) : 0;
    $isAdmin = true; //checkRole(CARE|MANAGEMENT);//isEmployee();
    $sTitle = escapeHtml($object->sName);
    // $output = createHeader(false, '* student', $nav);
    $output = "
    <div class=\"container border bg-white shadow-lg\">";
        // <form id=\"formEdit\" class=\"form-horizontal rowgap\" method=\"post\" enctype=\"multipart/form-data\" ";
    //if Edit form is in a modal/fullpage then resulting detail view will come in modal/fullpage
    // if ($modal == 1)
    //     $output .= " action=\"\" onsubmit=\"backgroundPostFormAndAlert(this, '{$GLOBALS->PATH_TO_CONTROL}student.php');\" >";
    // else
    //$output .= " action=\"student.php\" >";
    // <fieldset>
    $output .= "
            <input type=\"hidden\" name=\"isAjax\" value=\"1\">
            <!--<input type=\"hidden\" name=\"act\" value=\"edit\">-->
            <input type=\"hidden\" name=\"act\" value=\"{$action}\">
            <input type=\"hidden\" name=\"filled\" value=\"1\">
            <input type=\"hidden\" name=\"modal\" value=\"1\">
            <input type=\"text\" name=\"id\" value=\"{$object->sTokenEdit}{$object->id}\" hidden>
            <div class=\"row bg-curve mt-0\">
                <div class=\"col-12 text-center\">
                    <span class=\"float-left btn btn-sm\">Editing student</span>
                    <strong>{$sTitle}</strong>
                    <span class=\"float-right\">
                        <button id=\"myButton\" name=\"myButton\" class=\"btn btn-sm btn-primary\" type=\"submit\">Update</button>
                    </span>
                </div>
            </div>
";

    $output .= "
        <!-- Nav tabs -->
        <div class=\"divLDEANavTabs\">
          <ul class=\"nav nav-tabs mt-2 text-center\">
            <li class=\"nav-item min80\">
                <a class=\"nav-link active\" data-toggle=\"tab\" href=\"#tabPaneEditInfo1\">General</a>
            </li>
            <li class=\"nav-item min80\">
                <a class=\"nav-link \" data-toggle=\"tab\" href=\"#tabPaneEditInfo2\">Other</a>
            </li>
          </ul>
        </div>
        <!-- Tab-content and tab-panes -->
        <div class=\"tab-content bg-white shadow\" style=\"min-height:10rem\">";
    $output .= "
            <div class=\"tab-pane container-fluid pt-2 active\" id=\"tabPaneEditInfo1\">
                <div class=\"row\">";
    $checked = '';
    // $entries = getEnumValueList('student', 'edStatus');
    $input = "
                        <select id=\"edStatus\" name=\"edStatus\" class=\"form-control\" >
                            <option value=\"\">Select</option>";
    // for ($k=0; $k< count($entries); $k++)
    // {
    //     if ($object->edStatus != null)
    //         $checked = ($k == $object->edStatus) ? 'selected' : '';

    //     $input .= "
    //                         <option value=\"{$k}\" {$checked}>{$entries[$k]}</option>";
    // }
    $input .= "
                        </select>";
    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"edStatus\">Status</label>
                    <div class=\"\">{$input}
                    </div>
                </div>";

    $dateTimeBrowser = ($object->tdLeaveStartDate !=null) ? date("Y-m-d", strtotime($object->tdLeaveStartDate)):'';
    $input = "
                        <input id=\"tdLeaveStartDate\" name=\"tdLeaveStartDate\" type=\"date\" placeholder=\"\" class=\"form-control\" value=\"{$dateTimeBrowser}\" >";
    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"tdLeaveStartDate\">Leave Start Date</label>
                    <div class=\"\">{$input}
                    </div>
                </div>";

    $temp = escapeHtml($object->sName);
    $input = "
                    <input id=\"sName\" name=\"sName\" type=\"text\" placeholder=\"\" class=\"form-control\" value=\"{$temp}\" maxlength=\"250\" >";
    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"sName\">Name</label>
                    <div class=\"\">{$input}
                    </div>
                </div>";

    // $entries = getEnumValueList('student', 'erType');
    $input = "
                    <div>";
    // $cnt = count($entries);
    // for ($k=0; $k < $cnt; $k++)
    // {
    //     $checked = '';
    //     if ($object->erType != null)
    //         $checked = ($k == $object->erType) ? 'checked' : '';

    //     $input .= "
    //                     <input type=\"radio\" id=\"erType{$k}\" name=\"erType\" value=\"{$k}\" {$checked}/>
    //                     <label for=\"erType\">{$entries[$k]}</label>";
    //     if ($cnt < 3) $input .= "&emsp;";
    //     else $input .= "<br>";
    // }
    $input .= "
                    </div>";
    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"erType\">Type *</label>
                    <div class=\"\">{$input}
                    </div>
                </div>";

    $input = "
                        <input id=\"iAmount\" name=\"iAmount\" type=\"number\" placeholder=\"\" class=\"form-control\" value=\"{$object->iAmount}\" >";
    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"iAmount\">Amount</label>
                    <div class=\"\">{$input}
                    </div>
                </div>";

    // Suggestions datalist for sdlZone
    // $sql = "SELECT DISTINCT student.sdlZone FROM student";
    // $entries = querySelect($sql);
    $datalist = "
                        <datalist id=\"datalist_sdlZone\">";
    // for ($k=0; $k < count($entries); $k++)
    // {
    //     $temp = escapeHtml($entries[$k]->sdlZone);
    //     $datalist .= "
    //                         <option value=\"{$temp}\"></option>";
    // }
    $datalist .= "
                        </datalist>";
    $temp = escapeHtml($object->sdlZone);
    $input = "
                    <input list=\"datalist_sdlZone\" id=\"sdlZone\" name=\"sdlZone\" type=\"text\" placeholder=\"\" class=\"form-control\" value=\"{$temp}\"maxlength=\"64\" >{$datalist}";
    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"sdlZone\">Zone</label>
                    <div class=\"\">{$input}
                    </div>
                </div>";

    $temp = escapeHtml($object->sAddress);
    $input = "
                        <textarea id=\"sDescription\" name=\"sDescription\" type=\"text\" style=\"display:none\" placeholder=\"Pass in Quill function\" class=\"form-control\" cols=\"40\" rows=\"5\" maxlength=\"1000\" >{$temp}</textarea>
                        <div style=\"overflow-y:hidden;\">
                            <div id=\"sDescriptionQuill\" style=\"height:150px;\">{$temp}</div>
                        </div>";
    $output .= "
                <div class=\"form-group col-md-6\">
                    <label class=\"control-label\" for=\"sDescription\">Description</label>
                    <div class=\"\">{$input}
                    </div>
                </div>";

    $temp = escapeHtml($object->fileAttachment);
    if (empty($object->fileAttachment)) $existingFileNameHint = '';
    else
    {
        $existingFileNameHint = '<br>'.escapeHtml(substr($object->fileAttachment,47,6), '**', substr($object->fileAttachment,-4,4));
    }
    $input = "
                        <input id=\"fileAttachment\" name=\"fileAttachment\" type=\"file\" placeholder=\"\" class=\"form-control-file\" value=\"{$temp}\" >";
    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"fileAttachment\">Attachment<small>{$existingFileNameHint}</small></label>
                    <div class=\"\">{$input}
                    </div>
                </div>";

    $temp = escapeHtml($object->picScreenshot);
    if (empty($object->picScreenshot)) $existingFileNameHint = '';
    else
    {
        $existingFileNameHint = '<br>'.escapeHtml(substr($object->picScreenshot,46,6).'**'.substr($object->picScreenshot,-4,4));
    }
    $input = "
                        <input id=\"picScreenshot\" name=\"picScreenshot\" type=\"file\" placeholder=\"\" class=\"form-control-file\" value=\"{$temp}\" >";
    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"picScreenshot\">Pic Screenshot<small>{$existingFileNameHint}</small></label>
                    <div class=\"\">{$input}
                    </div>
                </div>";

    $checked = '';
    // $entries = getListXmlStringArrayItem('xdActivityType');
    $input = "
                        <select id=\"xdActivityType\" name=\"xdActivityType\" class=\"form-control\" >
                            <option value=\"\">Select</option>";
    // for ($k=0; $k < count($entries); $k++)
    // {
    //     if ($object->xdActivityType != null)
    //         $checked = ($k == $object->xdActivityType) ? 'selected' : '';

    //     $input .= "
    //                         <option value=\"{$k}\" {$checked}>{$entries[$k]}</option>";
    // }
    $input .= "
                        </select>";
    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"xdActivityType\">Activity Type</label>
                    <div class=\"\">{$input}
                    </div>
                </div>";

    $output .= "
              </div> <!-- row -->
            </div> <!-- Tab Pane -->
            <div class=\"tab-pane container-fluid pt-2 \" id=\"tabPaneEditInfo2\">
                <div class=\"row\">";
    // $entries = getDropdownRows('blog', null, null, null, null);
    $input = "
                        <select id=\"idd_blog\" name=\"idd_blog\" class=\"selectpicker form-control\" data-live-search=\"true\" data-style=\"dropdownborder\" >
                            <option value=\"\">Select</option>";
    // for ($k=0; $k < count($entries); $k++)
    // {
    //     $selected = '';
    //     if ($entries[$k]->id == $object->idd_blog) $selected = 'selected';
    //     $temp = escapeHtml($entries[$k]->sName);
    //     $input .= "
    //                         <option value=\"{$entries[$k]->sIdx}{$entries[$k]->id}\" {$selected}>{$temp}</option>";
    // }
    $input .= "
                    </select>";

    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"idd_blog\">Blog</label>
                    <div class=\"\">{$input}
                    </div>
                </div>";

    $input = "
                        <input id=\"fJdMatch\" name=\"fJdMatch\" type=\"number\" placeholder=\"\" step=\"any\" class=\"form-control\" value=\"{$object->fJdMatch}\" >";
    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"fJdMatch\">Jd Match</label>
                    <div class=\"\">{$input}
                    </div>
                </div>";

    $dateTimeBrowser = ($object->tdtIvTime !=null) ? date("Y-m-d\TH:i", strtotime($object->tdtIvTime)):'';
    $input = "
                        <input id=\"tdtIvTime\" name=\"tdtIvTime\" type=\"datetime-local\" placeholder=\"\" class=\"form-control\" value=\"{$dateTimeBrowser}\" >";
    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"tdtIvTime\">Iv Time</label>
                    <div class=\"\">{$input}
                    </div>
                </div>";

    $temp = escapeHtml($object->jsonMobile_poc);
    $temp_display = ''; //escapeHtml(getMobileFromJsonFormat($object->jsonMobile_poc));
    $input = "
                        <input id=\"jsonMobile_poc\" name=\"jsonMobile_poc\" type=\"hidden\" class=\"form-control\" value=\"{$temp}\" maxlength=\"250\">
                        <input id=\"jsonMobile_poc_display\" name=\"jsonMobile_poc_display\" type=\"text\" placeholder=\"Click to fill number\" class=\"form-control\" value=\"{$temp_display}\" onclick=\"showModalToEnterMobileGroup('jsonMobile_poc')\" readonly >";
    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"jsonMobile_poc\">Mobile poc</label>
                    <div class=\"\">{$input}
                    </div>
                </div>";

    // $entries = getDropdownRows('faq', null, null, null, null);
    $temp1 = escapeHtml(trim($object->add_faq));
    //If any old data which does not follow A,B, format then show that as first option
    $input = "
                        <input type=\"hidden\" value=\"null\" name=\"add_faq\">
                        <select id=\"add_faq\" name=\"add_faq[]\" class=\"selectpicker form-control\" data-live-search=\"true\" data-style=\"dropdownborder\" multiple >
                            <option value=\"\">Select</option>";
    // for ($k=0; $k < count($entries); $k++)
    // {
    //     $selected = '';
    //     if (strstr($object->add_faq, $entries[$k]->sName) !== false) $selected = 'selected';
    //     $temp = escapeHtml($entries[$k]->sName);
    //     $input .= "
    //                         <option value=\"{$temp}\" {$selected}>{$temp}</option>";
    // }
    $input .= "
                        </select>";
    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"add_faq\">Faq</label>
                    <div class=\"\">{$input}
                    </div>
                </div>";

    // $entries = getListXmlStringArrayItem('xcCalling');
    $firstOption = '';
    $input = "
                        <input type=\"hidden\" value=\"0\" name=\"xcCalling\">
                        <select id=\"xcCalling\" name=\"xcCalling[]\" class=\"selectpicker form-control\" data-live-search=\"true\" data-style=\"dropdownborder\" multiple >
                    {$firstOption}";
    // for ($k=0; $k < count($entries); $k++)
    // {
    //     $selected = (($object->xcCalling & (1<<$k)) > 0) ? 'selected' : '';
    //     $temp = escapeHtml($entries[$k]);
    //     $input .= "
    //                         <option value=\"{$k}\" {$selected}>{$temp}</option>";
    // }
    $input .= "
                        </select>";
    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"xcCalling\">Calling</label>
                    <div class=\"\">{$input}
                    </div>
                </div>";

    $checked = '';
    // $entries = getListXmlStringArrayItem('xrIs_GiveResult');
    $input = "
                    <div>";
    // $cnt = count($entries);
    // for ($k=0; $k < $cnt; $k++)
    // {
    //     if ($object->xrIs_GiveResult != null)
    //         $checked = ($k == $object->xrIs_GiveResult) ? 'checked' : '';

    //     $input .= "
    //                         <input type=\"radio\" id=\"xrIs_GiveResult{$k}\" name=\"xrIs_GiveResult\" value=\"{$k}\" {$checked}/>
    //                         <label for=\"xrIs_GiveResult\">{$entries[$k]}</label>";
    //     if ($cnt < 3) $input .= "&emsp;";
    //     else $input .= "<br>";
    // }
    $input .= "
                    </div>";
    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"xrIs_GiveResult\">Is Give Result</label>
                    <div class=\"\">{$input}
                    </div>
                </div>";

    // $entries = getDropdownRows('quote', null, null, null, null);
    $input = "
                        <input type=\"hidden\" value=\"null\" name=\"mdd_subject\">
                        <select id=\"mdd_subject\" name=\"mdd_subject[]\" class=\"selectpicker form-control\" data-live-search=\"true\" data-style=\"dropdownborder\" multiple >
                            <option value=\"\">Select</option>";
    $mddExploded = explode(',', $object->mdd_subject);
    $cntMddExploded = count($mddExploded);
    // $cntEntries = count($entries);
    // for ($k=0; $k < $cntEntries; $k++)
    // {
    //     $selected = '';
    //     for ($m=0; $m < $cntMddExploded; $m++)
    //     {
    //         if ($entries[$k]->id == $mddExploded[$m])
    //         {
    //             $selected = 'selected'; break;
    //         }
    //     }
    //     $temp = escapeHtml($entries[$k]->sName);
    //     $input .= "
    //                         <option value=\"{$entries[$k]->sIdx}{$entries[$k]->id}\" {$selected}>{$temp}</option>";
    // }
    $input .= "
                        </select>";
    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"mdd_subject\">Quote</label>
                    <div class=\"\">{$input}
                    </div>
                </div>";

    // $entries = getEnumValueList('student', 'mcLevels');
    $firstOption = '';
    $input = "
                        <input type=\"hidden\" value=\"0\" name=\"mcLevels\">
                        <select id=\"mcLevels\" name=\"mcLevels[]\" class=\"selectpicker form-control\" data-live-search=\"true\" data-style=\"dropdownborder\" multiple >
                        {$firstOption}";
    // for ($k=0; $k < count($entries); $k++)
    // {
    //     $selected = (($object->mcLevels & (1<<$k)) > 0) ? 'selected' : '';
    //     $temp = escapeHtml($entries[$k]);
    //     $input .= "
    //                         <option value=\"{$k}\" {$selected}>{$temp}</option>";
    // }
    $input .= "
                        </select>";
    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"mcLevels\">Levels</label>
                    <div class=\"\">{$input}
                    </div>
                </div>";

    $checked = '';
    // $entries = getListXmlStringArrayItem('xrIvBy');
    $input = "
                    <div>";
    // $cnt = count($entries);
    // for ($k=0; $k < $cnt; $k++)
    // {
    //     if ($object->xrIvBy != null)
    //         $checked = ($k == $object->xrIvBy) ? 'checked' : '';

    //     $input .= "
    //                         <input type=\"radio\" id=\"xrIvBy{$k}\" name=\"xrIvBy\" value=\"{$k}\" {$checked}/>
    //                         <label for=\"xrIvBy\">{$entries[$k]}</label>";
    //     if ($cnt < 3) $input .= "&emsp;";
    //     else $input .= "<br>";
    // }
    $input .= "
                    </div>";
    $output .= "
                <div class=\"form-group col-md-3\">
                    <label class=\"control-label\" for=\"xrIvBy\">Iv By</label>
                    <div class=\"\">{$input}
                    </div>
                </div>";

    $output .= "
              </div> <!-- row-->
            </div> <!-- tab-pane-->
        </div> <!-- tab-content-->";
    $output .= "
            <div class=\"row\">
                <div class=\"col-md-12 text-center\">
                    <button id=\"myButton1\" name=\"myButton\" class=\"btn btn-md btn-primary\" type=\"submit\">Update</button>
                    &emsp;<input type=\"checkbox\" id=\"Refresh_student\" name=\"isShowDetailOnSubmit\" value=\"1\" checked/> <label id=\"saveStatusLabel\" for=\"Refresh_student\" class=\"tiny\">Refresh detail view</label>
                </div>
            </div>
        </fieldset>
        </form>
    </div> <!-- container -->
";
    $sScript = "
<script>
    document.addEventListener( 'DOMContentLoaded', loadEditViewDesign);//If detail view loads as new page
    if (document.readyState == 'complete') loadEditViewDesign();//If detail view loads as modal
    function loadEditViewDesign()
    {
        var sDescriptionPlaceholder = ``;
        quillEditorScript('sDescription', 'sDescriptionQuill', sDescriptionPlaceholder, 1000);
    }
</script>
";
    // $output .= createFooter(false, $sScript);

    echo $output;
    //exit;
}


?>