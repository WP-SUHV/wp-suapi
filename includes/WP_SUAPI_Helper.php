<?php
namespace WP_SUAPI;

use SUHV\Suapi\ApiHandler;

class WP_SUAPI_Helper {
    /**
     * Uses get_options and initialize API_HANDLER
     * @return ApiHandler
     */
    public static function GET_INITIALIZED_API_HANDLER()
    {
        return new ApiHandler(get_option("wp-suapi_api-url"), get_option("wp-suapi_api-key"), get_option("wp-suapi_api-version"), get_option("wp-suapi_extra-usecache"));
    }
}
