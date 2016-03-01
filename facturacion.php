<?php
define("CLIENTAREA", true);
//define("FORCESSL", true); // Uncomment to force the page to use https://

require("init.php");
require('modules/addons/facturacom/wrapperapp.php');

$ca = new WHMCS_ClientArea();
$ca->setPageTitle("Facturación de servicios");
$ca->initPage();
$ca->requireLogin();

if ($ca->isLoggedIn()) {

    WrapperConfig::load();
    $configEntity = WrapperConfig::configEntity();

    // Getting invoices by client from factura.com
    $invoices = (array) WrapperHelper::getInvoices($ca->getUserID());
    $clientInvoices = array();
    //object to array
    foreach ($invoices['data'] as $key => $value) {
        $clientInvoices[$key] = (array) $value;
    }

    // Getting invoices from whmcs
    $whmcsInvoices = WrapperHelper::getWhmcsInvoices($ca->getUserID());

    $ca->assign('clientW', $ca->getUserID());
    $ca->assign('whmcsInvoices', $whmcsInvoices);
    $ca->assign('clientInvoices', $clientInvoices);
    $ca->assign('systemURL', $configEntity['systemURL']);
    $ca->assign('apiUrl', $configEntity['apiUrl']);
    $ca->assign('serieInvoices', $configEntity['serie']);

} else {

  # User is not logged in

}

# Define the template filename to be used without the .tpl extension
$ca->setTemplate('customer_area/clientfacturacion');
$ca->output();
