<?php

use EnjoyJob\Job;
use EnjoyJobExample\Event\TestEvent;

require dirname(__DIR__).'/vendor/autoload.php';

Job::dispatch(new TestEvent([
    'a' => 'aa',
    'b' => 'bb',
    'c' => 'cc',
]));

Job::execute();