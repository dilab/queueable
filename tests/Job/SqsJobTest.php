<?php


namespace Dilab\Queueable\Job;


use Dilab\Queueable\Contract\JobContract;
use Dilab\Queueable\Driver\SqsDriver;
use Dilab\Queueable\Queue;
use PHPUnit\Framework\TestCase;

class SqsJobTest extends TestCase
{
    /**
     * @var SqsDriver
     */
    public $sqsDriver;

    /**
     * @var Queue
     */
    public $queue;

    /**
     * @var SqsJob
     */
    public $sqsJob;

    public function setUp()
    {
        parent::setUp();

        $this->sqsDriver = $this->getMockBuilder(SqsDriver::class)->disableOriginalConstructor()->getMock();

        $this->queue = new Queue('email', $this->sqsDriver);
    }

    public function testAcknowledge()
    {
        $this->sqsJob = new SqsJob($this->queue, $this->message());

        $this->sqsDriver->expects($this->exactly(1))->method('delete');

        $this->sqsJob->acknowledge();
    }

    public function testRelease()
    {
        $this->sqsJob = new SqsJob($this->queue, $this->message());

        $this->sqsDriver->expects($this->exactly(1))->method('release');

        $this->sqsJob->release();
    }

    public function testAttempts()
    {
        $this->sqsJob = new SqsJob($this->queue, $this->message());

        $this->assertSame(5, $this->sqsJob->attempts());
    }

    public function testUserJobInstance()
    {
        $this->sqsJob = new SqsJob($this->queue, $this->message());

        $this->assertInstanceOf(JobContract::class, $this->sqsJob->userJobInstance());
    }

    public function testPayload()
    {
        $this->sqsJob = new SqsJob(
            $this->queue, $this->message()
        );

        $this->assertInstanceOf(Payload::class, $this->sqsJob->payload());
    }

    private function message($body = array())
    {
        $message = [
            'Attributes' => [
                'ApproximateReceiveCount' => '5'
            ],
            'Body' => [
                'id' => 1,
                'userJobInstance' => new SqsJobTestJob(),
                'payload' => new Payload(['name' => 'Xu'])
            ],
            'MD5OfBody' => '0c9ec408cb84147c5301921b42429ad7',
            'MD5OfMessageAttributes' => '9424c49126bc3ae7',
            'MessageAttributes' => [
                'City' => [
                    'DataType' => 'String',
                    'StringValue' => 'Any City',
                ],
                'PostalCode' => [
                    'DataType' => 'String',
                    'StringValue' => 'ABC123',
                ],
            ],
            'MessageId' => 'd6790f8d-d575-4f01-bc51-40122EXAMPLE',
            'ReceiptHandle' => 'AQEBzbVvfqNzFw==',
        ];

        $message['Body'] = array_merge($message['Body'], $body);

        return $message;
    }
}

class SqsJobTestJob implements JobContract
{
    public function handle(Payload $payload)
    {
        return;
    }

}