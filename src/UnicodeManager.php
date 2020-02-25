<?php
/**
 * Author: Danny Villa Kalonji
 * Date: 25/02/2020
 * Time: 02:28
 */

namespace Oxanfoxs\OneServiceToYouSMS;


class UnicodeManager
{
    /**
     * Determine whether a message should be encoded in unicode (UCS-2).
     *
     * @param $message
     * @return bool
     */
    public function detect($message)
    {
        return !preg_match('#^[A-Za-z0-9\s\-/\\|_*\#.,;:<>?{}éè&()\[\]`=@\'"!+%^$]+$#', $message);
    }

    /**
     * Encode a message to unicode (UCS-2).
     *
     * @param $message
     * @return string
     */
    public function encode($message)
    {
        return strtoupper(bin2hex(mb_convert_encoding($message, 'UCS-2', 'auto')));
    }

    /**
     * Decode a message from UCS-2 to UTF-8.
     *
     * @param $message
     * @return string
     */
    public function decode($message)
    {
        $message = substr($message, 2);
        $_message = hex2bin($message);
        $message = mb_convert_encoding($_message, 'UTF-8', 'UCS-2');
        return $message;
    }
}