<?php

/**
 * Author: Danny Villa Kalonji
 * Date: 05/01/2020
 * Time: 02:15
 */
namespace Oxanfoxs\OneServiceToYouSMS\Facades;

use \Illuminate\Support\Facades\Facade;

/**
 * Class MessageApi
 *
 * @method static string message()
 * @method static \Oxanfoxs\OneServiceToYouSMS\MessageApi setMessage(string $message, string $type = 'auto')
 * @method static string username()
 * @method static \Oxanfoxs\OneServiceToYouSMS\MessageApi setUsername(string $username)
 * @method static string password()
 * @method static \Oxanfoxs\OneServiceToYouSMS\MessageApi setPassword(string $passowrd)
 * @method static array mobileNumbers()
 * @method static \Oxanfoxs\OneServiceToYouSMS\MessageApi appendMobileNumber(string $number)
 * @method static \Oxanfoxs\OneServiceToYouSMS\MessageApi setMobileNumbers(array $numbers)
 * @method static string senderId()
 * @method static \Oxanfoxs\OneServiceToYouSMS\MessageApi setSenderId(string $from)
 * @method static bool isFlashed()
 * @method static \Oxanfoxs\OneServiceToYouSMS\MessageApi shouldFlash(bool $value)
 * @method static int type()
 * @method static string send()
 * @method static string checkCredit()
 *
 * @see \Oxanfoxs\OneServiceToYouSMS\MessageApi
 *
 * @package Oxanfoxs\OneServiceToYouSMS\Facades
 */
class MessageApi extends Facade
{
    const SIMPLE_TEXT_MESSAGE = 'text';

    const UNICODE_MESSAGE = 'unicode';

    const AUTO_DETECT = 'auto';

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