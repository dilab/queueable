<?php


namespace Dilab\Queueable\Driver;


use Aws\Sqs\SqsClient;

/**
 * Class SqsDriver
 * @package Dilab\Queueable\Driver
 *
 * Reference:
 * http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-sqs-2012-11-05.html
 */
class SqsDriver extends Driver
{

    /**
     * @var string
     */
    private $queueName;

    /**
     * @var SqsClient
     */
    private $sqsClient;

    /**
     * SqsDriver constructor.
     * @param string $queueName
     * @param SqsClient $sqsClient
     */
    public function __construct($queueName, SqsClient $sqsClient)
    {
        $this->queueName = $queueName;
        $this->sqsClient = $sqsClient;
    }

    public function connect($queueName)
    {
        return !empty($this->getQueueUrlByName($queueName));
    }

    public function name()
    {
        return 'Sqs';
    }

    public function pop($queueName, $options = [])
    {
        $response = $this->sqsClient->receiveMessage([
            'QueueUrl' => $this->getQueueUrlByName($queueName),
            'AttributeNames' => ['ApproximateReceiveCount'],
        ]);

        if (empty($response->get('Messages')) || count($response->get('Messages')) < 1) {
            return [];
        }

        return $response['Messages'][0];
    }

    public function push($queueName, array $body, $options = [])
    {
        if (isset($body['userJobInstance']) && is_object($body['userJobInstance'])) {
            $body['userJobInstance'] = serialize($body['userJobInstance']);
        }

        if (isset($body['payload']) && is_object($body['payload'])) {
            $body['payload'] = serialize($body['payload']);
        }

        $response = $this->sqsClient->sendMessage([
            'QueueUrl' => $this->getQueueUrlByName($queueName),
            'MessageBody' => json_encode($body),
        ]);

        return $response->get('MessageId');
    }

    public function delete($queueName, array $message)
    {
        $this->sqsClient->deleteMessage([
            'QueueUrl' => $this->getQueueUrlByName($queueName),
            'ReceiptHandle' => $message['ReceiptHandle'],
        ]);

        return true;
    }

    public function messages($queueName)
    {
        $response = $this->sqsClient->receiveMessage([
            'QueueUrl' => $this->getQueueUrlByName($queueName)
        ]);

        if (count($response->get('Messages')) < 1) {
            return [];
        }

        return $response['Messages'];
    }

    public function getQueueUrlByName($queueName)
    {
        $response = $this->sqsClient->getQueueUrl(['QueueName' => $queueName]);

        return $response->get('QueueUrl');
    }

}