<?php

namespace EnjoyJob\Support\Queue;

class Message
{
    private $rawJob;
    private $rawReservedJob;

    public function __construct($playload)
    {
        list($this->rawJob, $this->rawReservedJob) = $playload;
    }

    public function getJob()
    {
        return json_decode($this->rawJob, true);
    }

    public function getAttempts()
    {
        $job = $this->getJob();

        return isset($job['attempts']) ? $job['attempts'] : 0;
    }

    public function isEmpty()
    {
        return !$this->rawJob;
    }

    public function getRawJob()
    {
        return $this->rawJob;
    }

    public function getRawReservedJob()
    {
        return $this->rawReservedJob;
    }
}