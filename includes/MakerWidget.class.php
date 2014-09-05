<?php
/**
 * Class MakerWidget
 * @author Zheng Xian Qiu
 */

namespace MOHO;

class MakerWidget extends \WP_Widget
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct( 'coupon-maker', 'Coupon Maker' );
    }

    /**
     * Widget
     *
     * Define output for widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $instance )
    {
        $title = apply_filters( 'widget_title', $instance['title'] );
        $post = get_post();
        $storeID = (int) get_post_meta( $post->ID, 'wc_coupon_maker_store_id');
        $storeID = $storeID ?: -1;

        if( !CouponMaker::isWooCommerceActive( $storeID) ) {
            return;
        }

        if( !$this->validatePostType($post) ) {
            return; // If not valid, just return
        }

        echo $args['before_widget'];
        if( !empty( $title) ) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

?>
        <form id="coupon-maker">
            <div class="row">
                <div class="col-md-12">
                    <div class="notifier hidden alert">
                    </div>
                </div>
            </div>
            <div class="form">
                <div class="form-group">
                    <input type="number" class="form-control" name="moho_id" placeholder="MOHO ID" />
                </div>
                <input type="hidden" name="post_id" value="<?php the_ID();  ?>" />
                <button type="submit" class="btn btn-primary"><?php _e('Apply', 'woocommerce_coupon_maker'); ?></button>
            </div>
        </form>
<?php
        echo $args['after_widget'];
    }

    /**
     * Update
     *
     * Update widget configure
     *
     * @param array $newInstance
     * @param array $oldInstance
     */
    public function update( $newInstance, $oldInstance )
    {
        $instance = array();
        $instance['title'] = ( ! empty( $newInstance['title'] ) ) ? strip_tags( $newInstance['title'] ) : '';
        return $instance;
    }

    /**
     * Form
     *
     * Widget configure form output
     *
     * @param array $instance
     */
    public function form($instance)
    {
        $title = "";
        if( isset( $instance['title'] ) ) {
            $title = $instance['title'];
        }

?>
        <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo _e( 'Title: ') ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
<?php
    }

    /**
     * Validate Post Type
     *
     * Check is the post support "Coupon Maker"
     *
     * @param object $post the post object
     * @return bool
     */

    public function validatePostType($post)
    {
        // NOTE: Current force lock to our directory theme post type
        return ($post->post_type == 'item');
    }

}

