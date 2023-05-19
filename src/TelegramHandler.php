<?php

namespace Kagatan\MonologTelegram;

use Exception;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Monolog\LogRecord;

/**
 * Telegram Handler For Monolog
 *
 * This class helps you in logging your application events
 * into telegram using it's API.
 *
 * @author Moein Rahimi <m.rahimi2150@gmail.com>
 */
class TelegramHandler extends AbstractProcessingHandler
{
    private $token;
    private $channel;

    const host = 'https://api.telegram.org/bot';

    /**
     * getting token a channel name from Telegram Handler Object.
     *
     * @param string $token Telegram Bot Access Token Provided by BotFather
     * @param string $channel Telegram Channel userName
     * @param string $timezone set default date timezone
     * @param string $dateFormat set default date format
     * @param string $timeOut curl timeout
     */
    public function __construct($token, $channel, $timeOut = 100)
    {
        $this->token = $token;
        $this->channel = $channel;
        $this->timeOut = $timeOut;

        $format = "%message% \n[%context%]\n";
        $formatter = new LineFormatter($format, LineFormatter::SIMPLE_DATE, true);
        $this->setFormatter($formatter);

        parent::__construct('DEBUG', true);
    }

    /**
     * format the log to send
     * @param $record [] log data
     * @return void
     */
    public function write(LogRecord $record): void
    {
        $date = date('Y-m-d H:i:s');
        $message = $date . PHP_EOL .
            $this->getEmoji($record['level']) . $record['level_name'] . ': ' . $record['formatted'];

        $this->send($message);
    }

    /**
     *    send log to telegram channel
     * @param string $message Text Message
     * @return void
     *
     */
    public function send($message)
    {
        $message = substr($message, 0, 4096);

        if (!empty($this->token) and !empty($this->channel)) {

            try {
                $ch = curl_init();
                $url = self::host . $this->token . "/SendMessage";
                $timeOut = $this->timeOut;
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeOut);
                curl_setopt($ch, CURLOPT_TIMEOUT, $timeOut);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
                    'text'       => $message,
                    'chat_id'    => $this->channel,
                    'parse_mode' => 'html'
                )));
                $result = curl_exec($ch);
                $result = json_decode($result, 1);
                
                if ($result['ok'] !== false) {
                    return [
                        'ok'      => true,
                        'message' => 'Sended'
                    ];
                } else {
                    return [
                        'ok'      => false,
                        'message' => $result['description']
                    ];
                }

            } catch (Exception $error) {
                return [
                    'ok'      => false,
                    'message' => $error
                ];
            }
        } else {
            return [
                'ok'      => false,
                'message' => 'Not config chat or token'
            ];
        }

    }

    /**
     * make emoji for log events
     * @return array
     *
     */
    protected function emojiMap()
    {
        return [
            Logger::DEBUG     => '🚧',
            Logger::INFO      => '‍🗨',
            Logger::NOTICE    => '🕵',
            Logger::WARNING   => '⚡️',
            Logger::ERROR     => '🚨',
            Logger::CRITICAL  => '🤒',
            Logger::ALERT     => '👀',
            Logger::EMERGENCY => '🤕',
        ];


    }

    /**
     * return emoji for given level
     *
     * @param $level
     * @return string
     */
    protected function getEmoji($level)
    {
        $levelEmojiMap = $this->emojiMap();
        return $levelEmojiMap[$level];
    }
}
