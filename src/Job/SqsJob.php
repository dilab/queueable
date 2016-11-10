<?php


namespace Dilab\Queueable\Job;


class SqsJob extends Job
{
    public function acknowledge()
    {
        $this->message;
        $this->queue;
    }

    public function release()
    {
        // TODO: Implement release() method.
    }

    public function attempts()
    {

    }
}