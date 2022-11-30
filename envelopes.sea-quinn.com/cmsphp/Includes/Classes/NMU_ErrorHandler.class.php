<?php

if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
	// tell people trying to access this file directly goodbye...
	exit('This file can not be accessed directly...');
}


class ErrorHandler extends BaseClass
{
	protected $classSqlQuery;
	private $strSessionName = 'ErrorHandler';
	public static $bNotifyAdmin = false;

	function __construct($aArgs=[])
	{
		try
		{
			parent::__construct($aArgs);
			$this->classSqlQuery = new SqlDataQueries();
		}
		catch (Exception $EX)
		{
			ErrorHandler::ErrorHandler_CatchError($EX);
		}
	}

	public static function ErrorHandler_MessageDisplay()
	{
		try
		{
			$iPage = 0;
			if (isset($_REQUEST["page"]) && $_REQUEST["page"] != "")
				$iPage = $_REQUEST["page"];
			elseif(CORE_GetQueryStringVar("page") != "")
				$iPage = CORE_GetQueryStringVar("page");


			if (isset($_SESSION['cmsAdmin_PositiveOutcome']) && $_SESSION['cmsAdmin_PositiveOutcome'] != "")
				print'<div class="row"><div class="col-sm-10" style="color:#cc6633">'.$_SESSION['cmsAdmin_PositiveOutcome'].'</div></div>';
			$_SESSION['cmsAdmin_PositiveOutcome'] = "";

			if (isset($_SESSION['cmsAdmin_PositiveOutcome']) && $_SESSION['cmsAdmin_PositiveOutcome'] != "")
				print'<div class="row"><div class="col-sm-10" style="color:#cc6633">'.$_SESSION['cmsAdmin_PositiveOutcome'].'</div></div>';
			$_SESSION['cmsAdmin_NoticeOutcome'] = "";


			$classSession = new SessionMgmt();
			$aValues = $classSession->SessionMgmt_Select("ErrorHandler-Warning");
			if (isset($aValues["outcome"]) && $aValues["outcome"] == "Warning")
			{
				print'<div class="row"><div class="col-sm-10" style="color:#EB984E">'.$aValues["outcome-message"].'</div></div>';

				if(isset($aValues['outcome-corrections']) && count($aValues['outcome-corrections']) > 0)
				{
					print'<div class="row" style="padding:0px 0px 15px 0px;"><ul>';
					foreach ($aValues['outcome-corrections'] as $strMessage)
						print'<div class="col-sm-10"><li>'.$strMessage.'</li></div>';
					print'</ul></div>';
				}

				$classSession->SessionMgmt_DeleteValue("ErrorHandler-Warning");
			}
		}
		catch (Exception $EX)
		{
			ErrorHandler::ErrorHandler_CatchError($EX);
		}
	}


	public static function ErrorHandler_HandleCorrections($aIssues)
	{
		try
		{
			if (isset($aIssues) && count($aIssues) > 0)
			{
				$aValues = [];
				$classSession = new SessionMgmt();

				$aValues["outcome"] = "Warning";
				$aValues["outcome-message"] = "The following issues need to be corrected:";
				$aValues["outcome-corrections"] = $aIssues;
				$classSession->SessionMgmt_Set("ErrorHandler-Warning", $aValues);

				$_REQUEST[Const_Action] = $_REQUEST[Const_Action] ?? "";
				$_REQUEST[Const_Phase] = $_REQUEST[Const_Phase] ?? "";
				$_REQUEST[Const_ElementID] = $_REQUEST[Const_ElementID] ?? "";
				$_REQUEST[Const_Subaction] = $_REQUEST[Const_Subaction] ?? "";

				$strURL = CORE_GetURL(Const_ParentURL, $_REQUEST[Const_Action], $_REQUEST[Const_Phase], $_REQUEST[Const_ElementID], $_REQUEST[Const_Subaction], Const_Error);
				header("Location: ".$strURL);
				die;
			}
		}
		catch (Exception $EX)
		{
			ErrorHandler::ErrorHandler_CatchError($EX);
		}
	}


	public static function ErrorHandler_CatchError($EX, $aAdditional = [])
	{
		try
		{
			if (is_object($EX))
				$strErrorMsg = $EX->getMessage();
			else
				$strErrorMsg = $EX;

			$strBasicDetailsMsg = "";
			foreach($aAdditional as $strAdditional)
				$strBasicDetailsMsg .= $strAdditional;

			$strMessage = '<p>An error has occured. Please try again or for more help, contact us at <a mailto="edesign@nmu.edu">edesign@nmu.edu<a>.</p>
						   <p>'.$strErrorMsg.'</p><p>'.$strBasicDetailsMsg.'</p>';
			print $strMessage;
			die;
		}
		catch (Exception $EX)
		{
			print'A severe error has occured. Please try again or contact the NMU web team at edesign@nmu.edu';
			die;
		}
	}

}