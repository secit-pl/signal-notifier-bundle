# Symfony Signal Notifier Bundle

Signal support for Symfony Notifier 5.1+.

This bundle wraps [signal-cli](https://github.com/AsamK/signal-cli) into symfony notifier transport. 
To make it work you need to have possibility to execute [signal-cli](https://github.com/AsamK/signal-cli) command.

## Installation

From the command line run

```
$ composer require secit-pl/signal-notifier-bundle
```

## Configuration

First of all you should have properly set up the [signal-cli](https://github.com/AsamK/signal-cli). If it's working you are ready to proceed.

#### config/packages/notifier.yaml

```yaml
framework:
    notifier:
        texter_transports:
            signal: '%env(SIGNAL_DSN)%' # add Signal support
        channel_policy:
            urgent: ['sms/signal'] # setup it for specified channel
            high: ['email']
            medium: ['email']
            low: ['email']
```

#### .env

Configure the Signal DSN using the following format:

```dotenv
SIGNAL_DSN=signal://localhost?cli=SIGNAL_CLI_PATH&user=USER_NAME
```

Remember to properly encode the phone number. 
The + sign should be encoded as %2b so the number +481234567890 should be written as %2b481234567890!

For example:

```dotenv
SIGNAL_DSN=signal://localhost?cli=/usr/local/Cellar/signal-cli/0.6.8/bin/signal-cli&user=%2b48123456789
```

## Usage

You can now send the signal messages using Symfony Notifier.

If you'd like to send the message directly using Singal transport you can do it as follows:

```php
<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\TexterInterface;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index(TexterInterface $texter)
    {
        $texter->send((new SmsMessage(
            '+481234567890',
            'Hello :)'
        ))->transport('signal'));

        // ...
    }
}

```