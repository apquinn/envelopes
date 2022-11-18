<?php
require_once dirname(__FILE__)."/Includes/2015_FunctionsCommon.php";
define("Const_SelfName", "Finances.php");

function MainFinanceFunctions()
{
	try
	{
		$strStyle = 'style="float:left; padding-left:20px; "';

		###### BUDGET ######
		// GROUPS
		if(isset($_REQUEST[Const_Action]) && $_REQUEST[Const_Action] == "ListGroup")
			ListGroup('');

		if(isset($_REQUEST[Const_Action]) && $_REQUEST[Const_Action] == "EditGroup")
			ListGroup($_REQUEST['ID']);

		if(isset($_REQUEST[Const_Action]) && $_REQUEST[Const_Action] == "DeleteGroup")
			DeleteGroup($_REQUEST['ID']);

		if(isset($_REQUEST[Const_Action]) && $_REQUEST[Const_Action] == "AddGroup")
			AddGroup($_REQUEST['NewGroup'], $_REQUEST['ID']);


		// ENVELOPES
		if(isset($_REQUEST[Const_Action]) && $_REQUEST[Const_Action] == "AddEnvDisplay")
			AddEnvDisplay();

		if(isset($_REQUEST[Const_Action]) && $_REQUEST[Const_Action] == "SaveEditEnv")
			SaveEditEnv($_REQUEST['EnvName'], $_REQUEST['EnvAmount'], $_REQUEST['GroupName'], $_REQUEST['ID'], $_REQUEST['Month']);

		if(isset($_REQUEST[Const_Action]) && $_REQUEST[Const_Action] == "LoadBudget")
			LoadBudget($_REQUEST['Month'], $_REQUEST['URL'], $_REQUEST['SessionID'], $_REQUEST['DisplayType']);

		if(isset($_REQUEST[Const_Action]) && $_REQUEST[Const_Action] == "EditEnv")
			EditEnv($_REQUEST['ID']);

		if(isset($_REQUEST[Const_Action]) && $_REQUEST[Const_Action] == "Deactivate")
			Deactivate($_REQUEST['ID']);


		###### TRANSACTIONS ######
		if(isset($_REQUEST[Const_Action]) && $_REQUEST[Const_Action] == "SaveTransCat")
			SaveTransCat($_REQUEST['ID'], $_REQUEST['Cat'], $_REQUEST['Caller']);

		if(isset($_REQUEST[Const_Action]) && $_REQUEST[Const_Action] == "ShowTransDetails")
			ShowTransDetails($_REQUEST['ID'], $strStyle, $_REQUEST["View"]);

		if(isset($_REQUEST[Const_Action]) && $_REQUEST[Const_Action] == "SaveTransNotes")
			SaveTransNotes($_REQUEST['ID']);

		if(isset($_REQUEST[Const_Action]) && $_REQUEST[Const_Action] == "SplitTrans")
			SplitTrans($_REQUEST['ID'], $_REQUEST['NUMBER'], $strStyle, $_REQUEST['Month']);

		if(isset($_REQUEST[Const_Action]) && $_REQUEST[Const_Action] == "ListTransactions")
			ListTransactions($_REQUEST['Type'], $_REQUEST['Month'], $_REQUEST['Envelope'], $_REQUEST['Keyword'], $_REQUEST['SessionID']);

		if(isset($_REQUEST[Const_Action]) && $_REQUEST[Const_Action] == "ListTransactionDups")
			ListTransactionDups();

		if(isset($_REQUEST[Const_Action]) && $_REQUEST[Const_Action] == "MarkDup")
			MarkDup($_REQUEST['ID'], $_REQUEST['Type']);


		// COMPARE
		if(isset($_REQUEST[Const_Action]) && $_REQUEST[Const_Action] == "WipeTransTable")
			WipeTransTable($_REQUEST['Table']);

		if(isset($_REQUEST[Const_Action]) && $_REQUEST[Const_Action] == "SetTransCat")
			SetTransCat($_REQUEST['TransID'], $_REQUEST['EnvID']);


		// TRANSFER
		if(isset($_REQUEST[Const_Action]) && $_REQUEST[Const_Action] == "OpenTransfer")
			OpenTransfer($_REQUEST['ID']);

		if(isset($_REQUEST[Const_Action]) && $_REQUEST[Const_Action] == "SaveTransfer")
			SaveTransfer($_REQUEST['ID'], $_REQUEST['TransDate'], $_REQUEST['Amount'], $_REQUEST['Description'], $_REQUEST['FromEnvelopeID'], $_REQUEST['ToEnvelopeID']);

		if(isset($_REQUEST[Const_Action]) && $_REQUEST[Const_Action] == "DeleteTransfer")
			DeleteTransfer($_REQUEST['ID']);



		###### Sync ######
		if(isset($_REQUEST[Const_Action]) && $_REQUEST[Const_Action] == "SyncAmyDrew")
			SyncAmyDrew();

	}
	catch (Exception $EX)
	{
		print Const_emSQLError.": <BR>".$EX->getMessage();
	}
}


function AddChoice($strName, $iTransID, $iEnvID, &$aEnvTrans)
{
	if($iEnvID == 0)
		print'<div class="row finance-options-pad"><div class="col-md-2">'.$strName.'</div><div class="col-md-8">'.$aEnvTrans[$iEnvID].'</div></div>';
	else
		print'<div class="row finance-options-pad"><div class="col-md-2">'.$strName.'</div><div class="col-md-8"><a href="" onclick="SetTransCat('.$iTransID.', '.$iEnvID.'); return false;">'.$aEnvTrans[$iEnvID].'</a></div></div>';
}


function AddEnvDisplay()
{
	try
	{
		$classScreen = new ScreenElements("Budget", false);
		print $classScreen->ScreenElements_AddForm(CORE_GetURL(Const_RawURL, CORE_GetQueryStringVar(Const_Action), CORE_GetQueryStringVar(Const_Phase), '', '', ""), '');

		$classSqlQuery = new SqlDataQueries();
		$classSqlQuery->SpecifyDB("", "www_budget", "", "");
		$strQuery = "SELECT GroupName as ID, GroupName as Name FROM Groups ORDER BY GroupName";
		$aResults = $classSqlQuery->MySQL_Queries($strQuery);

		print'<div class="left-indent card card-body bg-light">';
			print'<div class="row">';
				print $classScreen->ScreenElements_AddInputField("EnvName", "Name", "col-md-4", false, "", "", "");
				print $classScreen->ScreenElements_AddInputFieldDollar("EnvAmount", "Amount", "col-md-2", false, "", false);
				print $classScreen->ScreenElements_AddSelect("GroupName", "Group (optional)", $aResults, "col-md-4", false, '', '');
			print'</div>';

			print'<div class="row">';
				print'<div class="col-md-5">';
					$classScreen->ScreenElements_AddButton("envelope", "save", "save envelope", "", false, 'SaveEditEnv(\'\')', "");
					$classScreen->ScreenElements_AddButton("envelope", "cancel", "cancel", "", false, 'ShowEnvelopes()', "");
					print $classScreen->ScreenElements_AddButtonGroup("envelope", 0);
				print'</div>';
			print'</div>';
		print'</div>';
		print'</form>';
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


function EditEnv($iID)
{
	try
	{
		$classScreen = new ScreenElements("Budget", false);
		$classSqlQuery = new SqlDataQueries();
		$classSqlQuery->SpecifyDB("", "www_budget", "", "");

		$strQuery = "SELECT * FROM www_budget.Envelope WHERE ID=".$iID;
		$aResults = $classSqlQuery->MySQL_Queries($strQuery);

		$_SESSION['EnvName_'.$iID] = $aResults[0]['EnvName'];
		$_SESSION['EnvAmount_'.$iID] = $aResults[0]['EnvDefaultAmount'];
		$_SESSION['Type_'.$iID] = $aResults[0]['Type'];
		$_SESSION['GroupName_'.$iID] = $aResults[0]['GroupName'];

		$strQuery = "SELECT GroupName as ID, GroupName as Name FROM Groups ORDER BY GroupName";
		$aResults = $classSqlQuery->MySQL_Queries($strQuery);

		if($_SESSION['Type_'.$iID] == "expenditure")
		{
			print'<div class="left-indent card card-body bg-light">';
				print'<div class="row">';
					print $classScreen->ScreenElements_AddInputField("EnvName_".$iID, "Name", "col-md-2", false, "", "", "");
					print $classScreen->ScreenElements_AddInputFieldDollar("EnvAmount_".$iID, "Amount", "col-md-2", false, "", false);
					print $classScreen->ScreenElements_AddSelect("GroupName_".$iID, "Group (optional)", $aResults, "col-md-3", false, '', '');
				print'</div>';

				print'<div class="row"><div class="col-md-12">';
					print'<a href="" onclick="SaveEditEnv('.$iID.'); return false;">save</a> &nbsp;';
					print'<a href="" onclick="CloseEnvEdit('.$iID.'); return false;">cancel</a> &nbsp;';
					print'<a href="" onclick="Deactivate('.$iID.'); return false;">deactivate</a>';
				print'</div></div>';

				print'<div class="row"><div class="col-md-12 finance-note-text">Deactivating an envelope does not delete it. The envelope continues to exist with all transactions preserved. It will continue to show up in reports for months in with it was active. However, you will no longer be able to add transactions nor will it be used to calculate your total monthly budget going forward.</div></div>';
			print'</div>';
		}
		elseif($_SESSION['Type_'.$iID] == "income")
		{
			print'<div class="left-indent card card-body bg-light">';
				print'<div class="row">';
					print $classScreen->ScreenElements_AddInputFieldHidden('EnvName_'.$iID, $_SESSION['EnvName_'.$iID]);
					print $classScreen->ScreenElements_AddInputFieldDollar("EnvAmount_".$iID, "Amount", "col-md-2", false, "", false);
					print $classScreen->ScreenElements_AddSelect("GroupName_".$iID, "Group (optional)", $aResults, "col_md_3", false, '', '');
				print'</div>';

				print'<div class="row"><div class="col-md-12">';
					print'<a href="" onclick="SaveEditEnv('.$iID.'); return false;">save</a> &nbsp; ';
					print'<a href="" onclick="CloseEnvEdit('.$iID.'); return false;">cancel</a>';
				print'</div></div>';
			print'</div>';
		}
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


function LoadBudget($strMonth, $strURL, $iSessionID, $strDisplayType)
{
	try
	{
		$dtBeginning = "";
		$dtEnd = "";

		if($strMonth == "")
			die;

		$classScreen = new ScreenElements("Budget", false);
		$classSessionMgmt = new SessionMgmt();
		$classSessionMgmt->SessionMgmt_SetSessionID($iSessionID);
		$classSessionMgmt->SessionMgmt_Set('Finances_Month', $strMonth);

		CalculateDates($strMonth, $dtBeginning, $dtEnd);

		$classSqlQuery = new SqlDataQueries();
		$classSqlQuery->SpecifyDB("", "www_budget", "", "");
		$strQuery = "SELECT * FROM Envelope WHERE EnvDateCreated<=".$dtEnd." AND Type='expenditure' AND (EnvDateDeactivated=0 OR EnvDateDeactivated>=".$dtEnd.") ORDER BY GroupName, EnvName";
		$aExpResults = $classSqlQuery->MySQL_Queries($strQuery);

		$strQuery = "SELECT * FROM Envelope WHERE EnvDateCreated<=".$dtEnd." AND Type='income' AND (EnvDateDeactivated=0 OR EnvDateDeactivated>=".$dtEnd.") ORDER BY GroupName, EnvName";
		$aIncomeResults = $classSqlQuery->MySQL_Queries($strQuery);

		$strQuery = "SELECT * FROM Envelope WHERE EnvDateCreated<=".$dtEnd." AND Type='credit payment' AND (EnvDateDeactivated=0 OR EnvDateDeactivated>=".$dtEnd.") ORDER BY GroupName, EnvName";
		$aCCResults = $classSqlQuery->MySQL_Queries($strQuery);


		$strOutput = $classScreen->ScreenElements_AddInputFieldHidden('DisplayType', $strDisplayType);
		if(count($aExpResults) > 0 || count($aIncomeResults) > 0 || count($aCCResults) > 0)
		{
			print'<div style="padding-top:0px; padding-bottom:8px; font-weight:bold">Expenditures</div>';
			if(count($aExpResults) > 0)
				$strOutput .= DrawBudgetItems($aExpResults, $dtBeginning, $dtEnd, 'Exp', $strURL, $strDisplayType);

			print'<div style="padding-top:8px; padding-bottom:8px; font-weight:bold">Income</div>';
			if(count($aIncomeResults) > 0)
				$strOutput .= DrawBudgetItems($aIncomeResults, $dtBeginning, $dtEnd, 'Income', $strURL, $strDisplayType);

			print'<div style="padding-top:8px; padding-bottom:8px; font-weight:bold">Credit Card</div>';
			if(count($aIncomeResults) > 0)
				$strOutput .= DrawBudgetItems($aCCResults, $dtBeginning, $dtEnd, 'Exp', $strURL, $strDisplayType);

			print "YYYYY".$strOutput;
		}
		else
			print'You have not created any envelopes<br/>';
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


function CalculateDates($strMonth, &$dtBeginning, &$dtEnd)
{
	if($strMonth == "All")
	{
		$dtBeginning = mktime(0,0,0, date("n", Const_StartDate)-1, 1, date("y", Const_StartDate));
		$dtEnd = mktime(0,0,0, date("n")+1, 0, date("y"));
	}
	elseif($strMonth == "Last 3 months")
	{
		$dtBeginning = mktime(0,0,0, date("n")-2, 1, date("y"));
		$dtEnd = mktime(0,0,0, date("n")+1, 0, date("y"));
	}
	elseif($strMonth == "Last 6 months")
	{
		$dtBeginning = mktime(0,0,0, date("n")-5, 1, date("y"));
		$dtEnd = mktime(0,0,0, date("n")+1, 0, date("y"));
	}
	elseif($strMonth == "Last 12 months")
	{
		$dtBeginning = mktime(0,0,0, date("n")-12, 1, date("y"));
		$dtEnd = mktime(0,0,0, date("n")+1, 0, date("y"));
	}
	else
	{
		if($strMonth == "")
			$strMonth = date("n-Y");
		$aParts = explode("-", $strMonth);
		$dtBeginning = mktime(0,0,0, $aParts[0], 1, $aParts[1]);
		$dtEnd = mktime(0,0,0, $aParts[0]+1, 0, $aParts[1]);
	}
}

function DrawBudgetItems($aResults, $dtBeginning, $dtEnd, $strType, $strURL, $strDisplayType)
{
	try
	{
		$classScreen = new ScreenElements("Budget", false);
		$classSqlQuery = new SqlDataQueries();
		$classSqlQuery->SpecifyDB("", "www_budget", "", "");

		foreach($aResults as $aRow)
		{
			if($aRow['GroupName'] != $aRow['EnvName'])
				$strName = $aRow['GroupName'].' - '.$aRow['EnvName'];
			else
				$strName = $aRow['EnvName'];

			$strQuery = "SELECT SUM(EnvAmAmount) FROM EnvelopeAmount WHERE EnvelopeID=".$aRow['ID']." AND EnvAmMonth<=".$dtEnd;
			$aBudgetedIncome = $classSqlQuery->MySQL_Queries($strQuery);
			if($aBudgetedIncome[0]['SUM(EnvAmAmount)'] == "")
				$aBudgetedIncome[0]['SUM(EnvAmAmount)'] = 0;

			$strQuery = "SELECT SUM(Amount) FROM Transactions WHERE TransactionType='debit' AND IgnoreTrans=0 AND IsSplit=0 AND EnvelopeID=".$aRow['ID']." AND TransDate<=".$dtEnd;
			$aActualExp = $classSqlQuery->MySQL_Queries($strQuery);
			if($aActualExp[0]['SUM(Amount)'] == "")
				$aActualExp[0]['SUM(Amount)'] = 0;

			$strQuery = "SELECT SUM(Amount) FROM Transactions WHERE TransactionType='credit' AND IgnoreTrans=0 AND IsSplit=0 AND EnvelopeID=".$aRow['ID']." AND TransDate<=".$dtEnd;
			$aActualCredits = $classSqlQuery->MySQL_Queries($strQuery);
			if($aActualCredits[0]['SUM(Amount)'] == "")
				$aActualCredits[0]['SUM(Amount)'] = 0;

			$strQuery = "SELECT EnvAmAmount FROM EnvelopeAmount WHERE EnvelopeID=".$aRow['ID']." AND EnvAmMonth>=".$dtBeginning." AND EnvAmMonth<=".$dtEnd;
			$aThisMonthAmountResults = $classSqlQuery->MySQL_Queries($strQuery);
			if($aThisMonthAmountResults[0]['EnvAmAmount'] == "")
				$aThisMonthAmountResults[0]['EnvAmAmount'] = 0;


			if($strType == 'Income')
			{
				$iActualBalance = $aActualCredits[0]['SUM(Amount)']-$aActualExp[0]['SUM(Amount)'];
				$iPlanned = $aBudgetedIncome[0]['SUM(EnvAmAmount)'];
				if($iPlanned > $iActualBalance)
					$iBalance = $aThisMonthAmountResults[0]['EnvAmAmount']-($iPlanned-$iActualBalance);
				else
					$iBalance = $aThisMonthAmountResults[0]['EnvAmAmount']+($iActualBalance-$iPlanned);
			}
			else
			{
				$iTotalBalance = ($aBudgetedIncome[0]['SUM(EnvAmAmount)']+$aActualCredits[0]['SUM(Amount)']+$aActualExp[0]['SUM(Amount)']);
				$iBalance = ($aThisMonthAmountResults[0]['EnvAmAmount']-$iTotalBalance);
			}

			$strName = '<a href="'.$strURL.'&FilterEnv='.$aRow['ID'].'" style="text-decoration: none">'.$strName.'</a>';
			if($aRow['EnvDateDeactivated'] == 0 && $aRow['Type'] != "credit payment")
				$strName = '<span style="font-size:small">(<a href="" onclick="EditEnv('.$aRow['ID'].'); return false;" style="text-decoration: none">edit</a>)</span> '.$strName;

			$strStatus = CORE_FormatMoney($iBalance, true, false, false, false).' of '.CORE_FormatMoney($aThisMonthAmountResults[0]['EnvAmAmount'], true, false, false, false);
			$strStatus .= $classScreen->ScreenElements_AddInputFieldHidden('ENV_EDIT_STATUS_'.$aRow['ID'], 'Closed');
			print'<div class="row">
						<div class="col-sm-8">'.$strName.'</div>
						<div class="col-sm-4 finance-budget-amount">'.$strStatus.'</div>
				  </div>';

			if($iBalance == "" || $iBalance < 0)
				$iBalance = 0;

			if($aThisMonthAmountResults[0]['EnvAmAmount'] == "" || $aThisMonthAmountResults[0]['EnvAmAmount'] < 0)
				$aThisMonthAmountResults[0]['EnvAmAmount'] = 0;

			AddProgressBar($strType, $iBalance, $aThisMonthAmountResults[0]['EnvAmAmount']);

			print'<div class="row"><div class="col-sm-12"><div id="DIV_ENV_EDIT_'.$aRow['ID'].'" style="display:none;"></div></div></div>';
		}
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


function AddProgressBar($strType, $iBalance, $iThisMonthAmountResults)
{
	$iBalance = round($iBalance, 0);
	$iThisMonthAmountResults = round($iThisMonthAmountResults, 0);

	if($iBalance >= $iThisMonthAmountResults)
		$iPercentage = 100;
	elseif($iBalance == 0)
		$iPercentage = 0;
	else
		$iPercentage = round(($iBalance/$iThisMonthAmountResults), 2)*100;
	
	$strColor1 = 'green';
	$strColor2 = 'red';

	if($strType == "Income")
	{
		$strTemp = $strColor1;
		$strColor1 = $strColor2;
		$strColor2 = $strTemp;
	}

	print'<div class="row"><div class="col-md-12">';
		print'<div class="fincance-bar-wrapper" style="background:'.$strColor1.'">';
			print'<div class="fincance-bar" style="background:'.$strColor2.'; width:'.$iPercentage.'%;"></div>';
		print'</div>';
	print'</div></div>';
}


function ListGroup($iID)
{
	try
	{
		$classScreen = new ScreenElements("Budget", false);
		$classSqlQuery = new SqlDataQueries();
		$classSqlQuery->SpecifyDB("", "www_budget", "", "");

		print'<div class="left-indent card card-body bg-light">';
			if($iID == '')
			{
				$strQuery = "SELECT * FROM Groups ORDER BY GroupName";
				$aResults = $classSqlQuery->MySQL_Queries($strQuery);

				foreach($aResults as $aRow)
					print '<div class="row">
							<div class="col-md-6">
								<a href="" onclick="GroupActions(\'DeleteGroup\', \''.$aRow['ID'].'\'); return false;">delete</a> &nbsp;
								<a href="" onclick="GroupActions(\'EditGroup\', \''.$aRow['ID'].'\'); return false;">edit</a> &nbsp; '.
								$aRow['GroupName'].'
							</div>
						</div>';

				print '<div class="row top-pad">';
						print $classScreen->ScreenElements_AddInputField("NewGroup", "New group", "col-md-4", false, "", "", "");

						print'<div class="col-md-4 align-buttons">';
							$classScreen->ScreenElements_AddButton("transfer", "save", "add new group", "", false, 'GroupActions(\'AddGroup\', \'\')', "");
							print $classScreen->ScreenElements_AddButtonGroup("transfer", 0);
						print'</div>';
				print'</div>';
			}
			else
			{
				$strQuery = "SELECT GroupName FROM Groups WHERE ID=".$iID;
				$aResults = $classSqlQuery->MySQL_Queries($strQuery);
				$_SESSION['NewGroup'] = $aResults[0]['GroupName'];

				print '<div class="row">'.
						$classScreen->ScreenElements_AddInputField("NewGroup", "New group", "col-md-4", false, "", "", "").'
						<div class="col-md-3 finances-budget-group-edit"><a href="" onclick="GroupActions(\'AddGroup\', \''.$iID.'\'); return false;">save</a> &nbsp; <a href="" onclick="GroupActions(\'Cancel\', \'\'); return false;">cancel</a></div>
					</div>';

				$_SESSION['NewGroup'] = '';
			}
		print'</div>';
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


function ListTransactions($strType, $strMonth, $iEnvelopeID, $strKeyword, $iSessionID)
{
	try
	{
		$dtBeginning = "";
		$dtEnd = "";

		$classSessionMgmt = new SessionMgmt();
		$classSessionMgmt->SessionMgmt_SetSessionID($iSessionID);
		$classSessionMgmt->SessionMgmt_Set('Finances_Month', $strMonth);

		$classScreen = new ScreenElements("Budget", true);
		$bSavePresets = false;

		if($strType == "ByDate" && $strMonth == "")
			$strMonth = date("n-Y", time());
		if($strType == "ByEnvelope" && $iEnvelopeID == "")
			$strType = "";
		if($strType == "ByKeyword" && $strKeyword == "")
			$strType = "";

		if($strMonth == "All" || $strType == "Uncat")
		{
			$dtBeginning = mktime(0,0,0, 1, 1, 2000);
			$dtEnd = mktime(0,0,0, date("m", time())+1, 0, date("Y", time()));
		}
		elseif(substr($strMonth, 0, 4) == "Last")
		{
			$iMonths = str_replace("Last ", "", str_replace(" months", "", $strMonth));
			$dtBeginning = mktime(0,0,0, date("n")-$iMonths, date("j"), date("y"));
			$dtEnd = time();
		}
		else
			CalculateDates($strMonth, $dtBeginning, $dtEnd);

		$classSqlQuery = new SqlDataQueries();
		$classSqlQuery->SpecifyDB("", "www_budget", "", "");


		$strHeading = "";
		if($iEnvelopeID != "")
			$strHeading .= $classSqlQuery->MySQL_Queries("SELECT EnvName FROM www_budget.Envelope WHERE ID=".$iEnvelopeID)[0]['EnvName'];

		if($strKeyword != "")
		{
			if($strHeading != "")
				$strHeading .= ", ";
			$strHeading .= $strKeyword;
		}

		$strQuery = "SELECT * FROM www_budget.Envelope WHERE EnvDateCreated<=".$dtEnd." AND (EnvDateDeactivated=0 OR EnvDateDeactivated>=".$dtEnd.") ORDER BY GroupName, EnvName";
		$aResults = $classSqlQuery->MySQL_Queries($strQuery);

		$aEnvs = [];
		BuildTransactionArray($aResults, $aEnvs);

		$strQuery = "SELECT * FROM Transactions WHERE IsSplit=0 AND IgnoreTrans=0 AND TransDate>=".$dtBeginning." AND TransDate<=".$dtEnd;
		if($iEnvelopeID != "")
			$strQuery .=" AND EnvelopeID=".$iEnvelopeID;

		if($strKeyword != "")
			$strQuery .=" AND (Description LIKE '%".$strKeyword."%' OR OriginalDescription LIKE '%".$strKeyword."%' OR Notes LIKE '%".$strKeyword."%' OR Amount LIKE '%".$strKeyword."%') ";

		if($strType == "Uncat")
			$strQuery .=" AND EnvelopeID=0";
		$strTotalQuery = str_replace("TransDate>=".$dtBeginning, "TransDate>=".Const_StartDate, $strQuery);

		$strQuery .= " ORDER BY TransDate DESC, Description, ID ASC";
		$aResults = $classSqlQuery->MySQL_Queries($strQuery);

		if($strHeading != "")
			print'<h5>'.$strHeading.'</h5>';

		if(count($aResults) > 0)
		{
			$iActualDebits = 0;
			$iActualCredits = 0;
			foreach($aResults as $aRow)	
			{
				$aTemp = array();
				$_SESSION["Envelope_".$aRow['ID']] = $aRow['EnvelopeID'];

				$strClass = '';
				if($aRow['EnvelopeID'] == "0")
				{
					$aMatch = LookForPastMatch($aRow['ID'], $aRow['Description'], $aRow['OriginalDescription'], $aRow['AccountName'], $aRow['TransactionType']);
					if(count($aMatch) > 0)
					{
						$bSavePresets = true;
						$_SESSION["Envelope_".$aRow['ID']] = $aMatch['ID'];
						$strClass = $aMatch['Class'];
					}
				}

				print'<div class="row transactions-row">';
					print'<div class="col-md-1 transactions-date">'.date("n/j", $aRow['TransDate']).'</div>';
					print'<div class="col-md-5 transactions-desc"><a href="" onclick="ShowTransDetails('.$aRow['ID'].', \''.$aRow['IsTransfer'].'\', \'ShowNormal\'); return false;">'.$aRow['Description'].'</a></div>';

					$classScreen->ScreenElements_AddInputFieldHidden('ORIG_AMOUNT_'.$aRow['ID'], $aRow['Amount']);
					if($aRow['TransactionType'] == "credit")
					{
						print'<div class="col-md-2 transactions-credit">'.CORE_FormatMoney($aRow['Amount'], true, true, true, false).'</div>';
						if($aRow['AccountName'] != "manual entry" && $aRow['EnvelopeID'] != 4)
							$iActualCredits += $aRow['Amount'];
					}
					else
					{
						print'<div class="col-md-2 transactions-debit">'.CORE_FormatMoney($aRow['Amount'], true, true, true, false).'</div>';
						if($aRow['AccountName'] != "manual entry" && $aRow['EnvelopeID'] != 4)
							$iActualDebits += $aRow['Amount'];
					}

					print $classScreen->ScreenElements_AddSelect("Envelope_".$aRow['ID'], "", $aEnvs, "col-md-4 transactions-env ", false, 'onchange="SaveTransCat('.$aRow['ID'].', \'\', \'\')"', $strClass);
				print '</div>';

				print'<div id="TR_'.$aRow['ID'].'" style="display:none;">'.date("n-j-Y", $aRow['TransDate']).'</div>';
				print'<div id="TR_'.$aRow['ID'].'_Transfer" style="display:none;">'.date("n-j-Y", $aRow['TransDate']).'</div>';
			}

			#if($bSavePresets)
			{	
				print'<div class="row">';
					print'<div class="col-md-8"></div>';
					$classScreen->ScreenElements_AddButton("SavePresetGroup", "SavePresetCategories", "Save Preset Categories", "", false, "SaveAllTransCat()", "onclick");
					print $classScreen->ScreenElements_AddButtonGroup("SavePresetGroup", 0);
				print'</div>';
			}

			if($iEnvelopeID != "")
			{
				$strQuery = "SELECT EnvAmAmount FROM www_budget.EnvelopeAmount WHERE EnvAmMonth=".$dtBeginning." AND EnvelopeID=".$iEnvelopeID;
				$aResults = $classSqlQuery->MySQL_Queries($strQuery);
                if(count($aResults) > 0)
    				$iBudgetedIn = $aResults[0]['EnvAmAmount'];
                else
    				$iBudgetedIn = 0;

				if($iEnvelopeID != 3)
					$iActualCredits += $iBudgetedIn;
			}
			else
			{
				$strQuery = "SELECT EnvAmAmount FROM www_budget.EnvelopeAmount WHERE EnvAmMonth=".$dtBeginning." AND EnvelopeID=3";
				$aResults = $classSqlQuery->MySQL_Queries($strQuery);
				if(count($aResults) > 0)
					$iBudgetedIn = $aResults[0]['EnvAmAmount'];
				else
					$iBudgetedIn = 0;
			}

			if($iActualCredits == "")
				$iActualCredits = "0";


			print'<div class="row" style="padding-top:50px; padding-bottom:5px;">';
				print'<div class="col-md-3"></div>';
				print'<div class="col-md-2 finance-budget-amount">budgeted</div>';
				print'<div class="col-md-2 finance-budget-amount">actual</div>';
			print'</div>';

			print'<div class="row">';
				print'<div class="col-md-3">Budgeted income</div>';
				print'<div class="col-md-2 finance-budget-amount">'.CORE_FormatMoney($iBudgetedIn, true, true, false, false).'</div>';
				print'<div class="col-md-2 finance-budget-amount">'.CORE_FormatMoney($iActualCredits, true, true, false, false).'</div>';
			print'</div>';


			print'<div class="row">';
				print'<div class="col-md-3">Actual debits</div>';
				print'<div class="col-md-2 finance-budget-amount"></div>';
				print'<div class="col-md-2 finance-budget-amount" style="text-decoration: underline">'.CORE_FormatMoney($iActualDebits, true, true, false, false).'</div>';
			print'</div>';

			$iResult = $iActualCredits+$iActualDebits;
			print'<div class="row">';
				print'<div class="col-md-3">Balance</div>';
				print'<div class="col-md-2 finance-budget-amount"></div>';
				print'<div class="col-md-2 finance-budget-amount">'.CORE_FormatMoney($iResult, true, true, false, false).'</div>';
			print'</div>';
			print'<div class="row"><div class="col-md-1"> &nbsp;</div></div>';


			#### ALL TIME TOTALS
			$aResults = $classSqlQuery->MySQL_Queries($strTotalQuery);
			$iActualDebits = 0;
			$iActualCredits = 0;
			foreach($aResults as $aRow)
			{
				if($aRow['AccountName'] != "manual entry" && $aRow['EnvelopeID'] != 4)
				{
					if($aRow['TransactionType'] == "credit")
						$iActualCredits += $aRow['Amount'];
					else
						$iActualDebits += $aRow['Amount'];
				}
			}

			if($iEnvelopeID != "")
				$strQuery = "SELECT SUM(EnvAmAmount) FROM www_budget.EnvelopeAmount WHERE EnvAmMonth>=".$dtBeginning." AND EnvAmMonth<=".$dtEnd." AND EnvelopeID=".$iEnvelopeID;
			else
				$strQuery = "SELECT SUM(EnvAmAmount) FROM www_budget.EnvelopeAmount WHERE EnvAmMonth>=".$dtBeginning." AND EnvAmMonth<=".$dtEnd." AND EnvelopeID=4";
			$aResults = $classSqlQuery->MySQL_Queries($strQuery);
			if(count($aResults) > 0 && $aResults[0]['SUM(EnvAmAmount)'] != "")
				$iActualCredits += $aResults[0]['SUM(EnvAmAmount)'];

			print'<div class="row">';
				print'<div class="col-md-3">Balance to date</div>';
				print'<div class="col-md-2 finance-budget-amount"></div>';
				print'<div class="col-md-2 finance-budget-amount">'.CORE_FormatMoney($iActualCredits, true, true, false, false).'</div>';
			print'</div>';

			print'<div class="row">';
				print'<div class="col-md-3">Expenditures to date</div>';
				print'<div class="col-md-2 finance-budget-amount"></div>';
				print'<div class="col-md-2 finance-budget-amount" style="text-decoration: underline">'.CORE_FormatMoney($iActualDebits, true, true, false, false).'</div>';
			print'</div>';

			$iResult = $iActualCredits+$iActualDebits;
			print'<div class="row">';
				print'<div class="col-md-3">Balance to date</div>';
				print'<div class="col-md-2 finance-budget-amount"></div>';
				print'<div class="col-md-2 finance-budget-amount">'.CORE_FormatMoney($iResult, true, true, false, false).'</div>';
			print'</div>';


			print'<div style="padding-bottom:50px;"></div>';
		}
		else
			print'<div>No transactions were found.</div> ';
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


function LookForPastMatch($iID, $strDesc, $strOrigDesc, $strAccountName, $strType)
{
	try
	{
		$aResult = array();

		$classSqlQuery = new SqlDataQueries();
		$classSqlQuery->SpecifyDB("", "www_budget", "", "");

		if($strDesc == "ELECTRONIC TRANSFER")
		{
			$strOrigDesc = substr($strOrigDesc, 0, 18);

			$strOrigDesc = substr($strOrigDesc, 0, strlen($strOrigDesc)-7);
			$strQuery = "SELECT * FROM Transactions 
						 WHERE IsSplit=0 
						 AND IgnoreTrans=0 
						 AND EnvelopeID!=0
						 AND Description='".addslashes($strDesc)."' 
						 AND OriginalDescription LIKE '%".addslashes($strOrigDesc)."%'
						 AND AccountName='".addslashes($strAccountName)."' 
						 AND TransactionType='".addslashes($strType)."' 
						 AND ID!=".$iID." 
						 ORDER BY TransDate DESC LIMIT 1";
		}

		if($strDesc == "PREAUTHORIZED DEBIT" || $strDesc == "PREAUTHORIZED DEBIT")
		{
			$strOrigDesc = substr($strOrigDesc, 0, strlen($strOrigDesc)-7);
			$strQuery = "SELECT * FROM Transactions
						 WHERE IsSplit=0 
						 AND IgnoreTrans=0 
						 AND EnvelopeID!=0
						 AND Description='".addslashes($strDesc)."' 
						 AND OriginalDescription LIKE '%".addslashes($strOrigDesc)."%'
						 AND AccountName='".addslashes($strAccountName)."' 
						 AND TransactionType='".addslashes($strType)."' 
						 AND ID!=".$iID." 
						 ORDER BY TransDate DESC LIMIT 1";
		}
		else
		{
		    $aParts = preg_split("/[?&@#*]/", $strDesc);
		    if(count($aParts) > 0)
		        $strSearch = $aParts[0];
            else
                $strSearch = $strDesc;

			$strQuery = "SELECT * FROM Transactions 
						 WHERE IsSplit=0 
						 AND IgnoreTrans=0 
						 AND EnvelopeID!=0
						 AND Description LIKE '".addslashes($strSearch)."%' 
						 AND AccountName='".addslashes($strAccountName)."' 
						 AND TransactionType='".addslashes($strType)."' 
						 AND ID!=".$iID." 
						 ORDER BY TransDate DESC LIMIT 1";
		}

		$aResults = $classSqlQuery->MySQL_Queries($strQuery);
		if(count($aResults) > 0)	
		{
			$aMatches = array();
			foreach($aResults as $aRow)
			{
				if($aRow['EnvelopeID'] != 0)
				{
					if(isset($aMatches[$aRow['EnvelopeID']]))
						$aMatches[$aRow['EnvelopeID']]++;
					else
						$aMatches[$aRow['EnvelopeID']] = 1;
				}
			}

			if(count($aMatches) >= 1)
			{
				$aResult['Class'] = ' finance-previous-choice ';
				foreach($aMatches as $iIndex=>$strValue)
					$aResult['ID'] = $iIndex;
			}
		}

		return $aResult;
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


function ShowTransDetails($iID, $strStyle, $strView)
{
	try
	{
		$classScreen = new ScreenElements("Budget", true);
		$strSimpleStyle = str_replace(' "', "", str_replace('style="', "", $strStyle));

		$classSqlQuery = new SqlDataQueries();
		$classSqlQuery->SpecifyDB("", "www_budget", "", "");
		$strQuery = "SELECT * FROM Transactions WHERE ID=".$iID;
		$aResults = $classSqlQuery->MySQL_Queries($strQuery);

		$strPad = '<div class="col-md-1"></div>';
		print'<div class="row top-pad">'.$strPad.'<div class="col-md-6 finance-header">Full Description</div></div>';
		print'<div class="row">'.$strPad.'<div class="col-md-11 finance-desc">'.$aResults[0]['OriginalDescription'].'</div></div>';

		print'<div class="row top-pad">';
			print $strPad;
			print '<div class="col-md-2 finance-header">Account</div>';
			print '<div class="col-md-9 finance-desc">'.$aResults[0]['AccountName'].'</div>';
		print'</div>';

		print'<div class="row bottom-pad">';
			print $strPad;
			print '<div class="col-md-2 finance-header">Type</div>';

			if($aResults[0]['ParentID'] != "0")
				print'<div class="col-md-9 finance-desc">Split transaction</div>';
			else
				print'<div class="col-md-9 finance-desc">Normal</div>';
				#print'<div class="col-md-9 finance-desc">Normal &nbsp;(<a href="" onclick="SplitTrans('.$iID.'); return false;"><span style="font-size:small">Split</span></a>)</div>';
		print'</div>';
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


function OpenTransfer($iID)
{
	try
	{
		$classSqlQuery = new SqlDataQueries();
		$classSqlQuery->SpecifyDB("", "www_budget", "", "");

		if(isset($iID) && $iID!="" && $iID!=0)
		{
			$classSqlQuery = new SqlDataQueries();
			$classSqlQuery->SpecifyDB("", "www_budget", "", "");

			$strQuery = "SELECT * FROM Transactions WHERE ID=".$iID;
			$aResults = $classSqlQuery->MySQL_Queries($strQuery);

			print'TransDate='.date("n/j/Y", $aResults[0]['TransDate']).';';
			print'Amount='.$aResults[0]['Amount'].';';
			print'Description='.$aResults[0]['Description'].';';

			$strQuery = "SELECT * FROM Transactions WHERE ID=".$aResults[0]['TransferPartnerID'];
			$aSecondResults = $classSqlQuery->MySQL_Queries($strQuery);

			if($aResults[0]['TransferType'] == "To")
			{
				print'FromEnvelopeID='.$aSecondResults[0]['EnvelopeID'].';';
				print'ToEnvelopeID='.$aResults[0]['EnvelopeID'].';';
			}
			else
			{
				print'FromEnvelopeID='.$aResults[0]['EnvelopeID'].';';
				print'ToEnvelopeID='.$aSecondResults[0]['EnvelopeID'].';';
			}
		}
		else
			CORE_ClearSessions();
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


function ListTransactionDups()
{
	try
	{
		$classSqlQuery = new SqlDataQueries();
		$classSqlQuery->SpecifyDB("", "www_budget", "", "");

		$strQuery = "SELECT * FROM Transactions WHERE IsSplit=0 AND IgnoreTrans=0 AND ConfirmedNotDuplicate=0 ORDER BY TransDate DESC, Description, ID ASC";
		$aResults = $classSqlQuery->MySQL_Queries($strQuery);

		$aIDToIgnore = array();
		if(count($aResults) > 0)
		{
			foreach($aResults as $aRow)
			{
				if(!in_array($aRow['ID'], $aIDToIgnore))
				{
					$_SESSION["Envelope_".$aRow['ID']] = $aRow['EnvelopeID'];

					$strQuery = "SELECT * FROM Transactions
									WHERE IsSplit=0 AND IgnoreTrans=0
									AND TransDate=".$aRow['TransDate']."
									AND Amount='".addslashes($aRow['Amount'])."'
									AND AccountName='".addslashes($aRow['AccountName'])."'
									AND TransactionType='".addslashes($aRow['TransactionType'])."'
									AND ID!=".$aRow['ID'];
					$aDuplicateResults = $classSqlQuery->MySQL_Queries($strQuery);

					if(count($aDuplicateResults) > 0)
					{
						print'<div class="dup-trans bottom-pad">';
							DisplayDupItem($aRow, "");
							foreach($aDuplicateResults as $aDup)
							{
								DisplayDupItem($aDup, "Dup");
								$aIDToIgnore[] = $aDup['ID'];
							}
						print'</div>';
					}
					else
					{
						$strQuery = "UPDATE Transactions SET ConfirmedNotDuplicate=1 WHERE ID=".$aRow['ID'];
						$classSqlQuery->MySQL_Queries($strQuery);
					}
				}
			}
		}
		else
			print'<div>No duplicate transactions were found.</div> ';
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


function DisplayDupItem($aRow, $strType)
{
	$strColor = '';
	if($aRow['TransactionType'] == "credit")
		$strColor = ' alt-creit-color ';

	if($strType == "")
	{
		print'<div class="row finance-header">';
			print'<div class="col-md-2">'.date("n/j/y", $aRow['TransDate']).'</div>';
			print'<div class="col-md-4">'.$aRow['AccountName'].'</div>';
		print'</div>';
	}

	print'<div class="row bottom-pad">';
		print'<div class="col-md-7">'.$aRow['Description'].'. <br>ID: '.$aRow['ID'].'</div>';
		print'<div class="col-md-2">'.CORE_FormatMoney($aRow['Amount'], true, true, true, false).'</div>';

		if($aRow['ConfirmedNotDuplicate'] == "0")
			print'<div class="col-md-3 '.$strColor.'"><a href="" onclick="MarkDup('.$aRow['ID'].', \'Duplicate\'); return false;">dup</a> | <a href="" onclick="MarkDup('.$aRow['ID'].', \'NotDuplicate\'); return false;">not dup</a></div>';
		else
			print'<div class="col-md-3 '.$strColor.'">Marked as not a duplicate</div>';
	print'</div>';
}


function SaveTransCat($iID, $iCat, $strCaller)
{
	try
	{
		$classSqlQuery = new SqlDataQueries();
		$classSqlQuery->SpecifyDB("", "www_budget", "", "");

		if($strCaller == "Compare")
		{
			$strQuery = "UPDATE Transactions SET EnvelopeID=".$iCat." WHERE ID=".$iID;
			$classSqlQuery->MySQL_Queries($strQuery);

			$classSqlQuery->MySQL_Queries(str_replace("Transactions", "Transactions_Proposed_Amy",  $strQuery));
			$classSqlQuery->MySQL_Queries(str_replace("Transactions", "Transactions_Proposed_Drew",  $strQuery));
		}
		else
		{
			$strQuery = "UPDATE Transactions SET EnvelopeID=".$iCat." WHERE ID=".$iID;
			$classSqlQuery->MySQL_Queries($strQuery);
		}
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


function SetTransCat($iTransID, $iEnvID)
{
	try
	{
		$classSqlQuery = new SqlDataQueries();
		$classSqlQuery->SpecifyDB("", "www_budget", "", "");

		$strQuery = "UPDATE Transactions SET EnvelopeID=".$iEnvID." WHERE ID=".$iTransID;
		$classSqlQuery->MySQL_Queries($strQuery);

		$classSqlQuery->MySQL_Queries(str_replace("Transactions", "Transactions_Proposed_Amy",  $strQuery));
		$classSqlQuery->MySQL_Queries(str_replace("Transactions", "Transactions_Proposed_Drew",  $strQuery));
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


function DeleteTransfer($iID)
{
	try
	{
		$classSqlQuery = new SqlDataQueries();
		$classSqlQuery->SpecifyDB("", "www_budget", "", "");
		$strQuery = "DELETE FROM Transactions WHERE ID=".$iID;
		$classSqlQuery->MySQL_Queries($strQuery);

		$strQuery = "DELETE FROM Transactions WHERE TransferPartnerID=".$iID;
		$classSqlQuery->MySQL_Queries($strQuery);
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


function SaveTransfer($iID, $strTransDate, $iAmount, $strDesc, $iFromEnvID, $iToEnvID)
{
	try
	{
		if($strTransDate == "")
			$aIssues[] = "Date is required.";

		$iAmount = str_replace("$", "", str_replace(",", "", $iAmount));
		if($iAmount == "")
			$aIssues[] = "Amount is required.";

		if($strDesc == "")
			$aIssues[] = "Description is required.";

		if($iFromEnvID == "")
			$aIssues[] = "Please specify the envelope to transfer from.";

		if($iToEnvID == "")
			$aIssues[] = "Please specify the envelope to transfer to.";

		if($iFromEnvID == $iToEnvID)
			$aIssues[] = "You cannot transfer money to the same envelope.";


		$classSqlQuery = new SqlDataQueries();
		$classSqlQuery->SpecifyDB("", "www_budget", "", "");

		if(isset($iID) && $iID != "" && $iID != 0)
		{
			$iToTransID = "";
			$iFromTransID = "";

			$strQuery = "SELECT * FROM Transactions WHERE ID=".$iID;
			$aResults = $classSqlQuery->MySQL_Queries($strQuery);
			if($aResults[0]['TransferType'] == "To")
				$iToTransID = $aResults[0]['ID'];
			else
				$iFromTransID = $aResults[0]['ID'];

			$strQuery = "SELECT * FROM Transactions WHERE TransferPartnerID=".$iID;
			$aResults = $classSqlQuery->MySQL_Queries($strQuery);
			if($aResults[0]['TransferType'] == "To")
				$iToTransID = $aResults[0]['ID'];
			else
				$iFromTransID = $aResults[0]['ID'];

			if($iFromTransID == "" || $iToTransID == "")
				$aIssues[] = "This transaction appears to be malformed. Please contact Drew.";
		}

		if(isset($aIssues) && $aIssues != "")
		{
			print'<span style="font-size:small">';
			print'<p style="color:red; margin-bottom:0px; margin-top:5px">Please fix the following</p>';
			print'<ul style="margin-top:5px">';
			foreach($aIssues as $strIssue)
				print'<li>'.$strIssue.'</li>';
			print'</ul></span>';

			$_SESSION['TransDate'] = $strTransDate;
			$_SESSION['Amount'] = $iAmount;
			$_SESSION['Description'] = $strDesc;
			$_SESSION['FromEnvelopeID'] = $iFromEnvID;
			$_SESSION['ToEnvelopeID'] = $iToEnvID;
		}
		else
		{
			$aParts = explode("/", $strTransDate);

			// FROM TRANSACTION
			if(isset($iID) && $iID != "" && $iID != 0)
				$strQuery = "UPDATE Transactions SET ";
			else
				$strQuery = "INSERT INTO Transactions SET ";

			$strQuery .= "TransDate=".mktime(0,0,0, $aParts[0], $aParts[1], $aParts[2]).",
						  EnvelopeID=".$iFromEnvID.",
						  Description='".addslashes($strDesc)."',
						  OriginalDescription='".addslashes($strDesc)."',
						  DescriptionGroup='',
						  Amount=".str_replace("$", "", str_replace(",", "", $iAmount)).",
						  AmountNumber=".str_replace("$", "", str_replace(",", "", $iAmount)).",
						  TransactionType='debit',
						  TransDateReadable='".addslashes($strTransDate)."',
						  Notes='',
						  IsDup=0,
						  IsConfirmedNotDup=0,
						  DateLoaded=".time().",
						  DateLoadedReadable='".addslashes(date("n/j/Y", time()))."',
						  AccountName='manual entry',
						  MatchID=0,
						  IsSplit=0,
						  ParentID=0,
						  IsTransfer=1,
						  TransferType='From'";



			if(isset($iID) && $iID != "" && $iID != 0)
				$strQuery .= " WHERE ID=".$iFromTransID;
			$aResults = $classSqlQuery->MySQL_Queries($strQuery);

			if(!isset($iID) && $iID == "" || $iID == 0)
				$iFromTransID = $aResults['ID'];


			// TO TRANSACTION
			if(isset($iID) && $iID != "" && $iID != 0)
				$strQuery = "UPDATE Transactions SET ";
			else
				$strQuery = "INSERT INTO Transactions SET ";

			$strQuery .= "TransDate=".mktime(0,0,0, $aParts[0], $aParts[1], $aParts[2]).",
						  EnvelopeID=".$iToEnvID.",
						  Description='".addslashes($strDesc)."',
						  OriginalDescription='".addslashes($strDesc)."',
						  DescriptionGroup='',
						  Amount=".str_replace("$", "", str_replace(",", "", $iAmount)).",
						  AmountNumber=".str_replace("$", "", str_replace(",", "", $iAmount)).",
						  TransactionType='credit',
						  AccountName='manual entry',
						  TransDateReadable='".addslashes($strTransDate)."',
						  DateLoaded=".time().",
						  DateLoadedReadable='".addslashes(date("n/j/Y", time()))."',
						  Notes='',
						  MatchID=0,
						  IsConfirmedNotDup=0,
						  IsSplit=0,
						  IsDup=0,
						  ParentID=0,
						  IsTransfer=1,
						  TransferPartnerID=".$iFromTransID.",
						  TransferType='To'";

			if(isset($iID) && $iID != "" && $iID != 0)
				$strQuery .= "WHERE ID=".$iToTransID;
			$aResults = $classSqlQuery->MySQL_Queries($strQuery);


			// FINISH RELATING IF INSERT
			if(!isset($iID) && $iID == "" || $iID == 0)
			{
				$strQuery = "UPDATE Transactions SET TransferPartnerID='".$aResults['ID']."' WHERE ID=".$iFromTransID;
				$classSqlQuery->MySQL_Queries($strQuery);
			}

			$classSqlQuery->Transaction_Commit();
		}
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


function Deactivate($iID)
{
	try
	{
		$classSqlQuery = new SqlDataQueries();
		$classSqlQuery->SpecifyDB("", "www_budget", "", "");

		$strQuery = "SELECT EnvName FROM  www_budget.Envelope WHERE ID=".$iID;
		$aResults = $classSqlQuery->MySQL_Queries($strQuery);

		$strQuery = "UPDATE www_budget.Envelope SET EnvName='".$aResults[0]['EnvName'].' (deactivated as of '.date("n-j-Y", mktime(0,0,-1, date("m")+1, 1, date("Y"))).')'."', EnvDateDeactivated=".mktime(0,0,-1, date("m")+1, 1, date("Y"))." WHERE ID=".$iID;
		$classSqlQuery->MySQL_Queries($strQuery);
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


function SaveEditEnv($strEnvName, $iEnvAmount, $strGroupName, $iID, $strMonth)
{
	try
	{
		$aParts = explode("-", $strMonth);
		$dtBeginning = mktime(0,0,0, $aParts[0], 1, $aParts[1]);
		$dtEnd = mktime(0,0,0, $aParts[0]+1, 0, $aParts[1]);

		if($strEnvName == "")
			$aIssues[] = "Envelope name is required.";

		$iEnvAmount = str_replace("$", "", str_replace(",", "", $iEnvAmount));
		if($iEnvAmount == "")
			$aIssues[] = "Envelope amount is required.";

		if(isset($aIssues) && $aIssues != "")
		{
			print'<span style="font-size:small">';
			print'<p style="color:red; margin-bottom:0px; margin-top:5px">Please fix the following</p>';
			print'<ul style="margin-top:5px">';
			foreach($aIssues as $strIssue)
				print'<li>'.$strIssue.'</li>';
			print'</ul></span>';

			$_SESSION['EnvName'] = $strEnvName;
			$_SESSION['EnvAmount'] = $iEnvAmount;
			$_SESSION['GroupName'] = $strGroupName;
			AddEnvDisplay();
		}
		else
		{
			$classSqlQuery = new SqlDataQueries();
			$classSqlQuery->SpecifyDB("", "www_budget", "", "");
			$classSqlQuery->Transaction_Start();

			if($strGroupName == "")
				$strGroupName = $strEnvName;

			if($iID != "")
			{
				$strQuery = "UPDATE Envelope SET ";
				$strQuery .= "EnvName='".addslashes($strEnvName)."',
							 GroupName='".addslashes($strGroupName)."',
							 EnvDefaultAmount='".addslashes($iEnvAmount)."',
							 EnvDateDeactivated=0";
			}
			else
			{
				$strQuery = "INSERT INTO Envelope SET ";
				$strQuery .= "EnvName='".addslashes($strEnvName)."',
							 GroupName='".addslashes($strGroupName)."',
							 EnvDefaultAmount='".addslashes($iEnvAmount)."',
							 EnvDateCreated=".time().",
							 EnvDateDeactivated=0";
			}

			if($iID != "")
				$strQuery .= " WHERE ID=".$iID;
			else
				$strQuery .= ", Type='expenditure'";

			$aResults = $classSqlQuery->MySQL_Queries($strQuery);

			if($iID == "")
				$iID = $aResults['ID'];

			$strQuery = "SELECT ID, EnvAmAmount FROM EnvelopeAmount WHERE EnvelopeID=".$iID." AND EnvAmMonth>=".$dtBeginning." AND EnvAmMonth<=".$dtEnd;
			$aEnvAmntResults = $classSqlQuery->MySQL_Queries($strQuery);
			if(count($aEnvAmntResults) == 0)
			{
				$strQuery = "INSERT INTO EnvelopeAmount SET EnvelopeID=".$iID.", EnvAmAmount=".$iEnvAmount.", EnvAmMonth=".$dtBeginning;
				$classSqlQuery->MySQL_Queries($strQuery);
			}
			elseif(count($aEnvAmntResults) == 1 && $aEnvAmntResults[0]['EnvAmAmount'] != $iEnvAmount)
			{
				$strQuery = "UPDATE EnvelopeAmount SET EnvAmAmount=".$iEnvAmount." WHERE ID=".$aEnvAmntResults[0]['ID'];
				$classSqlQuery->MySQL_Queries($strQuery);
			}
			elseif(count($aEnvAmntResults) > 1)
				throw exception("Too many entries were found in the EnvelopeAmount table. Contact The Man.");

			$classSqlQuery->Transaction_Commit();
		}
	}
	catch (Exception $EX)
	{
		print "ERROR<BR><BR> {$EX->getMessage()}";
	}
}


function AddGroup($strNewGroup, $iID)
{
	try
	{
		if($strNewGroup == "")
			$aIssues[] = "Group name is required.";

		if(strtolower($strNewGroup) == "income")
			$aIssues[] = "The group name 'Income' has been reserved by the application.";

		if(isset($aIssues) && $aIssues != "")
		{
			print'<span style="font-size:small">';
			print'<p style="color:red; margin-bottom:0px; margin-top:5px">Please fix the following</p>';
			print'<ul style="margin-top:5px">';
			foreach($aIssues as $strIssue)
				print'<li>'.$strIssue.'</li>';
			print'</ul></span>';
		}
		else
		{
			$classSqlQuery = new SqlDataQueries();
			$classSqlQuery->SpecifyDB("", "www_budget", "", "");
			$classSqlQuery->Transaction_Start();

			if($iID != "")
			{
				$strQuery = "SELECT ID FROM Groups WHERE GroupName='".addslashes($strNewGroup)."' AND ID!=".$iID;
				$aResults = $classSqlQuery->MySQL_Queries($strQuery);
				if(count($aResults) > 0)
					print'<font style="color:red">'.$strNewGroup.' already exists.</font><br/>';
				else
				{
					$strQuery = "SELECT GroupName FROM Groups WHERE ID=".$iID;
					$aResults = $classSqlQuery->MySQL_Queries($strQuery);

					$strQuery = "UPDATE Envelope SET GroupName='".addslashes($strNewGroup)."' WHERE GroupName='".addslashes($aResults[0]['GroupName'])."'";
					$aResults = $classSqlQuery->MySQL_Queries($strQuery);

					$strQuery = "UPDATE Groups SET GroupName='".addslashes($strNewGroup)."', GroupDateCreated=".time()." WHERE ID=".$iID;
					$classSqlQuery->MySQL_Queries($strQuery);
				}
			}
			else
			{
				$strQuery = "SELECT ID FROM Groups WHERE GroupName='".addslashes($strNewGroup)."'";
				$aResults = $classSqlQuery->MySQL_Queries($strQuery);
				if(count($aResults) > 0)
					print'<font style="color:red">'.$strNewGroup.' already exists.</font><br/>';
				else
				{
					$strQuery = "INSERT INTO Groups SET GroupName='".addslashes($strNewGroup)."', GroupDateCreated=".time();
					$classSqlQuery->MySQL_Queries($strQuery);
				}
			}
			$classSqlQuery->Transaction_Commit();
		}

		ListGroup('');
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


function DeleteGroup($iID)
{
	try
	{
		$classSqlQuery = new SqlDataQueries();
		$classSqlQuery->SpecifyDB("", "www_budget", "", "");
		$classSqlQuery->Transaction_Start();

		$strQuery = "SELECT GroupName FROM Groups WHERE ID=".$iID;
		$aResults = $classSqlQuery->MySQL_Queries($strQuery);

		$strQuery = "UPDATE Envelope SET GroupName=EnvName WHERE GroupName='".$aResults[0]['GroupName']."'";
		$classSqlQuery->MySQL_Queries($strQuery);

		$strQuery = "DELETE FROM Groups WHERE ID=".$iID;
		$classSqlQuery->MySQL_Queries($strQuery);
		$classSqlQuery->Transaction_Commit();

		ListGroup('');
	}
	catch (Exception $EX)
	{
		ErrorHandler::ErrorHandler_CatchError($EX);
	}
}


function MarkDup($iID, $strType)
{
	$classSqlQuery = new SqlDataQueries();
	$classSqlQuery->SpecifyDB("", "www_budget", "", "");

	if($strType == "Duplicate")
	{
		$strQuery = "UPDATE Transactions SET IgnoreTrans=1 WHERE ID=".$iID;
		$classSqlQuery->MySQL_Queries($strQuery);

		$strQuery = "UPDATE TransactionsDrew SET IgnoreTrans=1 WHERE ID=".$iID;
		$classSqlQuery->MySQL_Queries($strQuery);
	}
	else
	{
		$strQuery = "UPDATE Transactions SET ConfirmedNotDuplicate=1 WHERE ID=".$iID;
		$classSqlQuery->MySQL_Queries($strQuery);

		$strQuery = "UPDATE TransactionsDrew SET ConfirmedNotDuplicate=1 WHERE ID=".$iID;
		$classSqlQuery->MySQL_Queries($strQuery);
	}
}


MainFinanceFunctions();

?>
