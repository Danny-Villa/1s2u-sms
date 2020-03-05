<?php
/**
 * Author: Danny Villa Kalonji
 * Date: 03/01/2020
 * Time: 03:11
 */

namespace Oxanfoxs\OneServiceToYouSMS;


use Oxanfoxs\OneServiceToYouSMS\Exception\ArgumentMissedException;
use Oxanfoxs\OneServiceToYouSMS\Exception\InvalidCharacterException;
use Oxanfoxs\OneServiceToYouSMS\Exception\UnsupportedMessageTypeException;
use Oxanfoxs\OneServiceToYouSMS\Exception\ValueToLongException;
use Oxanfoxs\OneServiceToYouSMS\Exception\SmsNotSentException;
use Oxanfoxs\OneServiceToYouSMS\SmsCounter;

class MessageApi
{

    const SIMPLE_TEXT_MESSAGE = 'text';

    const UNICODE_MESSAGE = 'unicode';

    const AUTO_DETECT = 'auto';

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
     * The sender id is used for the "From" clause.
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
     * Manage unicode characters.
     *
     * @var UnicodeManager
     */
    protected $unicodeManager;

    /**
     * MessageApi constructor.
     */
    public function __construct()
    {
        $this->request = "https://api.1s2u.io/bulksms?";
        $this->mt = self::SIMPLE_TEXT_MESSAGE;
        $this->fl = 0;
        $this->setUsername(config('1s2u.username'));
        $this->setPassword(config('1s2u.password'));
        $this->unicodeManager = new UnicodeManager();
    }

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
     * This method auto-detect the right message type between simple text and unicode. The detection will depend on
     * used characters. However if any type is passed in there won't be auto-detection.
     *
     * @param string $message
     * @param int $type Determine the type of the message. It may be either simple text or unicode or auto-detect
     * @return $this
     */
    public function setMessage($message, $type = self::AUTO_DETECT)
    {
        $this->fixType($message, $type);

        if ($this->type() === self::UNICODE_MESSAGE) {
            if ($this->unicodeManager->detect($message))
                $this->msg = $this->unicodeManager->encode($message);
            else
                $this->msg = $message;
        } else {
            $this->msg = $message;
        }

        return $this;
    }

    /**
     * Fix the right type of the message.
     *
     * @param $message
     * @param $type
     * @return void
     */
    protected function fixType($message, $type)
    {
        if ($type === self::AUTO_DETECT) {
            if (!$this->unicodeManager->detect($message))
                $this->setType(self::SIMPLE_TEXT_MESSAGE);
            else
                $this->setType(self::UNICODE_MESSAGE);
        } else
            $this->setType($type);
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
        if (strlen($username) > 20)
            throw new ValueToLongException("The username cannot have more than 20 characters");

        if (!preg_match('#^[a-zA-Z0-9]+$#', $username))
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
        if (strlen($password) > 20)
            throw new ValueToLongException("The password cannot have more than 20 characters");

        if (!preg_match('#^[a-zA-Z0-9]+$#', $password))
            throw new InvalidCharacterException("The password may contain only alphanumeric characters.");

        $this->password = $password;

        return $this;
    }

    /**
     * Get the mobile numbers.
     *
     * @return array
     */
    public function mobileNumbers()
    {
        return $this->mno;
    }

    /**
     * Append a receiver mobile number.
     *
     * @param string $number
     * @return $this
     */
    public function appendMobileNumber($number)
    {
        if (!is_string((string)$number))
            throw new \InvalidArgumentException("The number should be a string.");

        if (strlen($number) > 20)
            throw new ValueToLongException("The mobile number cannot have more than 20 characters");

        if (!preg_match('#^[0-9]+$#', $number))
            throw new InvalidCharacterException("Only digits together with the country code are supported. Instead of plus sign (+) double zero (00) may be used.");

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
        $this->mno = array();
        foreach ($numbers as $item)
            $this->appendMobileNumber($item);

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
            if (strlen($from) > 16)
                throw new ValueToLongException("The sender ID cannot have more then 16 characters when numeric characters are used.");
        } elseif (preg_match('#^[a-zA-Z0-9]+$#', $from)) {
            if (strlen($from) > 11)
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
        $this->fl = $value === true ? true : false;
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
    private function setType($type)
    {
        if ($type !== self::SIMPLE_TEXT_MESSAGE && $type !== self::UNICODE_MESSAGE)
            throw new UnsupportedMessageTypeException('Only simple text and unicode messages are supported at the moment.');

        $this->mt = $type;

        return $this;
    }

    /**
     * Send the request to the provider attempting to send the message.
     *
     * @throws SmsNotSentException
     * @return bool|string|mixed
     */
    public function send()
    {
        $this->makeRequest();

        try {
            $response = file_get_contents($this->request);
        } catch(\Exception $ex) {
            throw new SmsNotSentException($ex->getMessage(), $ex->getCode());
        }

        return $response;
    }

    /**
     * Send request to check credits balance.
     *
     * @return bool|string|mixed
     */
    public function checkCredit()
    {
        $response = file_get_contents('https://api.1s2u.io/checkbalance?user='.$this->username().'&pass='.$this->password());

        return $response;
    }

    /**
     * Encode special characters in order not to collide with reserved HTTP/HTTPS characters.
     * Check the official documentation at https://www.1s2u.com for more information.
     *
     * @return $this
     */
    protected function encodeMessage()
    {
        return $this->setMessage(str_replace('%', '%25', $this->message()), $this->type())
            ->setMessage(str_replace('&', '%26', $this->message()), $this->type())
            ->setMessage(str_replace('+', '%2B', $this->message()), $this->type())
            ->setMessage(str_replace('#', '%23', $this->message()), $this->type())
            ->setMessage(str_replace('=', '%3D', $this->message()), $this->type())
            ->setMessage(str_replace('Enter', '%0A', $this->message()), $this->type());
    }

    /**
     * Build the request that will be sent.
     *
     * @return void
     */
    protected function makeRequest()
    {
        $this->validate();

        if ($this->type() === self::SIMPLE_TEXT_MESSAGE) {
            $smsCounter = new SmsCounter();
            $this->msg = $smsCounter->sanitizeToGSM($this->encodeMessage()->message());
            $message = urlencode($this->message());
        } else {
            $message = $this->message();
        }

        $this->request .= 'username='.$this->username().'&password='.$this->password().'&mno='.implode(',', $this->mobileNumbers());

        if (!empty($this->sid))
            $this->request .='&sid='.$this->senderId();
        $this->request .='&msg='.$message.'&mt='.($this->type() === self::UNICODE_MESSAGE ? 1 : 0).'&fl='.(int)$this->isFlashed();
    }

    /**
     * Check whether required values are filled.
     *
     * @throws ArgumentMissedException
     */
    protected function validate()
    {
        if (empty($this->username()))
            throw new ArgumentMissedException('User name is required.');
        if (empty($this->password()))
            throw new ArgumentMissedException('Password is required.');
        if (count($this->mobileNumbers()) < 1)
            throw new ArgumentMissedException('Mobile number is required.');
        if (count($this->mobileNumbers()) > 30)
            throw new ArgumentMissedException('Cannot send a message to more than 30 mobile numbers.');
        if (empty($this->message()))
            throw new ArgumentMissedException('Message is required.');
    }
}