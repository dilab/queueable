<?php


namespace Dilab\Queueable;


use Dilab\Queueable\Contract\JobContract;
use Dilab\Queueable\Driver\InMemoryDriver;
use Dilab\Queueable\Job\Job;
use Dilab\Queueable\Job\Payload;
use PHPUnit\Framework\TestCase;

class QueueTest extends TestCase
{
    /**
     * @var Queue
     */
    public $queue;

    public $queueName;

    /**
     * @var InMemoryDriver
     */
    public $inMemoryDriver;

    public function setUp()
    {
        parent::setUp();
        $this->queueName = 'test';
        $this->inMemoryDriver = new InMemoryDriver();
        $this->queue = new Queue($this->queueName, $this->inMemoryDriver);
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->queueName);
        unset($this->queue);
    }

    public function testPush()
    {
        $jobHandler = new QueueTestJob();
        $data = ['name' => 'Xu'];
        $payload = (new Payload($data));
        $this->queue->push($jobHandler, $payload);

        $result = $this->inMemoryDriver->pop($this->queueName);
        $handlerInDriver = $result['userJobInstance'];
        $payloadInDriver = $result['payload'];
        $this->assertInstanceOf(JobContract::class, $handlerInDriver);
        $this->assertInstanceOf(Payload::class, $payloadInDriver);
    }

    public function testPop()
    {
        $job = $this->queue->pop();
        $this->assertNull($job);

        $jobHandler = new QueueTestJob();
        $data = ['name' => 'Xu'];
        $payload = (new Payload($data));
        $this->queue->push($jobHandler, $payload);
        $job = $this->queue->pop();
        $this->assertInstanceOf(Job::class, $job);
    }

}

class QueueTestJob implements JobContract
{
    public function handle(Payload $payload)
    {
        return;
    }
}
