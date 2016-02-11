<?php

class WrapperConfig {

    static $apiUrl       = '';
    static $apiKey       = '';
	static $apiSecret    = '';
	static $serie        = '';
	static $dayOff       = '';
	static $activateDate = '';
    static $version      = '';
    static $access       = '';
    static $systemURL    = '';

    /**
     * Setting and saving vars in file system
     *
     * @param Array $vars
     * @return Array
     */
    static function load(){
        $moduleVars   = WrapperHelper::getConfigVars();
        $systemConfig = WrapperHelper::getSystemUrl();

        self::setApiUrl('http://devfactura.in/api/v1/');
        self::setApiKey($moduleVars['ApiKey']);
        self::setApiSecret($moduleVars['ApiSecret']);
        self::setSerie($moduleVars['Serie']);
        self::setDayOff($moduleVars['DayOff']);
        self::setActivateDate($moduleVars['ActivateDate']);
        self::setVersion($moduleVars['version']);
        self::setAccess($moduleVars['access']);
        self::setSystemURL($systemConfig['SystemURL']);

        self::saveConf();

        return true;
    }

    /**
     * Setting local vars with session vars to use globally
     *
     * @return Array
     */
    static function configEntity(){
        $configVars = self::getConf();

        return array(
            'apiKey'      => WrapperHelper::strDecode($configVars[0]),
            'apiSecret'   => WrapperHelper::strDecode($configVars[1]),
            'serie'       => WrapperHelper::strDecode($configVars[2]),
            'dayOff'      => WrapperHelper::strDecode($configVars[3]),
            'activateDate' => WrapperHelper::strDecode($configVars[4]),
            'apiUrl'      => WrapperHelper::strDecode($configVars[5]),
            'version'     => WrapperHelper::strDecode($configVars[6]),
            'access'      => WrapperHelper::strDecode($configVars[7]),
            'systemURL'   => WrapperHelper::strDecode($configVars[8]),
        );
    }

    /**
     * Saving configuration in .conf file
     *
     */
    static function saveConf(){

        $configFile = fopen(dirname(__FILE__) .'/facturacom.conf', 'w') or die('Unable to open file!');
        //write apiKey
        fwrite($configFile, WrapperHelper::strEncode(self::$apiKey)."\n");
        //write apiSecret
        fwrite($configFile, WrapperHelper::strEncode(self::$apiSecret)."\n");
        //write serie
        fwrite($configFile, WrapperHelper::strEncode(self::$serie)."\n");
        //write dayOff
        fwrite($configFile, WrapperHelper::strEncode(self::$dayOff)."\n");
        //write activateDay
        fwrite($configFile, WrapperHelper::strEncode(self::$activateDate)."\n");
        //write apiUrl
        fwrite($configFile, WrapperHelper::strEncode(self::$apiUrl)."\n");
        //write version
        fwrite($configFile, WrapperHelper::strEncode(self::$version)."\n");
        //write access
        fwrite($configFile, WrapperHelper::strEncode(self::$access)."\n");
        //write systemURL
        fwrite($configFile, WrapperHelper::strEncode(self::$systemURL));

        fclose($configFile);
    }

    /**
     * Read configuration from .conf file
     *
     * @return Array
     */
    static function getConf(){
        $fp = @fopen(dirname(__FILE__) .'/facturacom.conf', 'r');

        //Add each line to an array
        if ($fp) {
           $configVars = explode("\n", fread($fp, filesize(dirname(__FILE__) .'/facturacom.conf')));
        }

        return $configVars;
    }

    /**
     * Validate if configuration is set
     *
     * @param Array $configEntity
     * @return Boolean
     */
    static function issetConfig($configEntity){

        if(empty($configEntity['apiKey']) || empty($configEntity['apiSecret'])
            || empty($configEntity['serie']) || empty($configEntity['dayOff'])
            || empty($configEntity['activateDate']) || empty($configEntity['apiUrl'])
            || empty($configEntity['version']) || empty($configEntity['access'])
            || empty($configEntity['systemURL'])){

            return false;
        }else{
            return true;
        }
    }

    /**
     * Get Api Url
     *
     * @return String
     */
    static function getApiUrl(){
        return self::$apiUrl;
    }

    /**
     * Set Api Url
     *
     * @param String $apiUrl
     */
    static function setApiUrl($apiUrl){
        self::$apiUrl = $apiUrl;
    }

    /**
     * Get Api Key
     *
     * @return String
     */
    static function getApiKey(){
        return self::$apiKey;
    }

    /**
     * Set Api Key
     *
     * @param String $apiKey
     */
    static function setApiKey($apiKey){
        self::$apiKey = $apiKey;
    }

    /**
     * Get Api Secret
     *
     * @return String
     */
    static function getApiSecret(){
        return self::$apiSecret;
    }

    /**
     * Set Api Secret
     *
     * @param String $apiSecret
     */
    static function setApiSecret($apiSecret){
        self::$apiSecret = $apiSecret;
    }

    /**
     * Get Serie
     *
     * @return String
     */
    static function getSerie(){
        return self::$serie;
    }

    /**
     * Set Serie
     *
     * @param String $serie
     */
    static function setSerie($serie){
        self::$serie = $serie;
    }

    /**
     * Get DayOff
     *
     * @return String
     */
    static function getDayOff(){
        return self::$dayOff;
    }

    /**
     * Set DayOff
     *
     * @param String $dayOff
     */
    static function setDayOff($dayOff){
        self::$dayOff = $dayOff;
    }

    /**
     * Get ActivateDate
     *
     * @return String
     */
    static function getActivateDate(){
        return self::$activateDate;
    }

    /**
     * Set ActivateDate
     *
     * @param String $activateDate
     */
    static function setActivateDate($activateDate){
        self::$activateDate = $activateDate;
    }

    /**
     * Get Version
     *
     * @return String
     */
    static function getVersion(){
        return self::$version;
    }

    /**
     * Set Version
     *
     * @param String $version
     */
    static function setVersion($version){
        self::$version = $version;
    }

    /**
     * Get Access
     *
     * @return String
     */
    static function getAccess(){
        return self::$access;
    }

    /**
     * Set Access
     *
     * @param String $access
     */
    static function setAccess($access){
        self::$access = $access;
    }

    /**
     * Get SystemURL
     *
     * @return String
     */
    static function getSystemURL(){
        return self::$systemURL;
    }

    /**
     * Set SystemURL
     *
     * @param String $systemURL
     */
    static function setSystemURL($systemURL){
        self::$systemURL = $systemURL;
    }

}
