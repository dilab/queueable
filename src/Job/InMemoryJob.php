<?php


namespace Dilab\Queueable\Job;


class InMemoryJob extends Job
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
        return;
    }

    public function attempts()
    {
        return intval(0);
    }

    public function id()
    {
        return $this->message['id'];
    }

}