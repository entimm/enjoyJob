<?php

namespace EnjoyJob\Contracts;

interface Job
{
    public function handle($attempts);
}