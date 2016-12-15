<?php


namespace Dilab\Queueable;


use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use SebastianBergmann\CodeCoverage\RuntimeException;

class Worker implements LoggerAwareInterface
{
    /**
     * @var Queue
     */
    private $queue;

    /**
     * @var LoggerInterface
     */
    private $logger;

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

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    protected function workOnce($maxTries, $sleepSecs)
    {
        $job = $this->queue->pop();

        if (null == $job) {

            $this->log(sprintf('No job is received, sleep %s seconds ...', $sleepSecs));

            sleep($sleepSecs);

            return 0;
        }

        if ($job->attempts() > $maxTries) {

            $this->log(sprintf('Maximum tries of %s times have been reached, delete the job with id %s.', $maxTries, $job->id()));

            $job->acknowledge();

            return 1;
        }

        try {

            $job->fire();

            $this->log(sprintf('Complete job with id %s!', $job->id()));

            $job->acknowledge();

            return 2;

        } catch (\Exception $e) {

            $this->log(sprintf('Error while processing job with id %s!', $job->id()));

            $job->release();

            return 3;
        }

    }

    private function log($message)
    {
        if ($this->logger) {
            $this->logger->info($message);
        }
    }

}