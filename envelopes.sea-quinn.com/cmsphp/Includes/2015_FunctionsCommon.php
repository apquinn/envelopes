<?php
define("Const_StartDate", mktime(0,0,0, 12, 1, 2022));

if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
	exit('This file can not be accessed directly...');


function NMU_AutoLoad($strClassName)
{
	try
	{
		if(preg_match("/^[A-Za-z_]+$/", $strClassName) && ! strstr($strClassName, 'omega_')) 
			require_once dirname(dirname(__FILE__))."/Includes/Classes/NMU_$strClassName.class.php";
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}

spl_autoload_register('NMU_AutoLoad');
session_set_cookie_params((60*60*24*30),"/");
MiscFunctions::MiscFunctions_StartSession();


function CORE_RedirectAfterHeader($strLocation)
{
	try
	{
		print'<SCRIPT LANGUAGE="JavaScript">setTimeout(\'location.href="'.$strLocation.'"\', 0)</SCRIPT>';
	}
	catch (Exception $ex)
	{
		ErrorHandler::ErrorHandler_CatchError($ex);
	}
}


class ObjectNameMgmt
{
	private $strStorageName = "2015_DeclaredObjects";

	function __construct()
	{
		try
		{
			if(!isset($GLOBALS[$this->strStorageName]))
				$GLOBALS[$this->strStorageName] = array();
		}
		catch (Exception $EX)
		{
			$this->ObjectNameMgmt_ErrorHandler($ex);
		}
	}

	function ObjectNameMgmt_VarifyNameIsUnique($strObjName)
	{
		try
		{
			if($this->ObjectNameMgmt_CheckIfUsed($strObjName))
				throw new Exception('The object name "'.$strObjName.'" has already been used. It must be unique. (failed in ObjectNameMgmt_)');
			else
				$this->ObjectNameMgmt_StoreName($strObjName);
		}
		catch (Exception $EX)
		{
			$this->ObjectNameMgmt_ErrorHandler($ex);
		}
	}

	function ObjectNameMgmt_GetUniqueName($strPrefix)
	{
		try
		{
			$strUnique = str_replace(".", "", uniqid($strPrefix, true));
			while($this->ObjectNameMgmt_CheckIfUsed($strUnique))
				$strUnique = str_replace(".", "", uniqid($strPrefix, true));

			$this->ObjectNameMgmt_StoreName($strUnique);

			return $strUnique;
		}
		catch (Exception $EX)
		{
			$this->ObjectNameMgmt_ErrorHandler($ex);
		}
	}

	function ObjectNameMgmt_SelfDump()
	{
		try
		{
			$classDump = new Dump();
			$classDump->Display($this);
		}
		catch (Exception $EX)
		{
			$this->ObjectNameMgmt_ErrorHandler($ex);
		}
	}


	private function ObjectNameMgmt_StoreName($strObjName)
	{
		try
		{
			$GLOBALS[$this->strStorageName][] = $strObjName;
		}
		catch (Exception $EX)
		{
			$this->ObjectNameMgmt_ErrorHandler($ex);
		}
	}

	private function ObjectNameMgmt_CheckIfUsed($strObjName)
	{
		try
		{
			if(in_array($strObjName, $GLOBALS[$this->strStorageName]))
				return true;
			else
				return false;
		}
		catch (Exception $EX)
		{
			$this->ObjectNameMgmt_ErrorHandler($ex);
		}
	}

	private function ObjectNameMgmt_ErrorHandler($ex)
	{
		$classError = new ErrorHandler();
		$classError->ErrorHandler_CatchError($ex);
	}
}



function BuildTransactionArray($aEnvResults, &$aEnvs)
{
	foreach($aEnvResults as $aRow)
	{
		$strDeactivated = "";
		if(isset($aRow['EnvDateDeactivated']) && $aRow['EnvDateDeactivated'] != 0)
			$strDeactivated = ' - deactivated: '.ScreenElements::ScreenElements_DateDecode($aRow['EnvDateDeactivated'], 8);

		if($aRow['GroupName'] != $aRow['EnvName'])
			$strName = $aRow['GroupName'].' - '.$aRow['EnvName'].$strDeactivated;
		else
			$strName = $aRow['EnvName'].$strDeactivated;

		$aTemp['ID'] = $aRow['ID'];
		$aTemp['Name'] = $strName;

		$aEnvs[] = $aTemp;
	}
}


function PrintR()
{
	try
	{
		$aArgs = func_get_args();
		$classAdmin = new SystemAdmin();
		$classAdmin->SystemAdmin_Print($aArgs, "Basic");
		return true;
	}
	catch (Exception $EX)
	{
		$this->SystemAdmin_ErrorHandler($EX->getMessage());
	}
}


function CORE_GetURLPublic($strType, $strAction, $strPhase, $strElementID, $strSubAction, $strOutcome)
{
	try
	{
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on")
			$strHTTP = 'https://';
		else
			$strHTTP = 'http://';

		$aParts = explode("?", $strHTTP.$_SERVER['HTTP_HOST'].$_SERVER["REQUEST_URI"]);
		$strBaseURL = $aParts[0];

		if ($strType == Const_Home)
			$strURL = $strBaseURL;
		else
			$strURL = $strBaseURL.'?'.Const_ProcessType.'='.$strType;

		$strURL .= '&'.Const_Action.'='.$strAction;
		$strURL .= '&'.Const_Phase.'='.$strPhase;
		$strURL .= '&'.Const_ElementID.'='.$strElementID;
		$strURL .= '&'.Const_Subaction.'='.$strSubAction;

		if ($strOutcome != "")
			$strURL .= '&'."outcome".'='.$strOutcome;

		return $strURL;
	}
	catch (Exception $EX)
	{
		$this->SystemAdmin_ErrorHandler($EX->getMessage());
	}
}


function CORE_GenerateUniqueNumber()
{
	try
	{
		return str_replace(" ", "", str_replace("0.", "", microtime()));
	}
	catch (Exception $EX)
	{
		$this->SystemAdmin_ErrorHandler($EX->getMessage());
	}
}


function CORE_GetQueryStringVar($strVarName)
{
	try
	{
		$aVarName = [];
		$aVarValue = [];

		if (isset($GLOBALS["_SERVER"]['REQUEST_URI']) && !strstr($_SERVER['REQUEST_URI'], ".php"))
		{
			$strHTTP_SERVER_VARS = $GLOBALS["_SERVER"]['REQUEST_URI'];
			$strHTTP_SERVER_VARS = str_replace("\\", "", $strHTTP_SERVER_VARS);
			$aIndVarGroups = explode("&", $strHTTP_SERVER_VARS);

			if (is_array($aIndVarGroups))
			{
				foreach ($aIndVarGroups as $strGroup)
				{
					$aIndVarGroups = explode("=", $strGroup);
					if (isset($aIndVarGroups[0]))
						$aVarName[] = $aIndVarGroups[0];
					if (isset($aIndVarGroups[1]))
						$aVarValue[] = $aIndVarGroups[1];
				}

				$strResult = false;
				foreach ($aVarName as $iID => $strName)
					if ($strName == $strVarName)
						$strResult = $aVarValue[$iID];
			}
PrintR($strHTTP_SERVER_VARS, "Big String");
PrintR($strVarName, "VarName");
PrintR($strResult, "Var Value");
		}

		if (isset($strResult) && $strResult != "")
			return $strResult;
		else
			return "";

	}
	catch (Exception $EX)
	{
		$this->SystemAdmin_ErrorHandler($EX->getMessage());
	}
}


function CORE_ClearSessions()
{
	try
	{
		if (is_array($_SESSION))
			foreach ($_SESSION as $strVarName => $strVarValue)
				if (!strstr(strtolower($strVarName), "session"))
					unset($_SESSION[$strVarName]);
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


function CORE_GetURL($strType, $strAction, $strPhase, $strElementID, $strSubAction, $strOutcome)
{
	try
	{
		$strURL = '';

		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on")
			$strHTTP = 'https://';
		else
			$strHTTP = 'http://';

		if ($strType == Const_Home)
			$strURL = $strHTTP.$GLOBALS["_SERVER"]["HTTP_HOST"].$GLOBALS["_SERVER"]["DOCUMENT_URI"];
		else
		{
			if ($strType == Const_RawURL)
			{
				$classSqlQuery = new SqlDataQueries();
				$strQuery = "SELECT SubPath FROM cms_admin_comp WHERE Filename='".addslashes(Const_SelfName)."'";
				$aResults = $classSqlQuery->MySQL_Queries($strQuery);

				$strURL = $strHTTP.$GLOBALS["_SERVER"]["HTTP_HOST"]."/cgi-bin/".Const_SelfName;
			}
			if ($strType == Const_ParentURL)
			{
				if ($GLOBALS["_SERVER"]["HTTP_REFERER"] != "")
				{
					$aParts = explode("?", $GLOBALS["_SERVER"]["HTTP_REFERER"]);
					if (isset($aParts[0]))
						$strURL = $aParts[0];
				}
				elseif (isset($_SESSION['SessionParent']))
				{
					$aParts = explode("?", $_SESSION['SessionParent']);
					if (isset($aParts[0]))
						$strURL = $aParts[0];
				}
			}
			if ($strType == Const_SelfURL)
			{
				$aParts = explode("?", $GLOBALS["_SERVER"]["REQUEST_URI"]);
				if (isset($aParts[0]))
					$strURL = $aParts[0];
			}

			if (isset($_REQUEST[Const_Page]) && $_REQUEST[Const_Page] != "")
				$strURL .= '?'.Const_Page.'='.$_REQUEST[Const_Page];
			else
				$strURL .= '?'.Const_Page.'='.CORE_GetQueryStringVar(Const_Page);

			$strURL .= '&'.Const_Action.'='.$strAction;
			$strURL .= '&'.Const_Phase.'='.$strPhase;
			$strURL .= '&'.Const_ElementID.'='.$strElementID;

			if ($strSubAction != "")
				$strURL .= '&'.Const_Subaction.'='.$strSubAction;

			if ($strOutcome != "")
				$strURL .= '&'."outcome".'='.$strOutcome;
		}

		return $strURL;
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


function CORE_FormatMoney($iCash, $bShowDollarSign, $bShowCents, $bBlankZero, $bRedNegative)
{
	try
	{
		$strDollarSign = "";

		if ($iCash == "")
			$iCash = '0';

		if ($iCash == 0 && $bBlankZero)
			return '';
		elseif (is_numeric($iCash))
		{
			if ($bShowDollarSign)
				$strDollarSign = "$";

			if ($bShowCents === true)
				$iDecimals = 2;
			else
				$iDecimals = 0;

			$strNumber = $strDollarSign.number_format($iCash, $iDecimals);

			if ($bRedNegative && $iCash < 0)
			{
				$strNumber = str_replace("-", '', $strNumber);
				return '<span style="color:red">('.$strNumber.')</span>';
			}
			else
				return $strNumber;
		}
		else
			return $iCash;
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


function CORE_UnformatMoney($iCash)
{
	try
	{
		$iNumber = str_replace("\.", "", str_replace(",", "", str_replace("$", "", $iCash)));
		if ($iCash == "")
			return 0;

		if (is_numeric($iNumber))
			return $iNumber;

		else
			return $iCash;
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


define("Const_connHost", "localhost");
define("Const_connDB", "www_webadmin");
define("Const_connUser", "aquinn");
define("Const_connPSW", "WhiteSlab*1");


date_default_timezone_set('US/Eastern');

define("Const_ErrorMsgSQLError", "An error has occurred. Please try again or contact the Drew if this error persists. (Error 1000)");
define("Const_ErrorMsgCorrectItems", "The following need to be corrected: ");
define("Const_ErrorMsgRequiredItem", " is a required item.");

define("Const_Home", "home");
define("Const_RawURL", "raw");
define("Const_SelfURL", "self");
define("Const_ProcessType", "processtype");
define("Const_ParentURL", "parent");
define("Const_Page", "page");
define("Const_Action", "action");
define("Const_Phase", "phase");
define("Const_Phase1", "phase1");
define("Const_Phase2", "phase2");
define("Const_Phase3", "phase3");
define("Const_Phase4", "phase4");
define("Const_Phase5", "phase5");
define("Const_ElementID", "elementid");
define("Const_Subaction", "subaction");

define("Const_Error", "error");
define("Const_Warning", "warning");
define("Const_Informational", "informational");
define("Const_NotifyAdmin", "NotifyAdmin");
define("Const_Positive", "Positive");
define("Const_Success", "success");

define("Const_False", "false");
define("Const_True", "true");

define("Const_sLoginName", "SessionLoginUsername");
define("Const_sLoginIP", "SessionLoginIP");
define("Const_sNavParent", "SessionNavParent");
define("Const_sNavChild", "SessionNavChild");

$GLOBALS['aSuperUsers'] = ["aquinn"];












