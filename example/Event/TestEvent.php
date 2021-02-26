<?php

namespace EnjoyJobExample\Event;

use EnjoyJob\Contracts\Event;
use EnjoyJob\Contracts\Job;
use EnjoyJobExample\Listener\ExceptionTest;
use EnjoyJobExample\Listener\NormalTest;

class TestEvent extends Event implements Job
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function handle($attempts = 0)
    {
        echo 'DO MY SELF - '.$attempts.PHP_EOL;
    }

    public function listeners()
    {
        return [
            NormalTest::class,
            ExceptionTest::class,
        ];
    }
}
