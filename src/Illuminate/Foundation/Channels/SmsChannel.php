<?php

namespace Larawos\Illuminate\Foundation\Channels;

use Illuminate\Notifications\Notification;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;
use Larawos\Illuminate\Foundation\Exceptions\GeneralException;
use Log;

class SmsChannel
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
        $message = collect($notification->toSms($notifiable));
        $sms = new EasySms(config('easysms'));
        if (! ($message->has('content') && $message->has('template') && $message->has('data') && $message->has('phone'))) {
            throw new GeneralException('短信消息实体必须带有 `content`, `template`, `data`, `phone` 字段。');
        }

        try {
            $response = $sms->send($message['phone'], $message->except('phone')->all());
        } catch (NoGatewayAvailableException $e) {
            Log::error('短信发送失败');
        }


    }
}
