<?php
date_default_timezone_set('America/New_York');

require_once dirname(__FILE__)."/Includes/2015_FunctionsCommon.php";

if(!isset($_SESSION[Const_sLoginName]) || $_SESSION[Const_sLoginName] != "quinn")
{
	if(isset($_REQUEST[Const_Subaction]) && $_REQUEST[Const_Subaction] == "process")
	{
		if($_REQUEST["INPUT"] == "Wondermutt")
		{
			$_SESSION[Const_sLoginName] = 'quinn';
			header("Location: /Finances.shtml");
		}
	}
	else
	{
		$classScreen = new ScreenElements("Budget", true);
		print $classScreen->ScreenElements_AddForm("/cgi-bin/confirm.php?subaction=process", '');
		print '<div class="row">'.$classScreen->ScreenElements_AddInputField("INPUT", "Birthday", "col-md-4", false, 'onclick="Decode()"', "", "").'</div>';
		print'</form>';

		$classScreen->ScreenElements_AddButton("Commit", "Beef", "go", "", false, "", "");
		print $classScreen->ScreenElements_AddButtonGroup("Commit", 0);
	}
}


?>
