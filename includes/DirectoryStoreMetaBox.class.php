<?php
/**
 * Class DirectoryStoreMetaBox
 * @author Zheng Xian Qiu
 */

namespace MOHO;

class DirectoryStoreMetaBox
{
    /**
     * Registe Meta Box
     *
     * Register meta box for store directory.
     */
    public static function registerMetaBox()
    {
        $screens = array('item'); // NOTE: Current force support MOHO Directory Theme
        foreach($screens as $screen) {
            add_meta_box('coupon-maker', __('Coupon Maker'), array(__NAMESPACE__ . '\DirectoryStoreMetaBox', 'callback'), $screen);
        }
    }

    /**
     * Callback
     *
     * Print metabox to list stores.
     *
     * @param object $post
     */
    public static function callback( $post )
    {
        wp_nonce_field( 'coupon_maker', 'coupon_maker_nonce' );

        $storeID = get_post_meta( $post->ID, 'wc_coupon_maker_store_id', true );
        $discount = get_post_meta( $post->ID, 'wc_coupon_maker_discount', true );
        $minimumAmount = get_post_meta( $post->ID, 'wc_coupon_maker_minimum_amount', true );
?>
        <table>
            <tr>
                <th><label for="store_id"><?php echo _('Store ID') ?></label></th>
                <td>
                    <div class="form-group">
                        <input type="number" name="store_id" class="form-control" value="<?php echo esc_attr($storeID); ?>" />
                    </div>
                </td>
            </tr>
            <tr>
                <th><label for="discount_percent"><?php echo _('Discount') ?> (%)</label></th>
                <td>
                    <div class="form-group">
                        <input type="number" name="discount_percent" class="form-control" value="<?php echo esc_attr($discount); ?>" />
                    </div>
                </td>
            </tr>
            <tr>
                <th><label for="minimum_amount"><?php echo _('Minimun Amount') ?></label></th>
                <td>
                    <div class="form-group">
                        <input type="number" name="minimum_amount" class="form-control" value="<?php echo esc_attr($minimumAmount); ?>" />
                    </div>
                </td>
            </tr>
        </table>
<?php
    }

    /**
     * Save Post Handler
     *
     * Save metabox information
     *
     * @param int $postID the post id
     */
    public static function savePostHandler( $postID )
    {
        if( !isset( $_POST['coupon_maker_nonce'] ) ) { return; }
        if( !wp_verify_nonce( $_POST['coupon_maker_nonce'], 'coupon_maker' ) ) { return; }
        if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
        if( !isset($_POST['store_id']) ) { return; }

        $blogID = (int) $_POST['store_id'];
        $discount = (float) $_POST['discount_percent'];
        $minimumAmount = (float) $_POST['minimum_amount'];
        if( $blogID == -1 ) { // Clear Store Setting
            update_post_meta( $postID, 'wc_coupon_maker_store_id', null);
            return;
        }

        // Check active plugins include WooCommerce
        if( CouponMaker::isWooCommerceActive( $blogID) ) {
            // Save Setting
            update_post_meta( $postID, 'wc_coupon_maker_store_id', $blogID );
            if( $discount > 0 && $discount <= 100) {
                update_post_meta( $postID, 'wc_coupon_maker_discount', $discount );
            }
            if( $minimumAmount >= 0 ) {
                update_post_meta( $postID, 'wc_coupon_maker_minimum_amount', $minimumAmount );
            }
        }
    }
}

