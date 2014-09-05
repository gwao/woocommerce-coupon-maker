<?php
/**
 * Class CouponAjaxHandler
 * @author Zheng Xian Qiu
 */

namespace MOHO;

class CouponAjaxHandler
{
    const MOHO_API_ENDPOINT = "http://magic-bonus.com/api/v3";
    /**
     * Handle
     *
     * Handle ajax coupon maker request.
     */
    public static function handle()
    {
        $requestData = self::filterRequest( $_POST );
        $storeID = (int) get_post_meta( $requestData->post->ID, 'wc_coupon_maker_store_id' );

        $result = new \StdClass;

        if( !CouponMaker::isWooCommerceActive($storeID) ) {
            http_response_code(404);
            $result->error = __("Target store not active coupon service");
        } elseif( is_null($requestData->mohoID) ) {
            http_response_code(404);
            $result->error = __("MOHO ID is necessary!");
        } else {

            // Generate Random Coupon for test
            $couponCode = strtoupper(uniqid());
            $newCoupon = CouponAjaxHandler::createCoupon( $storeID, $requestData->mohoID, $couponCode);

            $message = "Coupon For: {$requestData->mohoID}, Code: {$newCoupon->post_title}";
            $result->message = $message;
        }
        echo json_encode($result);
        die();
    }

    /**
     * Filter Request
     *
     * Filter ajax request and leave necessary data.
     *
     * @param array $rawPOST origin post data
     * @return object filted data
     */
    public static function filterRequest(array $rawPost)
    {
        $filted = new \StdClass;

        $filted->post = isset($rawPost['post_id']) ? get_post( (int) $rawPost['post_id'] ) : NULL;
        $filted->mohoID = isset($rawPost['moho_id']) ? (int) $rawPost['moho_id'] : NULL;
        $filted->mohoID = $filted->mohoID > 0 ?: NULL;

        return $filted;
    }

    /**
     * Create Coupon
     *
     * Create coupon to specify store
     *
     * @param int $storeID
     * @param int $mohoID MOHO User ID
     * @param string $couponCode
     * @param int $amount
     * @param string $type
     * @param array $options
     * @return object|bool coupon post object
     */
    public static function createCoupon( $storeID, $mohoID, $couponCode, $amount = 10, $type = 'percent', $options = array() )
    {
        $coupon = null;

        switch_to_blog($storeID);

        $defaultOptions = array(
            'individual_use' => 'no',
            'apply_before_tax' => 'yes',
            'free_shipping' => 'no'
        );
        $options = array_merge($defaultOptions, $options);

        $coupon = array(
            'post_title' => $couponCode ,
            'post_content' => '',
            'post_status' => 'publish',
            'post_author' => 1,
            'post_type' => 'shop_coupon'
        );

        $newCouponID = wp_insert_post( $coupon );

        update_post_meta( $newCouponID, 'moho_id', $mohoID );
        update_post_meta( $newCouponID, 'discount_type', $type );
        update_post_meta( $newCouponID, 'coupon_amount', $amount );

        foreach($options as $optionName => $optionValue) {
            update_post_meta( $newCouponID, $optionName, $optionValue);
        }

        $coupon = get_post( $newCouponID );

        restore_current_blog();

        return $coupon;
    }
}

