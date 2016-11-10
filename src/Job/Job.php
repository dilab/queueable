<?php


namespace Dilab\Queueable\Job;

use Dilab\Queueable\Contract\JobContract;
use Dilab\Queueable\Queue;

abstract class Job
{
    /**
     * @var Queue
     */
    protected $queue;

    /**
     * @var array
     */
    protected $message;

    /**
     * Job constructor.
     * @param Queue $queue
     * @param array $message
     */
    public function __construct(Queue $queue, array $message)
    {
        $this->queue = $queue;
        $this->message = $message;
    }

    /**
     * @return void
     *
     * Fire user job
     */
    public function fire()
    {
        $userJobInstance = $this->userJobInstance();
        $payload = $this->payload();
        return $userJobInstance->handle($payload);
    }

    public function id()
    {
        return $this->message['id'];
    }

    public function attempts()
    {
        return intval(0);
    }

    public abstract function acknowledge();

    public abstract function release();

    /**
     * @return JobContract
     */
    public abstract function userJobInstance();

    /**
     * @return Payload
     */
    public abstract function payload();

}