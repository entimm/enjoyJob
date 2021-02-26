<?php

namespace EnjoyJob\Support\Queue;

use Redis;

class JobsQueue
{
    protected $name;

    protected $retryAfter = 600;

    private $queue;
    private $reservedSet;
    private $delayedSet;

    /**
     * @var Redis
     */
    private static $redis;

    public function __construct($name = 'job')
    {
        $this->name = $name;

        $this->queue = $this->name.':queue';
        $this->reservedSet = $this->name.':reserved';
        $this->delayedSet = $this->name.':delayed';
    }

    public function size()
    {
        return $this->getRedis()->eval(LuaScripts::size(), [$this->queue, $this->reservedSet, $this->delayedSet], 3);
    }

    public function push($rawJob)
    {
        return $this->getRedis()->lPush($this->queue, $rawJob);
    }

    public function delayAt($rawJob, $delayTime)
    {
        return $this->getRedis()->zAdd($this->delayedSet, $delayTime, $rawJob);
    }

    /*
     * 任务获取出列
     */
    public function pop()
    {
        $expireAt = time() + $this->retryAfter;

        $playload = $this->getRedis()->eval(LuaScripts::pop(), [$this->queue, $this->reservedSet, $expireAt], 2);

        return new Message($playload);
    }

    /*
     * 任务备份移除
     */
    public function resolved(Message $message)
    {
        return $this->getRedis()->zRem($this->reservedSet, $message->getRawReservedJob());
    }

    /*
     * 任务释放
     */
    public function release(Message $message, $delayTime)
    {
        return $this->getRedis()->eval(LuaScripts::release(), [$this->reservedSet, $this->delayedSet, $message->getRawReservedJob(), $delayTime], 2);
    }

    /*
     * 任务迁移
     */
    public function migrate()
    {
        $this->migrateExpiredDelayed();

        $this->migrateExpiredReserved();
    }

    /*
     * 过期的job迁移到备份队列
     */
    public function migrateExpiredReserved()
    {
        $rawJobs = $this->getRedis()->eval(LuaScripts::migrateExpiredJobs(), [$this->reservedSet, $this->queue, time()], 2);

        return $rawJobs;
    }

    /*
     * 过期的job迁移到延迟队列
     */
    public function migrateExpiredDelayed()
    {
        $rawJobs =  $this->getRedis()->eval(LuaScripts::migrateExpiredJobs(), [$this->delayedSet, $this->queue, time()], 2);

        return $rawJobs;
    }

    protected function getRedis()
    {
        if (!self::$redis) {
            self::$redis = new Redis();
            self::$redis->connect('127.0.0.1');
        }

        return self::$redis;
    }

    /**
     * @return Redis
     */
    public static function setRedis(Redis $redis)
    {
        self::$redis = $redis;
    }
}
