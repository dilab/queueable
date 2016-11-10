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
        if (!is_object($this->message['Body']['userJobInstance'])) {
            return unserialize($this->message['Body']['userJobInstance']);
        }

        return $this->message['Body']['userJobInstance'];
    }

    public function payload()
    {
        if (!is_object($this->message['Body']['payload'])) {
            return unserialize($this->message['Body']['payload']);
        }

        return $this->message['Body']['payload'];
    }


}