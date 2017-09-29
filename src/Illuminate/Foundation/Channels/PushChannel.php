<?php

namespace Larawos\Illuminate\Foundation\Channels;

use JPush\Exceptions\APIRequestException;
use Illuminate\Notifications\Notification;
use Larawos\Illuminate\Foundation\Exceptions\GeneralException;
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

        if (! ($message->has('platform') && $message->has('data'))) {
            throw new GeneralException('短信消息实体必须带有 `platform`, `data`字段。');
        }

        $push = (new Client(env('JPUSH_KEY', '极光推送key'), env('JPUSH_SECRETKEY', '极光推送secret')))->push();
        $push = isset($message['alias']) ?
            $push->setPlatform($message['platform'])->addAlias($data['alias']) :
            $push->setPlatform($message['platform'])->addAllAudience();
        $content = isset($message['data']['content']) ? $message['data']['content'] : $message['data'];

        $push_payload = $push->setNotificationAlert($content);

        try {
            $response = $push_payload->send();
            'local' != config('app.env') || Log::error($response);
        } catch (APIConnectionException $e) {
            'local' != config('app.env') || Log::error($e->getMessage());
        } catch (APIRequestException $e) {
            'local' != config('app.env') || Log::error($e->getMessage());
        }

    }
}
