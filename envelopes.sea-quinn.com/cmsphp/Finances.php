<?php
require_once dirname(__FILE__)."/Includes/2015_FunctionsCommon.php";
date_default_timezone_set('America/New_York');
define("Const_SelfName", "Finances.php");

function Finances_Main()
{
	try
	{
		if(!isset($_SESSION[Const_sLoginName]) || $_SESSION[Const_sLoginName] != "quinn")
			CORE_RedirectAfterHeader("/cgi-bin/confirm.php");
		else
		{
			if(isset($_REQUEST[Const_Action]))
			{
				# Import
				if($_REQUEST[Const_Action] == "ImportData" && $_REQUEST[Const_Phase] == Const_Phase1)
					ProcessImportPhase1();

				# TransGroup
				if($_REQUEST[Const_Action] == "TransGroups" && $_REQUEST[Const_Phase] == Const_Phase1)
					ProcessTransGroup1();
				if($_REQUEST[Const_Action] == "TransGroups" && $_REQUEST[Const_Phase] == Const_Phase2)
					ProcessTransGroup2();
			}
			else
			{
				print'<script>
						function GetCurrentTransTable()
						{
							return "Transactions";
						}
					</script>';
				$strAction = CORE_GetQueryStringVar(Const_Action);
				if($strAction == "")
					$strAction = "Budget";

				$strPhase = CORE_GetQueryStringVar(Const_Phase);
				if($strPhase == "")
					$strPhase = Const_Phase1;

				PreloadDatabaseFields();

				if(CORE_GetQueryStringVar('start') == 'true')
					CORE_ClearSessions();

				#if($strAction == "Reports" && $strPhase == Const_Phase1)
				#	DisplayReportPhase1();

				if($strAction == "Budget" && $strPhase == Const_Phase1)
					DisplayBudgetPhase1();

				if($strAction == "Trans" && $strPhase == Const_Phase1)
					DisplayTransPhase1();

				if($strAction == "ImportData" && $strPhase == Const_Phase1)
					DisplayImportPhase1();

				if($strAction == "TransGroups" && $strPhase == Const_Phase1)
					DisplayTransGroupPhase1();
				if($strAction == "TransGroups" && $strPhase == Const_Phase2)
					DisplayTransGroupPhase2();
			}
		}
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


// function DisplayPickPhase1()
// {
// 	try
// 	{
// 		ErrorHandler::ErrorHandler_MessageDisplay();

// 		$classSqlQuery = new SqlDataQueries();
// 		$strQuery = "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA='www_budget' AND TABLE_NAME like 'Transactions_Proposed_%'";
// 		$aResults = $classSqlQuery->MySQL_Queries($strQuery);
// 		$strURL = CORE_GetURL(Const_RawURL, CORE_GetQueryStringVar(Const_Action), CORE_GetQueryStringVar(Const_Phase), 'XXXX', CORE_GetQueryStringVar(Const_Subaction), "");

// 		print'<h3>Transaction Table Selection</h3>';
// 		print'<p>Select the transactions to work with.</p>';
// 		print'<div class="row"><div class="col-md-4"><a href="'.str_replace("XXXX", "Transactions", $strURL).'">main</a></div></div>';
// 		foreach($aResults as $aRow)
// 			print'<div class="row"><div class="col-md-4"><a href="'.str_replace("XXXX", $aRow['TABLE_NAME'], $strURL).'">'.str_replace("Transactions_Proposed_", "", $aRow['TABLE_NAME']).'</a></div></div>';
// 	}
// 	catch (Exception $EX)
// 	{
// 		ErrorHandler::ErrorHandler_CatchError($EX);
// 	}
// }


function DisplayBudgetPhase1()
{
	try
	{
		ErrorHandler::ErrorHandler_MessageDisplay();

		$classScreen = new ScreenElements("Budget", true);
		$classScreen->ScreenElements_AddForm(CORE_GetURL(Const_RawURL, CORE_GetQueryStringVar(Const_Action), CORE_GetQueryStringVar(Const_Phase), '', '', ""), '');
		$strURL = CORE_GetURL(Const_SelfURL, CORE_GetQueryStringVar(Const_Action), CORE_GetQueryStringVar(Const_Phase), '', '', "");
		if(strstr($strURL, "action=Budget"))
			$strURL = str_replace("action=Budget", "action=Trans", CORE_GetURL(Const_SelfURL, CORE_GetQueryStringVar(Const_Action), CORE_GetQueryStringVar(Const_Phase), '', '', ""));
		else
			$strURL = str_replace("action=", "action=Trans", CORE_GetURL(Const_SelfURL, CORE_GetQueryStringVar(Const_Action), CORE_GetQueryStringVar(Const_Phase), '', '', ""));
		print $classScreen->ScreenElements_AddInputFieldHidden('HIDDEN_URL', $strURL);

		$aSubHeads = [];
		$aSubHeads[] = ['Function'=>'ShowGroups(\'ListGroup\')', 'SubHead'=>'manage groups', 'div'=>'Groups']; 
		$aSubHeads[] = ['Function'=>'ShowEnvelopes()', 'SubHead'=>'add envelope', 'div'=>'Envelopes']; 
		$aSubHeads[] = ['Function'=>'OpenTransfer(\'\')', 'SubHead'=>'transfer', 'div'=>'Transfer']; 

		UpdateMonthlyAmounts();
		Finances_AddHeader("Budget", "LoadBudget()");
		Finances_AddSubHeader($aSubHeads);

		Finances_AddCloseAndDiv($aSubHeads);
		Finances_AddDiv("DIV_Budget", false);

		print'<script>LoadBudget()</script>';
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


function DisplayTransPhase1()
{
	try
	{
		ErrorHandler::ErrorHandler_MessageDisplay();

		$classScreen = new ScreenElements("Budget", true);
		$classScreen->ScreenElements_AddForm(CORE_GetURL(Const_RawURL, CORE_GetQueryStringVar(Const_Action), CORE_GetQueryStringVar(Const_Phase), '', '', ""), '');
		$strURL = CORE_GetURL(Const_RawURL, "TransGroups", Const_Phase1, '', '', "");

		$aSubHeads = [];
		$aSubHeads[] = ['Function'=>'ListUncat()', 'SubHead'=>'uncategorized'];
		$aSubHeads[] = ['Function'=>'ListTransactionDups()', 'SubHead'=>'duplicates'];
		$aSubHeads[] = ['Function'=>'FilterOpen()', 'SubHead'=>'filters', 'div'=>'Filters'];
		$aSubHeads[] = ['Function'=>'OpenTransfer(\'\')', 'SubHead'=>'transfer', 'div'=>'Transfer'];

        if(CORE_GetQueryStringVar("FilterEnv") != "")
    		$strJSFunction = "ListTransactions('ByDate', '".CORE_GetQueryStringVar("FilterEnv")."')";
	    else
    		$strJSFunction = "ListTransactions('ByDate', '')";
		Finances_AddHeader("Transactions", $strJSFunction);
		Finances_AddSubHeader($aSubHeads);

		Finances_AddCloseAndDiv($aSubHeads);

		Finances_AddDiv("DIV_Transactions", false);
		$strJSFunction = "ListTransactions('', '".CORE_GetQueryStringVar("FilterEnv")."')";
		print'<script type="text/javascript">'.$strJSFunction.'</script>';
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


function Finances_AddHeader($strTitle, $strJsToCall)
{
	try
	{
		$classScreen = new ScreenElements("Budget", true);
		$classSqlQuery = new SqlDataQueries();
		$classSqlQuery->SpecifyDB("", "www_budget", "", "");

		$strQuery = "SELECT MIN(TransDate) FROM www_budget.Transactions WHERE IgnoreTrans=0";
		$aResults = $classSqlQuery->MySQL_Queries($strQuery);
		if(count($aResults["MIN(TransDate)"]) == ""){
			$aResults[0]['MIN(TransDate)'] = time();
		}

		$strMonth = date("n-Y", time());
		$aMonth = [];
		$aMonths[] = ['ID'=>'All', 'Name'=>'All'];
		$aMonths[] = ['ID'=>'Last 3 months', 'Name'=>'Last 3 months'];
		$aMonths[] = ['ID'=>'Last 6 months', 'Name'=>'Last 6 months'];
		$aMonths[] = ['ID'=>'Last 12 months', 'Name'=>'Last 12 months'];
		while ($strMonth != date("n-Y", $aResults[0]['MIN(TransDate)']))
		{
			$aMonths[] = ['ID'=>$strMonth, 'Name'=>$strMonth];
			$aParts = explode("-", $strMonth);
			$strMonth = date("n-Y", mktime(0,0,0, $aParts[0]-1, 1, $aParts[1]));
		}
		$aMonths[] = ['ID'=>$strMonth, 'Name'=>$strMonth];

		$classSessionMgmt = new SessionMgmt();
		$_SESSION['Month'] = $classSessionMgmt->SessionMgmt_Select("Finances_Month");
		if($_SESSION['Month'] == "")
		{
			$_SESSION['Month'] = $aMonths[4]['ID'];
			$classSessionMgmt->SessionMgmt_Set('Finances_Month', $aMonths[4]['ID']);
		}

		print '<div class="row finances-menu-bar">';
			print '<div class="col-md-6 finances-menu-bar-title">'.$strTitle.'</div>';

			if($strJsToCall != "")
				print '<div class="col-md-3"></div>'.$classScreen->ScreenElements_AddSelect("Month", "", $aMonths, "col-md-3", false, 'onchange="'.$strJsToCall.'"', "");
		print '</div>';

		$classSessionMgmt = new SessionMgmt();
		$iSessionID = $classSessionMgmt->SessionMgmt_GetSessionID();
		print $classScreen->ScreenElements_AddInputFieldHidden('SessionID', $iSessionID);
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


function Finances_AddSubHeader($aSubHeads)
{
	$strBreaker = ' &nbsp;|&nbsp; ';
	$strResult = "";

	print'<div class="row finances-menu-bar">';
		print'<div class="col-md-10">';
			foreach($aSubHeads as $sSubHead)
				$strResult .= '<a href="" onclick="'.$sSubHead['Function'].'; return false">'.$sSubHead['SubHead'].'</a>'.$strBreaker;

			print substr($strResult, 0, strlen($strResult)-strlen($strBreaker));
		print'</div>';
	print'</div>';
}


function Finances_AddDiv($strID, $bIsWell = true)
{
	$strWell = '';
	if($bIsWell)
		$strWell = "finances-well";
	else
		$strWell = "finances-main";

		print'<div id="'.$strID.'" class="'.$strID.' '.$strWell.'">';
		if($strID == "DIV_Filters")
		{
			$classScreen = new ScreenElements("Budget", false);
			$classSqlQuery = new SqlDataQueries();
			$classSqlQuery->SpecifyDB("", "www_budget", "", "");

			$aEnvs = [];
			$strQuery = "SELECT ID, EnvName, GroupName FROM www_budget.Envelope WHERE EnvDateDeactivated=0 ORDER BY GroupName, EnvName";
			$aEnvResults = $classSqlQuery->MySQL_Queries($strQuery);
			BuildTransactionArray($aEnvResults, $aEnvs);

			$strQuery = "SELECT ID, EnvName, GroupName, EnvDateDeactivated FROM www_budget.Envelope WHERE EnvDateDeactivated>0 ORDER BY GroupName, EnvName, EnvDateDeactivated";
			$aEnvResults = $classSqlQuery->MySQL_Queries($strQuery);
			BuildTransactionArray($aEnvResults, $aEnvs);

			print'<div class="left-indent card card-body bg-light">';
				print'<div class="row">';
					print $classScreen->ScreenElements_AddInputField("Keyword", "Keyword", "col-md-2", false, "", "", "");
					$strJSFunction = "ListTransactions('ByEnvelope', '')";
					print $classScreen->ScreenElements_AddSelect("Envelope", "Envelope", $aEnvs, "col-md-4", false, $strJSFunction, "");

					print'<div class="col-md-4 align-buttons">';
						$strJSFunction = "ListTransactions('ByKeyword', '')";
						$classScreen->ScreenElements_AddButton("Filter", "search", "search", "", false, $strJSFunction, "onclick");
						$classScreen->ScreenElements_AddButton("Filter", "clear", "clear", "", false, "ClearSearch('ByKeyword', '')", "onclick");
						print $classScreen->ScreenElements_AddButtonGroup("Filter", 0);
					print'</div>';
				print'</div>';
			print'</div>';
		}


		if($strID == "DIV_Transfer")
		{
			$classScreen = new ScreenElements("Budget", false);
			$classSqlQuery = new SqlDataQueries();
			$classSqlQuery->SpecifyDB("", "www_budget", "", "");
			print $classScreen->ScreenElements_AddInputFieldHidden('HIDDEN_ID', '');

			print'<div id="DIV_TransferMessage"></div>';

			$aEnvs = [];
			$strQuery = "SELECT ID, EnvName, GroupName, EnvDateDeactivated FROM www_budget.Envelope WHERE (EnvDateDeactivated=0 OR EnvDateDeactivated>".mktime(0,0,0, date("m"), 1, date("Y")).") ORDER BY GroupName, EnvName";
			$aEnvResults = $classSqlQuery->MySQL_Queries($strQuery);
			BuildTransactionArray($aEnvResults, $aEnvs);

			print'<div class="left-indent card card-body bg-light">';
				print'<div class="row">';
					print $classScreen->ScreenElements_AddInputFieldDate("TransDate", "Date", "col-md-3", false, "");
					print $classScreen->ScreenElements_AddInputFieldDollar("Amount", "Amount", "col-md-2", false, "", "");

					//print $classScreen->ScreenElements_AddSelect("FromEnvelopeID", "From", $aEnvs, "col-md-3", false, 'onchange="LoadBudget()"', "");
					//print $classScreen->ScreenElements_AddSelect("ToEnvelopeID", "To", $aEnvs, "col-md-3", false, 'onchange="LoadBudget()"', "");
					print $classScreen->ScreenElements_AddSelect("FromEnvelopeID", "From", $aEnvs, "col-md-3", false, '', "");
					print $classScreen->ScreenElements_AddSelect("ToEnvelopeID", "To", $aEnvs, "col-md-3", false, '', "");
				print'</div>';

				print'<div class="row">'.$classScreen->ScreenElements_AddTextArea("Description", "Description", 3, "col-md-11", false, "", "").'</div>';

				print'<div class="row"><div class="col-md-4 bottom-pad">';
					if(CORE_GetQueryStringVar(Const_Action) == "Budget"){
						$classScreen->ScreenElements_AddButton("transfer", "save", "save", "", false, 'SaveTransferFromBudget()', "onclick");
					}
					else {
						$classScreen->ScreenElements_AddButton("transfer", "save", "save", "", false, 'SaveTransfer()', "onclick");
					}
					$classScreen->ScreenElements_AddButton("transfer", "cancel", "cancel", "", false, 'CloseTransfer()', "onclick");
					$classScreen->ScreenElements_AddButton("transfer", "delete", "delete", "", false, 'DeleteTransfer()', "onclick");
					print $classScreen->ScreenElements_AddButtonGroup("transfer", 0);
				print'</div></div>';
			print'</div>';
		}
	print'</div>';
}


function Finances_AddCloseAndDiv($aSubHeads)
{
	$strActions = '';
	foreach($aSubHeads as $aHead)
		if(isset($aHead['div']))
		{
			Finances_AddDiv("DIV_".$aHead['div']);
			$strActions .= ' if($( "#DIV_'.$aHead['div'].'" ).length && "DIV_'.$aHead['div'].'" != $strCurrent) { jQuery("#DIV_'.$aHead['div'].'").hide(); }';
		}

	print'<script>
		function Finances_CloseAll($strCurrent)
		{
			'.$strActions.'
		}
		</script>';
}


function PreloadDatabaseFields()
{
	try
	{
		$classSqlQuery = new SqlDataQueries();
		$classSqlQuery->SpecifyDB("", "www_budget", "", "");
		$classSqlQuery->Transaction_Start();

		$strQuery = "SELECT ID FROM www_budget.Envelope WHERE EnvName='Income' AND Type='income' AND GroupName='Income'";
		$aResults = $classSqlQuery->MySQL_Queries($strQuery);
		if(count($aResults) == 0)
		{
			$strQuery = "INSERT INTO www_budget.Envelope SET EnvName='Income', Type='income', GroupName='Income', EnvDefaultAmount=0, EnvDateCreated=".Const_StartDate.", EnvDateDeactivated=0";
			$aResults = $classSqlQuery->MySQL_Queries($strQuery);

			$strQuery = "INSERT INTO www_budget.EnvelopeAmount SET EnvelopeID=".$aResults['ID'].", EnvAmAmount=0, EnvAmMonth=".Const_StartDate;
			$classSqlQuery->MySQL_Queries($strQuery);
		}

		$strQuery = "SELECT ID FROM www_budget.Envelope WHERE EnvName='Credit Card Payment' AND Type='credit payment' AND GroupName='Credit Card Payment'";
		$aResults = $classSqlQuery->MySQL_Queries($strQuery);
		if(count($aResults) == 0)
		{
			$strQuery = "INSERT INTO www_budget.Envelope SET EnvName='Credit Card Payment', Type='credit payment', GroupName='Credit Card Payment',
															 EnvDefaultAmount=0, EnvDateCreated=".Const_StartDate.", EnvDateDeactivated=0";
			$aResults = $classSqlQuery->MySQL_Queries($strQuery);

			$strQuery = "INSERT INTO www_budget.EnvelopeAmount SET EnvelopeID=".$aResults['ID'].", EnvAmAmount=0, EnvAmMonth=".Const_StartDate;
			$classSqlQuery->MySQL_Queries($strQuery);
		}

		$classSqlQuery->Transaction_Commit();
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
		die;
	}
}


function UpdateMonthlyAmounts()
{
	try
	{
		$classSqlQuery = new SqlDataQueries();
		$classSqlQuery->SpecifyDB("", "www_budget", "", "");

		$strQuery = "SELECT MAX(EnvAmMonth) FROM www_budget.EnvelopeAmount";
		$aResults = $classSqlQuery->MySQL_Queries($strQuery);

		if($aResults[0]['MAX(EnvAmMonth)'] != "")
			$dtLatestRun = mktime(0,0,0, date("m", $aResults[0]['MAX(EnvAmMonth)']), 1, date("Y", $aResults[0]['MAX(EnvAmMonth)']));
		else
			$dtLatestRun = mktime(0,0,0, date("m", Const_StartDate)-1, 1, date("Y", Const_StartDate));

		while($dtLatestRun < mktime(0,0,0, date("m"), 1, date("Y")))
		{
			if(date("n", $dtLatestRun) == 12)
				$dtLatestRun = mktime(0,0,0, date("m", $dtLatestRun)+1, 1, date("Y")-1);
			else
				$dtLatestRun = mktime(0,0,0, date("m", $dtLatestRun)+1, 1, date("Y"));

			$strQuery = "SELECT ID, EnvDefaultAmount FROM www_budget.Envelope WHERE EnvDateDeactivated=0";
			$aResults = $classSqlQuery->MySQL_Queries($strQuery);

			foreach($aResults as $aRow)
			{
				$strQuery = "INSERT INTO www_budget.EnvelopeAmount SET EnvelopeID=".$aRow['ID'].", EnvAmAmount=".$aRow['EnvDefaultAmount'].", EnvAmMonth=".$dtLatestRun;
				$classSqlQuery->MySQL_Queries($strQuery);
			}
		}
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


function DisplayImportPhase1()
{
	try
	{
		ErrorHandler::ErrorHandler_MessageDisplay();

		$classSqlQuery = new SqlDataQueries();
		$classSqlQuery->SpecifyDB("", "www_budget", "", "");

		$classScreen = new ScreenElements("Budget", true);
		print $classScreen->ScreenElements_AddForm(CORE_GetURL(Const_RawURL, CORE_GetQueryStringVar(Const_Action), CORE_GetQueryStringVar(Const_Phase), '', 'Initial', ""), '');

		Finances_AddHeader("Import Transactions", "");
		print'<p>Please select a file for one or more of the financial institutions below.</p>';

		Finances_AddCloseAndDiv([]);

		$aAccounts = ["ChaseAmazon", "IncredibleChecking"];
		print'<span class="Import">';
		foreach($aAccounts as $strName)
		{
			$strQuery = "SELECT MAX(TransDate) FROM Transactions WHERE AccountName='".$strName."'";
			$aResults = $classSqlQuery->MySQL_Queries($strQuery);

			$strSpacedName = preg_replace('/([A-Z])/', ' $1', $strName).'<div class="latest-date">latest transaction date: '.DateEval($aResults).'</div>';
			print '<div class="row top-pad">'.$classScreen->ScreenElements_AddInputFieldUpload($strName, $strSpacedName, "col-md-8", "", $strName).'</div>';
		}
		print'</span>';

		print'<div class="row top-pad"><div class="col-md-4">';
			$classScreen->ScreenElements_AddButton("SubmitTransactions", "Process", "Process", "", false, "", "");
			print $classScreen->ScreenElements_AddButtonGroup("SubmitTransactions", 0);
		print'</div></div>';
		print'</form>';
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


function ProcessImportPhase1()
{
	try
	{
		$classScreen = new ScreenElements("Budget", false);
		$_SESSION['cmsAdmin_InformationalOutcome'] = '';
		$_SESSION['cmsAdmin_ErrMsgArray']="";
		$_SESSION['cmsAdmin_WrnMsgArray']="";

		$aIssues[] = 'Please select at least one or more files to upload.';
		foreach($_FILES as $aFile)
			if($aFile['name'] != "")
				unset($aIssues);

		if(isset($aIssues) && $aIssues != "")
		{
			$_SESSION['cmsAdmin_ErrMsgArray'] = $aIssues;
			throw new Exception(Const_emCorrectItems);
		}
		$aTransactions = array();
		foreach($_FILES as $strName=>$aFile)
			if($aFile["name"] != "")
				ProcessAccount($aTransactions, $strName);
		ProcessLoad($aTransactions);

		header("Location: ".CORE_GetURL(Const_ParentURL, $_REQUEST[Const_Action], Const_Phase1, '', "", ""));
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


function ProcessAccount(&$aTransactions, $strName)
{
	try
	{
		$strType = "";
		if(strstr($strName, "Chase"))
			$strType = "Chase";
		elseif(strstr($strName, "Incredible"))
			$strType = "Incredible";
		else
			throw new Exception("Could not determine format of ".$strName." file.");

		######
		$fhTrans = fopen($_FILES[$strName]['tmp_name'], "r");

		if($strType == "Chase")
			$aExpected = array("Transaction Date", "Post Date", "Description", "Category", "Type", "Amount", "Memo");
		elseif($strType == "Incredible")
			$aExpected = array("Account Name", "Processed Date", "Description", "Check Number", "Credit or Debit", "Amount");

		$iIsFirst = true;
		while (($aLineElements = fgetcsv($fhTrans, 4096, ",")) !== false)
		{
			$aTempArray = array();
			if(count($aLineElements) > 1 && strlen($aLineElements[0]) > 0)
			{
				if($iIsFirst)
				{
					$iIsFirst = false;
					for($I=0; $I<count($aLineElements); $I++)
						if($aLineElements[$I] != $aExpected[$I])
							throw new Exception("Column ".$aLineElements[$I]." appears to be new. Please check the export and see if new columns were added (".$strName.").");
				}
				elseif(count($aLineElements) == count($aExpected))
				{

					if($strType == "Incredible")
					{
						$aDateParts = explode("-", $aLineElements[1]);

						$aTempArray['ReadableDate'] = $aLineElements[1];
						$aTempArray['Description'] = $aLineElements[2];

						if($aLineElements[4] == "Credit")
						{
							$aTempArray['TransactionType'] = 'credit';
							$aTempArray['Amount'] = $aLineElements[5];
						}
						elseif($aLineElements[4] == "Debit")
						{
							$aTempArray['TransactionType'] = 'debit';
							$aTempArray['Amount'] = -$aLineElements[5];
						}
						else
							throw new Exception("An unknown transaction type, '".$aLineElements[4]."' was discovered for ".$strName.".");
					}
					elseif($strType == "Chase")
					{
						$aDateParts = explode("/", $aLineElements[0]);

						$aTempArray['ReadableDate'] = $aLineElements[0];
						$aTempArray['Description'] = $aLineElements[2];
						$aTempArray['Amount'] = floatval($aLineElements[5]);

						if(floatval($aLineElements[5]) >= 0)
							$aTempArray['TransactionType'] = 'credit';
						else
							$aTempArray['TransactionType'] = 'debit';
					}

					$aTempArray['AccountName'] = $strName;
					$aTempArray['OriginalDescription'] = $aTempArray['Description'];

					$aTempArray['TransDate'] = mktime(0,0,0, $aDateParts[0], $aDateParts[1], $aDateParts[2]);
					if($aTempArray['TransDate'] >= Const_StartDate)
					{
						if(!isset($aTempDateLow) || $aTempArray['TransDate'] < $aTempDateLow)
							$aTempDateLow = $aTempArray['TransDate'];

						$aTransactions[] = $aTempArray;
					}
				}
				else
				{
					print'<p>The following row in the '.$strName.' file has an issue:</p>';
					PrintR($aLineElements);
					print'<p>It should have these fields:</p>';
					PrintR($aExpected);
					print'<p>No data has been lost or corrupted, you may fix the issue in the csv and try again.</p>';
					die;
				}
			}
		}

		if(isset($aTempDateLow))
		{
			$classSqlQuery = new SqlDataQueries();
			$strQuery = "SELECT MAX(TransDate) FROM www_budget.Transactions WHERE AccountName='".$strName."'";
			$aResults = $classSqlQuery->MySQL_Queries($strQuery);

			if($aResults[0]['MAX(TransDate)'] != "" && $aTempDateLow > $aResults[0]['MAX(TransDate)'])
				$_SESSION['cmsAdmin_InformationalOutcome'] .= "There appears to be a gap between the oldest entry in your upload and the newest entries on file for '".$strName."'.
															   The oldest entries in this upload are from: ".date("n-j-Y", $aTempDateLow).". The newest entry stored is: ".date("n-j-Y", $aResults[0]['MAX(TransDate)']).".
															   Don't panic! Go to ".$strName." and download transactions that cover that time period. I'll weed out any duplicates.";
		}
	}
	catch (Exception $EX)
	{
		throw new Exception($EX->getMessage());
	}
}


function ProcessLoad($aTransactions)
{
	try
	{
		$classSqlQuery = new SqlDataQueries();
		$classSqlQuery->Transaction_Start();

		$strQuery = "DELETE FROM www_budget.TransactionsTemp";
		$classSqlQuery->MySQL_Queries($strQuery);

		foreach($aTransactions as $aTrans)
		{
			if($aTrans['TransDate'] >= Const_StartDate)
			{
				$strQuery = "INSERT www_budget.TransactionsTemp SET
							 Description='".addslashes($aTrans['Description'])."',
							 OriginalDescription='".addslashes($aTrans['OriginalDescription'])."',
							 Amount='".addslashes(CorrectDecimal($aTrans['Amount']))."',
							 TransactionType='".addslashes($aTrans['TransactionType'])."',
							 AccountName='".addslashes($aTrans['AccountName'])."',
							 TransDate='".addslashes($aTrans['TransDate'])."'";
				$classSqlQuery->MySQL_Queries($strQuery);
			}
		}

		$strQuery = "SELECT * FROM www_budget.TransactionsTemp";
		$aResults = $classSqlQuery->MySQL_Queries($strQuery);
		foreach($aResults as $aRow)
		{
			### CHECK FOR DUP
			$strQuery = "SELECT ID FROM www_budget.XXXXTABLEXXXX WHERE ID!=0 ";
			foreach($aRow as $strName=>$strValue)
			{
				if($strName == "Amount")
					$strQuery .= " AND ".$strName."=".addslashes(CorrectDecimal($strValue));
				elseif($strName != "ID" AND $strName != "OriginalDescription")
					$strQuery .= " AND ".$strName."='".addslashes($strValue)."'";
			}

			ProcessInsert($classSqlQuery, $strQuery, "Transactions", $aRow);
		}

		$strQuery = "DELETE FROM www_budget.TransactionsTemp";
		$classSqlQuery->MySQL_Queries($strQuery);

		$classSqlQuery->Transaction_Commit();

		$_SESSION['cmsAdmin_PositiveOutcome'] = "Successfully loaded ".$GLOBALS["Count_Transactions"]." new transactions to main.";
	}
	catch (Exception $EX)
	{
		$classSqlQuery->Transaction_Rollback();
		throw new Exception($EX->getMessage());
	}
}


function ProcessInsert(&$classSqlQuery, $strQuery, $strTable, $aRow)
{
	try
	{
		if(!isset($GLOBALS["Count_".$strTable]))
			$GLOBALS["Count_".$strTable] = 0;

		$strQuery = str_replace("XXXXTABLEXXXX", $strTable, $strQuery);
		$aResults = $classSqlQuery->MySQL_Queries($strQuery);
		if(count($aResults) == 0)
		{
			$GLOBALS["Count_".$strTable] += 1;
			InsertIntoTransactions($classSqlQuery, $strTable, $aRow['Description'], $aRow['OriginalDescription'], $aRow['Amount'], $aRow['TransactionType'], $aRow['AccountName'], $aRow['TransDate']);
		}
	}
	catch (Exception $EX)
	{
		$classSqlQuery->Transaction_Rollback();
		throw new Exception($EX->getMessage());
	}
}


function InsertIntoTransactions($classSqlQuery, $strTable, $strDescription, $strOriginalDescription, $iAmount, $strTransactionType, $strAccountName, $strTransDate)
{
	try
	{
		$iDateProcessed = time();
		$strDateProcessedReadable = date("n-j-y g:ia", time());

		if(strstr($strDescription, "#"))
			$strGroup = explode("#", $strDescription)[0];
		else
			$strGroup = $strDescription;

		$strQuery = "INSERT www_budget.".$strTable." SET
					 EnvelopeID=0,
					 Description='".addslashes(StripCrap($strDescription))."',
					 OriginalDescription='".addslashes(StripCrap($strOriginalDescription))."',
					 DescriptionGroup='".addslashes(StripCrap($strGroup))."',
					 Amount='".addslashes(ConfirmDecimal($iAmount))."',
					 AmountNumber='".addslashes(round(floatval($iAmount), 2))."',
					 TransactionType='".addslashes($strTransactionType)."',
					 AccountName='".addslashes($strAccountName)."',
					 TransDate='".addslashes($strTransDate)."',
					 TransDateReadable='".addslashes(date("n-j-Y", $strTransDate))."',
					 Notes='',
					 IsSplit=0,
					 ParentID=0,
					 IsTransfer=0,
					 TransferPartnerID=0,
					 TransferType=0,
					 DateLoadedReadable='".$strDateProcessedReadable."',
					 DateLoaded=".$iDateProcessed.",
					 IsDup=0,
					 IsConfirmedNotDup=0,
					 IgnoreTrans=0,
					 MatchID=0,
					 ConfirmedNotDuplicate=0";
		$classSqlQuery->MySQL_Queries($strQuery);

		$strQuery = "INSERT IGNORE INTO www_budget.TransactionsGroup SET
					 DescriptionGroup='".addslashes($strGroup)."', 
					 GroupName='".addslashes($strGroup)."'";
		$classSqlQuery->MySQL_Queries($strQuery);
	}
	catch (Exception $EX)
	{
		$classSqlQuery->Transaction_Rollback();
		throw new Exception($EX->getMessage());
	}
}


function StripCrap($strString)
{
	$strString = trim(preg_replace('/\t+/', '', $strString));

	return $strString;
}


function ConfirmDecimal($iAmount)
{
	if(!strstr($iAmount, "."))
		$iAmount = $iAmount.".00";

	return $iAmount;
}


function CorrectDecimal($iNumber)
{
	$iNumber = str_replace(",", "", $iNumber);

	if(!strstr($iNumber, "."))
		return $iNumber.".00";
	else
	{
		$aParts = explode(".", $iNumber);
		if(strlen($aParts[1]) == 1)
			return $iNumber."0";
		else
			return $iNumber;
	}
}


function DateEval($aResults)
{
	if(isset($aResults[0]['MAX(TransDate)']) && $aResults[0]['MAX(TransDate)'] != "")
		return date("n-j-Y", $aResults[0]['MAX(TransDate)']);
	else
		return "none";
}


function DisplayTransGroupPhase1()
{
	print'<span class="finances-menu-bar-title">Group Management</span>';
	print'<p>Grouping transaction names such as "walmart 5323" and "walmart superstore" into a common group name like "walmart" helps us see totals in the Reports section in a more usable way</p>';
	print'<script> function Finances_CloseAll($strCurrent) { $i = 5; } </script>';

	$strAdd = CORE_GetURL(Const_RawURL, CORE_GetQueryStringVar(Const_Action), CORE_GetQueryStringVar(Const_Phase), "", "", "");
	print'<p><a href="'.$strAdd.'">add new group</a></p>';

	$classSqlQuery = new SqlDataQueries();
	$classSqlQuery->SpecifyDB("", "www_budget", "", "");
	$strQuery = "SELECT DISTINCT(GroupName) FROM TransactionsGroup WHERE DescriptionGroup!=GroupName ORDER BY GroupName";
	$aResults = $classSqlQuery->MySQL_Queries($strQuery);

	foreach($aResults as $aRow)
	{
		$strEdit = CORE_GetURL(Const_RawURL, CORE_GetQueryStringVar(Const_Action), CORE_GetQueryStringVar(Const_Phase), $aRow['GroupName'], 'Edit', "");
		$strDelete = CORE_GetURL(Const_RawURL, CORE_GetQueryStringVar(Const_Action), CORE_GetQueryStringVar(Const_Phase), $aRow['GroupName'], 'Delete', "");
		print'<div class="row"><div class="col-md-12"><h5>'.$aRow['GroupName'].'</h5><a href="'.$strEdit.'">edit</a> &nbsp; <a href="'.$strDelete.'">delete</a></div></div>';

		$strQuery = "SELECT DescriptionGroup FROM TransactionsGroup WHERE GroupName='".addslashes(trim($aRow['GroupName']))."' ORDER BY DescriptionGroup";
		$aMoreResults = $classSqlQuery->MySQL_Queries($strQuery);
		print'<div class="row"><div class="col-md-12 well" style="padding-left:25px;">';
		foreach($aMoreResults as $aAnotherRow)
			print $aAnotherRow['DescriptionGroup'].'<br>';
		print'</div></div>';
	}
}


function DisplayTransGroupPhase2()
{
	print'<span class="finances-menu-bar-title">Group Management</span>';
	ErrorHandler::ErrorHandler_MessageDisplay();
	print'<p>Grouping transaction names such as "walmart 5323" and "walmart superstore" into a common group name like "walmart" helps us see totals in the Reports section in a more usable way</p>';
	print'<script> function Finances_CloseAll($strCurrent) { $i = 5; } </script>';

	$classScreen = new ScreenElements("TransGroup", true);
	print $classScreen->ScreenElements_AddForm(CORE_GetURL(Const_RawURL, CORE_GetQueryStringVar(Const_Action), CORE_GetQueryStringVar(Const_Phase), CORE_GetQueryStringVar(Const_ElementID), CORE_GetQueryStringVar(Const_Subaction), ""), "");

	$classSqlQuery = new SqlDataQueries();
	$classSqlQuery->SpecifyDB("", "www_budget", "", "");
	$strQuery = "SELECT DescriptionGroup as ID, DescriptionGroup as Name FROM www_budget.TransactionsGroup
				 WHERE DescriptionGroup=GroupName OR GroupName='".addslashes(trim(CORE_GetQueryStringVar(Const_ElementID)))."'";
	$aResults = $classSqlQuery->MySQL_Queries($strQuery);

	print '<div class="row">'.$classScreen->ScreenElements_AddInputField("GroupName", "Group name", "col-md-12", true, "", '', "").'</div>';
	print '<div class="row">'.$classScreen->ScreenElements_AddMultiSelect("GroupNameContains", "Group contains", $aResults, "col-md-12", true, "").'</div>';
	print'</form>';

	$strCancel = CORE_GetURL(Const_SelfURL, CORE_GetQueryStringVar(Const_Action), Const_Phase1, "", "", "");
	$classScreen->ScreenElements_AddButton("TransButtonGroup", "Save", "Save", "", true, "", "");
	$classScreen->ScreenElements_AddButton("TransButtonGroup", "Cancel", "Cancel", "", false, "", $strCancel);
	print $classScreen->ScreenElements_AddButtonGroup("TransButtonGroup", 0);
}


function DisplayComparePhase1()
{
	$classScreen = new ScreenElements("Budget", true);
	$classScreen->ScreenElements_AddForm(CORE_GetURL(Const_RawURL, CORE_GetQueryStringVar(Const_Action), CORE_GetQueryStringVar(Const_Phase), '', '', ""), '');

	$aSubHeads = [];
	$aSubHeads[] = ['Function'=>'WipeTransTable(\'Transactions_Proposed_Drew\')', 'SubHead'=>'Wipe Drew\'s table', 'div'=>'Groups']; 
	$aSubHeads[] = ['Function'=>'WipeTransTable(\'Transactions_Proposed_Amy\')', 'SubHead'=>'Wipe Amy\'s table', 'div'=>'Groups']; 

	Finances_AddHeader("Compare Drew and Amy", "");
	Finances_AddSubHeader($aSubHeads);
	Finances_AddCloseAndDiv($aSubHeads);


	if(CORE_GetQueryStringVar("outcome") !="")
	{
		print'<div id="MSG"><p class="finances-success">Table cleared successfully</p></div>';
		print'<script>
			setTimeout(function()
			{
				jQuery("#MSG").slideUp("slow"); 
				$aParts = document.location.href.split("&outcome")
				var obj = { Title: "Finance", Url: $aParts[0] };
		        window.history.pushState(obj, obj.Title, obj.Url);
			}, 4000);
		</script>';
	}
	Finances_AddDiv("DIV_Compare", false);

	print'<script>LoadCompare()</script>';
}





























































function ProcessTransGroup1()
{
	$classSqlQuery = new SqlDataQueries();
	$classSqlQuery->SpecifyDB("", "www_budget", "", "");

	if($_REQUEST[Const_Subaction] == "Edit")
	{
		CORE_ClearSessions();

		$strQuery = "SELECT DescriptionGroup FROM TransactionsGroup WHERE GroupName='".addslashes(trim($_REQUEST[Const_ElementID]))."'";
		$aResults = $classSqlQuery->MySQL_Queries($strQuery);
		$_SESSION['GroupName'] = $_REQUEST[Const_ElementID];
		foreach($aResults as $aRow)
			$aTemp[] = $aRow['DescriptionGroup'];
		$_SESSION['GroupNameContains'] = $aTemp;

		header("Location: ".CORE_GetURL(Const_ParentURL, $_REQUEST[Const_Action], Const_Phase2, $_REQUEST[Const_ElementID], $_REQUEST[Const_Subaction], ""));
	}
	if($_REQUEST[Const_Subaction] == "")
	{
		CORE_ClearSessions();
		
		header("Location: ".CORE_GetURL(Const_ParentURL, $_REQUEST[Const_Action], Const_Phase2, $_REQUEST[Const_ElementID], $_REQUEST[Const_Subaction], ""));
	}
	elseif($_REQUEST[Const_Subaction] == "Delete")
	{
		$strQuery = "UPDATE TransactionsGroup SET GroupName=DescriptionGroup WHERE GroupName='".addslashes(trim($_REQUEST[Const_ElementID]))."'";
		$classSqlQuery->MySQL_Queries($strQuery);

		header("Location: ".CORE_GetURL(Const_ParentURL, $_REQUEST[Const_Action], Const_Phase1, '', "", ""));
	}
}


function ProcessTransGroup2()
{
	$classSqlQuery = new SqlDataQueries();
	$classSqlQuery->SpecifyDB("", "www_budget", "", "");

	if($_REQUEST[Const_ElementID] != "")
	{
		$strQuery = "UPDATE TransactionsGroup SET GroupName=DescriptionGroup WHERE GroupName='".addslashes(trim($_REQUEST[Const_ElementID]))."'";
		$classSqlQuery->MySQL_Queries($strQuery);
	}

	foreach($_REQUEST['GroupNameContains'] as $strDescriptionGroup)
	{
		$strQuery = "UPDATE TransactionsGroup SET GroupName='".addslashes(trim($_REQUEST['GroupName']))."' WHERE DescriptionGroup='".addslashes(trim($strDescriptionGroup))."'";
		$classSqlQuery->MySQL_Queries($strQuery);
	}

	header("Location: ".CORE_GetURL(Const_ParentURL, $_REQUEST[Const_Action], Const_Phase1, '', "", ""));
}


function ListDuplicateTrans($aRow, $strType)
{
	if($aRow['Amount'] == "credit")
		$strAmount = '<span style="color:green">'.$aRow['Amount'].'</span>';
	else
		$strAmount = $aRow['Amount'];

	if($strType == "Child")
		$strCheck = HTML_AddCheck("CHECK_".$aRow['ID'], $aRow['ID'], '', '');
	else
		$strCheck = "";

	print'<tr><td width="25">'.$strCheck.'</td>
			  <td width="75">'.date("n-j-Y", $aRow['TransDate']).'</td>
			  <td style="padding-left:10px;">'.$aRow['Description'].'</td>
			  <td style="padding-left:10px;" align="right">'.$aRow['AccountName'].'</td>
			  <td width="60" style="padding-left:10px;" align="right">'.CORE_FormatMoney($strAmount, true, true, true, false).HTML_AddInputFieldHidden('ORIG_AMOUNT_'.$aRow['ID'], $strAmount).'</td></tr>';
	print'<tr id="TR_'.$aRow['ID'].'" style="display:none;"><td></td><td id="TD_'.$aRow['ID'].'" colspan="3" style="padding-left:10px; padding-bottom:15px;"></td></tr>';
}


function CalcTotals($strDB, $iNMBankStartingAmount, $iChaseStartingAmount)
{
	try
	{
		$classSqlQuery = new SqlDataQueries();
		$classSqlQuery->SpecifyDB("", $strDB, "", "");
	
		define("CONST_iInitialNMBank", $iNMBankStartingAmount);

		### NORMAL
		# Most Recent Transaction
		$strQuery = "SELECT MAX(TransDate) FROM Transactions WHERE AccountName='nmbank primary' AND IsSplit=0 AND IgnoreTrans=0 AND IsTransfer=0";
		$aResults = $classSqlQuery->MySQL_Queries($strQuery);
		$strNMBankDate = date("n-j-Y", $aResults[0]['MAX(TransDate)']);

		# Transactions
		$strQuery = "SELECT SUM(Amount) FROM Transactions WHERE AccountName='nmbank primary' AND IsSplit=0 AND IgnoreTrans=0 AND IsTransfer=0 AND TransactionType='debit' AND TransDate>=".Const_IgnorePriorTo;
		$aExpResults = $classSqlQuery->MySQL_Queries($strQuery);
		$iNMBankExpend = $aExpResults[0]['SUM(Amount)'];

		# Income
		$strQuery = "SELECT SUM(Amount) FROM Transactions WHERE AccountName='nmbank primary' AND IsSplit=0 AND IgnoreTrans=0 AND IsTransfer=0 AND TransactionType='credit' AND TransDate>=".Const_StartDate;
		$aIncResults = $classSqlQuery->MySQL_Queries($strQuery);
		$iNMBankIncome = $aIncResults[0]['SUM(Amount)'];

		$iNMBankBalance = (CONST_iInitialNMBank+$iNMBankIncome-$iNMBankExpend);

		# Most Recent Transaction
		define("CONST_iInitialChase", $iChaseStartingAmount);
		$strQuery = "SELECT MAX(TransDate) FROM Transactions WHERE AccountName='chase' AND IsSplit=0 AND IgnoreTrans=0 AND IsTransfer=0";
		$aResults = $classSqlQuery->MySQL_Queries($strQuery);
		$strChaseDate = date("n-j-Y", $aResults[0]['MAX(TransDate)']);

		# Transactions
		$strQuery = "SELECT SUM(Amount) FROM Transactions WHERE AccountName='chase' AND IsSplit=0 AND IgnoreTrans=0 AND IsTransfer=0 AND TransactionType='debit' AND TransDate>=".Const_StartDate;
		$aResults = $classSqlQuery->MySQL_Queries($strQuery);
		$iChaseExpend = $aResults[0]['SUM(Amount)'];

		# Income
		$strQuery = "SELECT SUM(Amount) FROM Transactions WHERE AccountName='chase' AND IsSplit=0 AND IgnoreTrans=0 AND IsTransfer=0 AND TransactionType='credit' AND TransDate>=".Const_StartDate;
		$aResults = $classSqlQuery->MySQL_Queries($strQuery);
		$iChaseIncome = $aResults[0]['SUM(Amount)'];

		$iChaseBalance = ($iChaseIncome-$iChaseExpend-CONST_iInitialChase);


		##### END OF MONTH BALANCE BASED ON EXPECTED TRANSACTIONS
		$strQuery = "SELECT ID, EnvName, EnvDateCreated FROM Envelope WHERE Type='expenditure' AND (EnvDateDeactivated=0 OR EnvDateDeactivated>".time().")";
		$aEnv = $classSqlQuery->MySQL_Queries($strQuery);
		$iBudgetOutRemaining = 0;

		foreach($aEnv as $aRow)
		{
			##### Total Amount Budgeted for Envelope
			$strQuery = "SELECT SUM(EnvAmAmount) FROM EnvelopeAmount WHERE EnvelopeID=".$aRow['ID'];
			$aBudgetedOut = $classSqlQuery->MySQL_Queries($strQuery);
			$iBudgetedOut = $aBudgetedOut[0]['SUM(EnvAmAmount)'];

			##### Total Added in Addition to Budgeted
			$strQuery = "SELECT SUM(Amount) FROM Transactions WHERE TransactionType='credit' AND IgnoreTrans=0 AND IsSplit=0 AND EnvelopeID=".$aRow['ID'];
			$aMoneyAddedResult = $classSqlQuery->MySQL_Queries($strQuery);
			if($aMoneyAddedResult[0]['SUM(Amount)'] != "")
				$iMoneyAdded = $aMoneyAddedResult[0]['SUM(Amount)'];
			else
				$iMoneyAdded = 0;

			$iTotalAmountAvailable = ($iBudgetedOut + $iMoneyAdded);

			##### Total Amount Spent for Envelope
			$strQuery = "SELECT SUM(Amount) FROM Transactions WHERE TransactionType='debit' AND IgnoreTrans=0 AND IsSplit=0 AND EnvelopeID=".$aRow['ID'];
			$aMoneySpentResult = $classSqlQuery->MySQL_Queries($strQuery);
			if($aMoneySpentResult[0]['SUM(Amount)'] != "")
				$iMoneySpent = $aMoneySpentResult[0]['SUM(Amount)'];
			else
				$iMoneySpent = 0;

			##### Any Budgeted Above Spent is Still Assumed to be Spent by End of Month
			$iBalance = ($iTotalAmountAvailable - $iMoneySpent);

			$iBudgetOutRemaining += ($iBalance);
		}

		##### Total Budgeted Income to the Income Folder
		$strQuery = "SELECT SUM(EnvAmAmount) FROM EnvelopeAmount WHERE EnvelopeID=3";
		$aBudgetedIn = $classSqlQuery->MySQL_Queries($strQuery);
		if($aBudgetedIn[0]['SUM(EnvAmAmount)'] != "")
			$iBudgetedIn = $aBudgetedIn[0]['SUM(EnvAmAmount)'];
		else
			$iBudgetedIn = 0;

		##### Total Actual Income to the Income Folder
		$strQuery = "SELECT SUM(Amount) FROM Transactions WHERE TransactionType='credit' AND IgnoreTrans=0 AND IsSplit=0 AND IsTransfer=0 AND EnvelopeID=3";
		$aActualCredits = $classSqlQuery->MySQL_Queries($strQuery);
		if($aActualCredits[0]['SUM(Amount)'] != "")
			$iActualCredits = $aActualCredits[0]['SUM(Amount)'];
		else
			$iActualCredits = 0;

		##### Total Actual Debits from the Income Folder
		$strQuery = "SELECT SUM(Amount) FROM Transactions WHERE TransactionType='debit' AND IgnoreTrans=0 AND IsSplit=0 AND IsTransfer=0 AND EnvelopeID=3";
		$aActualDebits = $classSqlQuery->MySQL_Queries($strQuery);
		if($aActualDebits[0]['SUM(Amount)'] != "")
			$iActualDebits = $aActualDebits[0]['SUM(Amount)'];
		else
			$iActualDebits = 0;

		$iActualIncomeBalance = $iActualCredits-$iActualDebits;
		$iIncomeToAdd = ($iBudgetedIn-$iActualIncomeBalance);

		#### END OF MONTH ALL MAXED
		$iFinalBalance = ($iIncomeToAdd-$iBudgetOutRemaining);


		$strEndOfMonth = date("n-j-Y", mktime(0,0,0, date("m")+1, 0, date("Y")));

		$strQuery = "SELECT ID FROM Transactions WHERE EnvelopeID=0 AND IgnoreTrans=0 AND IsSplit=0 ";
		$aUncat = $classSqlQuery->MySQL_Queries($strQuery);
		if(count($aUncat) > 0)
			$strAccuracy = "(you have uncategorized transactions, this number will not be accurate)";
		else
			$strAccuracy = '(as of '.$strEndOfMonth.')';

		/*
		print'<span style="font-size:small">';
		print'<table style="padding-bottom:20px; padding-top:10px;">';
		print'<tr><td colspan="4"><strong>Account Balances</strong></td></tr><tr>';
		print'<tr><td style="padding-left:20px;">Chase Credit Card Balance: </td><td style="padding-left:5px;">'.CORE_FormatMoney($iChaseBalance, true, true, false, true).'</td><td style="padding-left:5px;"><span style="font-size:smaller">(as of '.$strChaseDate.')</span></td></tr>';
		print'<tr><td style="padding-left:20px;">Northern Michigan Bank Balance: </td><td style="padding-left:5px;">'.CORE_FormatMoney($iNMBankBalance, true, true, false, true).'</td><td style="padding-left:5px;"><span style="font-size:smaller">(as of '.$strNMBankDate.')</span></td></tr>';
		print'</tr></table>';
		print'</span>';	
		*/
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}	
}


function DisplayReportPhase1()
{
	try
	{
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


function CHANGE_DATES()
{
	$classSqlQuery = new SqlDataQueries();
	$classSqlQuery->SpecifyDB("", "www_budget", "", "");

	$strQuery = "SELECT * FROM www_budget.Transactions ";
	$aResults = $classSqlQuery->MySQL_Queries($strQuery);
		
	foreach($aResults as $aRow)
	{
		$aParts = explode("-", $aRow['TransDateReadable']);
		$iNewDate = mktime(0,0,0, $aParts[0], $aParts[1], $aParts[2]);
		$strQuery = "UPDATE www_budget.Transactions SET TransDate=".$iNewDate." WHERE ID=".$aRow['ID'];
		$classSqlQuery->MySQL_Queries($strQuery);
	}
}

#if(strstr($_SERVER['SCRIPT_NAME'], Const_SelfName) != false)
{
	require_once dirname(__FILE__)."/Includes/2015_FunctionsCommon.php";

	$aNameParts = explode(".", Const_SelfName);
	$strMain = $aNameParts[0]."_Main";
	$strMain();
}



?>


