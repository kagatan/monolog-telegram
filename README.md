

monolog-telegram
=============

🔔 Telegram Handler which allows you log messages to telegram channels using bots for Laravel


# Installation
-----------
Install using composer:

```bash
composer require kagatan/monolog-telegram  
```

# Installation
Open up config/logging.php and find the channels key. Add the following channel to the list.

```php

 'channels' => [
    'stack' => [
        'driver'   => 'stack',
        'channels' => ['single', 'telegram'],
    ],
    
    ....
    
    'telegram' => [
        'driver'  => 'custom',
        'via'     => Kagatan\MonologTelegram\TelegramLogger::class,,
        'token'   => env('LOG_TELEGRAM_BOT_ID'),
        'channel' => env('LOG_TELEGRAM_CHAT_ID')
    ],
]

```

Add the following information to your .env file. Your LOG_TELEGRAM_BOT_ID is for the your bot key and LOG_TELEGRAM_CHAT_ID is the chat ID for a telegram user or channel.

```php
LOG_TELEGRAM_BOT_ID=123456789:ABCDEFGHIJKLMNOPQUSTUFWXYZabcdefghi
LOG_TELEGRAM_CHAT_ID=12345678
```

