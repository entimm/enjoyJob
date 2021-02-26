<?php

namespace EnjoyJob;

use EnjoyJob\Contracts\Event;
use EnjoyJob\Contracts\Job as JobContract;
use EnjoyJob\Support\Execute;
use EnjoyJob\Support\Queue\JobsQueue;

class Job
{
    public static $name = 'job';

    /**
     * @param  Event  $event
     * @param  int|string  $delay
     */
    public static function dispatch(Event $event, $delay = 0)
    {
        $delayAt = is_numeric($delay) ? time() + $delay : strtotime($delay);

        if ($event instanceof JobContract) {
            $data = [
                'handler' => null,
                'event' => serialize($event),
                'attempts' => 0,
                'pushedAt' => microtime(true),
            ];

            (new JobsQueue(self::$name))->delayAt(json_encode($data), $delayAt);
        } elseif (method_exists($event, 'handle')) {
            $event->handle();
        }

        foreach ($event->listeners() as $listener) {
            if (is_a($listener, JobContract::class, true)) {
                $data = [
                    'handler' => $listener,
                    'event' => serialize($event),
                    'attempts' => 0,
                    'pushedAt' => microtime(true),
                ];

                (new JobsQueue(self::$name))->delayAt(json_encode($data), $delayAt);
            } else {
                (new $listener($event))->handle();
            }
        }
    }

    public static function execute()
    {
        $execute = new Execute;
        $execute->handle();
    }
}
