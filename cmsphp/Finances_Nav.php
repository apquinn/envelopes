<?php

require_once dirname(__FILE__)."/Includes/2015_FunctionsCommon.php";
$_SESSION[Const_sLoginName] = "quinn";

print'<div class="row"><div class="col-md-8 mint_nav">';
	print'<a class="allcaps" href="Finances.shtml?action=ImportData&phase=phase1&start=true">Import</a> &nbsp;|&nbsp; ';
	print'<a class="allcaps" href="Finances.shtml?action=Trans&phase=phase1&start=true">Transactions</a> &nbsp;|&nbsp; ';
	print'<a class="allcaps" href="Finances.shtml?action=Budget&phase=phase1&start=true">Budget</a>';
	#print' &nbsp;|&nbsp; <a class="allcaps" href="Finances.shtml?action=Compare&phase=phase1&start=true">Compare Transactions</a>';
	#print' &nbsp;|&nbsp; <a class="allcaps" href="Finances.shtml?action=TransGroups&phase=phase1&start=true">Group Transactions</a>';
print'</div></div>';


?>