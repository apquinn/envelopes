<?php

if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
	// tell people trying to access this file directly goodbye...
	exit('This file can not be accessed directly...');
}


class SSOLogin extends BaseClass
{
	protected $aAuthorizedSites = [];
	protected $aApprovedSites = [];
	protected $strObjName = "SSOLoginClass";
	protected $strApplication = "";
	protected $strSessID = "";
	protected $strSessID1111 = "";

	function __construct($aArgs=[])
	{
		try
		{
			$aArgs['strObjName'] = $this->strObjName;
			$aArgs['bIsPersistant'] = true;
			parent::__construct($aArgs);

			if ($this->SSOLogin_CheckAuthentication('webadmin'))
				$_SESSION[Const_sLoginName] = $this->aAuthorizedSites['webadmin']['UserData']['uid'];
			else
				$_SESSION[Const_sLoginName] = "";

			$this->aApprovedSites = [];
			$this->aApprovedSites['studentconnect'] = ["Username" => "sso_stconnect", "Password" => "TCEBGnkq4BxaUOLO"];
			$this->aApprovedSites['webadmin'] = ["Username" => "sso_wwwadmin", "Password" => "4sIKrbpo4tmpHc9K"];
			$this->aApprovedSites['projreq'] = ["Username" => "sso_projreq", "Password" => "OH8QGbHEFbXeClS4"];
			$this->aApprovedSites['sra'] = ["Username" => "sso_sra", "Password" => "ZaR09IwDgXHgJL2D"];
			$this->aApprovedSites['generic'] = ["Username" => "sso_markcomm", "Password" => "HWkhkdbJcDX0uCn7"];

			$this->BaseClass_StoreSelf();
		}
		catch (Exception $EX)
		{
			ErrorHandler::ErrorHandler_CatchError($EX);
		}
	}


	function SSOLogin_Validate($strApplication)
	{
		try
		{
			if (isset($strApplication) && $strApplication != "" && isset($this->strSessID) && $this->strSessID != "")
			{
				$classSecureCall = new SecureRemoteCall();
				$classKarlSqlQuery = new SqlDataQueries();
				$classKarlSqlQuery->SpecifyDB(Const_connSSOHostMyNMU, Const_connSSODBMyNMU, $this->aApprovedSites[$strApplication]['Username'], $this->aApprovedSites[$strApplication]['Password']);

				$aVariables = ['Query'=>"SELECT * FROM ".Const_connSSODBMyNMU.".nmu_ssoview WHERE sessionid='".addslashes($this->strSessID)."'", 'QueryClass'=>$classKarlSqlQuery];
				$aResults = $classSecureCall->SecureRemoteCall_Curl("", "Query", $aVariables);

				if (count($aResults) > 0 && $aResults[0]['udata'] != "")
				{
					$classSqlQuery = new SqlDataQueries();
					$aUserData = [];

					if(unserialize($aResults[0]['udata']) != false)
						$aUnserializedResults = unserialize($aResults[0]['udata']);
					elseif(json_decode($aResults[0]['udata']) != null)
						$aUnserializedResults = json_decode($aResults[0]['udata']);
					else
						$aUnserializedResults['uid'] = $aResults[0]['udata'];

					foreach ($aUnserializedResults as $strName => $strValue)
					{
						if ($strName == "orgs")
						{
							$aTemp = [];
							$classSqlQuery->SpecifyDB(Const_connHostCseOrgReq, Const_connCseOrgReqDB, Const_connCseOrgReqUser, Const_connCseOrgReqPwd);

							foreach ($strValue as $iOrg)
							{
								if (is_numeric($iOrg))
								{
									$strQuery = "SELECT org_name FROM org_students_view WHERE org_id='".$iOrg."'";
									$aMoreResults = $classSqlQuery->MySQL_Queries($strQuery);

									if ($aMoreResults[0]['org_name'] != "")
										$aTemp[] = $aMoreResults[0]['org_name'];
								}
								else
									$aTemp[] = $iOrg;
							}

							$strValue = $aTemp;
						}

						$aUserData[$strName] = $strValue;
					}

					$this->aAuthorizedSites[$strApplication]['UserData'] = $aUserData;
					$_SESSION[Const_sLoginName] = $strUserID;

					print "success";
				}
			}

			$this->strApplication = "";
			$this->strSessID = "";

			$this->BaseClass_StoreSelf();
		}
		catch (Exception $EX)
		{
			ErrorHandler::ErrorHandler_CatchError($EX);
		}
	}

	function SSOLogin_ForceIdentityForTestingOnly($strApplication, $strUsername)
	{
		try
		{
			if ($strApplication != "all" && isset($this->aApprovedSites[$strApplication]) && $this->aApprovedSites[$strApplication]['Username'] != "" && $this->aApprovedSites[$strApplication]['Password'] != "")
			{
				$aFakeResults = [];
				$aFakeResults['uid'] = $strUsername;
				$aFakeResults['name'] = "Joe Ghost";
				$aFakeResults['orgs'] = ["Rock Throwing Club", "Brick Throwing Club", "Tire Throwing Club", "Dpt"];

				$this->aAuthorizedSites[$strApplication]['UserData'] = $aFakeResults;
				$_SESSION[Const_sLoginName] = $strUsername;

				$this->BaseClass_StoreSelf();
			}
			elseif ($strApplication == "all" && $strUsername != "")
			{
				foreach ($this->aApprovedSites as $strName => $strValue)
					$this->SSOLogin_ForceIdentityForTestingOnly($strName, $strUsername);
			}
		}
		catch (Exception $EX)
		{
			ErrorHandler::ErrorHandler_CatchError($EX);
		}
	}

	function SSOLogin_CheckAuthenticationWithButton($strApplication, $strFunctionCallBack)
	{
		try
		{
			if ($this->SSOLogin_CheckAuthentication($strApplication))
				return true;
			elseif (TEMP_GetQueryStringVar('printformat') == "ghost" || (isset($_REQUEST['printformat']) && $_REQUEST['printformat'] == "ghost"))
			{
				foreach ($this->aApprovedSites as $strName => $strValue)
					$this->SSOLogin_ForceIdentityForTestingOnly($strName, "ghost");
				$strURL = str_replace('printformat=ghost', '', $_SERVER['REQUEST_URI']);

				CORE_RedirectAfterHeader('http://'.$_SERVER['HTTP_HOST'].$strURL);
			}
			else
			{
				print'<div id="SSOLogin_LoginDivID_'.$strApplication.'_Wrapper">';
				print'<p class="SSOLogin_LoginPTag">To use this feature or application you must first login. You will need your standard Northern Michigan University account information. When you\'re ready to continue, just click login!</p>';
				print'<div id="SSOLogin_LoginDivID_'.$strApplication.'" class="SSOLogin_LoginDiv">';
				print'<button id="SSOLogin_LoginButtonID_'.$strApplication.'" class="btn btn-default SSOLogin_LoginButton" onclick="SSOLogin_OpenLogin(\''.$strApplication.'\', \''.$this->aApprovedSites[$strApplication]['Username'].'\', \''.$strFunctionCallBack.'\', \''.$this->classSession->SessionMgmt_GetSessionID().'\'); return false;">Login</button>';
				print'</div>';
				print'</div>';

				$strScript = '
					jQuery(document).ready(function(){
						jQuery("#SSOLogin_LoginDivID_'.$strApplication.'").load(SSOLogin_LoginBoxStyle(\''.$strApplication.'\'));
					});';

				ProcessJavascript::ProcessJavascript_WrapOutput($strScript);
				return false;
			}

			return true;
		}
		catch (Exception $EX)
		{
			ErrorHandler::ErrorHandler_CatchError($EX);
		}
	}

	function SSOLogin_CheckAuthentication($strApplication)
	{
		try
		{
			if ($strApplication != "" && isset($this->aAuthorizedSites[$strApplication]['UserData']['uid']) && $this->aAuthorizedSites[$strApplication]['UserData']['uid'] != "")
				return true;
			else
				return false;
		}
		catch (Exception $EX)
		{
			ErrorHandler::ErrorHandler_CatchError($EX);
		}
	}

	function SSOLogin_GetUserData()
	{
		try
		{
			if(count(func_get_args()) == 1)
				$aReturnVal = $this->aAuthorizedSites[func_get_args()[0]]['UserData'];
			else
				$aReturnVal = $this->aAuthorizedSites;

			return $aReturnVal;
		}
		catch (Exception $EX)
		{
			ErrorHandler::ErrorHandler_CatchError($EX);
		}
	}

	function SSOLogin_Logout($strApplication)
	{
		try
		{
			if (isset($this->aAuthorizedSites[$strApplication]))
				unset($this->aAuthorizedSites[$strApplication]);

			$_SESSION[Const_sLoginName] = "";

			$this->BaseClass_StoreSelf();
		}
		catch (Exception $EX)
		{
			ErrorHandler::ErrorHandler_CatchError($EX);
		}
	}

	function SSOLogin_LogoutAll()
	{
		try
		{
			unset($this->aAuthorizedSites);
			$_SESSION[Const_sLoginName] = "";

			$this->BaseClass_StoreSelf();
		}
		catch (Exception $EX)
		{
			ErrorHandler::ErrorHandler_CatchError($EX);
		}
	}

	function SSOLogin_StoreLoginResponse($strApplication, $strSessID)
	{
		try
		{
			$this->strApplication = $strApplication;
			$this->strSessID = $strSessID;
			$this->strSessID1111 = "BEEF";
			

			$this->BaseClass_StoreSelf();
			print'<script> window.close() </script>';
		}
		catch (Exception $EX)
		{
			ErrorHandler::ErrorHandler_CatchError($EX);
		}
	}
}



