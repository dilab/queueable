<?php


namespace Dilab\Queueable\Driver;


class InMemoryDriver extends Driver
{
    private $messages = [];

    public function connect()
    {
        return true;
    }

    public function pop($options = [])
    {
        return $this->messages[0];
    }

    public function push($message, $options = [])
    {
        // TODO: Implement push() method.
    }

    public function queues()
    {
        // TODO: Implement queues() method.
    }

    public function release($message, $options = [])
    {
        // TODO: Implement release() method.
    }

    public function jobs($queueName)
    {
        // TODO: Implement jobs() method.
    }

}