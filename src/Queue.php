<?php

namespace Dilab\Queueable;


use Dilab\Queueable\Contract\JobContract;
use Dilab\Queueable\Driver\Driver;
use Dilab\Queueable\Job\Job;
use Dilab\Queueable\Job\Payload;

class Queue
{
    /**
     * @var Driver
     */
    protected $driver;

    /**
     * @var string
     */
    protected $queueName;

    /**
     * Queue constructor.
     * @param Driver $driver
     */
    public function __construct($queueName, Driver $driver)
    {
        $this->queueName = $queueName;
        $this->driver = $driver;
    }

    /**
     * @return boolean
     */
    public function connect()
    {
        return $this->driver->connect($this->queueName);
    }

    /**
     * @param JobContract $job
     * @param Payload $payload
     * @param array $options
     *
     * Push a job to queue
     */
    public function push(JobContract $job, Payload $payload, $options = [])
    {
        $body = [
            'id' => md5(uniqid('', true)),
            'userJobInstance' => $job,
            'payload' => $payload
        ];

        $this->driver->push($this->queueName, $body);
    }

    /**
     * @return null|Job
     */
    public function pop()
    {
        $message = $this->driver->pop($this->queueName);

        if (empty($message)) {
            return null;
        }

        $jobClassNamespace = (new \ReflectionClass(Job::class))->getNamespaceName();

        $jobClassName = $jobClassNamespace . '\\' . $this->driver->name() . 'Job';

        return new $jobClassName($this, $message);
    }

    /**
     * @return Driver
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @return string
     */
    public function getQueueName()
    {
        return $this->queueName;
    }


}