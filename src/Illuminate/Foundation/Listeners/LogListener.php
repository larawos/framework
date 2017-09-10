<?php

namespace Larawos\Illuminate\Foundation\Listeners;

use Larawos\Illuminate\Foundation\Models\Log;
use Larawos\Illuminate\Foundation\Events\LogEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogListener implements ShouldQueue
{
    public $queue = 'logs';

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param LogEvent  $event
     * @return void
     */
    public function handle(LogEvent $event)
    {
        $user = auth()->check() ?
            auth()->user()->username.' ['.request()->ip().']' :
            '非注册用户 ['.request()->ip().']';

        Log::create([
                'content' => "{$user} 执行操作：{$event->description}"
            ]);
    }
}
