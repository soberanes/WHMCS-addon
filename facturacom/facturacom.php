<?php
/*
 * WHMCS Factura.com Addon
 * Factura Punto Com SAPI de CV - http://www.factura.com
 *
 * Developed by Paul Soberanes <@soberanees>
 *
 * Copyrights (c) 2016 - Factura.com
 */


// FOR DEBUG
error_reporting(1);
error_reporting(E_ALL & ~E_NOTICE);


if (!defined("WHMCS"))
	die("This file cannot be accessed directly");

require_once 'wrapperapp.php';

/**
 * Addon's configuration function
 *
 * @return Array
 */
function facturacom_config(){
    $configarray = array(
        'name'         => 'Factura.com',
    	'version'      => '1.3',
    	'author'       => 'Factura.com',
        'logo'         => 'https://factura.com/assets/images/logo_horizontal.svg',
    	'description'  => 'Módulo de integración con el servicio de
                Factura.com para administrar y emitir facturas electrónicas.',
    	'fields' => array(
    		'ApiKey' => array(
                "FriendlyName"  => "API KEY",
                "Type"          => "text",
                "Size"          => "100"
            ),
    		'ApiSecret' => array(
                "FriendlyName"  => "API SECRET",
                "Type"          => "text",
                "Size"          => "100"
            ),
    		'Serie' => array(
                "FriendlyName"  => "SERIE FACTURACIÓN",
                "Type"          => "text",
                "Size"          => "100"
            ),
    		'DayOff' => array(
                "FriendlyName"  => "DÍAS DE TOLERANCIA PARA FACTURAR DESPUÉS DE MES DE COMPRA",
                "Type"          => "dropdown",
                "Options"       => "1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30",
            ),
    		'ActivateDate' => array(
                "FriendlyName"  => "FECHA A PARTIR DE LA CUAL SE PUEDE FACTURAR (dd/mm/yyyy)",
                "Type"          => "text",
				"Size"          => "100"
            ),
    	)
	);

	return $configarray;
}

/**
 * Handle addon activation
 *
 * @return Array
 */
function facturacion_activate() {
    return array('status' => 'success', 'description' => '');
}

/**
 * Handle addon deactivation
 *
 * @return Array
 */
function facturacion_deactivate() {
    return array('status' => 'success', 'description' => '');
}

/**
 * Handle addon upgrade
 *
 * @param Array $vars
 */
function facturacion_upgrade($vars) {

}

/**
 * Handle addon admin area sidebar
 */
function facturacom_sidebar(){

}

/**
 * Handle addon admin area output
 *
 * @param Array $vars
 * @return Array
 */
function facturacom_output($vars){
    $wrapperApp = new WrapperApp();
    $wrapperApp->renderAdmin();
}
