<?php

/**
 * Author: Danny Villa Kalonji
 * Date: 05/01/2020
 * Time: 02:15
 */
namespace Oxanfoxs\OneServiceToYouSMS\Facades;

use \Illuminate\Support\Facades\Facade;

class MessageApi extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    public static function getFacadeAccessor()
    {
        return 'messageApi';
    }
}