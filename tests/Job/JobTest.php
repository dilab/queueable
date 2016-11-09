<?php


namespace Dilab\Queueable\Job;


use Dilab\Queueable\Contract\JobContract;
use Dilab\Queueable\Driver\InMemoryDriver;
use Dilab\Queueable\Queue;
use PHPUnit\Framework\TestCase;

class JobTest extends TestCase
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

    /**
     * @var InMemoryJob
     */
    public $inMemoryJob;

    /**
     * @var Payload
     */
    public $payload;

    /**
     * @var JobContract
     */
    public $userJobInstance;

    public function setUp()
    {
        parent::setUp();

        $this->inMemoryDriver = new InMemoryDriver();

        $this->userJobInstance = $this->getMockBuilder(JobTestJob::class)->getMock();

        $this->queueName = 'test';

        $this->queue = new Queue($this->queueName, $this->inMemoryDriver);

        $this->payload = new Payload(['name' => 'Xu']);

        $this->queue->push($this->userJobInstance, $this->payload);

        $this->inMemoryJob = $this->queue->pop();
    }

    public function testFire()
    {
        $this->userJobInstance->expects($this->once())->method('handle');

        $this->inMemoryJob->fire();
    }

    public function testAttempts()
    {
        $this->assertSame(0, $this->inMemoryJob->attempts());
    }

    public function testId()
    {
        $this->assertNotEmpty($this->inMemoryJob->id());
    }
}

class JobTestJob implements JobContract
{
    public function handle(Payload $payload)
    {
        return;
    }
}
