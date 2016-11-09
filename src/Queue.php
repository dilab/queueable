<?php


namespace Dilab\Queueable;


use Dilab\Queueable\Contract\JobContract;
use Dilab\Queueable\Driver\Driver;
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
    public function __construct(Driver $driver, $queueName)
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

    }

    public function pop()
    {

    }

}