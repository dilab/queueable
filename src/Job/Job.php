<?php


namespace Dilab\Queueable\Job;

abstract class Job
{
    /**
     * @var string
     */
    private $queueName;

    /**
     * @var Payload
     */
    private $payload;

    /**
     * @var string
     */
    private $type;

    public function fire()
    {

    }

    public abstract function handle(Payload $payload);

    public abstract function acknowledge();

    public abstract function release();

    public abstract function attempts();

    public abstract function id();
}