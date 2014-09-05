<?php
/**
 * Plugin Name: WooCommerce Coupon Maker
 * Description:  The WooCommerce coupon generator.
 * Author: Zheng Xian Qiu
 * Version: 1.0
 * Author URI: http://frost.tw/
 * Network: true
 *
 * @package Coupon_Maker
 * @version 1.0
*/

namespace MOHO;

if( ! defined( 'ABSPATH' )) {
    exit; // Exit if accessed directly
}

if( !defined('WC_COUPON_MAKER_URL') ) {
    define('WC_COUPON_MAKER_URL', plugin_dir_url(__FILE__));
}

if( !defined( 'WC_COUPON_MAKER_PATH') ) {
    define('WC_COUPON_MAKER_PATH', plugin_dir_path(__FILE__));
}

if ( ! class_exists('CouponMaker') ) {

    /**
     * Main Coupon Maker Class
     *
     * @version 1.0.0
     */

    final class CouponMaker {

        /**
         * @var CouponRegiter The single instance of the class
         */
        protected static $_instance = null;

        /**
         * CouponMaker Constructor
         *
         * @access private
         * @return CouponMaker
         */
        private function __construct()
        {
            if( function_exists("__autoload") ) {
                spl_autoload_register("__autoload");
            }

            spl_autoload_register( array($this, 'autoload') );

            // Initialize API
            // TODO: Add API Class and implement api feature

            // Hooks
            add_action( 'init', array( $this, 'init' ), 0 );
            add_action( 'widgets_init', array( $this, 'includeWidgets' ) );

            add_action( 'network_admin_menu', array( $this, 'networkAdminMenu') );
            add_action( 'wpmuadminedit', __NAMESPACE__ . '\OptionsPage::init' );

            do_action('woocommerce_coupon_maker_loaded');
        }

        /**
         * Main CouponMaker Instance
         *
         * Ensure only one instance of CouponMaker is loaded or can be loaded.
         *
         * @static
         * @see CouponMaker()
         * @return CouponMaker - Main Instance
         */
        public static function instance()
        {
            if( empty(self::$_instance) || !self::$_instance instanceof CouponMaker ) {
                self::$_instance = new CouponMaker();
            }

            return self::$_instance;
        }

        /**
         * Autoload
         *
         * @param mixed $class
         * @return void
         */

        public function autoload( $className )
        {
            $className = ltrim($className, __NAMESPACE__); // Force Limit Namespace
            $className = ltrim($className, '\\');
            $fileName  = '';
            $namespace = '';
            if ($lastNsPos = strrpos($className, '\\')) {
                $namespace = substr($className, 0, $lastNsPos);
                $className = substr($className, $lastNsPos + 1);
                $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
            }
            $fileName = WC_COUPON_MAKER_PATH . 'includes' . DIRECTORY_SEPARATOR . $fileName;
            $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.class.php';

            if( file_exists($fileName) ) {
                include_once $fileName;
            }
        }

        /**
         * Initialize CouponMaker when WordPress initializes.
         */

        public function init()
        {
            do_action('before_woocommerce_coupon_maker_init');

            add_action( 'wp_ajax_nopriv_coupon_maker', array($this, 'couponHandler') );
            add_action( 'wp_ajax_coupon_maker', array($this, 'couponHandler') );

            do_action('after_woocommerce_coupon_maker_init');
        }

        /**
         * Include coupon widgets
         */
        public function includeWidgets()
        {
            $directory_blog = get_site_option('wc_coupon_maker_directory_blog_id');
            if(get_current_blog_id() === (int) $directory_blog) {
                // Register widget for directory blog
                register_widget( __NAMESPACE__ . '\MakerWidget' );

                // Register Widget Scripts
                wp_enqueue_script( 'coupon-maker', WC_COUPON_MAKER_URL . '/js/coupon-maker.js', array('jquery') );
                wp_localize_script( 'coupon-maker', 'couponMaker', array( 'ajaxURL' => admin_url( 'admin-ajax.php' ) ) );
            }
        }

        /**
         * Add network admin menu
         */

        public function networkAdminMenu()
        {
            add_submenu_page('settings.php', 'Coupon Maker', 'Coupon Maker', 'manage_options', 'woocommerce-coupon-maker', __NAMESPACE__ . '\OptionsPage::page');
        }

        /**
         * Coupon Handler
         *
         * Handle ajax request to create coupon
         */

        public function couponHandler()
        {
            $result = new \StdClass;

            $result->message = "Hello World";
            echo json_encode($result);
            die();
        }

    }
}

/**
 * Returns the main instance of CouponMaker to preent the need to use globals.
 *
 * @return CouponMaker
 */
function CouponMaker()
{
    return CouponMaker::instance();
}

CouponMaker();
