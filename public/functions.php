<?php
//echo on local code but not on server
function isWebInputNull(&$sWebInput)
{
    return (($sWebInput == null) || ($sWebInput == '') || ($sWebInput == 'null') || ($sWebInput == 'Null') || ($sWebInput == 'NULL'));
}

//echo on local code but not on server
function appendToGlobalSuccess($sSuccess)
{
    if (isset($GLOBALS['sSuccessGlobal'])) $GLOBALS['sSuccessGlobal'] .= '<br>' . $sSuccess;
    $GLOBALS['sSuccessGlobal'] = $sSuccess;
}

function readAndClearGlobalSuccess($isClear=true)
{
    if (isset($GLOBALS['sSuccessGlobal']))
    {
        $temp = $GLOBALS['sSuccessGlobal'];
        if ($isClear==true) unset($GLOBALS['sSuccessGlobal']);
        return $temp;
    }
    else return null;
}

function appendToGlobalWarning($sWarning)
{
    if (isset($GLOBALS['sWarningGlobal'])) $GLOBALS['sWarningGlobal'] .= '<br>' . $sWarning;
    $GLOBALS['sWarningGlobal'] = $sWarning;
}

function readAndClearGlobalWarning($isClear=true)
{
    if (isset($GLOBALS['sWarningGlobal']))
    {
        $temp = $GLOBALS['sWarningGlobal'];
        if ($isClear==true) unset($GLOBALS['sWarningGlobal']);
        return $temp;
    }
    else return null;
}

function debug($sDebugMessage)
{
    if (!defined('SERVER_LIVE'))
        echo escapeHtml($sDebugMessage).'<br>';
}

function escapeHtml($string)
{
    $string = str_replace("`", "'", $string);
    $string = htmlentities($string, ENT_COMPAT | ENT_HTML401, 'UTF-8');
    //$string = str_replace("`", "&#96;", $string);//ct
    return $string;
}

function escpeHtmlQuill(&$string, $iMaxCharLength=5000)
{
    $string = escapeScriptTag($string);
    //$string = str_replace("`", "&#96;", $string);//ct
    $string = str_replace("`", "", $string);
    //remove all tags except the given list of tags
    //strip_tags(string,allowed list of tags)
    $string = strip_tags($string, "<p><h1><h2><h3><h4><h5><h6><br><sub><sup><ol><ul><li><span><a><s><em><strong><pre>");
    //If stored quill was truncated in between due to varchar limit then clean that html to avoid breaking of html pages by this text.
    if (strlen($string) > $iMaxCharLength-50)
    {
        //Parse this text in html and remove garbage tags
        $doc = new DOMDocument();
        $doc->substituteEntities = false;
        $content = mb_convert_encoding($string, 'html-entities', 'utf-8');
        $doc->loadHTML($content, LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED);
        $string = $doc->saveHTML();
    }
    return $string;
}

function escapeScriptTag($string)
{
    $sEscapedString = str_replace("<script>", "&lt;script&gt;", $string);
    $sEscapedString = str_replace("</script>", "&lt;/script&gt;", $sEscapedString);
    //$sEscapedString = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $string);
    //$sEscapedString = preg_replace("/[\n\r]/", "", $sEscapedString);
    return $sEscapedString;
}

function sessionStart()
{
    if (version_compare(PHP_VERSION, '5.4.0', '<')) {
        if(session_id() == '')
        {
            ini_set('session.cookie_httponly', 1 );
            ini_set('session.cookie_secure', 1);
            session_start();
            //session_regenerate_id(true);
        }
    } else  {
       if (session_status() == PHP_SESSION_NONE)
       {
           ini_set('session.cookie_httponly', 1 );
           ini_set('session.cookie_secure', 1);
           session_start();
           //session_regenerate_id(true);
       }
    }
    //Store system time to calculate script execution time
    $GLOBALS['microtime'] = microtime(true);
}

function todayDateForSql()
{
    return date("Y-m-d");
}

function currentMonthForSql()
{
    return date("Y-m");
}

function nowTimeForSql()
{
    return date("Y-m-d H:i:s");
}

function nowUtcTimeForSql()
{
    if (defined('SERVER_LIVE'))
        return gmdate("Y-m-d H:i:s");
    else
        return date("Y-m-d H:i:s");
}

function nowOnlyTimeForSql()
{
    return date("H:i:s");
}

function timeForSqlFromNow($timeStringInWords)
{
    return date("Y-m-d H:i:s", strtotime($timeStringInWords));
}

function lastMonthForSql()
{
    return date("Y-m", strtotime('-1 month'));
}

function todayDateString()
{
    return date("d M Y");
}

function previousMonthStartDateString()
{
    return date("Y-m", strtotime('-1 month')).'-01';
}

function previousMonthLastDateString()
{
    return date("Y-m-t", strtotime('-1 month'));
}

function currentMonthStartDateString()
{
    return date("Y-m").'-01';
}

function currentMonthLastDateString()
{
    return date("Y-m-t");
}

function givenDateLastDateString($tdStartDateOfMonth)
{
    return date("Y-m-t", strtotime($tdStartDateOfMonth));
}

function datetimeFromBrowserDatetime($timeStringInWords)
{
    $date = DateTime::createFromFormat("Y-m-d\TH:i", $timeStringInWords);
    if ($date === false) return '';
    return $date->format('Y-m-d H:i:s');
}

function datetimeToBrowserDatetime($datetime)
{
    return date("Y-m-d\TH:i", strtotime($datetime));
}

function getDateNDaysAfter($daysAfter)
{
    return date("Y-m-d", strtotime("{$daysAfter} days"));
}

function getDateNDaysBack($daysBack)
{
    return date("Y-m-d", strtotime("-{$daysBack} days"));
}

function getDatetimeNDaysBack($daysBack)
{
    return date("Y-m-d H:i:s", strtotime("-{$daysBack} days"));
}

function getDateNextWorkingDay()
{
    $tdStartDate = date("Y-m-d", strtotime("+1 days"));
    $sDay = date('D', strtotime($tdStartDate));
    $holidays = array("2021-04-21", "2021-08-15", "2021-08-23", "2021-08-30", "2021-10-02", "2021-11-04", "2021-11-05", "2021-12-25");
    if (($sDay == 'Sun') || in_array($tdStartDate, $holidays))
    {
        return date("Y-m-d", strtotime("+2 days"));
    }
    else return $tdStartDate;
}

//This date is of today if current time is 6PM+. This date is of yesterday if current time is day time
function getDateRecentWorkingDay()
{
    return date("Y-m-d", strtotime("-18 hours"));
}

function convertTimeZoneAddMinutes($time=null, $fromTimeZone='UTC', $toTimeZone='UTC', $iAfterMinutes=0)
{
    $date = new DateTime($time, new DateTimeZone($fromTimeZone));
    date_add($date,  date_interval_create_from_date_string($iAfterMinutes.' minutes'));
    $date->setTimezone(new DateTimeZone($toTimeZone));
    $time= $date->format('Y-m-d H:i:s');
    return $time;
}

function utcTimestampToDefaultDatetime($datetime, $sDateFormat='d M Y g:i A T')
{
    if (($datetime == null) || ($datetime == '')) return '-';
    if (defined('SERVER_LIVE'))
    {
        return date($sDateFormat, strtotime($datetime. ' UTC'));
    }
    else
    {
        return date($sDateFormat, strtotime($datetime));
    }
}

function utcTimestampToDefaultDatetimeAfterMinutes($datetime, $minutes)
{
    if (($datetime == null) || ($datetime == '')) return '-';
    if (defined('SERVER_LIVE'))
    {
        $datetimeRef = new DateTime($datetime. ' UTC');
        $datetimeRef->setTimezone(new DateTimeZone(DEFAULT_TIMEZONE));
    }
    else
    {
        $datetimeRef = new DateTime($datetime);
    }
    date_add($datetimeRef, date_interval_create_from_date_string($minutes.' minutes'));
    return $datetimeRef->format('Y-m-d H:i:s');
}

function unixTimestampToDatetime($timestamp)
{
    if (empty($timestamp)) return null;
    $datetime = date("Y-m-d H:i:s", $timestamp);
    if ($datetime === false) return null;
    else return $datetime;
}

function defaultDatetimeToUtcTimestamp($datetime)
{
    if (($datetime == null) || ($datetime == '')) return '-';
    //Reduce time by 5:30 hours
    return date("Y-m-d H:i:s", strtotime($datetime.' -330 minutes'));
}

function istTimeToGoogleRecordingTime($datetime)
{
    if (($datetime == null) || ($datetime == '')) return '-';
    //return date("Y-m-d G:i", strtotime($datetime.' -13 hours -30 minutes')).' GMT-8';
    return date("Y-m-d G:i", strtotime($datetime.' -12 hours -30 minutes')).' GMT-7';
}

function timeToNicetime($time)
{
    if (($time == null) || ($time == '')) return '-';

    //Table view has sorting which needs date in 2019-12-31 16:00 format
    if (isset($_REQUEST['act']) && ($_REQUEST['act'] == 'list') && ((!isset($_SESSION['listView'])) || ($_SESSION['listView'] == 'table')))
        return date("H:i", strtotime($time));

    return date("g:i A", strtotime($time));
}

function datetimeToFullDatetime($datetime)
{
    if (($datetime == null) || ($datetime == '')) return '-';
    $timestamp =  strtotime($datetime);
    if ($timestamp === false) return '-';
    //Table view has sorting which needs date in 2019-12-31 16:00 format
    if (isset($_REQUEST['act']) && ($_REQUEST['act'] == 'list') && ((!isset($_SESSION['listView'])) || ($_SESSION['listView'] == 'table')))
        return date("Y-m-d H:i T", $timestamp);

    return date("d M Y g:i A T", $timestamp);
}

function datetimeToNiceDatetime($datetime, $convertAlways=false, $isUTC=false)
{
    if (($datetime == null) || ($datetime == '')) return '-';
    if(defined('SERVER_LIVE') && $isUTC)
    {
        $timestamp =  strtotime($datetime. ' UTC');
    }
    else
    {
        $timestamp =  strtotime($datetime);
    }
    if ($timestamp === false) return '-';
    //Table view has sorting which needs date in 2019-12-31 16:00 format
    if ((!$convertAlways) && isset($_REQUEST['act']) && ($_REQUEST['act'] == 'list') && ((!isset($_SESSION['listView'])) || ($_SESSION['listView'] == 'table')))
        return date("Y-m-d H:i T", $timestamp);

    $todayDate = date(DATE_ATOM, time());
    //Today
    if (substr($todayDate,0,10) == substr($datetime,0,10)) return 'Today, '.date("d M g:i A T", $timestamp);
    else if (substr($todayDate,0,7) == substr($datetime,0,7)) return date("D,d M g:i A T", $timestamp);
    else if (substr($todayDate,0,4) == substr($datetime,0,4)) return date("d M g:i A T", $timestamp);
    else return date("d M Y g:i A T", $timestamp);
}

function datetimeToNiceShortDatetime($datetime, $sPrefixIfNotNull=null, $sReturnIfNull='-')
{
    $datetime = trim($datetime);
    if (($datetime == null) || ($datetime == '')) return $sReturnIfNull;
    $sResult = $sReturnIfNull;
    $timestamp =  strtotime($datetime);
    if ($timestamp === false) return '-';
    //Table view has sorting which needs date in 2019-12-31 16:00 format
    if (isset($_REQUEST['act']) && ($_REQUEST['act'] == 'list') && ((!isset($_SESSION['listView'])) || ($_SESSION['listView'] == 'table')))
    {
        $sResult = date("Y-m-d H:i T", $timestamp);
    }
    else
    {
        $todayDate = date(DATE_ATOM, time());
        //Today
        if (substr($todayDate, 0, 10) == substr($datetime, 0, 10))
            $sResult = 'Today, '.date("g:i A", $timestamp);
        else
            $sResult = date("d M g:i A", $timestamp);
    }
    return $sPrefixIfNotNull.$sResult;
}

function datetimeToNiceShortTime($datetime, $isUTC=false, $sPrefixIfNotNull=null, $sReturnIfNull='-')
{
    if (($datetime == null) || ($datetime == '')) return $sReturnIfNull;
    if(defined('SERVER_LIVE') && $isUTC)
    {
        $timestamp =  strtotime($datetime. ' UTC');
    }
    else
    {
        $timestamp =  strtotime($datetime);
    }
    if ($timestamp === false) return $sReturnIfNull;
    $todayDate = date(DATE_ATOM, time());
    //Today
    if (substr($todayDate,0,10) == substr($datetime,0,10)) return $sPrefixIfNotNull.date("g:i A", $timestamp);
    else return $sPrefixIfNotNull.date("d M g:i A", $timestamp);
}

function datetimeToNiceShortDate($datetime, $sPrefixIfNotNull=null, $sReturnIfNull='-')
{
    $datetime = trim($datetime);
    if (($datetime == null) || ($datetime == '')) return $sReturnIfNull;
    $sResult = $sReturnIfNull;
    $timestamp =  strtotime($datetime);
    if ($timestamp === false) return '-';
    //Table view has sorting which needs date in 2019-12-31 format
    if (isset($_REQUEST['act']) && ($_REQUEST['act'] == 'list') && ((!isset($_SESSION['listView'])) || ($_SESSION['listView'] == 'table')))
        $sResult = date("Y-m-d", $timestamp);

    $todayDate = date(DATE_ATOM, time());
    //Today
    if (substr($todayDate,0,10) == substr($datetime,0,10)) $sResult = 'Today';
    //if (substr($todayDate,0,10) == substr($datetime,0,10)) return 'Today '.date("d M", $timestamp);
    else if (substr($todayDate,0,4) == substr($datetime,0,4)) $sResult = date("d M", $timestamp);
    else $sResult = date("d M Y", $timestamp);

    return $sPrefixIfNotNull.$sResult;
}

/*
function datetimeToNiceShortDate($datetime)
{
    if (($datetime == null) || ($datetime == '')) return '-';
    //Table view has sorting which needs date in 2019-12-31 format
    if (isset($_REQUEST['act']) && ($_REQUEST['act'] == 'list') && ((!isset($_SESSION['listView'])) || ($_SESSION['listView'] == 'table')))
        return date("Y-m-d", strtotime($datetime));

    $todayDate = date(DATE_ATOM, time());
    //Today
    if (substr($todayDate,0,10) == substr($datetime,0,10)) return 'Today';
    //if (substr($todayDate,0,10) == substr($datetime,0,10)) return 'Today '.date("d M", strtotime($datetime));
    if (substr($todayDate,0,4) == substr($datetime,0,4)) return date("d M", strtotime($datetime));
    else return date("d M Y", strtotime($datetime));
}*/

function datetimeToNiceShortDateWithDay($datetime)
{
    if (($datetime == null) || ($datetime == '')) return '-';
    $timestamp =  strtotime($datetime);
    if ($timestamp === false) return '-';
    //Table view has sorting which needs date in 2019-12-31 format
    if (isset($_REQUEST['act']) && ($_REQUEST['act'] == 'list') && ((!isset($_SESSION['listView'])) || ($_SESSION['listView'] == 'table')))
        return date("Y-m-d", $timestamp);

    $todayDate = date(DATE_ATOM, time());
    //Today
    if (substr($todayDate,0,10) == substr($datetime,0,10)) return 'Today';
    //if (substr($todayDate,0,10) == substr($datetime,0,10)) return 'Today '.date("d M", $timestamp);
    if (substr($todayDate,0,4) == substr($datetime,0,4)) return date("D, d M", $timestamp);
    else return date("D, d M Y", $timestamp);
}

function dateTimeToDateWithDay($datetime)
{
    if(($datetime == null) || ($datetime == '')) return '-';
    $timestamp =  strtotime($datetime);
    if ($timestamp === false) return '-';
    return date("Y-m-d, D", $timestamp);
}

//For Birthdays: 2021-12-31 to "Today, 31 Dec"
function datetimeToNiceShortMonthDate($datetime)
{
    if (($datetime == null) || ($datetime == '')) return '-';
    $todayMonthDate = date('m-d');
    $tomorrowMonthDate = date('m-d', strtotime('+1 day'));
    $niceMonthDate = date("d M", strtotime($datetime));
    //Today
    if ($todayMonthDate == substr($datetime, 5, 6)) return 'Today, '.$niceMonthDate;
    else if ($tomorrowMonthDate == substr($datetime, 5, 6)) return 'Tomorrow, '.$niceMonthDate;
    else return $niceMonthDate;
}

function datetimeToDmy($datetime)
{
    if (($datetime == null) || ($datetime == '')) return '-';
    return date("d-M-Y", strtotime($datetime));
}

//Convert database datetime to its UTC 20180611T101500 format
function datetimeToIcalUtcTimeFormat($sDatetime=null, $sTimezone="Asia/Calcutta")
{
    /*
    if ($sDatetime == null)
        $unixTime = time();
    else
        $unixTime = strtotime($sDatetime);
    $objectDateTime = new Datetime();
    $objectDateTime->setTimestamp($unixTime);
    */
    $tz=timezone_open($sTimezone);
    $objectDateTime = date_create($sDatetime, $tz);
    $newTimeZone = new DateTimeZone('UTC');
    $objectDateTime->setTimezone($newTimeZone);
    $sDate = date_format($objectDateTime, 'Ymd');//date("Ymd", $unixTime);
    $sTime = date_format($objectDateTime, 'His');//date("His", $unixTime);
    return $sDate.'T'.$sTime.'Z';
}

function timeToNearQuarterhour($timeOnly)
{
    if (($timeOnly == null) || ($timeOnly == '')) return null;
    $hour = intval(date("H", strtotime($timeOnly)));
    $minute = intval(date("i", strtotime($timeOnly)));
    return intval(($hour*4)+(($minute+8)/15));
}

function isDateTimeInRange($datetimeRef, $minutes, $datetime)
{
    if (empty($datetimeRef)) $datetimeRef = date('Y-m-d H:i:s');
    if (empty($datetime)) $datetime = date('Y-m-d H:i:s');
    if (empty($minutes)) $minutes = 0;
    $DateTimeRef = new DateTime($datetimeRef);
    $DateTime2 = new DateTime($datetime);
    date_add($DateTimeRef, date_interval_create_from_date_string($minutes.' minutes'));
    //echo $DateTimeRef->format('Y-m-d H:i:s'); echo $DateTime2->format('Y-m-d H:i:s');exit;
    if ($DateTimeRef >= $DateTime2) return true;
    else return false;
}

function getDateTimeAfterMinutes($datetimeRef, $minutes)
{
    if (empty($datetimeRef)) $datetimeRef = date('Y-m-d H:i:s');
    if (empty($minutes)) $minutes = 0;
    $DateTimeRef = new DateTime($datetimeRef);
    date_add($DateTimeRef, date_interval_create_from_date_string($minutes.' minutes'));
    return $DateTimeRef->format('Y-m-d H:i:s');
}

function firstWord($statement, $isNameForEmail = true)
{
    if ($isNameForEmail)
    {
        //Remove salutations ending with dot or space
        if (substr($statement, 0, 5) == 'Mohd.') $statement = substr($statement, 5);
        else if (substr($statement, 0, 5) == 'Mohd ') $statement = substr($statement, 5);
        else if (substr($statement, 0, 3) == 'Md ') $statement = substr($statement, 3);
        else if (substr($statement, 0, 3) == 'Md.') $statement = substr($statement, 3);
        else if (substr($statement, 0, 3) == 'Mr.') $statement = substr($statement, 3);
        else if (substr($statement, 0, 3) == 'Mr ') $statement = substr($statement, 3);
        else if (substr($statement, 0, 4) == 'Mrs.') $statement = substr($statement, 4);
    }

    $statement = trim($statement);
    if (empty($statement)) return '';
    //else return explode(' ',trim($statement))[0];
    $niceName = '';
    $iUsefulChars = 0;
    for($j=0; $j < strlen($statement); $j++)
    {
        $ascii = ord($statement[$j]);
        //if ((($ascii >= 46) && ($ascii <= 57)) /* . / 0-9 */
        if ((($ascii >= 48) && ($ascii <= 57)) /* 0-9 */
            || (($ascii >= 65) && ($ascii <= 90))  /* A-Z */
            || (($ascii >= 97) && ($ascii <= 122)))  /* a-z */
        {
            $niceName .= $statement[$j];
            $iUsefulChars++;
        }
        //else if (strlen($niceName) > 3) break;
        else if ($iUsefulChars >= 3) break;
        else if ($ascii == 46) $niceName .= '.';
        else $niceName .= ' ';
    }

    if ($isNameForEmail)
    {
        //Now in sending mail process $statement has word before @ which will be used in sending mail title, replace department names with 'Team'
        $sDepartment = strtolower(trim($niceName));
        if (($sDepartment == 'hr') || ($sDepartment == 'sales') || ($sDepartment == 'frontoffice') || ($sDepartment == 'admin') || ($sDepartment == 'finance') || ($sDepartment == 'accounts') || ($sDepartment == 'info'))
            return 'Team';
    }

    return ucfirst(trim($niceName));
}

function firstCharacter($sName)
{
    return strtoupper(substr(firstWord($sName), 0, 1));
}

function fullNameFromEmail($sEmailAddressOnly)
{
    $sEmailExploded = explode('@', $sEmailAddressOnly);
    return ucfirst(trim(str_replace('.', ' ', $sEmailExploded[0])));
}

function validateIdTokenAndExitIfFail($tableName)
{
    $idToken =  isset($_POST['idToken']) ?  makeSafe($_POST['idToken']) : NULL;
    $sError = '';
    $xcProfile = 0;

    if ($idToken != NULL)
    {
        $idTokenLength = strlen($idToken);
        $id_login = substr($idToken, 0, $idTokenLength-4);
        $sToken = substr($idToken, $idTokenLength-4, 4);

        //Entry should be valid, account should be active
        $sql = "SELECT * FROM login WHERE (id= '{$id_login}') AND (login.iaEntryStatus=0)";
        $result = dbQuery($sql);
        if (mysqli_num_rows($result) > 0)
        {
            $row = mysqli_fetch_array($result);
            $id_login = $row['id'];
            $sTokenRow = $row['sToken'];
            $edStatus = $row['edStatus'];
            $iWrongCount = $row['iWrongCount'];
            if ($edStatus != 2) $sError = "Account Inactive. Call {$GLOBALS['contactNumber']} Customer Care";
            if ($iWrongCount > 3) $sError = "Account Locked. Call {$GLOBALS['contactNumber']} Customer Care";

            if (($sError == '') && ($sTokenRow != NULL))
            {
                //Compare Token
                if ($sTokenRow != $sToken)
                {
                    $sError = "Invalid Security Token. SignOut then Enter your Mobile number, Call {$GLOBALS['contactNumber']} Customer Care";
                }
                else
                {
                    $xcProfile = $row['xcProfile'];
                }
            }
        }
        else
        {
            $sError = "Invalid Security ID. SignOut then Login again, Call {$GLOBALS['contactNumber']} Customer Care";
        }
    }
    else
    {
        $sError = "Please update APP. Security Token missing. Call {$GLOBALS['contactNumber']} Customer Care";
    }

    //A guest user can add calls and add event. Guest user can register as Customer. so return profile bitmap 0
    if (($tableName == "customer") || ($tableName == "eventlog") || ($tableName == "calls"))
        return $xcProfile;

    if ($sError != '')
    {
        $i = 0;
        $response['sJsonTypeOverall']="SYNC_FROM_SERVER_TO_CLIENT";
        $response['sErrorTypeOverall']="SUCCESS_SERVER";
        $response['tSyncTime'] = date("Y-m-d H:i:s");
        $queryResult['sJsonType'] = $tableName;
        $queryResult['sErrorType'] = 'INVALID_USER';
        $queryResult['sMessage'] = $sError;
        $response['jaRecords'][$i++] = $queryResult;

        //Encode whole json response
        echo json_encode($response);
        closeConnection();
        exit();
    }
    return $xcProfile;

}

function validateAndGetLoginDetails($idToken, $sUserType)
{
    $i=0; $sError=''; $xcProfile = 0; $sEmail = NULL;

    //Entry should be valid, account should be active
    if ($idToken != NULL)
    {
        $idTokenLength = strlen($idToken);
        $id_login = substr($idToken, 0, $idTokenLength-4);
        //Handle if user has not received it's SMS OTP
        if ($id_login != 0) //Non-Guest user
        {
            $sToken = substr($idToken, $idTokenLength-4, 4);
            //Entry should be valid, account should be active
            $sql = "SELECT * FROM login WHERE (id= '{$id_login}') AND (login.iaEntryStatus=0)";
            $result = dbQuery($sql);
            if (($result != FALSE) && (mysqli_num_rows($result) > 0))
            {
                $row = mysqli_fetch_array($result);
                $id_login = $row['id'];
                $sTokenRow = $row['sToken'];
                $edStatus = $row['edStatus'];
                $iWrongCount = $row['iWrongCount'];
                if ($edStatus != 2) $sError = 'Account Inactive.';
                if ($iWrongCount > 3) $sError = 'Account Locked. Reset Password.';

                if (($sError == '') && ($sTokenRow != NULL))
                {
                    //Compare Token
                    if ($sTokenRow != $sToken)
                    {
                        $sError = 'Invalid Security Token. SignOut then login again.';
                        //Update iWrongCount
                        $sql = "UPDATE login SET iWrongCount = (iWrongCount + 1) WHERE (id= '{$id_login}')";
                        dbQuery($sql);
                    }
                    else
                    {
                        $xcProfile = $row['xcProfile'];
                        $sEmail = $row['sEmail'];
                        //TODO:Check if provided usertype is valid or not

                    }
                }
            }
            else
            {
                $sError = 'Invalid Security ID. SignOut then Login again,';
            }
        }
        else //Guest user
        {
            $row['id'] = 0;
            $row['xcProfile'] = 1;
            $row['sEmail'] = NULL;
            $row['sMobile'] = NULL;
        }
    }
    else
    {
        $sError = 'Security Token missing.';
    }

    if ($sError != '')
    {
        web_error_message("User Validation", $sError);
        exit();
    }

    return $row;
}

function closeConnectionWithOutputAndContinueProcessing($output)
{
    // Buffer all upcoming output...
    ob_start();
    // Send your response.
    echo $output;
    // Get the size of the output.
    $size = ob_get_length();
    // Disable compression (in case content length is compressed).
    header("Content-Encoding: none");
    header("Content-Length: {$size}");
    header("Connection: close");

    // Flush all output.
    ob_end_flush();
    ob_flush();
    flush();

    // Close current session (if it exists).
    if (session_id()) {
        session_write_close();
    }
    //This will flush all output to client and close the socket
    if (defined('SERVER_LIVE'))
        fastcgi_finish_request();
}

function sendSms($sMessage, $sMobileWithoutQuoates)
{
    global $credentials;
    //Send welcome SMS using msg91.com
    if (isset($credentials['msg91_sms_auth_key']) && (!empty($sMobileWithoutQuoates)))
    {
        //$sMessageEncoded = rawurlencode($sMessage." {$GLOBALS['company']}:{$GLOBALS['contactNumber']}");
        $sMessageEncoded = rawurlencode($sMessage);
        //Using OTP method for this as fast SMS and same price, Later change it to other methods
        //$url = "https://control.msg91.com/api/sendotp.php?authkey={$credentials['msg91_sms_auth_key']}&mobile=91{$sMobileWithoutQuoates}&message={$sMessageEncoded}&otp=0034&sender={$GLOBALS['sms6Characters']}";
        $url = "https://api.msg91.com/api/sendhttp.php?mobiles={$sMobileWithoutQuoates}&authkey={$credentials['msg91_sms_auth_key']}&route=4&sender={$GLOBALS['sms6Characters']}&country=91&message={$sMessageEncoded}";
        $smsResult = file_get_contents($url);
        return $smsResult;
    }
    return 'SMS NOT SENT';
}

function sendSmsOtp($otp, $sMobileWithoutQuoates)
{
    global $credentials;
    //Send welcome SMS using msg91.com: https://docs.msg91.com/p/tf9GTextN/e/Irz7-x1PK/MSG91
    if (isset($credentials['msg91_sms_auth_key']) && (!empty($sMobileWithoutQuoates)))
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.msg91.com/api/v5/flow/",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => "{\"flow_id\":\"608113ec11ca3367243bdc02\",\"sender\":\"CANGRA\",\"mobiles\":\"{$sMobileWithoutQuoates}\",\"OTP\":\"{$otp}\"}",
          CURLOPT_HTTPHEADER => array(
            "authkey: {$credentials['msg91_sms_auth_key']}",
            "content-type: application/JSON"
          ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) error_log("cURL Error #:" . $err);
        else return $response;
    }
    return 'SMS NOT SENT';
}

function sendSmsChooseLink($sTokenInterest, $id, $sMobileWithoutQuoates, $sName)
{
    global $credentials;
    $sName = substr($sName, 0, 17).' Ji';

    $regex = '/[^0-9]+/';
    $sMobileWithoutQuoates = preg_replace($regex, '', trim($sMobileWithoutQuoates));
    /*
//MSG91 requires mobile number in 91XXXXXXXXXX format
{
  "flow_id": "EnterflowID",
  "sender": "EnterSenderID",
  "mobiles": "919XXXXXXXXX",
  "VAR1": "VALUE 1",
  "VAR2": "VALUE 2"
}
    */
    if (strlen($sMobileWithoutQuoates) == 10)
        $sMobileWithoutQuoates = '91'.$sMobileWithoutQuoates;
    else if (strStartsWith($sMobileWithoutQuoates, '91') && (strlen($sMobileWithoutQuoates) == 12)) //Already 91 present then no action
    {
        //No correction
    }
    else if (strlen($sMobileWithoutQuoates) > 10)
        return 'ERROR: Not an Indian number';
    else if (strlen($sMobileWithoutQuoates) < 10)
        return 'ERROR: Invalid Number';

    if (isset($credentials['msg91_sms_auth_key']) && (!empty($sMobileWithoutQuoates)))
    {
        //https://docs.msg91.com/p/tf9GTextN/e/Irz7-x1PK/MSG91
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.msg91.com/api/v5/flow/",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => "{\"flow_id\":\"61eaad991b9eac381049fff2\",\"sender\":\"CANGRA\",\"mobiles\":\"{$sMobileWithoutQuoates}\",\"name\":\"{$sName}\",\"token\":\"{$sTokenInterest}{$id}\"}",
          CURLOPT_HTTPHEADER => array(
            "authkey: {$credentials['msg91_sms_auth_key']}",
            "content-type: application/JSON"
          ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) error_log("Error: cURL: " . $err);
        else return 'SUCCESS: '.$response;
    }
    return 'ERROR: SMS NOT SENT';
}

/*
    Check if a session user id exist or not. If not set redirect
    to login page. If the user session id exist and there's found
    $_GET['logout'] in the query string logout the user
*/

function validateSessionParams()
{
    // the user want to logout
    if (isset($_GET['logout'])) {
        logout(false);
    }

    //Check whether session params already set
    if ((!isset($_SESSION[$GLOBALS['keyColumn']])) || (!isset($_SESSION['id_login'])))
    {
        //Save Current URL in session so that we can forward to it after login
        $_SESSION['fwd'] =  'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        //$sEmailInSession = isset($_SESSION['sEmail']) ? $_SESSION['sEmail'] : '';
        //error_log("Relogin-{$sEmailInSession}: ".json_encode(debug_backtrace()));
        header('Location: ' . WEB_ROOT . 'signin.php');
        exit;
    }

    //Update Session key params every 15 minutes or 900 seconds from database to ensure that no removed member gets access
    $currentTime = nowTimeForSql();
    if (!isset($_SESSION['recentActivityTime'])) $_SESSION['recentActivityTime'] = $currentTime;

    //Check for login after last check was more than 5 minutes ago
    if (!(isDateTimeInRange($_SESSION['recentActivityTime'], 5, $currentTime)))
    {
        $sql = "SELECT * FROM login WHERE ({$GLOBALS['keyColumn']}='{$_SESSION[$GLOBALS['keyColumn']]}') AND (IF (tForceReLogin IS NULL, TRUE, (tForceReLogin < '{$_SESSION['recentActivityTime']}'))) AND (login.iaEntryStatus=0) AND (login.edStatus=2)";
        $result = dbQuery($sql);
        if (($result == false) || (mysqli_num_rows($result)==0))
        {
            error_log("validateSessionParams: ForcedReLogin {$_SESSION[$GLOBALS['keyColumn']]}");
            logout();
        }
        else
            $_SESSION['recentActivityTime'] = $currentTime;

        //Refresh combined role permission if some role's permissions got changed in userconfig.php
        if (isset($_SESSION['arrayUserTypeCombinedRole']))
        {
            $_SESSION['combinedRole'] = getlistTableUserPermissionForCombinedRole($_SESSION['arrayUserTypeCombinedRole']);
        }
        else
        {
            error_log("validateSessionParams: CombinedRole {$_SESSION[$GLOBALS['keyColumn']]}");
            logout();
        }

    }// else error_log('Inrange: '.$currentTime.$_SESSION['recentActivityTime']);
}

function forceReloginOtherUser($sEmail)
{
    $currentTime_SQL = makeSafeAndSingleQuote(nowTimeForSql());
    $sEmail_SQL = makeSafeAndSingleQuote($sEmail);
    $sql = "UPDATE login SET login.tForceReLogin={$currentTime_SQL} WHERE login.sEmail = {$sEmail_SQL}";
    $numRowsAffected = queryUpdate($sql);
    return $numRowsAffected;
}

function checkRole($bitmap)
{
    if (isset($_SESSION['activeRoleBitmap']) && ($_SESSION['activeRoleBitmap'] & $bitmap))
        return TRUE;
    else FALSE;
}

//Check if user has Employee profile even if not current profile
function isEmployee()
{
    if (isset($_SESSION['isEmployee']) && ($_SESSION['isEmployee']==TRUE))
        return TRUE;
    else FALSE;
}
//Check if current usertype is of a employee
function isUserTypeEmployee()
{
    global $sUserType, $listProfile, $listHomePage;
//$listProfile = ['guest', 'customer', 'interviewer', 'jobseeker', 'consultant', 'recruiter', 'employee'];
//$listHomePage = ['guest', 'customer', 'interviewer', 'jobseeker', 'consultant', 'recruiter', 'general', 'coord', 'networker', 'sales', 'hr', 'accounts', 'care', 'management'];
    for($i=count($listProfile)-1; $i<count($listHomePage); $i++)
    {
        if ($sUserType == $listHomePage[$i])
            return TRUE;
    }
    return FALSE;
}

//Restart session after reseting it taking care of keep forward
function resetSessionWithKeepForward($isForwardAfterLogin = true)
{
    sessionStart();
    $fwd = null;
    if (isset($_SESSION['fwd']))
    {
        $fwd = $_SESSION['fwd'];
    }
    session_unset();
    session_destroy();
    sessionStart();
    //Save Provided URL in session so that we can forward to it after login
    if ($isForwardAfterLogin)
    {
        if ($fwd != null )
            $_SESSION['fwd'] =  $fwd;
        else
        {
            $sQueryInUrl = $_SERVER['QUERY_STRING'];
            $sQueryPrefix = (empty($sQueryInUrl)) ? "?" : "&";
            $sActInUrl = (isset($_POST['act'])) ? "{$sQueryPrefix}act={$_POST['act']}" :"";
            $_SESSION['fwd'] =  'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}{$sActInUrl}";
        }
    }
}

// Logout a user with reset session
function logout($isForwardAfterLogin = true)
{
    $act = isset($_REQUEST['act']) ? ($_REQUEST['act']) : '';
    //Save stack trace in log if logout not due to signOut button
    if ($act != 'logout')
    {
        $sEmailInSession = isset($_SESSION['sEmail']) ? $_SESSION['sEmail'] : '';
        error_log("Relogin-{$sEmailInSession}: ".json_encode(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)));
    }
    resetSessionWithKeepForward($isForwardAfterLogin);
    header('Location: ' . WEB_ROOT . 'signin.php');
    exit;
}

//Check whether a index bit is set in bitmap or not. It can also retun 'true' 'false' string if required
function isBitSetInBitmap($iBitmap, $iIndex, $isReturnInStringFormat=false)
{
    if (empty($iBitmap)) return false;
    $isBitSet = (($iBitmap & (1 << $iIndex)) > 0) ? true : false;
    if ($isReturnInStringFormat) //where result is printed in javascript
        return ($isBitSet ? 'true' : 'false');
    else return $isBitSet;
}

function bitsToBitmap($arrayBits)
{
    $bitmap = 0;
    if ($arrayBits == null) return $bitmap;
    if (gettype($arrayBits) != 'array') return $bitmap;
    for($i=0; $i<count($arrayBits); $i++)
    {
        $bitmap |= (1<<$arrayBits[$i]);
    }
    return $bitmap;
}

//Convert Select multiple select options to string with comma
function multiSelectToString($arrOptions)
{
    $select = '';
    if (gettype($arrOptions) == 'array')
    {
        $select = implode(',', $arrOptions);
        /*
        $cnt = count($arrOptions);
        for($i=0; $i < $cnt-1; $i++)
        {
            $select .= $arrOptions[$i].',';
        }
        $select .= $arrOptions[$i];
        */
    }
    return $select;
}

//Convert Select multiple select options to string with comma
function multiOptionsToSelectString($arrOptions)
{
    $select = '';
    if (gettype($arrOptions) == 'array')
    {
        $select = implode(',', $arrOptions);
    }
    return $select;
    /*
    $select = '';
    if (gettype($arrOptions) == 'array')
    {
        $cnt = count($arrOptions);
        for($i=0; $i < $cnt; $i++)
        {
            $select .= $arrOptions[$i].',';
        }
    }
    return substr($select, 0, -1);
    */
}

//Convert Select multiple options to string with comma. NOTE: To avoid re-processing existing values, $existingMdd is being utilized
function multiSelectToMdd($tableName, $existingMdd, $arrSelect)
{
    //Partial IDs can't pass IN in select query so saving data as id1,id2,id3
    $select = '';
    if (gettype($arrSelect) == 'array')
    {
        $existingMddExploded = explode(',', $existingMdd);
        $cntExistingMddExploded = count($existingMddExploded);
        $cntArr = count($arrSelect);

        for($i=0; $i < $cntArr; $i++)
        {
            $sIdxId = $arrSelect[$i];
            $id = extractId($sIdxId);
            for ($k=0; $k < $cntExistingMddExploded; $k++)
            {
                if ($id == $existingMddExploded[$k])
                {
                    $select .= $id.',';
                    break;
                }
            }
            //if select value is not existing then confirm sIdx before saving it
            if ($k == $cntExistingMddExploded)
            {
                $sql = "SELECT sIdx FROM {$tableName} WHERE id='{$id}'";
                $result = querySelect($sql);
                $sIdx = extractToken($sIdxId);
                if ((count($result) > 0) && ($result[0]['sIdx'] == $sIdx))
                {
                    $select .= $id.',';
                }
            }
        }
    }
    return substr($select, 0, -1);
}

function mddToObjectNames($columnName, $mdd)
{
    if (empty($mdd)) return '';
    $mdd = makeSafe($mdd);
    $explodedTableName = explode('_',$columnName);
    $foreignTableName = $explodedTableName[1];
    $sObjectNames = '';
    $isUserTypeEmployee = isUserTypeEmployee();
    if ($foreignTableName == 'interviewer')
    {
        $sLinkIvrDetailsWithoutId = CONTROL_ROOT."interviewer.php?act=detail&id=";
        //$result = querySelect("SELECT interviewer.id, interviewer.sTokenDetails, interviewer.sName, interviewer.sMobile, interviewer.edStatus FROM interviewer WHERE interviewer.id IN ({$mdd})");
        $result = querySelect("SELECT interviewer.id, interviewer.sTokenDetails, interviewer.sName, interviewer.sMobile, interviewer.edStatus FROM interviewer WHERE (FIND_IN_SET(interviewer.id, '{$mdd}') > 0) AND (interviewer.edStatus=3)");
        for ($j=0; $j < count($result); $j++)
        {
            if ($isUserTypeEmployee)
            {
                $betaGammaPrefix = ($result[$j]['edStatus'] > 3) ? 'Gamma-' : '';
                $sObjectNames .= "<a target=\"blank\" href=\"{$sLinkIvrDetailsWithoutId}{$result[$j]['sTokenDetails']}{$result[$j]['id']}\">".$betaGammaPrefix.firstWord($result[$j]['sName']).'-'.substr($result[$j]['sMobile'], -4).'</a>, ';
            }
            else
                $sObjectNames .= firstWord($result[$j]['sName']).'-'.substr($result[$j]['sMobile'], -4).'; ';

        }
    }
    else
    {
        $isNameFromMdd = true;
        $arrMddEntries = getDropdownRows($foreignTableName, " (FIND_IN_SET({$foreignTableName}.id, '{$mdd}') > 0) ", null, null, null, $isNameFromMdd);
        for ($j=0; $j < count($arrMddEntries); $j++)
        {
            $sObjectNames .= ($arrMddEntries[$j]['sName']).'; ';
        }
        /*
        $mddExploded = explode(',', $mdd);
        $cntMddExploded = count($mddExploded);
        for($i=0; $i < $cntMddExploded; $i++)
        {
            //Get Name of the object Not complete dropdown
            if (empty($mddExploded[$i])) continue;
            $result = getDropdownRows($foreignTableName, "id='{$mddExploded[$i]}'", null, null, null);
            if (count($result) > 0)
                $sObjectNames .= $result[0]['sName'].'; ';
        }
        */
    }
    //return $sObjectNames;
    //echo rtrim($sObjectNames, "; ");
    return  rtrim($sObjectNames, "; ");
}

function mddToObjectNamesIvr($mdd, $id_jd=null)
{
    if (empty($mdd)) return '';
    $mdd = makeSafe($mdd);
    $sObjectNames = '';
    $isUserTypeEmployee = isUserTypeEmployee();
    $sLinkIvrDetailsWithoutId = CONTROL_ROOT."interviewer.php?act=detail&id=";
    //$result = querySelect("SELECT interviewer.id, interviewer.sTokenDetails, interviewer.sName, interviewer.sMobile, interviewer.edStatus FROM interviewer WHERE interviewer.id IN ({$mdd})");
    //get blocked interviewers for jd from ivrblock
    /*
    $arrResult = querySelect("SELECT idd_interviewer FROM ivrblock WHERE idd_jd = {$id_jd}");
    $arrBlockedIvr = array();
    for ($i = 0; $i < count($arrResult); $i++)
    {
        $arrBlockedIvr[$i] = $arrResult[$i]['idd_interviewer'];
    }*/

    //if related id_jd is provided to checkblocked jd
    if (empty($id_jd))
    {
        $sql = "SELECT interviewer.id, interviewer.sTokenDetails, interviewer.sName, interviewer.sMobile, interviewer.edStatus, NULL as jdBlockedStatus FROM interviewer WHERE (FIND_IN_SET(interviewer.id, '{$mdd}') > 0) AND (interviewer.edStatus=3)";
    }
    else
    {
        $sql = "SELECT interviewer.id, interviewer.sTokenDetails, interviewer.sName, interviewer.sMobile, interviewer.edStatus, ivrblock.edStatus AS jdBlockedStatus FROM interviewer LEFT JOIN ivrblock ON ((ivrblock.idd_interviewer = interviewer.id) AND (ivrblock.idd_jd = {$id_jd})) WHERE (FIND_IN_SET(interviewer.id, '{$mdd}') > 0) AND (interviewer.edStatus=3)";
    }
    //echo $sql; //exit;
    $result = querySelect($sql);
    for ($j=0; $j < count($result); $j++)
    {
        //if panel is blocked then do not show blocked panel
        //jdBlockedStatus=0 (0: blocked, 1: Unblocked, null: not defined)
        $sBlocked = '';
        $jdBlockedStatus = $result[$j]['jdBlockedStatus'];
        if ( ($jdBlockedStatus == 0) && ($jdBlockedStatus != null) )
            $sBlocked = "(Blocked For JD)";
        if ($isUserTypeEmployee)
        {
            $betaGammaPrefix = ($result[$j]['edStatus'] > 3) ? 'Gamma-' : '';
            $sObjectNames .= "<a target=\"blank\" href=\"{$sLinkIvrDetailsWithoutId}{$result[$j]['sTokenDetails']}{$result[$j]['id']}\">".$betaGammaPrefix.firstWord($result[$j]['sName']).'-'.substr($result[$j]['sMobile'], -4)."</a> <span class=\"text-danger\">{$sBlocked} </span>, ";
        }
        else
            $sObjectNames .= firstWord($result[$j]['sName']).'-'.substr($result[$j]['sMobile'], -4).'; ';
    }
    //return $sObjectNames;
    //echo rtrim($sObjectNames, "; ");
    return  rtrim($sObjectNames, "; ");
}

//search for exact id=2 in '3,22,5'
function isIdPresentInMdd($id, $sMddCommaSeperated)
{
    $arrMddExploded = explode(',', $sMddCommaSeperated);
    return in_array($id, $arrMddExploded);
}

//To find the second part of "title: name" format
function jdNameWithoutCorp($statement)
{
    if (empty($statement)) return '';
    else
    {
        $exploded = explode(': ',trim($statement));
        if (count($exploded) > 1) return $exploded[1];
        else return $statement;
    }
}

function wordsAfterDash($statement)
{
    if (empty($statement)) return '';
    else return explode('-',trim($statement))[1];
}

function strStartsWith($string, $subString)
{
    $len = strlen($subString);
    return (substr($string, 0, $len) === $subString);
}

function strEndsWith($string, $subString)
{
    $len = strlen($subString);
    if ( !$len ) {
        return true;
    }
    return (substr($string, -$len) === $subString);
}

$base32Chars = 'abcdefghijklmnopqrstuvwxyz123456';
function getRandomString($length)
{
    global $base32Chars;
    $randStr = $base32Chars;
    for($i=0;$i<$length;$i++)
    {
        $randStr[$i] = $base32Chars[mt_rand(0,31)];
    }
    return substr($randStr,0,$length);
}

//Cover 12345 as ***1***2***3***4***5***. lengthPadding = Number of stars
function paddingByRandomString($key,$lengthPadding)
{
    global $base32Chars;
    $randStr = str_pad('', strlen($key)*(1+$lengthPadding));
    $charCount=0;
    for($i=0;$i<strlen($key);$i++)
    {
        for($j=0; $j<$lengthPadding; $j++)
            $randStr[$charCount++] = $base32Chars[mt_rand(0,31)];
        $randStr[$charCount++] = $key[$i];
    }
    for($j=0; $j<$lengthPadding; $j++)
        $randStr[$charCount++] = $base32Chars[mt_rand(0,31)];
    return $randStr;
}

//This is called when invalid tokens and Ids are provided by user
function hackingAttempt($sOtherMessage = null, $isExit = true)
{
    $sMessage = $sOtherMessage."Regular invalid request may lead to blocking of IP address.";
    $url =  'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
    error_log("Hacking Attempt from IP:". get_ip_address().' URL: '.$url);
    web_error_message("CAUTION", $sMessage, $isExit);
}

//This is called when invalid tokens and Ids are provided by user
function hackingAttemptByAPI($sLogMessage = null, $isExit = true)
{
    //Do not send any error message
    $url =  'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
    error_log("Hacking Attempt from IP:". get_ip_address().' URL: '.$url.'  '.$sLogMessage);
    if ($isExit)
        exit();
    else return;
}
/*
function getCallingLink($sMobile)
{
    $len = strlen($sMobile);
    if ($len<10) return $sMobile;
    else if ($len==10) $prefix = "+91";
    else $prefix = "";
    //return "<a class=\"btn btn-md btn-outline-secondary\" href=\"tel:{$prefix}{$sMobile}\"><span class=\"im-phone fa-lg\"></span> {$sMobile}</a>";
    return "<a class=\"my-3\" href=\"tel:{$prefix}{$sMobile}\">{$sMobile}</a>";
}
*/
/*
function getCallingLink($sMobile, $isHref=true)
{
    $sMobileCallingLinks = "";
    if (strStartsWith($sMobile, '[{'))
    {
        $sError = null;
        //Get array of contact stdClass objects by json decode
        $arrStdObjects = decodeJsonAndCheckError($sMobile, $sError);
        if (!empty($sError)) return $sMobile;
        $arrLen = count($arrStdObjects);
        for ($i = 0; $i < $arrLen; $i++)
        {
            $stdObj = $arrStdObjects[$i];
            if (!empty($stdObj->mob)) //(!empty($arrStdObjects[$i]['mob']))
            {
                $sMobile = escapeHtml($stdObj->isd.$stdObj->mob);
                $sTip = (!empty($stdObj->tip)) ? escapeHtml($stdObj->tip).": " : "";
                if ($isHref == true)
                    $sMobileCallingLinks .= "{$sTip}<a class=\"my-3\" href=\"tel:{$sMobile}\">{$stdObj->isd} {$stdObj->mob}</a>,\n";
                else
                    $sMobileCallingLinks .= "{$sTip}{$stdObj->isd} {$stdObj->mob}, ";
            }
        }
        // to remove last accurance of comma in string
        if ($isHref == true)
           $sMobileCallingLinks = rtrim($sMobileCallingLinks, ",\n");
        else
           $sMobileCallingLinks = rtrim($sMobileCallingLinks, ", ");
        return $sMobileCallingLinks;
    }
    else
    {
        $sMobileArray = explode(",", $sMobile);
        $arrLen = count($sMobileArray);
        for ($i = 0; $i < $arrLen; $i++)
        {
            $sMobile = extractOnlyMobileWithoutIsd($sMobileArray[$i]);
            if (!empty($sMobile))
            {
                if ($isHref == true)
                    $sMobileCallingLinks .= "<a class=\"my-3\" href=\"tel:{$sMobile}\">{$sMobile}</a>,\n";
                else
                    $sMobileCallingLinks .= "{$sMobile}, ";
            }
        }
        //return "<a class=\"btn btn-md btn-outline-secondary\" href=\"tel:{$prefix}{$sMobile}\"><span class=\"im-phone fa-lg\"></span> {$sMobile}</a>";
        //return "<a class=\"my-3\" href=\"tel:{$sMobile}\">{$sMobile}</a>";
        if ($isHref == true)
           $sMobileCallingLinks = rtrim($sMobileCallingLinks, ",\n");
        else
           $sMobileCallingLinks = rtrim($sMobileCallingLinks, ", ");
        return $sMobileCallingLinks;

    }
}
*/

function getCallingLink($sMobile, $isHref=true, $isForWhatsApp=false)
{
    $sMobileCallingLinks = "";
    if (strStartsWith($sMobile, '[{'))
    {
        $sError = null;
        //Get array of contact stdClass objects by json decode
        $arrStdObjects = decodeJsonAndCheckError($sMobile, $sError);
        if (!empty($sError)) return $sMobile;
        $arrLen = count($arrStdObjects);

        //in whatsapp button only one mobile number is needed if $isForWhatsApp is true then first element of array of mobile numbers will be returned in proper format, by default $isForWhatsApp is false it will return calling links for each mobile number
        if ($isForWhatsApp == true)
        {
            $stdObj = $arrStdObjects[0];
            if (!empty($stdObj->mob)) //(!empty($arrStdObjects[$i]['mob']))
            {
                $sMobile = escapeHtml($stdObj->isd.$stdObj->mob);
                return $sMobile;
            }
        }

        for ($i = 0; $i < $arrLen; $i++)
        {
            $stdObj = $arrStdObjects[$i];
            if (!empty($stdObj->mob)) //(!empty($arrStdObjects[$i]['mob']))
            {
                $sMobile = escapeHtml($stdObj->isd.$stdObj->mob);
                $sTip = (!empty($stdObj->tip)) ? escapeHtml($stdObj->tip).": " : "";
                if ($isHref == true)
                    $sMobileCallingLinks .= "{$sTip}<a class=\"my-3\" href=\"tel:{$sMobile}\">{$stdObj->isd} {$stdObj->mob}</a>,\n";
                else
                    $sMobileCallingLinks .= "{$sTip}{$stdObj->isd} {$stdObj->mob}, ";
            }
        }
        // to remove last accurance of comma in string
        if ($isHref == true)
           $sMobileCallingLinks = rtrim($sMobileCallingLinks, ",\n");
        else
           $sMobileCallingLinks = rtrim($sMobileCallingLinks, ", ");
        return $sMobileCallingLinks;
    }
    else
    {
        $sMobileArray = explode(",", $sMobile);
        $arrLen = count($sMobileArray);
        //in whatsapp button only one mobile number is needed if $isForWhatsApp is true then first element of array of mobile numbers will be returned in proper format, by default $isForWhatsApp is false it will return calling links for each mobile number
        if ($isForWhatsApp == true)
        {
            $sMobile = getMobileWithIsd($sMobileArray[0]);
            return $sMobile;
        }
        for ($i = 0; $i < $arrLen; $i++)
        {
            $sMobile = extractOnlyMobileWithoutIsd($sMobileArray[$i]);//TODO: Why ISD is removed
            if (!empty($sMobile))
            {
                if ($isHref == true)
                    $sMobileCallingLinks .= "<a class=\"my-3\" href=\"tel:{$sMobile}\">{$sMobile}</a>,\n";
                else
                    $sMobileCallingLinks .= "{$sMobile}, ";
            }
        }
        //return "<a class=\"btn btn-md btn-outline-secondary\" href=\"tel:{$prefix}{$sMobile}\"><span class=\"im-phone fa-lg\"></span> {$sMobile}</a>";
        //return "<a class=\"my-3\" href=\"tel:{$sMobile}\">{$sMobile}</a>";
        if ($isHref == true)
           $sMobileCallingLinks = rtrim($sMobileCallingLinks, ",\n");
        else
           $sMobileCallingLinks = rtrim($sMobileCallingLinks, ", ");
        return $sMobileCallingLinks;

    }
}

function isMobileHasIsd($sMobile)
{
    return strStartsWith($sMobile, "+");
}
/*
function getMobileWithIsd($sMobile)
{
    $len = strlen($sMobile);
    if ($len < 10) return $sMobile;
    else if ($len == 10) $prefix = "+91";
    else $prefix = "";
    return $prefix.$sMobile;
}
*/
function getMobileWithIsd($sMobile, $sIsdSelected='+91') //default india
{
    //$sMobile is without ISD code
    if (empty($sMobile)) return '';
    //handle +19876543210(USA), +91-987 654 3210, (+91)9876543210, 09876543210 and numbers with non-numerical characters in between
    //if number starts with if+ then return as it is considering it already has ISD code
    $sIsdCode = '';
    $regex = '/[^0-9+]/';
    $sMobile = preg_replace($regex, '', trim($sMobile));
    //remove all 0 from left(start)
    $sMobile = ltrim($sMobile, "0");
    if (strStartsWith($sMobile, "+")) return $sMobile;
    $len = strlen($sMobile);
    $sIsdCode = $sIsdSelected;
    return $sIsdCode.$sMobile;
}

function extractOnlyMobileWithoutIsd($sMobile)//ct
{
    if (empty($sMobile)) return null;
    //handle +19876543210(USA), +91-987 654 3210, (+91)9876543210, 09876543210 and numbers with non-numerical characters in between
    //if number starts with if+ then return as it is considering it already has ISD code
    $regex = '/[^0-9+]/';
    $sMobile = preg_replace($regex, '', trim($sMobile));
    //remove all 0 from left(start)
    $sMobile = ltrim($sMobile, "0");
    if (strStartsWith($sMobile, "+91")) return substr($sMobile, 3);
    else if (strStartsWith($sMobile, "+1")) return substr($sMobile, 2);
    return $sMobile;
}

function get_ip_address() {
    // check for shared internet/ISP IP
    if (!empty($_SERVER['HTTP_CLIENT_IP']) && validate_ip($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }

    // check for IPs passing through proxies
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // check if multiple ips exist in var
        if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {
            $iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($iplist as $ip) {
                if (validate_ip($ip))
                    return $ip;
            }
        } else {
            if (validate_ip($_SERVER['HTTP_X_FORWARDED_FOR']))
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED']) && validate_ip($_SERVER['HTTP_X_FORWARDED']))
        return $_SERVER['HTTP_X_FORWARDED'];
    if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && validate_ip($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
        return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
    if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && validate_ip($_SERVER['HTTP_FORWARDED_FOR']))
        return $_SERVER['HTTP_FORWARDED_FOR'];
    if (!empty($_SERVER['HTTP_FORWARDED']) && validate_ip($_SERVER['HTTP_FORWARDED']))
        return $_SERVER['HTTP_FORWARDED'];

    // return unreliable ip since all else failed
    return $_SERVER['REMOTE_ADDR'];
}

/**
 * Ensures an ip address is both a valid IP and does not fall within
 * a private network range.
 */
function validate_ip($ip) {
    if (strtolower($ip) === 'unknown')
        return false;

    // generate ipv4 network address
    $ip = ip2long($ip);

    // if the ip is set and not equivalent to 255.255.255.255
    if ($ip !== false && $ip !== -1) {
        // make sure to get unsigned long representation of ip
        // due to discrepancies between 32 and 64 bit OSes and
        // signed numbers (ints default to signed in PHP)
        $ip = sprintf('%u', $ip);
        // do private network range checking
        if ($ip >= 0 && $ip <= 50331647) return false;
        if ($ip >= 167772160 && $ip <= 184549375) return false;
        if ($ip >= 2130706432 && $ip <= 2147483647) return false;
        if ($ip >= 2851995648 && $ip <= 2852061183) return false;
        if ($ip >= 2886729728 && $ip <= 2887778303) return false;
        if ($ip >= 3221225984 && $ip <= 3221226239) return false;
        if ($ip >= 3232235520 && $ip <= 3232301055) return false;
        if ($ip >= 4294967040) return false;
    }
    return true;
}

function createEvent($tableName, $id, $xdEventType, $sParam0, $sParam1, $sParam2, $sParam3, $sParam4, $sParam5)
{
    $id_login = isset($_SESSION['id_login']) ? $_SESSION['id_login'] : 0;
    //$sTokenEdit = getRandomString(16);
    //$sTokenDetails = getRandomString(16);
    //$sIdx = getRandomString(16);
    $sParam0_SQL = makeSafeAndSingleQuote($sParam0);
    $sParam1_SQL = makeSafeAndSingleQuote($sParam1);
    $sParam2_SQL = makeSafeAndSingleQuote($sParam2);
    $sParam3_SQL = makeSafeAndSingleQuote($sParam3);
    $sParam4_SQL = makeSafeAndSingleQuote($sParam4);
    $sParam5_SQL = makeSafeAndSingleQuote($sParam5);
    $sql = "INSERT INTO eventlog (xdEventType, id_login, sTable, iaItem, sParam0, sParam1, sParam2, sParam3, sParam4, sParam5) VALUES ({$xdEventType}, {$id_login}, '{$tableName}', '{$id}', {$sParam0_SQL}, {$sParam1_SQL}, {$sParam2_SQL}, {$sParam3_SQL}, {$sParam4_SQL}, {$sParam5_SQL})";

    dbQuery($sql);
}

function createActiveLog($enum_table_hash, $id, $xdActivityType, $iaSubType=null, $iaResult=null, $iaTableRef=null, $iaItemRef=null)
{
    //skip ACTIVITY_DASHBOARD activity
    if ($xdActivityType == ACTIVITY_DASHBOARD) return;

    $id_login = isset($_SESSION['id_login']) ? $_SESSION['id_login'] : 0;
    $enum_table_hash_SQL = makeSafeAndSingleQuote($enum_table_hash);
    $xdActivityType = makeSafeAndSingleQuote($xdActivityType);
    $id_SQL = makeSafeAndSingleQuote($id);
    $iaSubType = makeSafeAndSingleQuote($iaSubType);
    $iaResult = makeSafeAndSingleQuote($iaResult);
    $iaTableRef = makeSafeAndSingleQuote($iaTableRef);
    $iaItemRef = makeSafeAndSingleQuote($iaItemRef);
    $sql = "INSERT INTO activelog (xdActivityType, id_login, iaTable, iaItem, iaSubType, iaResult, iaTableRef, iaItemRef) VALUES ({$xdActivityType}, {$id_login}, {$enum_table_hash_SQL}, {$id_SQL}, {$iaSubType},  {$iaResult} , {$iaTableRef}, {$iaItemRef} )";
    dbQuery($sql);
}

function isValidRequest($parameter)
{
    if (isset($_REQUEST[$parameter]) && ($_REQUEST[$parameter] != '') && ($_REQUEST[$parameter] != 'null') && ($_REQUEST[$parameter] != 'NULL'))
        return true;
    else return false;
}

function getFileAbsoutePath($sFilePath)
{
    if (empty($sFilePath)) return '';
    //path: https://www.cangra.in/files/CV_Jd4651_wfzg_Manjunath11y_4m(1).doc
    else if (strStartsWith($sFilePath, 'http')) return $sFilePath;
    //path: ./../../files/CV_Jd5281_SakshamSrivastava_mmln.pdf
    else return WEB_ROOT.substr($sFilePath, 8);
}

//Using composer.json
function loadComposerPsr4Package($dir)
{
    $composer = json_decode(file_get_contents("{$dir}/composer.json"), 1);
    $namespaces = $composer['autoload']['psr-4'];

    // Foreach namespace specified in the composer, load the given classes
    foreach ($namespaces as $namespace => $classpaths) {
        if (!is_array($classpaths)) {
            $classpaths = array($classpaths);
        }
        spl_autoload_register(function ($classname) use ($namespace, $classpaths, $dir) {
            // Check if the namespace matches the class we are looking for
            if (preg_match("#^".preg_quote($namespace)."#", $classname)) {
                // Remove the namespace from the file path since it's psr4
                $classname = str_replace($namespace, "", $classname);
                $filename = preg_replace("#\\\\#", "/", $classname).".php";
                foreach ($classpaths as $classpath) {
                    $fullpath = $dir."/".$classpath."/{$filename}";
                    if (file_exists($fullpath)) {
                        include_once $fullpath;
                    }
                }
            }
        });
    }
}

//get min cand exp for panel
function getCandExpMinForPanel($iExpPanel, $iIvrBonus=500)
{
/*
Panel   Bonus   Cand_Min  Cand_Max
0-8.9     -      0.0      (iExp-3)
9-10.9    -      1.0      (iExp-3)
11-12.9   <551   2.0      (iExp-2)
11-12.9   >551   3.0      (iExp-1)
13-14.9   <551   3.0      (iExp)
13-14.9   >551   7.0      (iExp+1)
15 and +  <551   7.0      (iExp+2)
15 and +  >551   7.0      (iExp+3)
*/
    if ($iExpPanel < 9) $iCndExpMin = 0;
    else if ($iExpPanel < 11) $iCndExpMin = 1;
    else if (($iExpPanel < 13) && ($iIvrBonus <  551)) $iCndExpMin = 2;
    else if (($iExpPanel < 13) && ($iIvrBonus >  551)) $iCndExpMin = 3;
    else if (($iExpPanel < 15) && ($iIvrBonus <  551)) $iCndExpMin = 3;
    else if (($iExpPanel < 15) && ($iIvrBonus >  551)) $iCndExpMin = 7;
    else $iCndExpMin = 7;
    return $iCndExpMin;
}

//get max cand exp for panel
function getCandExpMaxForPanel($iExpPanel, $iIvrBonus=500)
{
    if ($iExpPanel < 9) $iCndExpMax = $iExpPanel-3;
    else if ($iExpPanel < 11) $iCndExpMax = $iExpPanel-3;
    else if (($iExpPanel < 13) && ($iIvrBonus <  551)) $iCndExpMax = $iExpPanel-2;
    else if (($iExpPanel < 13) && ($iIvrBonus >  551)) $iCndExpMax = $iExpPanel-1;
    else if (($iExpPanel < 15) && ($iIvrBonus <  551)) $iCndExpMax = $iExpPanel;
    else if (($iExpPanel < 15) && ($iIvrBonus >  551)) $iCndExpMax = $iExpPanel+1;
    else $iCndExpMax = $iExpPanel+3;
    return $iCndExpMax;
}

//get Max And Critical Bonus For Iv
function getMaxAndCriticalBonusForIv($id)
{
    $sql = "SELECT (SELECT pricing.iCorpFee FROM pricing WHERE ((pricing.iaEntryStatus=0) AND (pricing.idd_corporate=interview.id_corporate) AND (pricing.sName=jd.sPricing) AND (pricing.iCandBottomExp<=interview.iExp) AND (pricing.iIvrBottomExp<=10)) ORDER BY pricing.iCorpFee DESC LIMIT 1) AS revenue, interview.sName, interview.iExp, corporate.xrIs_Premium FROM interview LEFT JOIN jd ON (jd.id = interview.idd_jd) LEFT JOIN corporate ON (corporate.id=interview.id_corporate) WHERE (interview.id={$id})";
    $result = querySelectObject($sql);
    $revenue = null;
    $xrIs_Premium = null;
    if ($result != null)
    {
        $revenue = $result['revenue'];
        $xrIs_Premium = $result['xrIs_Premium'];
    }
    //Default if pricing is not defined
    if ($revenue == null) $revenue = 1000;

    //Copy this table from 'Bonu' sheet in online sheet 'CANGRA Web and App Requirements'
    //(revenue, maxBonus, criticalBonus, PremiumMaxBonus, PremiumCritialBonus)
    $maxBonusPercent = array(
array(750, /*400*/ 54, /*400*/ 54, /*500*/ 67, /*500*/ 67),
array(800, /*500*/ 63, /*400*/ 50, /*500*/ 63, /*500*/ 63),
array(850, /*500*/ 59, /*500*/ 59, /*500*/ 59, /*500*/ 59),
array(900, /*500*/ 56, /*500*/ 56, /*500*/ 56, /*500*/ 56),
array(950, /*500*/ 53, /*500*/ 53, /*600*/ 64, /*600*/ 64),
array(1000, /*500*/ 50, /*600*/ 60, /*600*/ 60, /*600*/ 60),
array(1050, /*500*/ 48, /*600*/ 58, /*600*/ 58, /*600*/ 58),
array(1100, /*500*/ 46, /*600*/ 55, /*600*/ 55, /*600*/ 55),
array(1150, /*500*/ 44, /*700*/ 61, /*700*/ 61, /*800*/ 70),
array(1200, /*500*/ 42, /*700*/ 59, /*700*/ 59, /*800*/ 67),
array(1250, /*600*/ 48, /*700*/ 56, /*800*/ 64, /*800*/ 64),
array(1300, /*600*/ 47, /*800*/ 62, /*800*/ 62, /*800*/ 62),
array(1350, /*600*/ 45, /*800*/ 60, /*800*/ 60, /*800*/ 60),
array(1400, /*600*/ 43, /*800*/ 58, /*800*/ 58, /*900*/ 65),
array(1450, /*600*/ 42, /*800*/ 56, /*800*/ 56, /*900*/ 63),
array(1500, /*600*/ 40, /*800*/ 54, /*800*/ 54, /*900*/ 60),
array(1550, /*700*/ 46, /*800*/ 52, /*800*/ 52, /*1000*/ 65),
array(1600, /*700*/ 44, /*800*/ 50, /*800*/ 50, /*1000*/ 63),
array(1700, /*700*/ 42, /*800*/ 48, /*800*/ 48, /*1000*/ 59),
array(1750, /*700*/ 40, /*800*/ 46, /*800*/ 46, /*1100*/ 63),
array(1800, /*800*/ 45, /*1000*/ 56, /*1000*/ 56, /*1100*/ 62),
array(1850, /*800*/ 44, /*1000*/ 55, /*1000*/ 55, /*1200*/ 65),
array(1900, /*800*/ 43, /*1000*/ 53, /*1000*/ 53, /*1200*/ 64),
array(1950, /*800*/ 42, /*1000*/ 52, /*1000*/ 52, /*1200*/ 62),
array(2000, /*800*/ 40, /*1000*/ 50, /*1000*/ 50, /*1200*/ 60),
array(2050, /*800*/ 40, /*1000*/ 49, /*1000*/ 49, /*1200*/ 59),
array(2100, /*900*/ 43, /*1000*/ 48, /*1000*/ 48, /*1200*/ 58),
array(2150, /*900*/ 42, /*1000*/ 47, /*1000*/ 47, /*1200*/ 56),
array(2350, /*900*/ 39, /*1000*/ 43, /*1000*/ 43, /*1200*/ 52),    );

    //Now walk through above table to find nearest revenue and its max bonus for corporate based on whether premium
    $cnt = count($maxBonusPercent);
    $xrIs_Premium = ($xrIs_Premium > 0) ? 3 : 1;//Index 3,4 OR 1,2
    $iMaxBonusPercent = 50; $iCriticalBonusPercent = 60; //
    for ($i=0; $i < $cnt; $i++)
    {
        if ($revenue >= $maxBonusPercent[0])
        {
            $iMaxBonusPercent = $maxBonusPercent[$xrIs_Premium];//Index 3 or 1
            $iCriticalBonusPercent = $maxBonusPercent[$xrIs_Premium+1]; // index 4 or 2
        }
    }

    return array('iMaxBonus' => ROUND($revenue * $iMaxBonusPercent / 100),
    'iCriticalBonus' => ROUND($revenue * $iCriticalBonusPercent / 100));
}

// split email into name, email: 'person@place.com', 'monarch <themonarch@tgoci.com>','blahblah',   "'doc venture' <doc@venture.com>"
function splitAndCleanNameEmailAddress($sNameEmailWithBracket)
{
    $sName = null;
    $sEmail = $sNameEmailWithBracket;

    // Check if name<email> format
    if (strstr($sNameEmailWithBracket, '<'))
    {
        $exploded_by_lessthan = explode('<', $sNameEmailWithBracket);
        $sName = trim($exploded_by_lessthan[0]);
        $sEmailWithBracket = $exploded_by_lessthan[1];
        $exploded_by_greaterthan = explode('>', $sEmailWithBracket);
        $sEmail = $exploded_by_greaterthan[0];
    }
    //Replace blank characters or unnecessary characters
    $regex = '/[^a-zA-Z0-9+_@.,;\-]+/';
    $sEmailClean = preg_replace($regex, '', $sEmail);
    $sEmailClean = strtolower(trim($sEmailClean));
    return array('sName' => $sName, 'sEmail' => $sEmailClean);
}

//Convert emails with comms and semicolomns to email-name array
function emailsStringToArray($to)
{
    $arrObjNameEmail = array();
    //Replace all semicolon with comma
    $to = str_replace(';', ',', $to);
    $to = str_replace('`', '', $to);
    $to = str_replace('-obs', '', $to);
    $array_after_comma = explode(',', $to);
    foreach($array_after_comma as $address)
    {
        //Get name and email part from full address
        $objNameEmail = splitAndCleanNameEmailAddress($address);//trim($address);
        if (!empty($objNameEmail['sEmail']))
        {
            //Push that email-name value to this array avoiding repetition
            if (!in_array($objNameEmail, $arrObjNameEmail))
            {
                //Push that name-email object to this array
                array_push($arrObjNameEmail, $objNameEmail);
            }
        }
    }
    return $arrObjNameEmail;
}

//Convert emails with comms and semicolomns to email only array
function emailsStringToEmailOnlyArray($to)
{
    $arrEmail = array();
    //Replace all semicolon with comma
    $to = str_replace(';', ',', $to);
    $to = str_replace('`', '', $to);
    $to = str_replace('-obs', '', $to);
    $array_after_comma = explode(',', $to);
    foreach($array_after_comma as $address)
    {
        //Get name and email part from full address
        $objNameEmail = splitAndCleanNameEmailAddress($address);//trim($address);
        if (!empty($objNameEmail['sEmail']))
        {
            //Push that email value to this array avoiding repetition
            if (!in_array($objNameEmail['sEmail'], $arrEmail))
            {
                array_push($arrEmail, $objNameEmail['sEmail']);
            }
        }
    }
    return $arrEmail;
}

//Change Relative path: ./../../pics/iv_20042_b5k5djmwr1ijotxa.jpg  to ./pics/iv_20042_b5k5djmwr1ijotxa.jpg
function getRootRelatedPathFromControlRelatedPath($controlRelatedPath)
{
    //Since stored path is ./../../pics/iv_20042_b5k5djmwr1ijotxa.jpg but we are currently at cangra.com/index.php so above path will not work. We need to change the relative path such that it works on this page
    if (empty($controlRelatedPath)) return null;
    return substr($controlRelatedPath, 6);
}

//If standing at current month 2022-03 then function return ['2022-03', '2022-02', '2022-01','2021-12', '2021-11',........'2021-01', '2020-12', .........]
function getPreviousMonthsNamesArray($noOfMonths)
{
    //Create blank array
    $arrYearMonths = array();
    //Current year
    //$yearCurrent = intval(date("Y"));
    for($i = 0; $i < $noOfMonths; $i++)
    {
        $arrYearMonths[] = date("Y-m", strtotime("-{$i} month"));
    }
    return $arrYearMonths;
}

//Functions to get first and last dates of Financial year based on invoice date.
function startDateOfFinancialYear($tdInvoiceDate = null)
{
    //1 april -31 dec same year
    //1 jan-31 march- year-1
    if ($tdInvoiceDate == null) $tdInvoiceDate = date("Y-m-d");
    $time = strtotime($tdInvoiceDate);
    $iInvoiceYear = intval(date("Y", $time));
    $iInvoiceMonth = intval(date("m", $time));

    //$sInvoiceDateMonth = substr($sInvoiceDate, 5, 5);
    //if ($sInvoiceDateMonth =< '12-31' && $sInvoiceDateMonth >= '04-01')
    if ($iInvoiceMonth < 4)
        $iFinancialYearStart = $iInvoiceYear - 1;
    else
        $iFinancialYearStart = $iInvoiceYear;

    $startDateOfFinancialYear = $iFinancialYearStart."-04-01";

    return $startDateOfFinancialYear;
}

function startYearOfFinancialYear($tdInvoiceDate = null)
{
    //1 april-31 dec same year
    //1 jan-31 march- year-1
    if ($tdInvoiceDate == null) $tdInvoiceDate = date("Y-m-d");
    $time = strtotime($tdInvoiceDate);
    $iInvoiceYear = intval(date("Y", $time));
    $iInvoiceMonth = intval(date("m", $time));
    if ($iInvoiceMonth < 4)
        $iFinancialYearStart = $iInvoiceYear - 1;
    else
        $iFinancialYearStart = $iInvoiceYear;
    return $iFinancialYearStart;
}

function startYearOfPrevFinancialYear($tdInvoiceDate = null)
{
    //1 april-31 dec same year
    //1 jan-31 march- year-1
    if ($tdInvoiceDate == null) $tdInvoiceDate = date("Y-m-d");
    $time = strtotime($tdInvoiceDate);
    $iInvoiceYear = intval(date("Y", $time));
    $iInvoiceMonth = intval(date("m", $time));
    if ($iInvoiceMonth < 4)
        $iFinancialYearStart = $iInvoiceYear - 2;
    else
        $iFinancialYearStart = $iInvoiceYear - 1;
    return $iFinancialYearStart;
}

function lastDateOfFinancialYear($tdInvoiceDate = null)
{
    if ($tdInvoiceDate == null) $tdInvoiceDate = date("Y-m-d");
    $time = strtotime($tdInvoiceDate);
    $iInvoiceYear = intval(date("Y", $time));
    $iInvoiceMonth = intval(date("m", $time));

    if ($iInvoiceMonth < 4)
        $iFinancialYearEnd = $iInvoiceYear;
    else
        $iFinancialYearEnd = $iInvoiceYear + 1;

    $lasttDateOfFinancialYear = $iFinancialYearEnd."-03-31";

    return $lasttDateOfFinancialYear;

}

function lastYearOfFinancialYear($tdInvoiceDate = null)
{
    if ($tdInvoiceDate == null) $tdInvoiceDate = date("Y-m-d");
    $time = strtotime($tdInvoiceDate);
    $iInvoiceYear = intval(date("Y", $time));
    $iInvoiceMonth = intval(date("m", $time));
    if ($iInvoiceMonth < 4)
        $iFinancialYearEnd = $iInvoiceYear;
    else
        $iFinancialYearEnd = $iInvoiceYear + 1;
    return $iFinancialYearEnd;
}

function lastYearOfPrevFinancialYear($tdInvoiceDate = null)
{
    if ($tdInvoiceDate == null) $tdInvoiceDate = date("Y-m-d");
    $time = strtotime($tdInvoiceDate);
    $iInvoiceYear = intval(date("Y", $time));
    $iInvoiceMonth = intval(date("m", $time));
    if ($iInvoiceMonth < 4)
        $iFinancialYearEnd = $iInvoiceYear - 1;
    else
        $iFinancialYearEnd = $iInvoiceYear;
    return $iFinancialYearEnd;
}

function getAmountNumberToWords(float $number)
{
    $decimal = round($number - ($no = floor($number)), 2) * 100;
    $hundred = null;
    $digits_length = strlen($no);
    $i = 0;
    $str = array();
    $words = array(0 => '', 1 => 'One', 2 => 'Two',
        3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
        7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
        10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
        13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
        16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
        19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
        40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
        70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety');
    $digits = array('', 'Hundred','Thousand','Lakh', 'Crore');
    while( $i < $digits_length ) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
        } else $str[] = null;
    }
    $Rupees = implode('', array_reverse($str));
    $paise = ($decimal > 0) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
    return ($Rupees ? $Rupees . 'Rupees ' : '') . $paise;
}

//get mobile number from json format
function getMobileFromJsonFormat($sMobile)
{
    $sMobileWithComma = "";
    if (strStartsWith($sMobile, '[{'))
    {
        $arrayData = json_decode($sMobile, true);
        //if arrayData null then return smobile
        if ($arrayData == null) return $sMobile;
        else
        {
            $arrLen = count($arrayData);
            for ($i = 0; $i < $arrLen; $i++)
            {
                if (!empty($arrayData[$i]['mob']))
                {
                     $sTip = (!empty($arrayData[$i]['tip'])) ? ' ('.$arrayData[$i]['tip'].')' : '';
                     $sMobileWithComma .= $arrayData[$i]['isd'].' '.$arrayData[$i]['mob'].$sTip.', ';
                }
            }
            // to remove last accurance of comma in string
            $sMobileWithoutLastComma = rtrim($sMobileWithComma, ", ");
            return escapeHtml($sMobileWithoutLastComma);
        }
    }
    else
        return escapeHtml($sMobile);
}

//in process json validation in php
function decodeJsonAndCheckError($string, &$sError, $isErrorLog=true)
{
    // decode the JSON data
    $objResult = json_decode($string);
    $sError = null;
    // switch and check possible JSON errors
    switch (json_last_error()) {
        case JSON_ERROR_NONE:
            $sError = ''; // JSON is valid // No error has occurred
            break;
        case JSON_ERROR_DEPTH:
            $sError = 'The maximum stack depth has been exceeded.';
            break;
        case JSON_ERROR_STATE_MISMATCH:
            $sError = 'Invalid or malformed JSON.';
            break;
        case JSON_ERROR_CTRL_CHAR:
            $sError = 'Control character error, possibly incorrectly encoded.';
            break;
        case JSON_ERROR_SYNTAX:
            $sError = 'Syntax error, malformed JSON.';
            break;
        // PHP >= 5.3.3
        case JSON_ERROR_UTF8:
            $sError = 'Malformed UTF-8 characters, possibly incorrectly encoded.';
            break;
        // PHP >= 5.5.0
        case JSON_ERROR_RECURSION:
            $sError = 'One or more recursive references in the value to be encoded.';
            break;
        // PHP >= 5.5.0
        case JSON_ERROR_INF_OR_NAN:
            $sError = 'One or more NAN or INF values in the value to be encoded.';
            break;
        case JSON_ERROR_UNSUPPORTED_TYPE:
            $sError = 'A value of a type that cannot be encoded was given.';
            break;
        default:
            $sError = 'Unknown JSON error occured.';
            break;
    }
    if ((!empty($sError)) && ($isErrorLog))
    {
        error_log("JSON Decode Failed:" . $sError. ' :: '. $string. ' :: '. json_encode(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)) );
    }
    return $objResult;
}

function getMonthNumberFromDate($tdDate)
{
    if ($tdDate == null) $tdDate = date("Y-m-d");
    $time = strtotime($tdDate);
    //$iYear = intval(date("Y", $time));
    $iMonth = intval(date("m", $time));
    return $iMonth;
}

//Convert 2.4 hour to '2 hours 24 minutes'
function getHoursMinutesStringFromDecimalHours($fHours)
{
    if(($fHours == null) || ($fHours == '')) return null;
    $sHoursMinutes = "";
    $iHours = intval($fHours);
    $sHoursMinutes .= ($iHours > 0) ? $iHours." Hours " : "" ;
    $iMinutes = round(($fHours - $iHours) * 60);
    $sHoursMinutes .= ($iMinutes > 0) ? $iMinutes." Minutes " : "" ;
    return $sHoursMinutes;
}

//3.25 rupees = 3 rupees 25 paise
function getRupeesPaisefromDecimalRupees($fRupees)
{
    $iRupeesPaise = "";
    $iRupees = intval($fRupees);
    $iRupeesPaise .= ($iRupees > 0 )? $iRupees." Rupees ": "";
    $iPaise = round(($fRupees - $iRupees)*100);
    $iRupeesPaise .= ($iPaise > 0 )? $iPaise." Paise": "";
    return $iRupeesPaise;
}

function getCurrentMonthOneToTwelve()
{
    $iCurrMonth = intval(date('m'));
    return $iCurrMonth;
}

function getCurrentYear()
{
    $iCurrYear = intval(date('Y'));
    return $iCurrYear;
}

function isSubstringFoundCaseInsensitive($string, $subString)
{
    if (empty($subString)) return true;
    $isFound = strripos($string,$subString);
    if ($isFound === false) return false;
    else return true;
}

function saveFormOpenTimeInSession()
{
    $_SESSION['tdtAddEditFormOpenTime'] = nowTimeForSql(); //"2023-06-02 11:40:00"; //
}

function getDurationFormOpenTimeInSession()
{
    if (isset($_SESSION['tdtAddEditFormOpenTime']))
    {
        $iUnixTimeCurrent = time();
        $iUnixTimeOld = strtotime(date($_SESSION['tdtAddEditFormOpenTime']));
        $diffInSec = $iUnixTimeCurrent - $iUnixTimeOld;
        unset($_SESSION['tdtAddEditFormOpenTime']);
        return $diffInSec;
    }
    return null;
}

function secondsRemainingForTimeFromNow($datetime)
{
    if (empty($datetime)) return null;
    $iUnixTimeCurrent = time();
    //$iUnixTimeTarget = strtotime(date($_SESSION['datetime']));
    $iUnixTimeTarget = strtotime(date($datetime));
    $diffInSec = $iUnixTimeTarget - $iUnixTimeCurrent;
    return $diffInSec;
}

function sessionEditFormOpenTimeSet()
{
    $_SESSION['EditFormOpenTime'] = nowTimeForSql();
}

function sessionEditFormOpenTimeReadAndClear()
{
    $_SESSION['EditFormOpenTime'] = nowTimeForSql();
}

function roundTo($number, $to)
{
    return round($number/$to, 0)* $to;
}

//Extract Google doc or drive video FileId from the complete https URL
function getGoogleDriveFileIdFromUrl($sFileUrl)
{
    $sFileUrl = current(explode("?", $sFileUrl));
    $arrFileUrlExploded = explode("/", $sFileUrl);
    //Google recording: https://drive.google.com/file/d/1yUPTSAMxRTj3fURZEWIwgs-6h3D82m-U/view?usp=sharing
    //Google transcript doc: https://docs.google.com/document/d/119Kh2cn2hdPbveDP5lUxoeETMHa2tKRqTJqUzW_hfWI/edit?usp=sharing
    //To handle change of URL format in future, better check length of each part of URL. Valid File ID length is min 32.
    for ($i=0; $i < count($arrFileUrlExploded); $i++)
    {
        if (strlen($arrFileUrlExploded[$i]) >= 32)
        {
            return $arrFileUrlExploded[$i];
        }
    }
    return null;
}

function getTimeInHrs($iMinutes)
{
    if ($iMinutes <= 60) return $iMinutes ." Minutes";
    $iHours = floor($iMinutes / 60); // Get the number of whole hours
    $iMinutes = $iMinutes % 60; // Get the remainder of the hours
    //$sTimeInHours = printf ("%d:%02d", $iHours, $iMinutes);
    return $iHours . " Hours " . $iMinutes ." Minutes" ;
}

function arrayKeyStartsWithId($array)
{
    if (empty($array)) return null;
    else
    {
        for ($i = 0; $i < count($array); $i++)
        {
            if(strStartsWith($array[$i], 'id_'))
            {
                return $array[$i];
            }
        }
    }
    return null;
}

//Ckeck Email format (Regex)
function isValidEmail($sEmail, $isToBeReplaced=false)
{
    //To avoid replacing a short general string with new email , wrong email should have atleast 2 charcters before @ and 5 characters after @
    //RegEx for Minimum email format
    //$regexMinimumForWrongEmail = "/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]{2,}+@[a-zA-Z.-]{5,}$/";

    //remove -obs from email which might be added intentionly
    $sEmail = str_replace('-obs', '', $sEmail);
    //$regexForEmail = "/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/";
    if ($isToBeReplaced)
        //RegEx for Minimum email format
        $regexForEmail = "/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]{4,}+@[a-zA-Z0-9.-]{5,}$/";
    else
        //RegEx for proper email format
        $regexForEmail = "/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]{2,}+@[a-zA-Z0-9]+[a-zA-Z0-9.-]*\.[a-zA-Z0-9-]{2,4}$/";

    if (preg_match($regexForEmail, $sEmail) != 1)
        return false;
    else return true;
}

function validateEmailFormatOrExit($sEmail, $sErrorMsg=null, $isExitOnError=true, $isToBeReplaced=false, $isAllowMultipleEmails=true)
{
    //Provided email may have many valid emails seperated by comma or semicolon
    //Replace all semicolon with comma
    $sEmail = str_replace(';', ',', $sEmail);
    $array_after_comma = explode(',', $sEmail);
    //Check if they want only single email to be there
    if ((!$isAllowMultipleEmails) && (count($array_after_comma) > 1))
        webErrorMessage("Email Validation", "ERROR: Only one email is allowed");
    foreach($array_after_comma as $address)
    {
        $isValidEmail = isValidEmail($address, $isToBeReplaced);
        if ($isValidEmail)
        {
            //return true; // NO action
        }
        else if ((!$isValidEmail) && ($isExitOnError))
        {
            if (empty($sErrorMsg)) $sErrorMsg = 'Invalid Email format in ' . escapeHtml($address);
            webErrorMessage("Email Validation", "ERROR: {$sErrorMsg}");
        }
        else return false;
    }
    return true;//if still not failed or returned
}

function generateApiResponseJson($isSuccess, $sResponseError, $objData)
{
    $sStatus = ($isSuccess) ? 'SUCCESS': 'ERROR';
    $arrResponse = array("response_status"=>$sStatus, "response_error"=>$sResponseError, "response_data"=>$objData);
    $response = json_encode($arrResponse);
    return $response;
}

function convertHtmlToText($sHtml)
{
    require_once $GLOBALS['PATH_TO_CONTROL'].'class.html2text.php';
    $object = new html2text($sHtml);
    return $object->get_text();
    /*
    require_once $GLOBALS['PATH_TO_ROOT'].'/libraries/vendor/soundasleep/html2text/html2text.php';
    return convert_html_to_text($sHtml);
    */
}

function getDeviceAndOsType()
{
    if (isset($_SESSION['xdDeviceType'])) return $_SESSION['xdDeviceType'];

    // (A) USER AGENT
    $ua = strtolower($_SERVER["HTTP_USER_AGENT"]);

    // (B) MOBILE TABLE DESKTOP
    $isMob = is_numeric(strpos($ua, "mobile"));//return type bool
    $isSmartWatch = is_numeric(strpos($ua, "watch"));//return type bool
    $isTab = is_numeric(strpos($ua, "tablet"));//return type bool
    $isTabAndroid = is_numeric(strpos($ua, "android"));//return type bool
    $isTabIpad = is_numeric(strpos($ua, "ipad"));//return type bool

    // Platform check
    $isWin = is_numeric(strpos($ua, "windows"));
    $isMacintosh = is_numeric(strpos($ua, "macintosh"));
    $isLinux = is_numeric(strpos($ua, "linux"));

    $isAndroid = is_numeric(strpos($ua, "android"));
    //echo "user Agent: <div style ='font-size: 50px;'>{$isAndroid}</div><br>";



    $isIPhone = is_numeric(strpos($ua, "iphone"));
    $isIPad = is_numeric(strpos($ua, "ipad"));
    //var_dump($isIPad);
    $isIOS = $isIPad || $isIPhone;
    $isOther = false;
    $isDesktop = !$isMob && !$isTab;
    //$isDesktop = (!$isMob && !$isTab) ? 1 : 0;
    //desktop 0-windows 1-macintosh 2-linux 3-other
    //Mobile 4 windows 5 ios 6 android 7-other
    //Tablet 8 windows 9 ios 10 android 11-other
    //smart watch 12
    //$xdDeviceType = (!$isMob && !$isTab) ? 0 : ($isMob ? 1 : 2) ;
    if ($isMob)
    {
        if($isIPad) $xdDeviceType = 9; //ipad
        else if ($isWin) $xdDeviceType = 4;
        else if ($isIOS) $xdDeviceType = 5;
        else if ($isAndroid) $xdDeviceType = 6;
        else $xdDeviceType = 7; //to handle other os
    }
    else if ($isTab)//for tablet temporary solution
    {
        if ($isWin) $xdDeviceType = 8;
        else if ($isIOS) $xdDeviceType = 9;
        else if ($isAndroid) $xdDeviceType = 10; //android tablet
        else $xdDeviceType = 11;
    }

    else if ($isAndroid)//for tablet temporary solution
    {
        if ($isWin) $xdDeviceType = 8;
        //else if ($isIOS) $xdDeviceType = 9;
        else if ($isAndroid) $xdDeviceType = 10; //android tablet
        else $xdDeviceType = 11;
    }
    //else if ($isSmartWatch) $xdDeviceType = 12;
    else //only desktop
    {
        if ($isWin) $xdDeviceType = 0;
        else if ($isMacintosh) $xdDeviceType = 1;//mac desktop or laptop
        else if ($isLinux) $xdDeviceType = 2;
        else $xdDeviceType = 3;
    }

    $_SESSION['xdDeviceType'] = $xdDeviceType ;

    return $xdDeviceType;
}

function isEditLoginEmailPassword($xcProfile)
{
    global $sUserType;
    //Check for Login Edit permission
    $tableName_DEALNF = getTableNameForRole_DEALNF('login', $sUserType);
    //Edit allowed if permission or self profile
    $isEditLogin = isEdit($tableName_DEALNF);
    if ($isEditLogin) return true;
    //Networker can edit email password of interviewer but not of any other employee
    if (checkRole(NETWORKER) && ($xcProfile < 8))
    {
        error_log("Role Networker");
        return true;
    }
    //AM and CRM can edit email password of recruiter
    else if (checkRole(AM|CRM) && ($xcProfile < 64))
    {
        error_log("Role AM, CRM");
        return true;
    }
    else return false;

}

function sendNotification($arrTo, $sTitle, $sMessagae, $sLinkClickAction)
{
    // $to is device token
    $sTitle = escapeHtml($sTitle);
    $sMessagae = escapeHtml($sMessagae);
    $url ="https://fcm.googleapis.com/fcm/send";
    $pathToAssets = $GLOBALS['PATH_TO_ASSETS'];//ASSETS_ROOT
    $pathToAssetsCDN = (defined('SERVER_LIVE')) ? "https://cangra-com.b-cdn.net/assets/" : $pathToAssets;
    $sLinkIcon = "{$pathToAssetsCDN}images/icon.png";
    $fields=array(
        //"to"=>$to,
        //"registration_ids" => array("fo2OAJhou4GeP0FgdaGcaK:APA91bEKxVKOBb6fzWDOiS1kCVVl69hpAc1QNSf2EXvHAog6ndfzp-2QElYXoyyIS_QrZGXRKfPaois8di2xYFYdiR1vHebWQ_AWhUgAp_j25BDhH4jmQ_Q5qDESdKaGaTEsj0bVWL9g"),
        "registration_ids" => $arrTo,
        "notification"=>array(
            "body"=>$sMessagae,
            "title"=>$sTitle,
            "icon"=>$sLinkIcon,
            "click_action"=>$sLinkClickAction
        )
    );

    $headers=array(
        "Authorization: key={$GLOBALS['firebaseAuthorization']}",
        'Content-Type:application/json'
    );

    $ch=curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_POST,true);
    curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($fields));
    $result=curl_exec($ch);
    //print_r($result);
    curl_close($ch);

    $jsonObject = json_decode($result);
    $isSuccess = "";
    $sResponseError = "";
    $objData = "";
    if (empty($jsonObject))
    {
        $isSuccess = false;
        $sResponseError = "No response from firebase(FCM) API";
    }
    else if($jsonObject -> success == 1)
    {
        $isSuccess = false;
    }
    else
    {
        $isSuccess = false;
        $sResponseError = $jsonObject -> results;
    }
    generateApiResponseJson($isSuccess, $sResponseError, $objData);
}

// google recaptcha verify
function verifyGoogleCaptcha()
{
    if ((isset($_POST['g-recaptcha-response'])) && (!empty($_POST['g-recaptcha-response'])))
    {
        // Verify the reCAPTCHA API response
        $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$GLOBALS['googleCaptchaSecretKey'].'&response='.$_POST['g-recaptcha-response']);

        // Decode JSON data of API response
        $responseData = json_decode($verifyResponse);

        // If the reCAPTCHA API response is valid
        if($responseData->success)
        {
            // do nothing
        }
        else webErrorMessage("Captcha", "Captcha verification failed");
    }
    else webErrorMessage("Captcha", "Please fill captcha");
}

function getViewGoogleCaptcha()
{
    $output = "
    <script src=\"https://www.google.com/recaptcha/api.js\" async defer></script>
    <div class=\"field-wrap\">
        <div class=\"g-recaptcha\" data-sitekey=\"6Lc0UhAqAAAAAA8TgSSKrQ33mKpccLsMzQkShPVT\"></div>
    </div>";
    return $output;
}

$listEnumValues['student']['edStatus'] = ['New', 'Active', 'Inactive'];
$listEnumValues['student']['erType'] = ['Draft', 'Penalty', 'Reward'];
$listEnumValues['student']['sdlZone'] = ['Lucknow', 'Kanpur', 'Ayodhya'];
$listEnumValues['student']['xdActivityType'] = ['Activity-1', 'Activity-2', 'Activity-3'];
$listEnumValues['student']['blog'] = ['Blog-1', 'Blog-2', 'Blog-3'];
$listEnumValues['student']['faq'] = ['Faq-1', 'Faq-2', 'Faq-3'];
$listEnumValues['student']['xcCalling'] = ['Answered', 'Not Picked', 'Topic Discussed'];
$listEnumValues['student']['quote'] = ['Quote-1', 'Quote-2', 'Quote-3'];
$listEnumValues['student']['mcLevels'] = ['Level-1', 'Level-2', 'Level-3'];
$listEnumValues['student']['xrIs_GiveResult'] = ['Y', 'N'];
