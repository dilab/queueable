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
        return $this->userJobInstance->handle($this->payload);
    }

    public abstract function acknowledge();

    public abstract function release();

    public abstract function attempts();

    public abstract function id();
}