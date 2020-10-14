# Ileti Merkezi SMS Notifications Channel for Laravel

This package makes it easy to send sms notifications using [Ileti Merkezi](https://www.iletimerkezi.com/) with Laravel 7.0+ and 8.0

## Contents

- [Installation](#installation)
    - [Setting up the Ileti Merkezi service](#setting-up-the-Ileti-Merkezi-service)
- [Usage](#usage)
    - [ On-Demand Notifications](#on-demand-notifications)
- [Changelog](#changelog)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)


## Installation

You can install this package via composer:

``` bash
composer require macellan/ileti-merkezi
```


### Setting up the Ileti Merkezi service

Add your Ileti Merkezi sms gate login, password and default sender name to your config/services.php:

```php
// config/services.php
...
    'sms' => [ 
        'iletimerkezi' => [
            'key' => env('ILETIMERKEZI_KEY'),
            'secret' => env('ILETIMERKEZI_SECRET'),
            'origin' => env('ILETIMERKEZI_ORIGIN'),
            'enable' => env('ILETIMERKEZI_ENABLE', true),
            'debug' => env('ILETIMERKEZI_DEBUG', false), //will log sending attempts and results
            'sandboxMode' => env('ILETIMERKEZI_SANDBOX_MODE', false) //will not invoke API call
        ],
    ],
...
```


## Usage

You can use the channel in your via() method inside the notification:

```php
use Illuminate\Notifications\Notification;
use Macellan\IletiMerkezi\IletiMerkeziMessage;

class AccountApproved extends Notification
{
    public function via($notifiable)
    {
        return ['iletimerkezi'];
    }

    public function toIletiMerkezi($notifiable)
    {
        return IletiMerkeziMessage::create()
            ->setBody('Your account was approved!')
            ->setSendTime(now());  
    }
}
```

In your notifiable model, make sure to include a routeNotificationForSms() method, which returns a phone number or an array of phone numbers.

```php
public function routeNotificationForSms()
{
    return str_replace(['+', ' '], '', $this->phone);
}
```


### On-Demand Notifications

Sometimes you may need to send a notification to someone who is not stored as a "user" of your application. Using the Notification::route method, you may specify ad-hoc notification routing information before sending the notification:

```php
Notification::route('iletimerkezi', '905322234433')  
            ->notify(new AccountApproved());
```


## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Security

If you discover any security related issues, please email fatih@aytekin.me instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Fatih Aytekin](https://github.com/faytekin)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
