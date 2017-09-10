<?php

namespace Larawos\Illuminate\Foundation\Channels;

use JPush\Exceptions\APIRequestException;
use Illuminate\Notifications\Notification;
use JPush\Exceptions\APIConnectionException;
use JPush\Client;
use Log;

class PushChannel
{
    /**
     * 发送给定通知.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toPush($notifiable);

        $push = (new Client(env('JPUSH_KEY', '极光推送key'), env('JPUSH_SECRETKEY', '极光推送secret')))->push();
        $push = isset($message['alias']) ?
            $push->setPlatform($message['platform'])->addAlias($data['alias']) :
            $push->setPlatform($message['platform'])->addAllAudience();

        $push_payload = $push->setNotificationAlert($message['data']['content']);

        try {
            $response = $push_payload->send();
            'local' != config('app.env') || Log::info($response);
        } catch (APIConnectionException $e) {
            'local' != config('app.env') || Log::info($e->getMessage());
        } catch (APIRequestException $e) {
            'local' != config('app.env') || Log::info($e->getMessage());
        }

    }
}
