<?php
function hook_facturacom_AdminAreaHeadOutput($vars) {
	require_once 'wrapperapp.php';
	//$version = rand(1, 1000);
	$version = MONITIS_RESOURCE_VERSION;
	$head = '<script src="../modules/addons/facturacom/pages/js/functions.js" type="text/javascript"></script>';
	return $head;
}
add_hook("AdminAreaHeadOutput", 1, "hook_facturacom_AdminAreaHeadOutput");
