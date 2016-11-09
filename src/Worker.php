<?php


namespace Dilab\Queueable;


class Worker
{
    /**
     * @var Queue
     */
    private $queue;

    private $maxTries = 5;

    private $sleepSecs = 5;

    /**
     * Worker constructor.
     *
     */
    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    public function work($maxTries = 5, $sleepSecs = 5)
    {
        while (true) {

            $this->workOnce($maxTries, $sleepSecs);

        }
    }

    protected function workOnce($maxTries, $sleepSecs)
    {
        $job = $this->queue->pop();

        if (null == $job) {

            sleep($sleepSecs);

            return 0;
        }

        if ($job->attempts() > $maxTries) {

            $job->acknowledge();

            return 1;
        }

        try {

            $job->fire();

            return 2;

        } catch (\Exception $e) {

            $job->release();

            return 3;
        }

    }

}