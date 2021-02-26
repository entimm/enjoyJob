<?php

namespace EnjoyJob\Support;

use EnjoyJob\Exception\AbandonException;
use Exception;
use EnjoyJob\Job;
use EnjoyJob\Support\Queue\JobsQueue;
use EnjoyJob\Support\Queue\Message;

class Execute
{
    const RETRY_FREQ = [
        60,
        60,
        60,
        300,
        300,
        3600,
        3600,
        7200,
        18000,
    ];

    /**
     * @var JobsQueue
     */
    private $queue;

    public function __construct()
    {
        $this->init();
    }

    protected function init()
    {
        $this->queue = new JobsQueue(Job::$name);

        function unserialize_mycallback($classname)
        {
            throw new Exception('类无法实例化: '.$classname);
        }

        ini_set('unserialize_callback_func', '\EnjoyJob\Support\Execute\unserialize_mycallback');
    }

    public function handle()
    {
        do {
            do {
                // 出列，并备份到"保留队列"中
                $message = $this->queue->pop();
                if ($message->isEmpty()) break;
                $this->process($message);
            } while (true);

            $this->queue->migrate();
            if (PHP_SAPI !== 'cli') {
                break;
            }
            sleep(3);
        } while (true);
    }

    protected function process(Message $message)
    {
        // 任务开始处理

        try {
            $job = $message->getJob();
            // 可能引发Error异常
            $event = unserialize($job['event']);
            $handler = $job['handler'];

            $execute = !$handler ? $event : new $handler($event);
            $execute->handle($message->getAttempts());

            $this->queue->resolved($message);
        } catch (AbandonException $e) {
            $this->queue->resolved($message);
        } catch (Exception $e) {
            if ($message->getAttempts() >= count(self::RETRY_FREQ)) {
                // 不再重试了，从"保留队列"中移除
                $this->queue->resolved($message);
                return;
            }
            $delay = self::RETRY_FREQ[$message->getAttempts()];
            $score = time() + 1;
            // 从"保留队列"中转入延迟队列
            $this->queue->release($message, $score);
        }
    }
}
