<?php

namespace EnjoyJobExample\Listener;

use EnjoyJob\Contracts\Job;
use EnjoyJob\Contracts\Listener;
use EnjoyJobExample\Event\TestEvent;

class NormalTest extends Listener implements Job
{
    public function __construct(TestEvent $event)
    {
        parent::__construct($event);
    }

    public function handle($attempts)
    {
        echo "data {$attempts} = ".json_encode($this->event->data()).PHP_EOL;
    }
}
