<?php

namespace Kagatan\MonologTelegram;

use Monolog\Logger;

class TelegramLogger
{
    /**
     * Create a custom Monolog instance.
     *
     * @param array $config
     * @return \Monolog\Logger
     */
    public function __invoke(array $config)
    {
        return new Logger('log', [new TelegramHandler($config['token'], $config['channel'])]);
    }
}
