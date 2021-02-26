<?php

namespace EnjoyJob\Contracts;

abstract class Listener
{
    protected $event;
    protected $attempts;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    abstract public function handle($attempts);
}