<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\Functions;
use App\Http\Traits\dbBasic;

class StudentController extends Controller
{
    use Functions;
    use dbBasic;
    /*
    ok
    Display a listing of the resource.
    */
    // public function List(Request $request, $range, $view)
    public function List(Request $request)
    {
        //$students = Student :: all();
        $sql = "SELECT * FROM student";
        $arrayObjects = DB:: select($sql); //array();
        $sUserType = 'employee'; // employee, guest
        $listView = isset($request->listView) ? $request->listView : 'table';
        $range = isset($request->range) ? $request->range : 'r1';
        $isDetail = true; 
        $isAdd = true; 
        //$isList = true; 
        $isEdit = true;
        $arguments = array (
            'action' => 'list',
            'sUserType' => $sUserType,
            'listView' => $listView, 
            'range' => $range, 
            'isDetail' => $isDetail, 
            'isAdd' => $isAdd, 
            'isEdit' => $isEdit, 
            'arrayObjects' => $arrayObjects
        );
        //$isDetail = true;
        // echo "<pre>";
        // print_r($arguments); exit;
        // echo "ok"; 
        //exit;
        return view('student.viewStudentList', $arguments);
    }
    /*
    Show the form for creating a new resource.
    */
    public function Add(Request $request)
    //public function Add()
    {
        $filled = isset($_REQUEST['filled']) ? $_REQUEST['filled'] : 0;
        if ($filled == 1)
        {
            // echo "<pre>"; 
            // print_r($request); exit;

            $arrRecentInsertedColumns = $this->addData($request);
            // echo "<pre>"; 
            // print_r($arrRecentInsertedColumns); exit;

            // if ($arrRecentInsertedColumns == null) $this->webErrorMessage("student Add", "ERROR: Add failed");
            // $recentInsertedId = $arrRecentInsertedColumns['id;
            $sLinkDetails = "";
            $isAjax = isset($_REQUEST['isAjax']) ? $this->makeSafe($_REQUEST['isAjax']) : 0;
            if ($isAjax == 0)
            {
                //go to detail view of recently created entry

                //It is not background AJAX action so redirect to other page
                // header('Location: '.$sLinkDetails);
                // exit;
            }
            else //Ajax based success messages
            {
                // echo "SUCCESS: <div class=\"text-center\">Student has been added.<br><a class=\"btn btn-sm btn-secondary mt-2\" href=\"{$sLinkDetails}\" target=\"_blank\">See Created Entry</a></div>";
                echo "SUCCESS: Student has been added."; //exit;
            }
        }
        else
        {
            // return view('student', ['action' => 'add']);
            //echo "ok"; exit;
            return view('student.viewStudentAdd', ['action' => 'add']);
            //viewstudentAdd();
        }
    }
    public function Detail(Request $request, $id)
    {
        //$tableName_DEALNF = getTableNameForRole_DEALNF('student', $sUserType);
        //$isDetail = isDetail($tableName_DEALNF);
        //Validate login status except when guest with detail permission
        //if (!(($sUserType == 'guest') && $isDetail)) validateSessionParams();
        if (!isset($_REQUEST['id']))
        {
            $sError = "ERROR: Select a valid entry";
            //break;
        }
        // $id = $this->extractId($_REQUEST['id);
        // $id = $_REQUEST['id;
        // $sTokenDetails = $this->extractToken($_REQUEST['id);
        //$tableName_DEALNF = getTableNameForRole_DEALNF('student', $sUserType);
        $isEdit = false; //isEdit($tableName_DEALNF);
        /*
        if (!$isDetail)
        {
            $sError = "ERROR: Detail view is not allowed for ".$sUserType;
            break;
        }
        */
        // $object = getDetailViewRowWithForeignData('student', "student.id={$id}");
        // if(($object == null) || ($object['sTokenDetails != $sTokenDetails))
            // hackingAttempt();

        // createActiveLog(TABLE_student, $id, ACTIVITY_DETAIL);
        $sql = "SELECT * FROM student WHERE student.id={$id}";
        $object = DB:: select($sql); //array();
        $isEdit = true;
        $arguments = array (
            'action' => 'detail',
            'isEdit' => $isEdit, 
            'object' => $object[0]
        );
        //$isDetail = true;
        //echo "<pre>"; 
        //print_r($arrayObjects); exit;
        return view('student.viewStudentDetail', $arguments);
        // viewstudentDetails($isEdit, $object);        
    }
    public function Edit(Request $request, $id)
    {
        // dd("ok");
        //validateSessionParams();
        // if (!isset($_REQUEST['id))
        // {
        //     $sError = "ERROR: Select a valid entry";
        //     break;
        // }
        // $id = $this->extractId($request->id);
        // $sTokenEdit = $this->extractToken($request->id);
        $id = $this->extractId($id);
        $sTokenEdit = $this->extractToken($id);

        $sUserType = 'guest';
        // $tableName_DEALNF = getTableNameForRole_DEALNF('student', $sUserType);
        //Edit allowed if permission or self profile
        $isEdit = true; //isEdit($tableName_DEALNF);
        // if (!$isEdit)
        // {
        //     $sError = "ERROR: Edit is not allowed for ".$sUserType;
        //     break;
        // }
        // $object = getTableRowById('student', $id);
        // if(($object == null) || ($object['sTokenEdit != $sTokenEdit))
        //     hackingAttempt();
        $sql = "SELECT * FROM student WHERE student.id={$id}";
        $object = DB:: select($sql); //array();
        $filled = isset($_REQUEST['filled']) ? $this->makeSafe($_REQUEST['filled']) : 0;
        $arguments = array (
            'action' => 'edit',
            'isEdit' => $isEdit, 
            'object' => $object[0]
        );
        //$isDetail = true;
        // echo $sql;
        // echo "<pre>"; 
        // print_r($object); exit;
        if ($filled == 1)
        {
            // dd("filled=1");
            $this->editData($request, $object[0]);
        }
        else
        {
            // dd("filled=0");
            return view('student', $arguments); 
            // viewstudentEdit($object);
        }
    }


    public function addData($request)
    {
        // echo "<pre>"; 
        // print_r($request); exit;

        global $sError;
        $sError = '';
        $sRcvdColumns = '';
        $sRcvdValues = '';

        $sTokenEdit = $this->getRandomString(16);
        $sTokenDetails = $this->getRandomString(16);
        $sIdx = $this->getRandomString(16);

        //preparing array result to return to calling function
        $arrRecentInsertedColumns = array();
        $arrRecentInsertedColumns['sTokenDetails'] = $sTokenDetails;
        $arrRecentInsertedColumns['sTokenEdit'] =  $sTokenEdit;

        //Create SQL received columns string and VALUES string
        $sRcvdColumns = 'sTokenEdit, sTokenDetails, sIdx';
        $sRcvdValues = "'{$sTokenEdit}', '{$sTokenDetails}', '{$sIdx}'";

        $edStatus = isset($request->edStatus) ? trim($request->edStatus) : null;
        if ($this->isWebInputNull($edStatus)) { $edStatus = null; $edStatus_SQL = 'NULL'; }
        else
        {
            $edStatus = intval($edStatus);
            $edStatus_SQL = $edStatus;
        }
        if (isset($request->edStatus))
        {
            $sRcvdColumns .= ", edStatus";
            $sRcvdValues .= ", {$edStatus_SQL}";
        }

        $tdLeaveStartDate = isset($request->tdLeaveStartDate) ? trim($request->tdLeaveStartDate) : null;
        if ($this->isWebInputNull($tdLeaveStartDate)) { $tdLeaveStartDate = null; $tdLeaveStartDate_SQL = 'NULL'; }
        else $tdLeaveStartDate_SQL = $this->makeSafeAndSingleQuote($tdLeaveStartDate);
        if (isset($request->tdLeaveStartDate))
        {
            $sRcvdColumns .= ", tdLeaveStartDate";
            $sRcvdValues .= ", {$tdLeaveStartDate_SQL}";
        }

        $sName = isset($request->sName) ? trim($request->sName) : null;
        if ($this->isWebInputNull($sName)) { $sName = null; $sName_SQL = 'NULL'; }
        else $sName_SQL = $this->makeSafeAndSingleQuote($sName);
        if (isset($request->sName))
        {
            $sRcvdColumns .= ", sName";
            $sRcvdValues .= ", {$sName_SQL}";
        }

        $erType = isset($request->erType) ? trim($request->erType) : null;
        if (isset($request->erType) && $this->isWebInputNull($erType))
        {
            $erType = null; $erType_SQL = 'NULL';
            $this->webErrorMessage("Student Add", "ERROR: Type is mandatory");
        }
        else
        {
            $erType = intval($erType);
            $erType_SQL = $erType;
        }
        if (isset($request->erType))
        {
            $sRcvdColumns .= ", erType";
            $sRcvdValues .= ", {$erType_SQL}";
        }

        $iAmount = isset($request->iAmount) ? trim($request->iAmount) : null;
        if ($this->isWebInputNull($iAmount)) { $iAmount = null; $iAmount_SQL = 'NULL'; }
        else
        {
            $iAmount = intval($iAmount);
            $iAmount_SQL = $iAmount;
        }
        if (isset($request->iAmount))
        {
            $sRcvdColumns .= ", iAmount";
            $sRcvdValues .= ", {$iAmount_SQL}";
        }

        $sdlZone = isset($request->sdlZone) ? trim($request->sdlZone) : null;
        if ($this->isWebInputNull($sdlZone)) { $sdlZone = null; $sdlZone_SQL = 'NULL'; }
        else $sdlZone_SQL = $this->makeSafeAndSingleQuote($sdlZone);
        if (isset($request->sdlZone))
        {
            $sRcvdColumns .= ", sdlZone";
            $sRcvdValues .= ", {$sdlZone_SQL}";
        }

        $sDescription = isset($request->sDescription) ? $request->sDescription : null;
        if ($this->isWebInputNull($sDescription)) { $sDescription = null; $sDescription_SQL = 'NULL'; }
        else $sDescription_SQL = $this->makeSafeAndSingleQuote($sDescription);
        if (isset($request->sDescription))
        {
            $sRcvdColumns .= ", sDescription";
            $sRcvdValues .= ", {$sDescription_SQL}";
        }

        if (isset($_FILES['fileAttachment']))
        {
            $iFileSize = $_FILES['fileAttachment']['size'];
            if ($iFileSize != 0)
            {
                $sFileName = $_FILES['fileAttachment']['name'];
                $sFileTmpName = $_FILES['fileAttachment']['tmp_name'];
                $sFileType = $_FILES['fileAttachment']['type'];
                $arrFileNameExploded = explode('.', $_FILES['fileAttachment']['name']);
                $sFileExt = strtolower(end($arrFileNameExploded));
                $arrExtAllowed = array("docx", "doc", "xlsx", "xls", "pdf", "rtf", "txt", "jpg", "jpeg", "bmp", "png", "gif", "tiff", "tif");
                if (in_array($sFileExt, $arrExtAllowed) === false)
                    $this->webErrorMessage("Student Test Add", "ERROR: Attachment .{$sFileExt} file extension is not allowed.");
                if ($iFileSize > 2097152)
                    $this->webErrorMessage("Student Add", "ERROR: Attachment file size must be less than 2 MB.");
                if (!isset($sName)) $sName = '';
                // $serverFileName = 'student_fileAttachment_'.preg_replace('/[^a-z0-9_]/i', '', $sName).'_'.getRandomString(16).'.'.$sFileExt;
                $serverFileName = 'student_fileAttachment_'.preg_replace('/[^a-z0-9_]/i', '', $sName).$sFileExt;
                //$serverFilePath = $GLOBALS['PATH_TO_UPLOAD_FILES'].$serverFileName;
                $serverFilePath = $serverFileName;
                $fileAttachment_SQL = $this->makeSafeAndSingleQuote($serverFilePath);
                //Save file in server
                $isSaved = move_uploaded_file($sFileTmpName, $serverFilePath);
                if ($isSaved != false)
                {
                    $sRcvdColumns .= ", fileAttachment";
                    $sRcvdValues .= ", {$fileAttachment_SQL}";
                } else $this->webErrorMessage("Student Add", "ERROR: Attachment file upload failed");
            }
        }
        else if(isset($request->fileAttachment))
        {
            $fileAttachment = isset($request->fileAttachment) ? trim($request->fileAttachment) : null;
            if ($this->isWebInputNull($fileAttachment)) { $fileAttachment = null; $fileAttachment_SQL = 'NULL'; }
            else $fileAttachment_SQL = $this->makeSafeAndSingleQuote($fileAttachment);
            $sRcvdColumns .= ", fileAttachment";
            $sRcvdValues .= ", {$fileAttachment_SQL}";
        }

        if (isset($_FILES['picScreenshot']))
        {
            $iFileSize = $_FILES['picScreenshot']['size'];
            if ($iFileSize != 0)
            {
                $sFileName = $_FILES['picScreenshot']['name'];
                $sFileTmpName = $_FILES['picScreenshot']['tmp_name'];
                $sFileType = $_FILES['picScreenshot']['type'];
                $arrFileNameExploded = explode('.', $_FILES['picScreenshot']['name']);
                $sFileExt = strtolower(end($arrFileNameExploded));
                $arrExtAllowed = array("docx", "doc", "xlsx", "xls", "pdf", "rtf", "txt", "jpg", "jpeg", "bmp", "png", "gif", "tiff", "tif");
                if (in_array($sFileExt, $arrExtAllowed) === false)
                    $this->webErrorMessage("Student Add", "ERROR: Pic Screenshot .{$sFileExt} file extension is not allowed.");
                if ($iFileSize > 2097152)
                    $this->webErrorMessage("Student Add", "ERROR: Pic Screenshot file size must be less than 2 MB.");
                if (!isset($sName)) $sName = '';
                // $serverFileName = 'student_picScreenshot_'.preg_replace('/[^a-z0-9_]/i', '', $sName).'_'.getRandomString(16).'.'.$sFileExt;
                $serverFileName = 'student_picScreenshot_'.preg_replace('/[^a-z0-9_]/i', '', $sName).$sFileExt;
                // $serverFilePath = $GLOBALS['PATH_TO_UPLOAD_FILES'].$serverFileName;
                $serverFilePath = $serverFileName;
                $picScreenshot_SQL = $this->makeSafeAndSingleQuote($serverFilePath);
                //Save file in server
                $isSaved = move_uploaded_file($sFileTmpName, $serverFilePath);
                if ($isSaved != false)
                {
                    $sRcvdColumns .= ", picScreenshot";
                    $sRcvdValues .= ", {$picScreenshot_SQL}";
                } else $this->webErrorMessage("Student Add", "ERROR: Pic Screenshot file upload failed");
            }
        }

        $xdActivityType = isset($request->xdActivityType) ? trim($request->xdActivityType) : null;
        if ($this->isWebInputNull($xdActivityType)) { $xdActivityType = null; $xdActivityType_SQL = 'NULL'; }
        else
        {
            $xdActivityType = intval($xdActivityType);
            $xdActivityType_SQL = $xdActivityType;
        }
        if (isset($request->xdActivityType))
        {
            $sRcvdColumns .= ", xdActivityType";
            $sRcvdValues .= ", {$xdActivityType_SQL}";
        }

        $id_jd = isset($request->id_jd) ? trim($request->id_jd) : null;
        if (isset($request->id_jd) && $this->isWebInputNull($id_jd))
        {
            $id_jd = null; $id_jd_SQL = 'NULL';
            $this->webErrorMessage("Student Add", "ERROR: JD is mandatory");
        }
        // else $id_jd = extractIdFromIdxIdAfterValidate($id_jd, 'jd');
        if (empty($id_jd)) { $id_jd = null; $id_jd_SQL = 'NULL'; }
        else $id_jd_SQL = $id_jd;
        if (isset($request->id_jd))
        {
            $sRcvdColumns .= ", id_jd";
            $sRcvdValues .= ", {$id_jd_SQL}";
        }

        $idd_blog = isset($request->idd_blog) ? trim($request->idd_blog) : null;
        if ($this->isWebInputNull($idd_blog)) { $idd_blog = null; $idd_blog_SQL = 'NULL'; }
        else $idd_blog = trim($request->idd_blog); //extractIdFromIdxIdAfterValidate($idd_blog, 'blog');
        if (empty($idd_blog)) { $idd_blog = null; $idd_blog_SQL = 'NULL'; }
        else $idd_blog_SQL = $idd_blog;
        if (isset($request->idd_blog))
        {
            $sRcvdColumns .= ", idd_blog";
            $sRcvdValues .= ", {$idd_blog_SQL}";
        }

        $fJdMatch = isset($request->fJdMatch) ? trim($request->fJdMatch) : null;
        if ($this->isWebInputNull($fJdMatch)) { $fJdMatch = null; $fJdMatch_SQL = 'NULL'; }
        else
        {
            $fJdMatch = floatval($fJdMatch);
            $fJdMatch_SQL = $fJdMatch;
        }
        if (isset($request->fJdMatch))
        {
            $sRcvdColumns .= ", fJdMatch";
            $sRcvdValues .= ", {$fJdMatch_SQL}";
        }

        $tdtIvTime = isset($request->tdtIvTime) ? trim($request->tdtIvTime) : null;
        if ($this->isWebInputNull($tdtIvTime)) { $tdtIvTime = null; $tdtIvTime_SQL = 'NULL'; }
        else
        {
            $tdtIvTime = $this->datetimeFromBrowserDatetime($tdtIvTime);
            $tdtIvTime_SQL = $this->makeSafeAndSingleQuote($tdtIvTime);
        }
        if (isset($request->tdtIvTime))
        {
            $sRcvdColumns .= ", tdtIvTime";
            $sRcvdValues .= ", {$tdtIvTime_SQL}";
        }

        $jsonMobile_poc = isset($request->jsonMobile_poc) ? trim($request->jsonMobile_poc) : null;
        if ($this->isWebInputNull($jsonMobile_poc)) { $jsonMobile_poc = null; $jsonMobile_poc_SQL = 'NULL'; }
        else $jsonMobile_poc_SQL = $this->makeSafeAndSingleQuote($jsonMobile_poc);
        if (isset($request->jsonMobile_poc))
        {
            $sRcvdColumns .= ", jsonMobile_poc";
            $sRcvdValues .= ", {$jsonMobile_poc_SQL}";
        }

        $add_faq = isset($request->add_faq) ? $request->add_faq : null;
        if ($this->isWebInputNull($add_faq)) { $add_faq = null; $add_faq_SQL = 'NULL'; }
        else
        {
            $add_faq = multiSelectToString($add_faq);
            $add_faq_SQL = $this->makeSafeAndSingleQuote($add_faq);
        }
        if (isset($request->add_faq))
        {
            $sRcvdColumns .= ", add_faq";
            $sRcvdValues .= ", {$add_faq_SQL}";
        }

        $xcCalling = isset($request->xcCalling) ? $request->xcCalling : null;
        if ($this->isWebInputNull($xcCalling)) { $xcCalling = null; $xcCalling_SQL = 'NULL'; }
        else
        {
            $xcCalling = bitsToBitmap($xcCalling);
            $xcCalling_SQL = $xcCalling;
        }
        if (isset($request->xcCalling))
        {
            $sRcvdColumns .= ", xcCalling";
            $sRcvdValues .= ", {$xcCalling_SQL}";
        }

        $xrIs_GiveResult = isset($request->xrIs_GiveResult) ? trim($request->xrIs_GiveResult) : null;
        if ($this->isWebInputNull($xrIs_GiveResult)) { $xrIs_GiveResult = null; $xrIs_GiveResult_SQL = 'NULL'; }
        else
        {
            $xrIs_GiveResult = intval($xrIs_GiveResult);
            $xrIs_GiveResult_SQL = $xrIs_GiveResult;
        }
        if (isset($request->xrIs_GiveResult))
        {
            $sRcvdColumns .= ", xrIs_GiveResult";
            $sRcvdValues .= ", {$xrIs_GiveResult_SQL}";
        }

        $mdd_quote = isset($request->mdd_quote) ? $request->mdd_quote : null;
        if ($this->isWebInputNull($mdd_quote)) { $mdd_quote = null; $mdd_quote_SQL = 'NULL'; }
        else
        {
            $mdd_quote = multiSelectToMdd('quote', null, $mdd_quote);
            $mdd_quote_SQL = $this->makeSafeAndSingleQuote($mdd_quote);
        }
        if (isset($request->mdd_quote))
        {
            $sRcvdColumns .= ", mdd_quote";
            $sRcvdValues .= ", {$mdd_quote_SQL}";
        }

        $mcLevels = isset($request->mcLevels) ? $request->mcLevels : null;
        if ($this->isWebInputNull($mcLevels)) { $mcLevels = null; $mcLevels_SQL = 'NULL'; }
        else
        {
            $mcLevels = bitsToBitmap($mcLevels);
            $mcLevels_SQL = $mcLevels;
        }
        if (isset($request->mcLevels))
        {
            $sRcvdColumns .= ", mcLevels";
            $sRcvdValues .= ", {$mcLevels_SQL}";
        }

        $xrIvBy = isset($request->xrIvBy) ? trim($request->xrIvBy) : null;
        if ($this->isWebInputNull($xrIvBy)) { $xrIvBy = null; $xrIvBy_SQL = 'NULL'; }
        else
        {
            $xrIvBy = intval($xrIvBy);
            $xrIvBy_SQL = $xrIvBy;
        }
        if (isset($request->xrIvBy))
        {
            $sRcvdColumns .= ", xrIvBy";
            $sRcvdValues .= ", {$xrIvBy_SQL}";
        }

        if ($sError != '') $this->webErrorMessage("Student add", $sError);
        $sql = "INSERT INTO student ({$sRcvdColumns}) VALUES ({$sRcvdValues})";
        // DB::insert('insert into users (id, name) values (?, ?)', [1, 'Dayle']);
        $arrayObjects = DB:: insert($sql); //array();
        $recentInsertedId = DB::getPdo()->lastInsertId();
        // echo $lastInsertedId; exit;
        // echo "<pre>";
        // print_r($arrayObjects); exit;
        // $recentInsertedId = queryInsert($sql);
        if ($recentInsertedId == 0) $this->webErrorMessage("Student Add", "ERROR: Invalid Data. Contact Tech Support.");
        else
        {
            //preparing array result to return to calling function
            //$object = getTableRowById('student', $recentInsertedId);
            $arrRecentInsertedColumns['id'] = $recentInsertedId;
            $iTimeTakenInFillingForm = $this->getDurationFormOpenTimeInSession();
            // $this->createActiveLog(TABLE_student, $recentInsertedId, ACTIVITY_ADD, null, $iTimeTakenInFillingForm);
            //Create Event for entry add
            // $this->createEvent('student', $recentInsertedId, 3, null, null, null, null, null, null);
            return $arrRecentInsertedColumns;
        }
        return null;
    }

    function editData($request, $objectExisting)
    {
        // echo "<pre>"; 
        // print_r($_REQUEST); exit;

        global $sError, $sUserType;
        $sError = '';
        $sChangedColumns = '';
        $sRcvdSetColumns ='id=id';// So that all later concatenation have comma in start
        $edStatus = isset($_REQUEST->edStatus) ? $_REQUEST->edStatus : 99;
        if ($objectExisting == null) $this->webErrorMessage("student Update", "ERROR: Invalid existing entry.");
    
        //Create SQL received columns string and VALUES string
    
        if (isset($_REQUEST['edStatus']))
        {
            if ($this->isWebInputNull($_REQUEST['edStatus'])) { $edStatus = null; $temp = 'NULL'; }
            else
            {
                $edStatus = intval($_REQUEST['edStatus']);
                $temp = $edStatus;
            }
            $sRcvdSetColumns .= ", edStatus={$temp}";
            if ($edStatus != $objectExisting->edStatus) $sChangedColumns .= "Status ({$objectExisting->edStatus} -> {$edStatus}), ";
        } //else $edStatus = $objectExisting['edStatus'];
    
        if (isset($_POST->tdLeaveStartDate))
        {
            if ($this->isWebInputNull($_POST->tdLeaveStartDate)) { $tdLeaveStartDate = null; $temp = 'NULL'; }
            else
            {
                $tdLeaveStartDate = trim($_POST->tdLeaveStartDate);
                $temp = $this->makeSafeAndSingleQuote($tdLeaveStartDate);
            }
            $sRcvdSetColumns .= ", tdLeaveStartDate={$temp}";
            if ($tdLeaveStartDate != $objectExisting->tdLeaveStartDate) $sChangedColumns .= "Leave Start Date ({$objectExisting->tdLeaveStartDate} -> {$tdLeaveStartDate}), ";
        } //else $tdLeaveStartDate = $objectExisting->tdLeaveStartDate;
    
        if (isset($_REQUEST['sName']))
        {
            if ($this->isWebInputNull($_REQUEST['sName'])) { $sName = null; $temp = 'NULL'; }
            else
            {
                $sName = trim($_REQUEST['sName']);
                $temp = $this->makeSafeAndSingleQuote($sName);
            }
            $sRcvdSetColumns .= ", sName={$temp}";
            if ($sName != $objectExisting->sName) $sChangedColumns .= "Name, ";
        } //else $sName = $objectExisting->sName;
    
        if (isset($_POST->erType))
        {
            if ($this->isWebInputNull($_POST->erType))
                $this->webErrorMessage("student Edit", "ERROR: Type is mandatory");
            else
            {
                $erType = intval($_POST->erType);
                $temp = $erType;
            }
            $sRcvdSetColumns .= ", erType={$temp}";
            if ($erType != $objectExisting->erType) $sChangedColumns .= "Type ({$objectExisting->erType} -> {$erType}), ";
        } //else $erType = $objectExisting->erType;
    
        if (isset($_POST->iAmount))
        {
            if ($this->isWebInputNull($_POST->iAmount)) { $iAmount = null; $temp = 'NULL'; }
            else
            {
                $iAmount = intval($_POST->iAmount);
                $temp = $iAmount;
            }
            $sRcvdSetColumns .= ", iAmount={$temp}";
            if ($iAmount != $objectExisting->iAmount) $sChangedColumns .= "Amount ({$objectExisting->iAmount} -> {$iAmount}), ";
        } //else $iAmount = $objectExisting->iAmount;
    
        if (isset($_POST->sdlZone))
        {
            if ($this->isWebInputNull($_POST->sdlZone)) { $sdlZone = null; $temp = 'NULL'; }
            else
            {
                $sdlZone = trim($_POST->sdlZone);
                $temp = $this->makeSafeAndSingleQuote($sdlZone);
            }
            $sRcvdSetColumns .= ", sdlZone={$temp}";
            if ($sdlZone != $objectExisting->sdlZone) $sChangedColumns .= "Zone, ";
        } //else $sdlZone = $objectExisting['sdlZone'];
    
        if (isset($_POST->sDescription))
        {
            if ($this->isWebInputNull($_POST->sDescription)) { $sDescription = null; $temp = 'NULL'; }
            else
            {
                $sDescription = $_POST->sDescription;
                $temp = $this->makeSafeAndSingleQuote($sDescription);
            }
            $sRcvdSetColumns .= ", sDescription={$temp}";
            if ($sDescription != $objectExisting->sDescription) $sChangedColumns .= "Description, ";
        } //else $sDescription = $objectExisting['sDescription'];
    
        if (isset($_FILES['fileAttachment']))
        {
            $iFileSize = $_FILES['fileAttachment']['size'];
            if ($iFileSize != 0)
            {
                $sFileName = $_FILES['fileAttachment']['name'];
                $sFileTmpName = $_FILES['fileAttachment']['tmp_name'];
                $sFileType = $_FILES['fileAttachment']['type'];
                $arrFileNameExploded = explode('.', $_FILES['fileAttachment']['name']);
                $sFileExt = strtolower(end($arrFileNameExploded));
                $arrExtAllowed = array("docx", "doc", "xlsx", "xls", "pdf", "rtf", "txt", "jpg", "jpeg", "bmp", "png", "gif", "tiff", "tif");
                if (in_array($sFileExt, $arrExtAllowed) === false)
                    $this->webErrorMessage("student Edit", "ERROR: Attachment .{$sFileExt} file extension is not allowed.");
                if ($iFileSize > 2097152)
                    $this->webErrorMessage("student Edit", "ERROR: Attachment file size must be less than 2 MB.");
                if (!isset($sName)) $sName = '';
                $serverFileName = 'student_fileAttachment_'.preg_replace('/[^a-z0-9_]/i', '', $sName).'_'.getRandomString(16).'.'.$sFileExt;
                $serverFilePath = $GLOBALS['PATH_TO_UPLOAD_FILES'].$serverFileName;
                $fileAttachment = $this->makeSafeAndSingleQuote($serverFilePath);
                //Save file in server
                $isSaved = move_uploaded_file($sFileTmpName, $serverFilePath);
                if ($isSaved != false)
                    $sRcvdSetColumns .= ", fileAttachment={$fileAttachment}";
                else $this->webErrorMessage("student Edit", "ERROR: Attachment file upload failed");
            }
        }
    
        else if(isset($_POST->fileAttachment))
        {
            $fileAttachment = isset($_POST->fileAttachment) ? trim($_POST->fileAttachment) : null;
            if ($this->isWebInputNull($fileAttachment)) { $fileAttachment = null; $fileAttachment_SQL = 'NULL'; }
            else $fileAttachment_SQL = $this->makeSafeAndSingleQuote($fileAttachment);
            $sRcvdSetColumns .= ", fileAttachment={$fileAttachment_SQL}";
        }
    
        if (isset($_FILES['picScreenshot']))
        {
            $iFileSize = $_FILES['picScreenshot']['size'];
            if ($iFileSize != 0)
            {
                $sFileName = $_FILES['picScreenshot']['name'];
                $sFileTmpName = $_FILES['picScreenshot']['tmp_name'];
                $sFileType = $_FILES['picScreenshot']['type'];
                $arrFileNameExploded = explode('.', $_FILES['picScreenshot']['name']);
                $sFileExt = strtolower(end($arrFileNameExploded));
                $arrExtAllowed = array("docx", "doc", "xlsx", "xls", "pdf", "rtf", "txt", "jpg", "jpeg", "bmp", "png", "gif", "tiff", "tif");
                if (in_array($sFileExt, $arrExtAllowed) === false)
                    $this->webErrorMessage("student Edit", "ERROR: Pic Screenshot .{$sFileExt} file extension is not allowed.");
                if ($iFileSize > 2097152)
                    $this->webErrorMessage("student Edit", "ERROR: Pic Screenshot file size must be less than 2 MB.");
                if (!isset($sName)) $sName = '';
                $serverFileName = 'student_picScreenshot_'.preg_replace('/[^a-z0-9_]/i', '', $sName).'_'.getRandomString(16).'.'.$sFileExt;
                $serverFilePath = $GLOBALS['PATH_TO_UPLOAD_FILES'].$serverFileName;
                $picScreenshot = $this->makeSafeAndSingleQuote($serverFilePath);
                //Save file in server
                $isSaved = move_uploaded_file($sFileTmpName, $serverFilePath);
                if ($isSaved != false)
                    $sRcvdSetColumns .= ", picScreenshot={$picScreenshot}";
                else $this->webErrorMessage("student Edit", "ERROR: Pic Screenshot file upload failed");
            }
        }
    
        if (isset($_POST->xdActivityType))
        {
            if ($this->isWebInputNull($_POST->xdActivityType)) { $xdActivityType = null; $temp = 'NULL'; }
            else
            {
                $xdActivityType = intval($_POST->xdActivityType);
                $temp = $xdActivityType;
            }
            $sRcvdSetColumns .= ", xdActivityType={$temp}";
            if ($xdActivityType != $objectExisting['xdActivityType']) $sChangedColumns .= "Activity Type ({$objectExisting['xdActivityType']} -> {$xdActivityType}), ";
        } //else $xdActivityType = $objectExisting['xdActivityType'];
    
        if (isset($_POST->id_jd))
        {
            if ($this->isWebInputNull($_POST->id_jd))
                $this->webErrorMessage("student Edit", "ERROR: JD is mandatory");
            else
            {
                $id_jd = $this->extractIdFromIdxIdAfterValidate($_POST->id_jd, 'jd');
                $temp = $id_jd;
            }
            $sRcvdSetColumns .= ", id_jd={$temp}";
            if ($id_jd != $objectExisting->id_jd) $sChangedColumns .= "JD ({$objectExisting->id_jd} -> {$id_jd}), ";
        } //else $id_jd = $objectExisting['id_jd'];
    
        if (isset($_POST->idd_blog))
        {
            if ($this->isWebInputNull($_POST->idd_blog)) { $idd_blog = null; $temp = 'NULL'; }
            else
            {
                $idd_blog = $this->extractIdFromIdxIdAfterValidate($_POST->idd_blog, 'blog');
                $temp = $idd_blog;
            }
            $sRcvdSetColumns .= ", idd_blog={$temp}";
            if ($idd_blog != $objectExisting->idd_blog) $sChangedColumns .= "Blog ({$objectExisting->idd_blog} -> {$idd_blog}), ";
        } //else $idd_blog = $objectExisting->idd_blog;
    
        if (isset($_POST->fJdMatch))
        {
            if ($this->isWebInputNull($_POST->fJdMatch)) { $fJdMatch = null; $temp = 'NULL'; }
            else
            {
                $fJdMatch = floatval($_POST->fJdMatch);
                $temp = $fJdMatch;
            }
            $sRcvdSetColumns .= ", fJdMatch={$temp}";
            if ($fJdMatch != $objectExisting->fJdMatch) $sChangedColumns .= "Jd Match ({$objectExisting->fJdMatch} -> {$fJdMatch}), ";
        } //else $fJdMatch = $objectExisting->fJdMatch;
    
        if (isset($_POST->sLinkIntroVideo))
        {
            if ($this->isWebInputNull($_POST->sLinkIntroVideo)) { $sLinkIntroVideo = null; $temp = 'NULL'; }
            else
            {
                $sLinkIntroVideo = trim($_POST->sLinkIntroVideo);
                $temp = $this->makeSafeAndSingleQuote($sLinkIntroVideo);
            }
            $sRcvdSetColumns .= ", sLinkIntroVideo={$temp}";
            if ($sLinkIntroVideo != $objectExisting->sLinkIntroVideo) $sChangedColumns .= "Link Intro Video, ";
        } //else $sLinkIntroVideo = $objectExisting['sLinkIntroVideo'];
    
        if (isset($_POST->tdtIvTime))
        {
            if ($this->isWebInputNull($_POST->tdtIvTime)) { $tdtIvTime = null; $temp = 'NULL'; }
            else
            {
                $tdtIvTime = datetimeFromBrowserDatetime($_POST->tdtIvTime);
                $temp = $this->makeSafeAndSingleQuote($tdtIvTime);
            }
            $sRcvdSetColumns .= ", tdtIvTime={$temp}";
            if ($tdtIvTime != $objectExisting->tdtIvTime) $sChangedColumns .= "Iv Time ({$objectExisting->tdtIvTime} -> {$tdtIvTime}), ";
        } //else $tdtIvTime = $objectExisting['tdtIvTime'];
    
        if (isset($_POST->jsonMobile_poc))
        {
            if ($this->isWebInputNull($_POST->jsonMobile_poc)) { $jsonMobile_poc = null; $temp = 'NULL'; }
            else
            {
                $jsonMobile_poc = trim($_POST->jsonMobile_poc);
                $temp = $this->makeSafeAndSingleQuote($jsonMobile_poc);
            }
            $sRcvdSetColumns .= ", jsonMobile_poc={$temp}";
            if ($jsonMobile_poc != $objectExisting->jsonMobile_poc) $sChangedColumns .= "Mobile poc ({$objectExisting->jsonMobile_poc} -> {$jsonMobile_poc}), ";
        } //else $jsonMobile_poc = $objectExisting->jsonMobile_poc;
    
        if (isset($_POST->add_faq))
        {
            if ($this->isWebInputNull($_POST->add_faq)) { $add_faq = null; $temp = 'NULL'; }
            else
            {
                $add_faq = multiSelectToString($_POST->add_faq);
                $temp = $this->makeSafeAndSingleQuote($add_faq);
            }
            $sRcvdSetColumns .= ", add_faq={$temp}";
            if ($add_faq != $objectExisting->add_faq) $sChangedColumns .= "Faq, ";
        } //else $add_faq = $objectExisting->add_faq;
    
        if (isset($_POST->xcCalling))
        {
            if ($this->isWebInputNull($_POST->xcCalling)) { $xcCalling = null; $temp = 'NULL'; }
            else
            {
                $xcCalling = $this->bitsToBitmap($_POST->xcCalling);
                $temp = $xcCalling;
            }
            $sRcvdSetColumns .= ", xcCalling={$temp}";
            if ($xcCalling != $objectExisting->xcCalling) $sChangedColumns .= "Calling ({$objectExisting->xcCalling} -> {$xcCalling}), ";
        } //else $xcCalling = $objectExisting->xcCalling;
    
        if (isset($_POST->xrIs_GiveResult))
        {
            if ($this->isWebInputNull($_POST->xrIs_GiveResult)) { $xrIs_GiveResult = null; $temp = 'NULL'; }
            else
            {
                $xrIs_GiveResult = intval($_POST->xrIs_GiveResult);
                $temp = $xrIs_GiveResult;
            }
            $sRcvdSetColumns .= ", xrIs_GiveResult={$temp}";
            if ($xrIs_GiveResult != $objectExisting->xrIs_GiveResult) $sChangedColumns .= "Is Give Result ({$objectExisting->xrIs_GiveResult} -> {$xrIs_GiveResult}), ";
        } //else $xrIs_GiveResult = $objectExisting->xrIs_GiveResult;
    
        if (isset($_POST->mdd_quote))
        {
            if ($this->isWebInputNull($_POST->mdd_quote)) { $mdd_quote = null; $temp = 'NULL'; }
            else
            {
                $mdd_quote = $this->multiSelectToMdd('quote', $objectExisting->mdd_quote, $_POST->mdd_quote);
                $temp = $this->makeSafeAndSingleQuote($mdd_quote);
            }
            $sRcvdSetColumns .= ", mdd_quote={$temp}";
            if ($mdd_quote != $objectExisting->mdd_quote) $sChangedColumns .= "Quote, ";
        } //else $mdd_quote = $objectExisting['mdd_quote'];
    
        if (isset($_POST->mcLevels))
        {
            if ($this->isWebInputNull($_POST->mcLevels)) { $mcLevels = null; $temp = 'NULL'; }
            else
            {
                $mcLevels = $this->bitsToBitmap($_POST->mcLevels);
                $temp = $mcLevels;
            }
            $sRcvdSetColumns .= ", mcLevels={$temp}";
            if ($mcLevels != $objectExisting->mcLevels) $sChangedColumns .= "Levels ({$objectExisting->mcLevels} -> {$mcLevels}), ";
        } //else $mcLevels = $objectExisting['mcLevels'];
    
        if (isset($_POST->xrIvBy))
        {
            if ($this->isWebInputNull($_POST->xrIvBy)) { $xrIvBy = null; $temp = 'NULL'; }
            else
            {
                $xrIvBy = intval($_POST->xrIvBy);
                $temp = $xrIvBy;
            }
            $sRcvdSetColumns .= ", xrIvBy={$temp}";
            if ($xrIvBy != $objectExisting->xrIvBy) $sChangedColumns .= "Iv By ({$objectExisting->xrIvBy} -> {$xrIvBy}), ";
        } //else $xrIvBy = $objectExisting['xrIvBy'];
    
        if ($sError != '') $this->webErrorMessage("Student Update", $sError);
        $sql = "UPDATE student SET {$sRcvdSetColumns} WHERE student.id={$objectExisting->id}";
        // echo $sql; exit;
        $numRowsAffected = DB:: update($sql);
        // $numRowsAffected = queryUpdate($sql, "ERROR: Invalid data or contact Tech-support");
        if ($numRowsAffected == 0)
            $sSuccess = "SUCCESS: No changes";//no data change;
        else
        {
            $sSuccess = "SUCCESS: Changes Saved";
            // $iTimeTakenInFillingForm = getDurationFormOpenTimeInSession();
            // createActiveLog(TABLE_student, $objectExisting['id'], ACTIVITY_EDIT_SUBMIT, null, $iTimeTakenInFillingForm);
        }
    
        //Create Event entry if there is state change or update
        // if (($edStatus != 99) && ($edStatus != $objectExisting['edStatus']))
        // {
        //     createEvent('student', $objectExisting['id'], 2, $edStatus, $objectExisting['edStatus'], $sChangedColumns, null, null, null);
        // }
        // else if ($sChangedColumns != '') //Create Event for entry update
        // {
        //     createEvent('student', $objectExisting['id'], 4, null, null, 'Updated: '.$sChangedColumns, null, null, null);
        // }
    
        $isAjax = isset($_REQUEST['isAjax']) ? $this->makeSafe($_REQUEST['isAjax']) : 0;
        // $isShowDetailOnSubmit = isset($_REQUEST['isShowDetailOnSubmit']) ? makeSafe($_REQUEST['isShowDetailOnSubmit']) : 0;
        // if ($isShowDetailOnSubmit == 1) //Not background action so show detail page
        // {
        //     $tableName_DEALNF = getTableNameForRole_DEALNF('student', $sUserType);
        //     $isEdit = isEdit($tableName_DEALNF);
        //     $object = getDetailViewRowWithForeignData('student', "student.id={$objectExisting['id']}");
        //     // appendToGlobalSuccess($sSuccess);
        //     viewstudentDetails($isEdit, $object);
        // }
        if ($isAjax == 0) //Not background action so show detail page
        {
            echo $sSuccess;
            //Redirect to details page.
            // $redirectTo = CONTROL_ROOT."student.php?act=detail&id={$objectExisting['sTokenDetails']}{$objectExisting['id']}&nav=b";
            // header('Location: '.$redirectTo);
            // exit;
        } else { //Ajax based success messages
            echo $sSuccess;
        }
    }



}
