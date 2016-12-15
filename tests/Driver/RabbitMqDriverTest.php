<?php


namespace Dilab\Queueable\Driver;

use Dilab\Queueable\Contract\JobContract;
use Dilab\Queueable\Job\Payload;
use \Mockery as m;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;

class RabbitMqDriverTest extends TestCase
{

    /**
     * @var RabbitMqDriver
     */
    public $rabbitMqDriver;

    public $streamConnection;

    public $channel;

    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testConnect()
    {
        $this->channel = m::spy(AMQPChannel::class);

        $this->streamConnection = m::mock(AMQPStreamConnection::class)->shouldReceive('channel')->once()->andReturn(
            $this->channel
        )->getMock();

        $this->rabbitMqDriver = new RabbitMqDriver($this->streamConnection);

        $this->rabbitMqDriver->connect($queueName = 'test');

        $this->channel->shouldHaveReceived('queue_declare')->with($queueName, false, true, false, false)->once();
    }

    public function testName()
    {
        $this->streamConnection = m::mock(AMQPStreamConnection::class);

        $this->rabbitMqDriver = new RabbitMqDriver($this->streamConnection);

        $this->assertSame('RabbitMq', $this->rabbitMqDriver->name());
    }

    public function testPop()
    {
        $message = new AMQPMessage('123');

        $message->delivery_info = array(
            'delivery_tag' => '456'
        );

        $this->channel = m::mock(AMQPChannel::class, [
            'queue_declare' => true,
            'basic_qos' => true,
            'basic_get' => $message
        ]);

        $this->streamConnection = m::mock(AMQPStreamConnection::class, [
                'channel' => $this->channel]
        );

        $this->rabbitMqDriver = new RabbitMqDriver($this->streamConnection);

        $message = $this->rabbitMqDriver->pop($queueName = 'test');

        $this->channel->shouldHaveReceived('queue_declare')->with($queueName, false, true, false, false)->once();

        $this->assertSame(['body' => '123', 'deliveryTag' => '456'], $message);
    }

    public function testPush()
    {
        $this->channel = m::spy(AMQPChannel::class);

        $this->streamConnection = m::mock(AMQPStreamConnection::class, ['channel' => $this->channel]);

        $this->rabbitMqDriver = new RabbitMqDriver($this->streamConnection);

        $this->rabbitMqDriver->push($queueName = 'email', $msg = [
            'id' => md5(uniqid('', true)),
            'userJobInstance' => (new RabbitMqDriverTestJob()),
            'payload' => (new Payload(['name' => 'Xu']))
        ]);

        $msgExpected = json_encode([
            'id' => $msg['id'],
            'userJobInstance' => serialize($msg['userJobInstance']),
            'payload' => serialize($msg['payload']),
        ]);

        $this->channel->shouldHaveReceived('queue_declare')->with($queueName, false, true, false, false)->once();

        $this->channel->shouldHaveReceived('basic_publish');
    }

    public function testDelete()
    {
        $this->channel = m::spy(AMQPChannel::class);

        $this->streamConnection = m::mock(AMQPStreamConnection::class, ['channel' => $this->channel]);

        $this->rabbitMqDriver = new RabbitMqDriver($this->streamConnection);

        $this->rabbitMqDriver->delete($queueName = 'email', ['body' => '123', 'deliveryTag' => '456']);

        $this->channel->shouldHaveReceived('queue_declare')->with($queueName, false, true, false, false)->once();

        $this->channel->shouldHaveReceived('basic_ack')->with('456');
    }


}

class RabbitMqDriverTestJob implements JobContract
{
    public function handle(Payload $payload)
    {
        return;
    }
}
