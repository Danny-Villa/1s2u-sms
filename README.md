# 1s2u-sms

It is a package which makes it possible to send messages (SMS) through the gateway of https://www.1s2u.com

## Installation

Use the package manager [composer](https://getcomposer.org/) to install 1s2u-sms.

```bash
composer require dannyvilla/1s2u-sms
```
The `OneServiceToYouSMSServiceProdiver` is auto-discovered and registered by default, but if you want to register it yourself:

Add the ServiceProvider in config/app.php :
```php
'providers' => [
    /*
     * Package Service Providers...
     */
    Oxanfoxs\OneServiceToYouSMS\Provider\OneServiceToYouSMSServiceProdiver::class,

]
```

Add the Facade in config/app.php :

```php
'aliases' => [
    ...
    
    'MessageApi' => Oxanfoxs\OneServiceToYouSMS\Facades\MessageApi::class,

]
```
You should publish the package to get access to config file :

```bash
php artisan vendor:publish --provider="Oxanfoxs\OneServiceToYouSMS\Provider\OneServiceToYouSMSServiceProdiver"
```
This will create a new config file named config/1s2u.php.
Then you can update credentials within the 1s2u configuration file.

```php
     ...

    'username' => 'your-username-here',

     ....

    'password' => 'your-password-here',
```

## Usage

1s2u api allows to send 2 types of message.
- Simple text message : this type contains only [GSM 03.38](https://en.wikipedia.org/wiki/GSM_03.38) characters
- Unicode message : this type use [UCS-2](https://en.wikipedia.org/wiki/Universal_Coded_Character_Set) charset.

##### Send an auto-detected type message.
```php
$response = MessageApi::setMessage('Hello word')
                      ->setSenderId('SMS')
                      ->setMobileNumbers(['123456789', '234567891'])
                      ->appendNumber('123456678')
                      ->send();
```

##### Send a simple text message.
```php
$response = MessageApi::setMessage('Hello word', MessageApi::SIMPLE_TEXT_MESSAGE)
                      ->setSenderId('FROM')
                      ->setMobileNumbers(['1234567890'])
                      ->send();
```

##### Send a unicode message.
```php
$response = MessageApi::setMessage('Hello word', MessageApi::UNICODE_MESSAGE)
                      ->setSenderId('FROM')
                      ->setMobileNumbers(['1234567890'])
                      ->send();
```

##### Send a flash message.
```php
$response = MessageApi::setMessage('Hello word')
                      ->setSenderId('FROM')
                      ->setMobileNumbers(['1234567890'])
                      ->shouldFlash(true)
                      ->send();
```


##### Checking credit.
\Oxanfoxs\OneServiceToYouSMS\MessageApi::checkCredit() method allows to check credits balance.
```php
$response = MessageApi::checkCredit()
```

To get more information about the response please check the official documentation [here](https://1s2u.com/sms-developers.asp)

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

## License
[MIT](https://choosealicense.com/licenses/mit/)