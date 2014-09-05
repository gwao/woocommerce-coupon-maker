<?php
/**
 * Class CouponAjaxHandler
 * @author Zheng Xian Qiu
 */

namespace MOHO;

class CouponAjaxHandler
{
    /**
     * Handle
     *
     * Handle ajax coupon maker request.
     */
    public static function handle()
    {
        $requestData = self::filterRequest( $_POST );

        $result = new \StdClass;

        $message = $requestData->post->post_title . ' & MOHO ID: ' . $requestData->mohoID;


        $result->message = $message;
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

        return $filted;
    }
}

