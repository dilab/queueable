<?php


namespace Dilab\Queueable;


use SebastianBergmann\CodeCoverage\RuntimeException;

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

    public function work($maxTries = 5, $sleepSecs = 5)
    {
        if (!$this->canConnect()) {
            throw new RuntimeException('Can not connect to queue ' . $this->queue->getQueueName());
        }

        while (true) {
            $this->workOnce($maxTries, $sleepSecs);
        }
    }

    public function canConnect()
    {
        return $this->queue->connect();
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

            $job->acknowledge();

            return 2;

        } catch (\Exception $e) {

            $job->release();

            return 3;
        }

    }

}