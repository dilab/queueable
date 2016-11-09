<?php


namespace Dilab\Queueable;


class Worker
{
    /**
     * @var Queue
     */
    private $queue;

    /**
     * Worker constructor.
     *
     */
    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    public function work()
    {
        while (true) {

            $job = $this->queue->pop();

            if ($job->attempts() > $this->maxTries()) {

                $job->acknowledge();

            }

            try {

                $job->fire();

            } catch (\Exception $e) {

                $job->release();

            }

        }
    }

}