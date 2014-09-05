<?php
/**
 * Class OptionsPage
 * @author Zheng Xian Qiu
 *
 * @package CouponMaker
 */

namespace MOHO;

class OptionsPage
{
    /**
     * Init
     *
     * Initialize WordPress options page
     */
    public static function init()
    {
        if( isset($_POST['wc_coupon_maker']) ) {
            check_admin_referer('woocommerce-coupon-maker-options');

            if( isset($_POST['directory_blog']) ) {
                    update_site_option( 'wc_coupon_maker_directory_blog_id', (int) $_POST['directory_blog'] );
            }

            wp_redirect( add_query_arg('page', 'woocommerce-coupon-maker', add_query_arg( 'updated', 'true', network_admin_url( 'settings.php' ) ) ) );
            exit();
        }
    }

    /**
     * Page
     *
     * Generate WordPress options page
     */
    public static function page()
    {
?>
        <form action="settings.php" method="post">
        <h2><?php echo __('Coupon Maker', 'coupon-maker') ?></h2>
        <input name="wc_coupon_maker" type="hidden" value="1" />
<?php
        wp_nonce_field( 'woocommerce-coupon-maker-options' );
?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="directory_blog"><?php _e( 'Directory Blog ID' ) ?></label></th>
                <td>
                    <input name="directory_blog" type="text" id="directory_blog" class="regular-text" value="<?php echo get_site_option('wc_coupon_maker_directory_blog_id'); ?>" />
                </td>
            </tr>
        </table>
<?php
        submit_button();
?>
        </form>
<?php
    }
}

