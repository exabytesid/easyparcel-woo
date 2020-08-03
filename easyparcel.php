<?php
/*
Plugin Name: EasyParcel
Plugin URI: https://easyparcel.co.id/?pg=MyAccount&tab=Integrate&do=WooInsert
Description: EasyParcel plugin to enable courier and shipping rate to display in checkout page. To get started, activate EasyParcel plugin and then go to WooCommerce > Settings > Shipping > EasyParcel Shipping to set up your Integration ID.
Version: 1.0.0
Author: EasyParcel
Author URI: https://www.easyparcel.my/
WC requires at least: 3.0.0
WC tested up to: 4.2.4
*/
if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}
/*
 * Check if WooCommerce is active
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {


    if ( ! class_exists( 'WC_Integration_Easyparcel' ) ) :

        class WC_Integration_Easyparcel {

            /**
            * Construct the plugin.
            */
            public function __construct() {
                 add_action( 'woocommerce_shipping_init', array( $this, 'init' ) );
            }

            /**
            * Initialize the plugin.
            */
            public function init() {
                // start a session


                // Checks if WooCommerce is installed.
                if ( class_exists( 'WC_Integration' ) ) {
                    // Include our integration class.
                    include_once plugin_dir_path(__FILE__) . 'include/easyparcel_shipping.php';

                   // Register the integration.
                    add_filter( 'woocommerce_shipping_methods', array( $this, 'add_integration' ) );
                } else {
                    // throw an admin error if you like
                }
            }

            /**
             * Add a new integration to WooCommerce.
             */
            public function add_integration( $integrations ) {
                $integrations['easyparcel'] = 'WC_Easyparcel_Shipping_Method';
                return $integrations;
            }

        }

        $WC_Integration_Easyparcel = new WC_Integration_Easyparcel( __FILE__ );

     endif;

}


