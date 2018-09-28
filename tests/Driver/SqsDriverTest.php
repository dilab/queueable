<?php


namespace Dilab\Queueable\Driver;

use Aws\MockHandler;
use Aws\Result;
use Aws\Sqs\SqsClient;
use Dilab\Queueable\Contract\JobContract;
use Dilab\Queueable\Driver\SqsDriver;
use Dilab\Queueable\Job\Payload;
use PHPUnit\Framework\TestCase;

class SqsDriverTest extends TestCase
{
    /**
     * @var SqsDriver
     */
    public $sqsDriver;

    /**
     * @var SqsClient
     */
    public $sqsClient;

    /**
     * @var MockHandler
     */
    public $mock;

    public function setUp()
    {
        parent::setUp();

        $this->mock = new MockHandler();

        // Create a client with the mock handler.
        $this->sqsClient = new SqsClient([
            'region' => 'us-west-2',
            'version' => 'latest',
            'credentials' => [
                'key' => 'my-access-key-id',
                'secret' => 'my-secret-access-key',
            ]
        ]);

        $this->sqsDriver = new SqsDriver('test', $this->sqsClient);
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testConnect()
    {
        $expected = 'https://queue.amazonaws.com/123456789101/MyQueue';
        $this->setSqsClientResponse([
            ['QueueUrl' => $expected],
            []
        ]);
        $result = $this->sqsDriver->connect('email');
        $this->assertTrue($result);

        $result = $this->sqsDriver->connect('email');
        $this->assertFalse($result);
    }

    public function testName()
    {
        $this->assertEquals('Sqs', $this->sqsDriver->name());
    }

    public function testGetQueueUrlByName()
    {
        $expected = 'https://queue.amazonaws.com/123456789101/MyQueue';

        $this->setSqsClientResponse([
            ['QueueUrl' => $expected]
        ]);

        $result = $this->sqsDriver->getQueueUrlByName('email');

        $this->assertEquals($expected, $result);
    }

    public function testPop()
    {
        $expected = [
            'Attributes' => [
                'ApproximateReceiveCount' => '5',
            ],
            'Body' => 'My first message',
            'MD5OfBody' => '77a8f18b5388a3865556cc642424151c',
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
        $this->setSqsClientResponse([
            ['QueueUrl' => 'https://queue.amazonaws.com/123456789101/MyQueue'],
            ['Messages' => [$expected]]
        ]);
        $result = $this->sqsDriver->pop('email');
        $this->assertEquals($expected, $result);

        $expected = [];
        $this->setSqsClientResponse([
            ['QueueUrl' => 'https://queue.amazonaws.com/123456789101/MyQueue'],
            ['Messages' => [$expected]]
        ]);
        $result = $this->sqsDriver->pop('email');
        $this->assertEquals($expected, $result);

        $expected = [];
        $this->setSqsClientResponse([
            ['QueueUrl' => 'https://queue.amazonaws.com/123456789101/MyQueue'],
            ['Messages' => null]
        ]);
        $result = $this->sqsDriver->pop('email');
        $this->assertSame($expected, $result);
    }

    public function testPush()
    {
        $this->setSqsClientResponse([
            ['QueueUrl' => 'https://queue.amazonaws.com/123456789101/MyQueue'],
            [
                'MD5OfMessageAttributes' => '00484c6859e48f06',
                'MD5OfMessageBody' => '51b0a32539163aa0',
                'MessageId' => 'da68f62c-0c07-4bee-bf5f-7e856EXAMPLE',
            ]
        ]);

        $expected = 'da68f62c-0c07-4bee-bf5f-7e856EXAMPLE';

        $result = $this->sqsDriver->push('email', [
            'id' => md5(uniqid('', true)),
            'userJobInstance' => (new SqsDriverTestJob()),
            'payload' => (new Payload(['name' => 'Xu']))
        ]);

        $this->assertEquals($expected, $result);
    }

    public function testDelete()
    {
        $this->setSqsClientResponse([
            ['QueueUrl' => 'https://queue.amazonaws.com/123456789101/MyQueue'],
            []
        ]);

        $result = $this->sqsDriver->delete('email', [
            'Attributes' => [
                'ApproximateFirstReceiveTimestamp' => '1442428276921',
                'ApproximateReceiveCount' => '5',
                'SenderId' => 'AIDAIAZKMSNQ7TEXAMPLE',
                'SentTimestamp' => '1442428276921',
            ],
            'Body' => 'My first message',
            'MD5OfBody' => '77a8f18b5388a3865556cc642424151c',
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
        ]);

        $this->assertTrue($result);
    }

    public function testMessages()
    {
        $expected = [
            [
                'Attributes' => [
                    'ApproximateReceiveCount' => '5',
                ],
                'Body' => 'My first message',
                'MD5OfBody' => '77a8f18b5388a3865556cc642424151c',
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
            ],
            [
                'Attributes' => [
                    'ApproximateReceiveCount' => '5',
                ],
                'Body' => 'My second message',
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
            ]
        ];

        $this->setSqsClientResponse([
            ['QueueUrl' => 'https://queue.amazonaws.com/123456789101/MyQueue'],
            ['Messages' => $expected]
        ]);


        $result = $this->sqsDriver->messages('email');
        $this->assertEquals($expected, $result);
    }

    private function setSqsClientResponse(array $response)
    {
        foreach ($response as $res) {
            $this->mock->append(new Result($res));
        }

        $this->sqsClient->getHandlerList()->setHandler($this->mock);
    }


}

class SqsDriverTestJob implements JobContract
{
    public function handle(Payload $payload)
    {
        return;
    }
}