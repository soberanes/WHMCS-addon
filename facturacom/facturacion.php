<?php

define("CLIENTAREA", true);
//define("FORCESSL", true); // Uncomment to force the page to use https://

require("init.php");
require('wrapperapp.php');

$ca = new WHMCS_ClientArea();
$ca->setPageTitle("Facturación de servicios");

$ca->initPage();

//$ca->requireLogin(); // Uncomment this line to require a login to access this page

# To assign variables to the template system use the following syntax.
# These can then be referenced using {$variablename} in the template.

$ca->assign('variablename', $value);

# Check login status
if ($ca->isLoggedIn()) {

  # User is logged in - put any code you like here

  # Here's an example to get the currently logged in clients first name

  //Getting customer logged data
  $result = mysql_query("SELECT id, firstname FROM tblclients WHERE id=" . $ca->getUserID());
  $data = mysql_fetch_array($result);
  $userid = $data[0];
  $clientname = $data[1];
  $ca->assign('clientname', 'paul');

  //Getting customer logged orders
  $params = array(
      'filter' => 'userid',
      'value' => $userid;
  );
  $orders = array(
      'ok' => 0,
      'val' => 1
  ); //WrapperHelper::getWhmcsOrders($params);

  $ca->assign('orders', $orders);
  $ca->assign('ordereee', 'ordereee');

} else {

  # User is not logged in

}

# Define the template filename to be used without the .tpl extension

$ca->setTemplate('customer_area/clientfacturacion');

$ca->output();
