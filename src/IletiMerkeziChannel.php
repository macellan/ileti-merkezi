<?php

namespace Macellan\IletiMerkezi;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Macellan\IletiMerkezi\Exceptions\CouldNotSendNotification;

class IletiMerkeziChannel
{
    /**
     * Login to API endpoint.
     *
     * @var string
     */
    protected $key;

    /**
     * Password to API endpoint.
     *
     * @var string
     */
    protected $hash;

    /**
     * API endpoint wsdl url.
     *
     * @var string
     */
    protected $endPoint;

    /**
     * Registered sender. Should be requested in Ileti Merkezi user's page.
     *
     * @var string
     */
    protected $origin;

    /**
     * Debug flag. If true, messages send/result wil be stored in Laravel log.
     *
     * @var bool
     */
    protected $debug;

    /**
     * If true, will run.
     *
     * @var bool
     */
    protected $enable;

    /**
     * Sandbox mode flag. If true, endpoint API will not be invoked, useful for dev purposes.
     *
     * @var bool
     */
    protected $sandboxMode;

    public function __construct(array $config = [])
    {
        $this->key = Arr::get($config, 'key');
        $secret = Arr::get($config, 'secret');
        $this->hash = Arr::get($config, 'hash', hash_hmac('sha256', $this->key, $secret));
        $this->endPoint = 'https://api.iletimerkezi.com/v1/send-sms/json';
        $this->origin = Arr::get($config, 'origin');
        $this->debug = Arr::get($config, 'debug', false);
        $this->enable = Arr::get($config, 'enable', false);
        $this->sandboxMode = Arr::get($config, 'sandboxMode', false);
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param Notification $notification
     *
     * @return object|void
     * @throws CouldNotSendNotification
     * @noinspection PhpUndefinedMethodInspection
     */
    public function send($notifiable, Notification $notification)
    {
        if (! $this->enable) {
            if ($this->debug) {
                Log::info('Ileti Merkezi is disabled');
            }

            return;
        }

        /** @var IletiMerkeziMessage $message */
        $message = $notification->toIletiMerkezi($notifiable);
        if (is_string($message)) {
            $message = new IletiMerkeziMessage($message);
        }

        $message->numbers[] = $notifiable->routeNotificationFor('sms');

        $result = $this->postData((object) [
            'numbers' => $message->numbers,
            'message' => $message->body,
            'sendDateTime' => $message->sendTime,
        ]);

        if ($this->debug && $result) {
            Log::info('Ileti Merkezi send result - '.print_r($result, true));
        }

        return $result;
    }

    /**
     * @param $sms
     * @return object|void
     * @throws CouldNotSendNotification
     * @noinspection PhpUndefinedMethodInspection
     */
    protected function postData($sms)
    {
        $data = [
            'request' => [
                'authentication' => [
                    'key' => $this->key,
                    'hash' => $this->hash,
                ],
                'order' => [
                    'sender' => $this->origin,
                    'sendDateTime' => $sms->sendDateTime,
                    'iys' => 0,
                    'message' => [
                        'text' => $sms->message,
                        'receipents' => [
                            'number' => $sms->numbers,
                        ]
                    ]
                ],
            ]
        ];

        if ($this->debug) {
            Log::info('Ileti Merkezi sending sms - '.print_r($sms, true));
        }

        if ($this->sandboxMode) {
            return;
        }

        try {
            return Http::post($this->endPoint, $data)->throw()->json();
        } catch (\Exception $exception) {
            $message = $exception->getMessage();

            if ($exception instanceof \Illuminate\Http\Client\RequestException) {
                $result = json_decode($exception->response->toPsrResponse()->getBody()->getContents());
                if (is_object($result) && isset($result->response)) {
                    $message = $result->response->status->message;
                }
            }

            if ($this->debug) {
                Log::info('Ileti Merkezi communication with endpoint failed. Reason => '.$message);
            }

            throw CouldNotSendNotification::couldNotCommunicateWithEndPoint($exception, $message);
        }
    }
}
