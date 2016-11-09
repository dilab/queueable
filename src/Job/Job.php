<?php


namespace Dilab\Queueable\Job;

use Dilab\Queueable\Contract\JobContract;
use Dilab\Queueable\Queue;

abstract class Job
{
    /**
     * @var Queue
     */
    private $queue;

    /**
     * @var Payload
     */
    private $payload;

    /**
     * @var JobContract
     */
    private $userJob;

    /**
     * Job constructor.
     * @param Queue $queue
     * @param Payload $payload
     * @param JobContract $userJob
     */
    public function __construct(Queue $queue, Payload $payload, JobContract $userJob)
    {
        $this->queue = $queue;
        $this->payload = $payload;
        $this->userJob = $userJob;
    }

    public function fire()
    {

    }

    public abstract function acknowledge();

    public abstract function release();

    public abstract function attempts();

    public abstract function id();
}