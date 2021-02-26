<?php

namespace EnjoyJob\Contracts;

abstract class Event
{
    protected $data = [];

    /**
     * @return string[]
     */
    public function listeners()
    {
        return [];
    }

    public function data()
    {
        return $this->data;
    }
}