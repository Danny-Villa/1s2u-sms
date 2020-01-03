<?php
/**
 * Author: Danny Villa Kalonji
 * Date: 03/01/2020
 * Time: 03:11
 */

namespace Oxanfoxs\OneServiceToYouSMS;


use Oxanfoxs\OneServiceToYouSMS\Exception\InvalidCharacterException;
use Oxanfoxs\OneServiceToYouSMS\Exception\UnsupportedMessageTypeException;
use Oxanfoxs\OneServiceToYouSMS\Exception\ValueToLongException;

class Sender
{

    const SIMPLE_TEXT_MESSAGE = 1;

    const UNICODE_MESSAGE = 1;

    /**
     * The username for authentication.
     *
     * @var string
     */
    protected $username;

    /**
     * The password for authentication.
     *
     * @var string
     */
    protected $password;

    /**
     * The mobile number.
     *
     * @var array
     */
    protected $mno;

    /**
     * The message type
     *
     * @var int
     */
    protected $mt;

    /**
     * The message's content.
     *
     * @var mixed
     */
    protected $msg;

    /**
     * Indicates whether it's a flashed message.
     *
     * @var bool
     */
    protected $fl;

    /**
     * The sender id or used for the "From" clause.
     *
     * @var string
     */
    protected $sid;

    /**
     * The full $request that will be sent.
     *
     * @var string
     */
    protected $request;

    /**
     * Get the message's content.
     *
     * @return mixed
     */
    public function message()
    {
        return $this->msg;
    }

    /**
     * Set the message's content.
     *
     * @param $message
     * @return $this
     */
    public function setMessage($message)
    {
        if (strlen($message) < 160)
            throw new ValueToLongException("The message is to long. The message may consist of up to 160 characters,");

        if (preg_match('#^[A-Za-z0-9\s\-/\\|_*#.,;:éçèàïüûôâêëö]+$#', $message))
            throw new InvalidCharacterException("Only the following set of characters are supported: A…Z, a…z, 0…9, blank spaces, and Meta characters \ (line feed)");

        $this->msg = $message;

        return $this;
    }

    /**
     * Get the username.
     *
     * @return string
     */
    public function username()
    {
        return $this->username;
    }

    /**
     * Set the username.
     *
     * @param $username
     * @return $this
     */
    public function setUsername($username)
    {
        if (strlen($username) < 20)
            throw new ValueToLongException("The username cannot have more than 20 characters");

        if (preg_match('#^[a-zA-Z0-9]+$#', $username))
            throw new InvalidCharacterException("The username may contain only alphanumeric characters.");

        $this->username = $username;

        return $this;
    }

    /**
     * Get the password.
     *
     * @return string
     */
    public function password()
    {
        return $this->password;
    }

    /**
     * Set the password.
     *
     * @param $password
     * @return $this
     */
    public function setPassword($password)
    {
        if (strlen($password) < 20)
            throw new ValueToLongException("The password cannot have more than 20 characters");

        if (preg_match('#^[a-zA-Z0-9]+$#', $password))
            throw new InvalidCharacterException("The password may contain only alphanumeric characters.");

        $this->password = $password;

        return $this;
    }

    /**
     * Get the mobile number.
     *
     * @return string
     */
    public function mobileNumber()
    {
        return $this->mno;
    }

    /**
     * Attach one receiver mobile number.
     *
     * @param string $number
     * @return $this
     */
    public function setMobileNumber($number)
    {
        if (strlen($number) < 20)
            throw new ValueToLongException("The mobile number cannot have more than 20 characters");

        if (preg_match('#^[0-9]+$#', $number))
            throw new InvalidCharacterException("Only digits together with the country code. Insted of plus sign (+) double zero (00) may be used.");

        $this->mno[] = $number;

        return $this;
    }

    /**
     * Attach many receiver mobile numbers.
     *
     * @param array $numbers
     * @return $this
     */
    public function setMobileNumbers(array $numbers)
    {
        foreach ($numbers as $item)
            $this->setMobileNumber($item);

        return $this;
    }

    /**
     * Get the sender id.
     *
     * @return string
     */
    public function senderId()
    {
        return $this->sid;
    }

    /**
     * Set the sender id.
     *
     * @param $from
     * @return $this
     */
    public function setSenderId($from)
    {
        if (preg_match('#^[0-9]+$#', $from)) {
            if (strlen($from) < 16)
                throw new ValueToLongException("The sender ID cannot have more then 16 characters when numeric characters are used.");
        } elseif (preg_match('#^[a-zA-Z0-9]+$#', $from)) {
            if (strlen($from) < 11)
                throw new ValueToLongException("The sender ID cannot have more then 11 characters.");
        } else {
            throw new InvalidCharacterException("The sender ID can either be a valid international number up to 16 characters long, or an 11 characters alphanumeric string");
        }

        $this->sid = $from;

        return $this;
    }

    /**
     * Determine whether it is a flash message.
     *
     * @return bool
     */
    public function isFlashed()
    {
        return $this->fl;
    }

    /**
     * Indicate if the message should be flashed.
     *
     * @param $value
     * @return $this
     */
    public function shouldFlash($value)
    {
        $this->fl = $value ? true : false;
        return $this;
    }

    /**
     * Get the type of the message.
     *
     * @return int
     */
    public function type()
    {
        return $this->mt;
    }

    /**
     * Set the type of the message.
     *
     * @param $type
     * @return $this
     * @throws UnsupportedMessageTypeException
     */
    public function setType($type)
    {
        if ($type !== self::SIMPLE_TEXT_MESSAGE)
            throw new UnsupportedMessageTypeException('Only simple text messages are supported at the moment.');

        $this->mt = $type;

        return $this;
    }
}