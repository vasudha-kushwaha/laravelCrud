<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Student extends Controller
{
    // use cangraTrait;
    /**
     * Display a listing of the resource.
     */
    public function List(Request $request)
    {
        //$students = Student :: all();
        $sql = "SELECT * FROM students";
        $arrayObjects = DB:: select($sql); //array();
        $sUserType = 'guest';
        $listView = 'table';
        $range = 'r1';
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
        //echo "<pre>"; 
        //print_r($students); exit;
        //echo "ok vk"; exit;
        return view('list', $arguments);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function Add()
    {
        return view('student', ['action' => 'add']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $arrRecentInsertedColumns = Self::addData($request);
        if ($arrRecentInsertedColumns == null) webErrorMessage("Student Add", "ERROR: Add failed");
        $recentInsertedId = $arrRecentInsertedColumns['id'];
        $sLinkDetails = CONTROL_ROOT."vulnerabilitytest.php?act=vulnerabilitytestdetail&id={$arrRecentInsertedColumns['sTokenDetails']}{$arrRecentInsertedColumns['id']}";
        $isAjax = isset($_REQUEST['isAjax']) ? makeSafe($_REQUEST['isAjax']) : 0;
        if ($isAjax == 0)
        {
            //It is not background AJAX action so redirect to other page
            return redirect()->route('studentDetail', "{$arrRecentInsertedColumns['sTokenDetails']}{$arrRecentInsertedColumns['id']}");// redirect to named route
            // header('Location: '.$sLinkDetails);
            // exit;
        }
        else //Ajax based success messages
        {
            echo "SUCCESS: <div class=\"text-center\">Student has been added.<br><a class=\"btn btn-sm btn-info\" href=\"http://localhost/mylaragonlaravelapp/public/index.php/v4/student/detail/{$arrRecentInsertedColumns['sTokenDetails']}{$arrRecentInsertedColumns['id']}\">See created entry</a></div>";
        }
        //return redirect()->route('studentList');// redirect to named route
    }
    public function addData(Request $request)
    {
        // Validate and save the data
        global $sError;
        $sError = '';
        $sRcvdColumns = '';
        $sRcvdValues = '';
    
        $sTokenEdit = getRandomString(16);
        $sTokenDetails = getRandomString(16);
        $sIdx = getRandomString(16);
    
        //preparing array result to return to calling function
        $arrRecentInsertedColumns = array();
        $arrRecentInsertedColumns['sTokenDetails'] = $sTokenDetails;
        $arrRecentInsertedColumns['sTokenEdit'] =  $sTokenEdit;
    
        //Create SQL received columns string and VALUES string
        $sRcvdColumns = 'sTokenEdit, sTokenDetails, sIdx';
        $sRcvdValues = "'{$sTokenEdit}', '{$sTokenDetails}', '{$sIdx}'";
    
        $edStatus = isset($_POST['edStatus']) ? trim($_POST['edStatus']) : null;
        if (isWebInputNull($edStatus)) { $edStatus = null; $edStatus_SQL = 'NULL'; }
        else
        {
            $edStatus = intval($edStatus);
            $edStatus_SQL = $edStatus;
        }
        if (isset($_POST['edStatus']))
        {
            $sRcvdColumns .= ", edStatus";
            $sRcvdValues .= ", {$edStatus_SQL}";
        }
    
        $tdLeaveStartDate = isset($_POST['tdLeaveStartDate']) ? trim($_POST['tdLeaveStartDate']) : null;
        if (isWebInputNull($tdLeaveStartDate)) { $tdLeaveStartDate = null; $tdLeaveStartDate_SQL = 'NULL'; }
        else $tdLeaveStartDate_SQL = makeSafeAndSingleQuote($tdLeaveStartDate);
        if (isset($_POST['tdLeaveStartDate']))
        {
            $sRcvdColumns .= ", tdLeaveStartDate";
            $sRcvdValues .= ", {$tdLeaveStartDate_SQL}";
        }
    
        $sName = isset($_POST['sName']) ? trim($_POST['sName']) : null;
        if (isWebInputNull($sName)) { $sName = null; $sName_SQL = 'NULL'; }
        else $sName_SQL = makeSafeAndSingleQuote($sName);
        if (isset($_POST['sName']))
        {
            $sRcvdColumns .= ", sName";
            $sRcvdValues .= ", {$sName_SQL}";
        }
    
        $erType = isset($_POST['erType']) ? trim($_POST['erType']) : null;
        if (isset($_POST['erType']) && isWebInputNull($erType))
        {
            $erType = null; $erType_SQL = 'NULL';
            webErrorMessage("Vulnerability Test Add", "ERROR: Type is mandatory");
        }
        else
        {
            $erType = intval($erType);
            $erType_SQL = $erType;
        }
        if (isset($_POST['erType']))
        {
            $sRcvdColumns .= ", erType";
            $sRcvdValues .= ", {$erType_SQL}";
        }
    
        $iAmount = isset($_POST['iAmount']) ? trim($_POST['iAmount']) : null;
        if (isWebInputNull($iAmount)) { $iAmount = null; $iAmount_SQL = 'NULL'; }
        else
        {
            $iAmount = intval($iAmount);
            $iAmount_SQL = $iAmount;
        }
        if (isset($_POST['iAmount']))
        {
            $sRcvdColumns .= ", iAmount";
            $sRcvdValues .= ", {$iAmount_SQL}";
        }
    
        $sdlZone = isset($_POST['sdlZone']) ? trim($_POST['sdlZone']) : null;
        if (isWebInputNull($sdlZone)) { $sdlZone = null; $sdlZone_SQL = 'NULL'; }
        else $sdlZone_SQL = makeSafeAndSingleQuote($sdlZone);
        if (isset($_POST['sdlZone']))
        {
            $sRcvdColumns .= ", sdlZone";
            $sRcvdValues .= ", {$sdlZone_SQL}";
        }
    
        $sDescription = isset($_POST['sDescription']) ? $_POST['sDescription'] : null;
        if (isWebInputNull($sDescription)) { $sDescription = null; $sDescription_SQL = 'NULL'; }
        else $sDescription_SQL = makeSafeAndSingleQuote($sDescription);
        if (isset($_POST['sDescription']))
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
                    webErrorMessage("Vulnerability Test Add", "ERROR: Attachment .{$sFileExt} file extension is not allowed.");
                if ($iFileSize > 2097152)
                    webErrorMessage("Vulnerability Test Add", "ERROR: Attachment file size must be less than 2 MB.");
                if (!isset($sName)) $sName = '';
                $serverFileName = 'vulnerabilitytest_fileAttachment_'.preg_replace('/[^a-z0-9_]/i', '', $sName).'_'.getRandomString(16).'.'.$sFileExt;
                $serverFilePath = $GLOBALS['PATH_TO_UPLOAD_FILES'].$serverFileName;
                $fileAttachment_SQL = makeSafeAndSingleQuote($serverFilePath);
                //Save file in server
                $isSaved = move_uploaded_file($sFileTmpName, $serverFilePath);
                if ($isSaved != false)
                {
                    $sRcvdColumns .= ", fileAttachment";
                    $sRcvdValues .= ", {$fileAttachment_SQL}";
                } else webErrorMessage("Vulnerability Test Add", "ERROR: Attachment file upload failed");
            }
        }
        else if(isset($_POST['fileAttachment']))
        {
            $fileAttachment = isset($_POST['fileAttachment']) ? trim($_POST['fileAttachment']) : null;
            if (isWebInputNull($fileAttachment)) { $fileAttachment = null; $fileAttachment_SQL = 'NULL'; }
            else $fileAttachment_SQL = makeSafeAndSingleQuote($fileAttachment);
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
                    webErrorMessage("Vulnerability Test Add", "ERROR: Pic Screenshot .{$sFileExt} file extension is not allowed.");
                if ($iFileSize > 2097152)
                    webErrorMessage("Vulnerability Test Add", "ERROR: Pic Screenshot file size must be less than 2 MB.");
                if (!isset($sName)) $sName = '';
                $serverFileName = 'vulnerabilitytest_picScreenshot_'.preg_replace('/[^a-z0-9_]/i', '', $sName).'_'.getRandomString(16).'.'.$sFileExt;
                $serverFilePath = $GLOBALS['PATH_TO_UPLOAD_FILES'].$serverFileName;
                $picScreenshot_SQL = makeSafeAndSingleQuote($serverFilePath);
                //Save file in server
                $isSaved = move_uploaded_file($sFileTmpName, $serverFilePath);
                if ($isSaved != false)
                {
                    $sRcvdColumns .= ", picScreenshot";
                    $sRcvdValues .= ", {$picScreenshot_SQL}";
                } else webErrorMessage("Vulnerability Test Add", "ERROR: Pic Screenshot file upload failed");
            }
        }
    
        $xdActivityType = isset($_POST['xdActivityType']) ? trim($_POST['xdActivityType']) : null;
        if (isWebInputNull($xdActivityType)) { $xdActivityType = null; $xdActivityType_SQL = 'NULL'; }
        else
        {
            $xdActivityType = intval($xdActivityType);
            $xdActivityType_SQL = $xdActivityType;
        }
        if (isset($_POST['xdActivityType']))
        {
            $sRcvdColumns .= ", xdActivityType";
            $sRcvdValues .= ", {$xdActivityType_SQL}";
        }
    
        $id_jd = isset($_POST['id_jd']) ? trim($_POST['id_jd']) : null;
        if (isset($_POST['id_jd']) && isWebInputNull($id_jd))
        {
            $id_jd = null; $id_jd_SQL = 'NULL';
            webErrorMessage("Vulnerability Test Add", "ERROR: JD is mandatory");
        }
        else $id_jd = extractIdFromIdxIdAfterValidate($id_jd, 'jd');
        if (empty($id_jd)) { $id_jd = null; $id_jd_SQL = 'NULL'; }
        else $id_jd_SQL = $id_jd;
        if (isset($_POST['id_jd']))
        {
            $sRcvdColumns .= ", id_jd";
            $sRcvdValues .= ", {$id_jd_SQL}";
        }
    
        $idd_blog = isset($_POST['idd_blog']) ? trim($_POST['idd_blog']) : null;
        if (isWebInputNull($idd_blog)) { $idd_blog = null; $idd_blog_SQL = 'NULL'; }
        else $idd_blog = extractIdFromIdxIdAfterValidate($idd_blog, 'blog');
        if (empty($idd_blog)) { $idd_blog = null; $idd_blog_SQL = 'NULL'; }
        else $idd_blog_SQL = $idd_blog;
        if (isset($_POST['idd_blog']))
        {
            $sRcvdColumns .= ", idd_blog";
            $sRcvdValues .= ", {$idd_blog_SQL}";
        }
    
        $fJdMatch = isset($_POST['fJdMatch']) ? trim($_POST['fJdMatch']) : null;
        if (isWebInputNull($fJdMatch)) { $fJdMatch = null; $fJdMatch_SQL = 'NULL'; }
        else
        {
            $fJdMatch = floatval($fJdMatch);
            $fJdMatch_SQL = $fJdMatch;
        }
        if (isset($_POST['fJdMatch']))
        {
            $sRcvdColumns .= ", fJdMatch";
            $sRcvdValues .= ", {$fJdMatch_SQL}";
        }
    
        $sLinkIntroVideo = isset($_POST['sLinkIntroVideo']) ? trim($_POST['sLinkIntroVideo']) : null;
        if (isWebInputNull($sLinkIntroVideo)) { $sLinkIntroVideo = null; $sLinkIntroVideo_SQL = 'NULL'; }
        else $sLinkIntroVideo_SQL = makeSafeAndSingleQuote($sLinkIntroVideo);
        if (isset($_POST['sLinkIntroVideo']))
        {
            $sRcvdColumns .= ", sLinkIntroVideo";
            $sRcvdValues .= ", {$sLinkIntroVideo_SQL}";
        }
    
        $tdtIvTime = isset($_POST['tdtIvTime']) ? trim($_POST['tdtIvTime']) : null;
        if (isWebInputNull($tdtIvTime)) { $tdtIvTime = null; $tdtIvTime_SQL = 'NULL'; }
        else
        {
            $tdtIvTime = datetimeFromBrowserDatetime($tdtIvTime);
            $tdtIvTime_SQL = makeSafeAndSingleQuote($tdtIvTime);
        }
        if (isset($_POST['tdtIvTime']))
        {
            $sRcvdColumns .= ", tdtIvTime";
            $sRcvdValues .= ", {$tdtIvTime_SQL}";
        }
    
        $jsonMobile_poc = isset($_POST['jsonMobile_poc']) ? trim($_POST['jsonMobile_poc']) : null;
        if (isWebInputNull($jsonMobile_poc)) { $jsonMobile_poc = null; $jsonMobile_poc_SQL = 'NULL'; }
        else $jsonMobile_poc_SQL = makeSafeAndSingleQuote($jsonMobile_poc);
        if (isset($_POST['jsonMobile_poc']))
        {
            $sRcvdColumns .= ", jsonMobile_poc";
            $sRcvdValues .= ", {$jsonMobile_poc_SQL}";
        }
    
        $add_faq = isset($_POST['add_faq']) ? $_POST['add_faq'] : null;
        if (isWebInputNull($add_faq)) { $add_faq = null; $add_faq_SQL = 'NULL'; }
        else
        {
            $add_faq = multiSelectToString($add_faq);
            $add_faq_SQL = makeSafeAndSingleQuote($add_faq);
        }
        if (isset($_POST['add_faq']))
        {
            $sRcvdColumns .= ", add_faq";
            $sRcvdValues .= ", {$add_faq_SQL}";
        }
    
        $xcCalling = isset($_POST['xcCalling']) ? $_POST['xcCalling'] : null;
        if (isWebInputNull($xcCalling)) { $xcCalling = null; $xcCalling_SQL = 'NULL'; }
        else
        {
            $xcCalling = bitsToBitmap($xcCalling);
            $xcCalling_SQL = $xcCalling;
        }
        if (isset($_POST['xcCalling']))
        {
            $sRcvdColumns .= ", xcCalling";
            $sRcvdValues .= ", {$xcCalling_SQL}";
        }
    
        $xrIs_GiveResult = isset($_POST['xrIs_GiveResult']) ? trim($_POST['xrIs_GiveResult']) : null;
        if (isWebInputNull($xrIs_GiveResult)) { $xrIs_GiveResult = null; $xrIs_GiveResult_SQL = 'NULL'; }
        else
        {
            $xrIs_GiveResult = intval($xrIs_GiveResult);
            $xrIs_GiveResult_SQL = $xrIs_GiveResult;
        }
        if (isset($_POST['xrIs_GiveResult']))
        {
            $sRcvdColumns .= ", xrIs_GiveResult";
            $sRcvdValues .= ", {$xrIs_GiveResult_SQL}";
        }
    
        $mdd_quote = isset($_POST['mdd_quote']) ? $_POST['mdd_quote'] : null;
        if (isWebInputNull($mdd_quote)) { $mdd_quote = null; $mdd_quote_SQL = 'NULL'; }
        else
        {
            $mdd_quote = multiSelectToMdd('quote', null, $mdd_quote);
            $mdd_quote_SQL = makeSafeAndSingleQuote($mdd_quote);
        }
        if (isset($_POST['mdd_quote']))
        {
            $sRcvdColumns .= ", mdd_quote";
            $sRcvdValues .= ", {$mdd_quote_SQL}";
        }
    
        $mcLevels = isset($_POST['mcLevels']) ? $_POST['mcLevels'] : null;
        if (isWebInputNull($mcLevels)) { $mcLevels = null; $mcLevels_SQL = 'NULL'; }
        else
        {
            $mcLevels = bitsToBitmap($mcLevels);
            $mcLevels_SQL = $mcLevels;
        }
        if (isset($_POST['mcLevels']))
        {
            $sRcvdColumns .= ", mcLevels";
            $sRcvdValues .= ", {$mcLevels_SQL}";
        }
    
        $xrIvBy = isset($_POST['xrIvBy']) ? trim($_POST['xrIvBy']) : null;
        if (isWebInputNull($xrIvBy)) { $xrIvBy = null; $xrIvBy_SQL = 'NULL'; }
        else
        {
            $xrIvBy = intval($xrIvBy);
            $xrIvBy_SQL = $xrIvBy;
        }
        if (isset($_POST['xrIvBy']))
        {
            $sRcvdColumns .= ", xrIvBy";
            $sRcvdValues .= ", {$xrIvBy_SQL}";
        }
    
        if ($sError != '') webErrorMessage("Vulnerability Test add", $sError);
        $sql = "INSERT INTO vulnerabilitytest ({$sRcvdColumns}) VALUES ({$sRcvdValues})";
        $recentInsertedId = queryInsert($sql);
        $arrayObjects = DB:: select($sql); //array();
        if ($recentInsertedId == 0) webErrorMessage("Vulnerability Test Add", "ERROR: Invalid Data. Contact Tech Support.");
        else
        {
            
            //preparing array result to return to calling function
            //$object = getTableRowById('vulnerabilitytest', $recentInsertedId);
            $arrRecentInsertedColumns['id'] = $recentInsertedId;
            $iTimeTakenInFillingForm = getDurationFormOpenTimeInSession();
            createActiveLog(TABLE_VULNERABILITYTEST, $recentInsertedId, ACTIVITY_ADD, null, $iTimeTakenInFillingForm);
            //Create Event for entry add
            createEvent('vulnerabilitytest', $recentInsertedId, 3, null, null, null, null, null, null);
            return $arrRecentInsertedColumns;
        }
        //return null;
        //Student::create($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $id = Self::extractId($id);
        $item = Student::findOrFail($id);
        return view('student', ['action' => 'detail', 'object' => $item]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $id = Self::extractId($id);
        $item = Student::findOrFail($id);
        return view('student', ['action' => 'edit', 'objectExisting' => $item]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $id = Self::extractId($id);
        $item = Student::findOrFail($id);
        $item->update($request->all());
        return redirect()->route('your-resource.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = Student::findOrFail($id);
        $item->delete();
        return redirect()->route('your-resource.index');
    }
}
