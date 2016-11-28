<?php


namespace Dilab\Queueable\Driver;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

/**
 * Class RabbitMqDriver
 * @package Dilab\Queueable\Driver
 *
 * Reference:
 * http://www.rabbitmq.com/tutorials/tutorial-two-php.html
 */
class RabbitMqDriver extends Driver
{
    private $streamConnection;

    /**
     * @var AMQPChannel
     */
    private $channel;

    public function __construct(AMQPStreamConnection $streamConnection)
    {
        $this->streamConnection = $streamConnection;
    }

    public function connect($queueName)
    {
        $this->channel = $this->streamConnection->channel();

        $this->channel->queue_declare($queueName, false, true, false, false);

        return true;
    }

    public function name()
    {
        return 'RabbitMq';
    }

    public function pop($queueName, $options = [])
    {
        $this->connect($queueName);

        $this->channel->basic_qos(null, 1, null);

        $message = $this->channel->basic_get($queueName);

        if (!$message) {
            return [];
        }

        return [
            'body' => $message->getBody(),
            'deliveryTag' => $message->get('delivery_tag')
        ];
    }

    public function push($queueName, array $body, $options = [])
    {
        $this->connect($queueName);

        if (isset($body['userJobInstance']) && is_object($body['userJobInstance'])) {
            $body['userJobInstance'] = serialize($body['userJobInstance']);
        }

        if (isset($body['payload']) && is_object($body['payload'])) {
            $body['payload'] = serialize($body['payload']);
        }

        $this->channel->basic_publish(json_encode($body), '', $queueName);
    }

    public function delete($queueName, array $message)
    {
        $this->connect($queueName);

        $this->channel->basic_ack($message['deliveryTag']);
    }

    public function messages($queueName)
    {
        // TODO: Implement messages() method.
    }

}