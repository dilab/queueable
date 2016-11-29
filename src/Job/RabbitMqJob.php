<?php


namespace Dilab\Queueable\Job;



class RabbitMqJob extends Job
{
    public function acknowledge()
    {
        $this->queue->getDriver()->delete(
            $this->queue->getQueueName(),
            $this->message
        );
    }

    public function release()
    {
        $this->queue->getDriver()->release(
            $this->queue->getQueueName(),
            $this->message
        );
    }

    public function userJobInstance()
    {
        $body = $this->body();

        if (!is_object($body['userJobInstance'])) {
            return unserialize($body['userJobInstance']);
        }

        return $body['userJobInstance'];
    }

    public function payload()
    {
        $body = $this->body();

        if (!is_object($body['payload'])) {
            return unserialize($body['payload']);
        }

        return $body['payload'];
    }

    private function body()
    {
        if (!is_array($this->message['body'])) {
            return json_decode($this->message['body'], true);
        }
        return $this->message['body'];
    }

}