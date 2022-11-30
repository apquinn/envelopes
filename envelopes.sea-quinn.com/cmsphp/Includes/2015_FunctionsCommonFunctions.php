<?php

require_once "2015_FunctionsCommon.php";


function FunctionsCommonFunctions2015_Main($argv)
{
	$argv[0] = str_replace("./", "", $argv[0]);

	try
	{
		if (isset($_REQUEST['action']) && $_REQUEST['action'] != "")
		{
			if ($_REQUEST['action'] == 'ScreenElements_FileUpload_ProcessUpload')
				ScreenElements_FileUpload_ProcessUpload($_REQUEST['Fieldname'], $_REQUEST['Directory']);

			if ($_REQUEST['action'] == 'FileUploadUpload')
				FileUploadUpload($_REQUEST['ObjName'], $_REQUEST['FieldName'], $_REQUEST['OldFileName'], $_REQUEST['AdjustedFileName']);

			if ($_REQUEST['action'] == 'FileUploadRemove')
				FileUploadRemove($_REQUEST['ObjName'], $_REQUEST['FieldName']);

			if ($_REQUEST['action'] == 'SaveCaption')
				SaveCaption($_REQUEST['ObjName'], $_REQUEST['FieldName'], $_REQUEST['Caption']);

			if ($_REQUEST['action'] == 'FileUploadResetField')
				FileUploadResetField($_REQUEST['ObjName'], $_REQUEST['FieldName']);

			if ($_REQUEST['action'] == 'FileUploadResetFieldAll')
				FileUploadResetFieldAll($_REQUEST['ObjName']);

			if ($_REQUEST['action'] == 'StoreToDBOnCompletion')
				StoreToDBOnCompletion($_REQUEST['ObjName'], $_REQUEST['FieldName'], $_REQUEST['ID']);

			if ($_REQUEST['action'] == 'MySetCookie')
				MySetCookie($_REQUEST['CookieName'], $_REQUEST['CookieValue']);

			if ($_REQUEST['action'] == 'ValidateLogin')
				ValidateLogin($_REQUEST['ApplicationID']);

			if ($_REQUEST['action'] == 'TimePopUp')
				TimePopUp();

			if ($_REQUEST['action'] == 'SecureRemoteCall_CurlProcess')
				SecureRemoteCall_CurlProcess($_REQUEST['SessionMgmt_SessionID'], $_REQUEST['SecureRemoteCall_CurlUniqueIndex']);

			if ($_REQUEST['action'] == 'SecureRemoteCall_PostProcess')
				SecureRemoteCall_PostProcess($_REQUEST['SessionMgmt_SessionID'], $_REQUEST['SecureRemoteCall_PostUniqueIndex']);
		}
		elseif (isset($argv[0]) && strstr("/htdocs/cmsphp/Includes/2015_FunctionsCommonFunctions.php", $argv[0]))
		{
			if (isset($argv[1]) && $argv[1] == "SendMail_ProcessQueueGroup")
				SendMail_ProcessQueueGroup();
		}
	}
	catch (Exception $EX)
	{
		print Const_ErrorMsgSQLError.": <BR>".$EX->getMessage();
	}
}


function ScreenElements_FileUpload_ProcessUpload($strFieldname, $strDirectory)
{
	try
	{
		$classScreen = new ScreenElements("temp", true);
		$aPaths = $classScreen->ScreenElements_GetUploadFilePath($strDirectory);

		$classSqlQuery = new SqlDataQueries();
		$strExts = ["jpg", "jpeg", "gif"];

		if($_FILES[$strFieldname]['name'] != "")
		{
			$aParts = explode(".", $_FILES[$strFieldname]['name']);
			$strActualExt = strtolower($aParts[(count($aParts) - 1)]);
			$strActualName = str_replace(".".$aParts[(count($aParts) - 1)], "", $_FILES[$strFieldname]['name']);

			$bFound = false;
			foreach ($strExts as $strAnExt)
				if (strtolower(trim($strAnExt)) == strtolower(trim($strActualExt)))
					$bFound = true;

			if (!$bFound)
				{ print 'Error: file must be of type "jpg", "jpeg", "gif"'; die; }
			else
			{
				$i=1;
				$strAddOn = "";
				while (file_exists($aPaths["ImageDir"].$strActualName.$strAddOn.'.'.$strActualExt))
					$strAddOn = ' ('.$i++.')';
				$strFinalName .= $strActualName.$strAddOn.'.'.$strActualExt;

				if (!move_uploaded_file($_FILES[$strFieldname]['tmp_name'], $aPaths["ImageDir"].$strFinalName))
					print 'Error Occured: An error occurred uploading file '.$_FILES[$strFieldname]['name'];
			}

			print $aPaths["HttpFullImageDir"].$strFinalName."=====".$aPaths["HttpImageDir"].$strFinalName;
		}
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}

function SecureRemoteCall_PostProcess($strSessionMgmt_SessionID, $iSecureRemoteCall_PostUniqueIndex)
{
	try
	{
		$classSession = new SessionMgmt();
		$classSession->SessionMgmt_SetSessionID($strSessionMgmt_SessionID);

		$classSecureCall = new SecureRemoteCall();
		$classSecureCall->SecureRemoteCall_PostProcess((int)$iSecureRemoteCall_PostUniqueIndex);
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


function SecureRemoteCall_CurlProcess($strSessionMgmt_SessionID, $iSecureRemoteCall_CurlUniqueIndex)
{
	try
	{
		$classSession = new SessionMgmt();
		$classSession->SessionMgmt_SetSessionID($strSessionMgmt_SessionID);

		$classSecureCall = new SecureRemoteCall();
		$classSecureCall->SecureRemoteCall_CurlProcess((int)$iSecureRemoteCall_CurlUniqueIndex);
		#PrintR($classSession->SessionMgmt_SelectAll());
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


function TimePopUp()
{
    try
    {
    	print'
    		<div id="ptTimeSelectCntr" class="" style="block; display:none">
    			<div class="ui-widget ui-widget-content ui-corner-all">
    				<div id="ptTimeSelectLeftBlock" class="ptTimeSelectLeftBlock">
    					<div class="ptTimeSelectHead ptTimeSelectHeadLeft ui-widget-header"><strong>Hour</strong></div>
    					<div class="ptTimeSelectOptions ptTimeSelectHrDiv">
    						<a id="Hour_1" class="ptTimeSelectButtons ptTimeSelectHr ptTimeSelectUnselectedButton" href="javascript: void(0);">1</a>
    						<a id="Hour_2" class="ptTimeSelectButtons ptTimeSelectHr ptTimeSelectUnselectedButton" href="javascript: void(0);">2</a>
    						<a id="Hour_3" class="ptTimeSelectButtons ptTimeSelectHr ptTimeSelectUnselectedButton" href="javascript: void(0);">3</a>
    						<a id="Hour_4" class="ptTimeSelectButtons ptTimeSelectHr ptTimeSelectUnselectedButton" href="javascript: void(0);">4</a>
    						<a id="Hour_5" class="ptTimeSelectButtons ptTimeSelectHr ptTimeSelectUnselectedButton" href="javascript: void(0);">5</a>
    						<a id="Hour_6" class="ptTimeSelectButtons ptTimeSelectHr ptTimeSelectUnselectedButton" href="javascript: void(0);">6</a>
    						<a id="Hour_7" class="ptTimeSelectButtons ptTimeSelectHr ptTimeSelectUnselectedButton" href="javascript: void(0);">7</a>
    						<a id="Hour_8" class="ptTimeSelectButtons ptTimeSelectHr ptTimeSelectUnselectedButton" href="javascript: void(0);">8</a>
    						<a id="Hour_9" class="ptTimeSelectButtons ptTimeSelectHr ptTimeSelectUnselectedButton" href="javascript: void(0);">9</a>
    						<a id="Hour_10" class="ptTimeSelectButtons ptTimeSelectHr ptTimeSelectUnselectedButton" href="javascript: void(0);">10</a>
    						<a id="Hour_11" class="ptTimeSelectButtons ptTimeSelectHr ptTimeSelectUnselectedButton" href="javascript: void(0);">11</a>
    						<a id="Hour_12" class="ptTimeSelectButtons ptTimeSelectHr ptTimeSelectUnselectedButton" href="javascript: void(0);">12</a>
    					</div>
    				</div>
    
    
    				<div id="ptTimeSelectRightBlock" class="ptTimeSelectRightBlock">
    					<div class="ptTimeSelectHead ptTimeSelectHeadRight ui-widget-header"><strong>Minutes</strong></div>
    					<div class="ptTimeSelectOptions ptTimeSelectMinDiv">
    						<a id="Minute_00" class="ptTimeSelectButtons ptTimeSelectMin ptTimeSelectUnselectedButton" href="javascript: void(0);">00</a>
    						<a id="Minute_15" class="ptTimeSelectButtons ptTimeSelectMin ptTimeSelectUnselectedButton" href="javascript: void(0);">15</a>
    						<a id="Minute_30" class="ptTimeSelectButtons ptTimeSelectMin ptTimeSelectUnselectedButton" href="javascript: void(0);">30</a>
    						<a id="Minute_45" class="ptTimeSelectButtons ptTimeSelectMin ptTimeSelectUnselectedButton" href="javascript: void(0);">45</a>
    					</div>
    
    					<div class="ptTimeSelectOptions ptTimeSelectAmPmDiv ptTimeSelectOptionsAmPm">
    						<a id="AmPm_AM" class="ptTimeSelectButtons ptTimeSelectHr ptTimeSelectUnselectedButton" href="javascript: void(0);">AM</a>
    						<a id="AmPm_PM" class="ptTimeSelectButtons ptTimeSelectHr ptTimeSelectUnselectedButton" href="javascript: void(0);">PM</a>
    					</div>
    
    					<div class="ptTimeSelectOptions ptTimeSelectSetButton">
    						<a id="TimeSelectSet" class="ptTimeSelectButtons" href="javascript: void(0);">Set</a>
    						<a id="TimeSelectClear" class="ptTimeSelectButtons" href="javascript: void(0);">Clear</a>
    					</div>
    				</div>
    			</div>
    		</div>';
    }
    catch (Exception $EX)
    {
        ErrorHandler::ErrorHandler_CatchError($EX);
    }
}


function SendMail_ProcessQueueGroup()
{
	try
	{
		$classMail = new SendMail(0);

		while ($classMail->SendMail_ProcessQueueGroup())
			sleep($classMail->SendMail_GetPauseTime());
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}

function ValidateLogin($strApplicationID)
{
	try
	{
		$classLogin = new SSOLogin();
		$classLogin->SSOLogin_Validate($strApplicationID);
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


function MySetCookie($strCookieName, $strCookieValue)
{
	try
	{
		$iDuration = (time() + (60 * 60 * 24 * 365));
		setcookie($strCookieName, $strCookieValue, $iDuration, "/", ".nmu.edu", true, false);
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


function StoreToDBOnCompletion($strObjName, $strFieldName, $iID)
{
	try
	{
		$classFile = new FileUploadJQ($strObjName, false);
		$classFile->FileUploadJQ_StoreToDBOnCompletion($strFieldName, $iID);
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


function FileUploadResetField($strObjName, $strFieldName)
{
	try
	{
		$classFile = new FileUploadJQ($strObjName, false);
		$classFile->FileUploadJQ_ResetField($strFieldName);
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


function FileUploadResetFieldAll($strObjName)
{
	try
	{
		$classFile = new FileUploadJQ($strObjName, false);
		$aResult = $classFile->FileUploadJQ_ResetFieldAll();
		foreach ($aResult as $strFieldName)
			print $strFieldName."XXXXOOOOXXXX";
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


function FileUploadUpload($strObjName, $strFieldName, $strOldFileName, $strAdjustedFileName)
{
	try
	{
		$classFile = new FileUploadJQ($strObjName, false);
		$strResult = $classFile->FileUploadJQ_MoveFile($strFieldName, $strOldFileName, $strAdjustedFileName);

		if ($strResult == "")
		{
			$aThisField = $classFile->FileUploadJQ_GetFilesField($strFieldName);
			if (exif_imagetype($classFile->FileUploadJQ_GetDirectoryPath($aThisField['FileName'])) > 0)
				print "IsImage";
		}
		else
			print $strResult;
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


function FileUploadRemove($strObjName, $strFieldName)
{
	try
	{
		$classFile = new FileUploadJQ($strObjName, false);
		$strResult = $classFile->FileUploadJQ_RemoveMarkForRemoval($strFieldName);

		print $strResult;
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


function SaveCaption($strObjName, $strFieldName, $strCaption)
{
	try
	{
		$classFile = new FileUploadJQ($strObjName, false);
		$classFile->FileUploadJQ_AddCaption($strFieldName, $strCaption);
		$aThisField = $classFile->FileUploadJQ_GetFilesField($strFieldName);

		if ($classFile->FileUploadJQ_IsFieldImage($strFieldName))
			print'image';
		print "XXXXOOOOXXXX";
		print $aThisField['FileName'];
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


FunctionsCommonFunctions2015_Main($argv);



