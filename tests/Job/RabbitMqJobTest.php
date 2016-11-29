<?php


namespace Dilab\Queueable\Job;


use Dilab\Queueable\Contract\JobContract;
use Dilab\Queueable\Driver\RabbitMqDriver;
use Dilab\Queueable\Queue;
use PHPUnit\Framework\TestCase;

class RabbitMqJobTest extends TestCase
{
    /**
     * @var RabbitMqDriver
     */
    public $rabbitMqDriver;

    /**
     * @var Queue
     */
    public $queue;

    /**
     * @var RabbitMqJob
     */
    public $rabbitMqJob;

    public function setUp()
    {
        parent::setUp();

        $this->rabbitMqDriver = $this->getMockBuilder(RabbitMqDriver::class)->disableOriginalConstructor()->getMock();

        $this->queue = new Queue('email', $this->rabbitMqDriver);
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testAcknowledge()
    {
        $this->rabbitMqJob = new RabbitMqJob($this->queue, $this->message());

        $this->rabbitMqDriver->expects($this->exactly(1))->method('delete');

        $this->rabbitMqJob->acknowledge();
    }

    public function testRelease()
    {
        $this->rabbitMqJob = new RabbitMqJob($this->queue, $this->message());

        $this->rabbitMqDriver->expects($this->exactly(1))->method('release');

        $this->rabbitMqJob->release();
    }

    public function testUserJobInstance()
    {
        $this->rabbitMqJob = new RabbitMqJob($this->queue, $this->message());

        $this->assertInstanceOf(JobContract::class, $this->rabbitMqJob->userJobInstance());
    }

    public function testPayload()
    {
        $this->rabbitMqJob = new RabbitMqJob($this->queue, $this->message());

        $this->assertInstanceOf(Payload::class, $this->rabbitMqJob->payload());
    }

    private function message($body = array())
    {
        $message = [
            'body' => [
                'id' => 1,
                'userJobInstance' => serialize(new SqsJobTestJob()),
                'payload' => serialize(new Payload(['name' => 'Xu']))
            ],
            'deliveryTag' => 'tag',
        ];

        $message['body'] = array_merge($message['body'], $body);

        return $message;
    }
}


class RabbitMqJobTestJob implements JobContract
{
    public function handle(Payload $payload)
    {
        return;
    }

}
