<?php

namespace EnjoyJobExample\Listener;

use EnjoyJob\Contracts\Job;
use EnjoyJob\Contracts\Listener;
use EnjoyJob\Exception\Exception;
use EnjoyJobExample\Event\TestEvent;

class ExceptionTest extends Listener implements Job
{
    public function __construct(TestEvent $event)
    {
        parent::__construct($event);
    }

    public function handle($attempts)
    {
        echo '出问题啦, 当前重试次数:'.$attempts.PHP_EOL;
        throw new Exception('出问题啦');
    }
}
