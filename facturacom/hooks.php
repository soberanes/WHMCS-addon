<?php
function hook_facturacom_AdminAreaHeadOutput($vars) {
	$head =  '<link href="//cdn.datatables.net/1.10.11/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />';

	return $head;
}
add_hook("AdminAreaHeadOutput", 1, "hook_facturacom_AdminAreaHeadOutput");

function hook_facturacom_AdminAreaFooterOutput($vars) {

	$foot =  '<script src="//cdn.datatables.net/1.10.11/js/jquery.dataTables.min.js" type="text/javascript"></script>';
	// $foot =  '<script src="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/jquery.dataTables.js" type="text/javascript"></script>';
	$foot .= '<script src="../modules/addons/facturacom/pages/js/functions.js" type="text/javascript"></script>';

	return $foot;
}
add_hook("AdminAreaFooterOutput", 1, "hook_facturacom_AdminAreaFooterOutput");

//@TODO hook para checar versión de API
