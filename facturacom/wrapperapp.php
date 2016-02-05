<?php
define('WRAPPER_APP_PATH', dirname(realpath(__FILE__)));

/*
 * @TODO Includes lib
 */
require_once 'lib/wrapperhelper.php';
require_once 'lib/wrapperconf.php';
require_once 'lib/wrapperrouter.php';
require_once 'lib/wrapperapi.php';

class WrapperApp {
    private static $errors   = array();
    private static $warnings = array();
    private static $messages = array();

    /** @var $wrapConfig WrapConfig */
    var $wrapConfig;

    /**
     * Class constructor - initialize static configurations
     *
     * @param Array $vars
     */
    function WrapperApp(){
        WrapperConfig::load();
    }

    /**
     * Render invoices panel in admin section
     *
     * @param Array $vars
     */
    public function renderAdmin(){
        WrapperRouter::route();
    }

}
