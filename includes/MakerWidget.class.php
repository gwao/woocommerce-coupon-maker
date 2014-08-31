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
        echo "Hello World";
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

}

