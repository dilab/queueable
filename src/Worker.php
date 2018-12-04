<?php


namespace Dilab\Queueable;


use Dilab\Queueable\Exception\OutOfOrder;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use SebastianBergmann\CodeCoverage\RuntimeException;

class Worker implements LoggerAwareInterface
{
    const STATUS_CODE_IDLE = 0;
    const STATUS_CODE_MAX_TRY_REACH = 1;
    const STATUS_CODE_OK = 2;
    const STATUS_CODE_ERROR = 3;
    const STATUS_CODE_OUT_OF_ORDER = 4;

    /**
     * @var array
     */
    private $callBacks = [
        'beforeFetchJob' => null,
        'beforeCompleteJob' => null,
        'afterCompleteJob' => null,
        'heartbeat' => null, // depreciated
    ];

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
            $this->heartbeat($maxTries, $sleepSecs);
        }
    }

    public function attach($name, callable $func)
    {
        if (!in_array($name, array_keys($this->callBacks))) {
            throw new \RuntimeException(__('Invalid callback name %s', $name));
        }

        $this->callBacks[$name] = $func;
    }

    public function canConnect()
    {
        return $this->queue->connect();
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    protected function heartbeat($maxTries, $sleepSecs)
    {
        $this->triggerCallback('heartbeat');
        $this->triggerCallback('beforeFetchJob');
        return $this->workOnce($maxTries, $sleepSecs);
    }

    protected function workOnce($maxTries, $sleepSecs)
    {
        $job = $this->queue->pop();

        if (null == $job) {

            $this->log(sprintf('No job is received, sleep %s seconds ...', $sleepSecs));

            sleep($sleepSecs);

            return self::STATUS_CODE_IDLE;
        }

        if ($job->attempts() > $maxTries) {

            $this->log(sprintf('Maximum tries of %s times have been reached, ignore job by releasing it back to queue - %s.', $maxTries, $job->name()));

            $job->release();

            return self::STATUS_CODE_MAX_TRY_REACH;
        }

        try {

            $this->triggerCallback('beforeCompleteJob');

            $job->fire();

            $this->log(sprintf('Complete job - %s!', $job->name()));

            $job->acknowledge();

            $this->triggerCallback('afterCompleteJob');

            return self::STATUS_CODE_OK;

        } catch (\Exception $e) {

            $this->log(sprintf('Error while processing job - %s!', $job->name()));

            $job->release();

            return self::STATUS_CODE_ERROR;
        }

    }

    private function log($message)
    {
        if ($this->logger) {
            $this->logger->info($message);
        }
    }

    private function triggerCallback($name)
    {
        if (is_callable($this->callBacks[$name])) {
            call_user_func($this->callBacks[$name]);
        }
    }


}