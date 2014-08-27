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
            add_action( 'widgets_init', array( $this, 'include_widgets' ) );

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
            if( empty(self::$_instance) or !self::$_instance instanceof CouponMaker ) {
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
            $className = ltrim($className, '\\' . __NAMESPACE__); // Force Limit Namespace
            $className = ltrim($className, '\\');
            $fileName  = '';
            $namespace = '';
            if ($lastNsPos = strrpos($className, '\\')) {
                $namespace = substr($className, 0, $lastNsPos);
                $className = substr($className, $lastNsPos + 1);
                $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
            }
            $fileName = 'includes' . DIRECTORY_SEPARATOR . $fileName;
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
            // TODO: Add some initialize code

            do_action('after_woocommerce_coupon_maker_init');
        }

        /**
         * Include coupon widgets
         */
        public function include_widgets()
        {
            $directory_blog = get_site_option('woocommerce_coupon_maker_directory_blog_id');
            if(get_current_blog_id() === $directory_blog) {
                // Register widget for directory blog
                // TODO: Add widget
            }
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

// Global for backwords campatibility
$GLOBALS['couponMaker'] = CouponMaker();
