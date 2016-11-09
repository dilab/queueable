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
    private $driver;

    /**
     * @var string
     */
    private $queueName;

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
     * @param JobContract $job
     * @param Payload $payload
     * @param array $options
     *
     * Push a job to queue
     */
    public function push(JobContract $job, Payload $payload, $options = [])
    {
        $message = [
            'id' => md5(uniqid('', true)),
            'userJobInstance' => serialize(clone $job),
            'payloadData' => $payload->data()
        ];

        $this->driver->push($this->queueName, $message);
    }

    public function pop()
    {
        $message = $this->driver->pop($this->queueName);

        if (empty($message)) {
            return null;
        }

        $jobClassNamespace = (new \ReflectionClass(Job::class))->getNamespaceName();

        $jobClassName = $jobClassNamespace . '\\' . $this->driver->name() . 'Job';

        return new $jobClassName(
            $this,
            new Payload($message['payloadData']),
            unserialize($message['userJobInstance'])
        );
    }
}