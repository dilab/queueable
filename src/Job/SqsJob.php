<?php


namespace Dilab\Queueable\Job;


class SqsJob extends Job
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

    public function attempts()
    {
        return (int)$this->message['Attributes']['ApproximateReceiveCount'];
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
        if (!is_array($this->message['Body'])) {
            return json_decode($this->message['Body'], true);
        }
        return $this->message['Body'];
    }


}