<?php 
if ( ! class_exists( 'Easyship_Shipping_API' ) ) {
    class Easyparcel_Shipping_API {

        private static $apikey = '';
        private static $apiSecret = '';
        private static $easyparcel_email = '';
        private static $integration_id = '';
        private static $sender_postcode = '';
        private static $sender_state = '';
        private static $sender_country = '';
        private static $api_url = "http://easyparcel.slamet/id/id/?ac=GetEPRate"; 
        private static $auth_url = "http://easyparcel.slamet/id/id/?ac=CheckValidUser";

        private static $list_courier_url = "http://easyparcel.slamet/id/id/?ac=GetIntegrationCouriers";

         /**
         * init
         *
         * @access public
         * @return void
         */
        public static function init() {

            $WC_Easyparcel_Shipping_Method = new WC_Easyparcel_Shipping_Method();
            self::$easyparcel_email = $WC_Easyparcel_Shipping_Method->settings['easyparcel_email'];
            self::$integration_id = $WC_Easyparcel_Shipping_Method->settings['integration_id'] ;
            self::$sender_postcode = $WC_Easyparcel_Shipping_Method->settings['sender_postcode'] ;
            self::$sender_state = "" ;

        }

        public static function auth()
        {
            $url = self::$auth_url;
            $auth = array( 
                "user_email"=>self::$easyparcel_email,
                "integration_id"=>self::$integration_id
                );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $auth);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            ob_start();
            $r = curl_exec($ch);
            ob_end_clean();
            curl_close ($ch);
            $json = json_decode($r);

            return $json->message;
        }

        public static function getCourierList()
        {
            $url = self::$list_courier_url;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 0);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            ob_start();
            $r = curl_exec($ch);
            ob_end_clean();
            curl_close ($ch);
            $json = json_decode($r);

            return $json;
        }

        public static function getShippingRate($destination,$items,$weight)
        {
          $WC_Country = new WC_Countries();
          if($WC_Country->get_base_country() == 'ID'){
 

             $weight=ceil($weight);

            if($weight == 0 || $weight ==''){$weight=1;}
            $url = self::$api_url;

            $WC_Easyparcel_Shipping_Method = new WC_Easyparcel_Shipping_Method();

                if($WC_Easyparcel_Shipping_Method->settings['cust_rate'] == 'normal_rate')
                 {  self::$easyparcel_email = '';
                    self::$integration_id = ''; 
                }

            //prevent user select fix Rate but didnt put postcode no result
            if($WC_Easyparcel_Shipping_Method->settings['cust_rate']  == 'fix_rate' && self::$sender_postcode == '')
            { self::$sender_postcode = '11950';}

        $pv = ''; 
            $f = array(
                "user_email"=>self::$easyparcel_email,
                "integration_id"=>self::$integration_id,
                "pick_country" =>$WC_Country->get_base_country(),
                "pick_code"            => self::$sender_postcode, 
                "send_country"    => $destination["country"],
                "pick_state"      => self::$sender_state,
                "send_state"    => $destination["state"],
                "send_code"       => ($destination["postcode"] == '') ? 0 : $destination["postcode"], # required
                'weight'=>$weight
             
            );
         // print_r($f);die();
            //country validation 
        if($WC_Country->get_base_country()=='ID'){
            foreach($f as $k => $v){ $pv .= $k . "=" . $v . "&"; }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $pv);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            ob_start();
            $r = curl_exec($ch);
            ob_end_clean();
            curl_close ($ch);
             $json = json_decode($r);
          
            if(sizeof($json->rates) > 0){
            
                return $json->rates;
            }

        }else{
            return array();
        }
    }

            // should never reach here
            return array(); // return empty array
        }
    }
}
