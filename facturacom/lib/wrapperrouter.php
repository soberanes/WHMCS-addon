<?php

class WrapperRouter {

    static function route(){
        //addonmodules.php?module=facturacom
        $pageName = (isset($_GET['module'])) ? 'admin' : 'clientarea';
        self::showPage($pageName);
    }

    static function showPage($pageName){

        try{
            include_once WRAPPER_APP_PATH . '/pages/' . $pageName . '.php';
        }catch(Exception $e){
            echo 'Exception: ' . $e->getMessage() . '\n';
        }

    }

}
