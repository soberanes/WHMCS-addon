<?php
/**
 * Handler for Json Ajax Calls from CUSTOMER AREA
 * @author Paul Soberanes  <@soberanees>
 * @copyright (c) Octuber 2015, Factura.com
 */
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Origin: http://devfactura.in');
header('Access-Control-Allow-Credentials : true');

require_once 'wrapperhelper.php';
require_once 'wrapperconf.php';
require_once 'wrapperapi.php';

if (isset($_POST['function'])) {
    $resultado = null;
    eval('$resultado = ' . $_POST['function'] . '();');
    echo json_encode($resultado);
} else {
    echo json_encode(array("Error" => "Fail"));
}


/**
 * Cancel invoice in Factura.com system
 *
 * @param Global $_POST
 * @return Array
 */
function cancelInvoice(){
    $uid = $_POST['uid'];
    $response = WrapperHelper::cancelInvoice($uid);

    return $response;
}

/**
 * Send invoice via email to customer
 *
 * @param Global $_POST
 * @return Array
 */
function sendInvoice(){
    $uid = $_POST['uid'];
    $response = WrapperHelper::sendInvoiceEmail($uid);

    return $response;
}
