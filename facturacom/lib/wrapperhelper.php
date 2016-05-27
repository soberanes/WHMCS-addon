<?php
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Functions:
 *      dump
 *      strEncode
 *      strDecode
 *      getConfigVars
 *      getSystemUrl
 *      getWhmcsClients
 *      getWhmcsOrders
 *      getWhmcsInvoices
 *      getInvoices
 *      sendInvoiceEmail
 *      cancelInvoice
 *      getLocation
 *      getClient
 *      createInvoice
 */

class WrapperHelper {

    /**
     * Display dump of the argument given
     *
     * @param Type $var
     */
    static function dump($arg, $die = false) {
    	echo "<pre>";
    	var_dump($arg);
    	echo "</pre>";

        if($die)
            die;
    }

    /**
     * Encode a string in base64
     *
     * @param String $str
     * @return String
     */
    static function strEncode($str){
        return base64_encode($str);
    }

    /**
     * Decode a string in base64
     *
     * @param String $str
     * @return String
     */
    static function strDecode($str){
        return base64_decode($str, true);
    }

    /**
     * Get facturacom's module configuration vars
     *
     * @return Object
     */
    static function getConfigVars(){
        $vars = Capsule::table('tbladdonmodules')
                            ->where('module', 'facturacom')
                            ->get();

        $settings = array();
        foreach ($vars as $var) {
            $settings[$var->setting] = $var->value;
        }

        return $settings;
    }

    /**
     * Get facturacom's System URL from configuration
     *
     * @return Object
     */
    static function getSystemURL(){
        $vars = Capsule::table('tblconfiguration')
                            ->where('setting', 'SystemURL')
                            ->get();

        $settings = array();
        foreach ($vars as $var) {
            $settings[$var->setting] = $var->value;
        }

        return $settings;
    }

    /**
     * Get WHMCS clients or client by filter
     *
     * @param Array $filter
     * @return Object
     */
    static function getWhmcsClients($filter = null){
        if(isset($filter)){
            // @TODO client by filter
            if(!is_array($filter))
                return null;

            $clientsObj = null;
        }else{
            // get all clients
            $clientsObj = Capsule::table('tblclients')->get();
        }

        return $clientsObj;
    }

    /**
     * Get WHMCS orders or orders by filter
     *
     * @param Array $filter
     * @return Object
     */
    static function getWhmcsOrders($filter = null){
        if(isset($filter)){

            $ordersObj = Capsule::table('tblinvoices')
                                    ->where('tblinvoices.invoicenum', $filter['value'])
                                    ->get();
            return $ordersObj;
        }else{
            // get all orders
            $ordersObj = Capsule::table('tblinvoices')->get();
        }

        return $ordersObj;
    }


        /**
         * Get WHMCS orders or orders by filter
         *
         * @param Int $clientId
         * @return Object
         */
        static function getWhmcsInvoices($clientId){
            if(!isset($clientId)){
                return array(
                    'Error' => 'No se ha recibido el id del cliente.',
                );
            }

            $invoiceList = array();
            $facturaInvoiceList = array();
            $invoicesObj = Capsule::table('tblinvoices')
                            ->where('tblinvoices.userid', $clientId)
                            ->get();

            foreach($invoicesObj as $key => $value){
                $invoiceList[$value->id]["orderId"]         = $value->id;
                $invoiceList[$value->id]["orderNum"]        = $value->invoicenum;
                $invoiceList[$value->id]["clientId"]        = $value->userid;
                $invoiceList[$value->id]["orderDate"]       = date("d-m-Y",strtotime($value->date));
                $invoiceList[$value->id]["invoiceDueDate"]  = date("d-m-Y",strtotime($value->duedate));
                $invoiceList[$value->id]["invoiceDatePaid"] = date("d-m-Y",strtotime($value->datepaid));
                $invoiceList[$value->id]["total"]           = $value->total;
                $invoiceList[$value->id]["status"]          = (strtolower($value->status) == 'paid') ? 'Pagada' : 'No pagada';
                $invoiceList[$value->id]["orderdata"]       = self::getInvoiceItems($value->id);
                $invoiceList[$value->id]["sent"] = false;
                $invoiceList[$value->id]["open"] = true;

                if($value->status != "Paid"){
                    $invoiceList[$value->id]["open"] = false;
                }

                // open
                /* validar que la factura esté dentro del mes +X días y a partir
                    de la fecha de facturación configurada
                */
                $order_month   = date("m",strtotime($value->datepaid));
                $order_year    = date("Y",strtotime($value->datepaid));
                $current_day   = date("d");
                $current_month = date("m");
                $current_year  = date("Y");
                $configEntity  = WrapperConfig::configEntity();

                if(!WrapperConfig::issetConfig($configEntity)){
                    $invoiceList[$value->id]["open"] = false;
                }

                $arr = explode('/', $configEntity['activateDate']);
                /* formatear la fecha a dd-mm-aaaa porque la fecha datepaid
                   tiene ese formato en WHMCS y deben tener el mismo formato para
                   compararse. */
                $newDate = $arr[0].'-'.$arr[1].'-'.$arr[2];


                $activateDate = strtotime($newDate); //1 septiembre 2015
                $paidDate     = strtotime($value->datepaid); //6 Octubre 2015

                // Validate plugin activation date vs payment date
                if($paidDate < $activateDate){
                    $invoiceList[$value->id]["open"] = false;
                }

                // Validate payment date vs current date
                if($order_month != $current_month){
                    $order_day = date("d",strtotime($value->datepaid));

                    if($order_month < $current_month){
                      if(intval($current_day) > $configEntity['dayOff']){
                          $invoiceList[$value->id]["open"] = false;
                      }

                      if($order_month == 12){
                        $order_year += 1;
                      }
                    }elseif($order_year < $current_year){
                      $invoiceList[$value->id]["open"] = false;
                    }

                }

            }

            $facturaInvoices = WrapperHelper::getInvoices($clientId)->data;

            foreach ($facturaInvoices as $key => $value) {
                $facturaInvoiceList[$value->NumOrder] = $value;
                if(array_key_exists($value->NumOrder, $invoiceList)){
                    $invoiceList[$value->id]["sent"] = true;
                }
            }


            $collection = array_diff_key($invoiceList, $facturaInvoiceList);
            return $collection;
        }

    /**
     * Get Factura.com invoices
     *
     * @param Int $clientId
     * @return Array
     */
    static function getInvoiceItems($invoiceId){
        $itemsObj = Capsule::table('tblinvoiceitems')
                                ->where('tblinvoiceitems.invoiceid', $invoiceId)
                                ->get();
        return json_encode($itemsObj);
    }

    /**
     * Get Factura.com invoices
     *
     * @param Int $clientId
     * @return Array
     */
    static function getInvoices($clientId = null){

        $configEntity = WrapperConfig::configEntity();

        $url = $configEntity['apiUrl'] . 'whmcs/invoices';
        $url.= (isset($clientId)) ? '/' . $clientId : '';

        $request = 'GET';

        return WrapperApi::callCurl($url, $request);

    }

    /**
     * Send invoice to customer via email
     *
     * @param Int $uid
     * @return Array
     */
    static function sendInvoiceEmail($uid){
        if(!isset($uid)){
            return array(
                'Error' => 'No se ha recibido el id de la factura.',
            );
        }

        $configEntity = WrapperConfig::configEntity();

        $url     = $configEntity['apiUrl'] . 'invoice/' . $uid . '/email';
        $request = 'GET';

        return WrapperApi::callCurl($url, $request);
    }

    /**
     * Cancel invoice in Factura.com system
     *
     * @param Int $uid
     * @return Array
     */
    static function cancelInvoice($uid){
        if(!isset($uid)){
            return array(
                'Error' => 'No se ha recibido el id de la factura.',
            );
        }

        $configEntity = WrapperConfig::configEntity();

        $url     = $configEntity['apiUrl'] . 'invoice/' . $uid . '/cancel';
        $request = 'GET';

        return WrapperApi::callCurl($url, $request);
    }

    /**
     * Get Location by Postal Code
     *
     * @param String $cp
     * @return Array
     */
    static function getLocation($cp){
        if(!isset($cp)){
            return array(
                'Error' => 'No se ha recibido el Código Postal.',
            );
        }

        $configEntity = WrapperConfig::configEntity();

        $url     = $configEntity['apiUrl'] . 'getCodPos?cp=' . $cp;
        $request = 'GET';

        return WrapperApi::callCurl($url, $request);
    }

    /**
     * Get Client from Factura.com by RFC
     *
     * @param String $rfc
     * @return Array
     */
    static function getClient($rfc){
        if(!isset($rfc)){
            return array(
                'Error' => 'No se ha recibido el RFC del cliente.',
            );
        }

        $configEntity = WrapperConfig::configEntity();

        $url     = $configEntity['apiUrl'] . 'clients/' . $rfc;
        $request = 'GET';

        return WrapperApi::callCurl($url, $request);
    }

    /**
     * Update client information and create Invoice
     *
     * @param Int $orderNum
     * @param Array $orderItems
     * @param Array $clientData
     * @param String $serieInvoices
     * @param Int $clientW
     * @param String $paymentMethod
     * @return Array
     */
    static function createInvoice($orderNum, $orderItems, $clientData, $serieInvoices, $clientW, $paymentMethod){
        if($clientData[0] == ""){
            return array(
                'Error' => 'No se ha recibido el UID del cliente.',
            );
        }

        $configEntity = WrapperConfig::configEntity();
        $client_uid   = $clientData[0]["value"];
        $clientRFC    = $clientData[4]['value'];

        $clientFactura = self::getClient($clientRFC);
        $params = array(
            'nombre'           => $clientData[5]["value"],
            'apellidos'        => $clientData[6]["value"],
            'email'            => $clientData[7]["value"],
            'telefono'         => $clientData[8]["value"],
            'razons'           => $clientData[9]["value"],
            'rfc'              => $clientData[4]["value"],
            'calle'            => $clientData[10]["value"],
            'numero_exterior'  => $clientData[11]["value"],
            'numero_interior'  => $clientData[12]["value"],
            'codpos'           => $clientData[13]["value"],
            'colonia'          => $clientData[14]["value"],
            'estado'           => $clientData[16]["value"],
            'ciudad'           => $clientData[15]["value"],
            'delegacion'       => $clientData[15]["value"],
            'save'             => true,
            'client_reference' => $clientW,
        );

        if($clientFactura->status == 'error'){
            $clientUrl     = $configEntity['apiUrl'] . 'clients/create';
        }else{
            $clientUrl     = $configEntity['apiUrl'] . 'clients/' . $client_uid . '/update';
        }

        $request = 'POST';
        $processClient = WrapperApi::callCurl($clientUrl, $request, $params);

        if($processClient->status != 'success'){
            return array(
                'Error' => 'Ha ocurrido un error. Por favor revise sus datos e inténtelo de nuevo.',
            );
        }

        $itemsCollection = json_decode($orderItems);
        $invoiceConcepts = array();

        //Adding concepts to invoice
        foreach ($itemsCollection as $value){
            $productPrice = 0;

            if($configEntity["iva"] == 'on'){
              $productPrice = $value->amount - ($value->amount * 0.16);
            }else{
              $productPrice = $value->amount;
            }

            $product = array(
                'cantidad'  => 1,
                'unidad'    => 'Servicio',
                'concept'   => $value->description,
                'precio'    => $productPrice,
                'subtotal'  => $value->amount * 1,
            );

            array_push($invoiceConcepts, $product);
        }

        if($paymentMethod == 'Deposito'){
          $paymentMethodText = 'No Identificado';
        }else{
          $paymentMethodText = 'No Identificado';
        }

        $invoiceData = array(
            'rfc'           => $clientRFC,
            'items'         => $invoiceConcepts,
            'numerocuenta'  => 'No Identificado',
            'formapago'     => 'Pago en una Sola Exhibición',
            'metodopago'    => $paymentMethodText,
            'currencie'     => 'MXN',
            'iva'           => 1,
            'num_order'     => $orderNum,
            'seriefactura'  => $serieInvoices,
            'save'          => 'true'
        );

        $invoiceUrl     = $configEntity['apiUrl'] . 'invoice/create';
        $request = 'POST';
        $createInvoice = WrapperApi::callCurl($invoiceUrl, $request, $invoiceData);
        if($createInvoice->status != 'success'){
            return array(
                'Error' => $createInvoice->message,
            );
        }

        return $createInvoice;

    }

}
