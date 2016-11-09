<?php


namespace Dilab\Queueable\Driver;


class InMemoryDriver extends Driver
{
    private $messages = [];

    public function name()
    {
        return 'InMemory';
    }

    public function connect($queueName)
    {
        return true;
    }

    public function pop($queueName, $options = [])
    {
        $messages = $this->messages($queueName);

        if (empty($messages)) {
            return [];
        }

        list($first) = $messages;

        return $first;
    }

    public function push($queueName, $message, $options = [])
    {
        if (!isset($this->messages[$queueName])) {
            $this->messages[$queueName] = [];
        }

        array_push($this->messages[$queueName], $message);
    }

    public function delete($queueName, $message)
    {
        $messages = $this->messages($queueName);

        foreach ($messages as $i => $message) {
            if ($message['id'] == $message['id']) {
                unset($messages[$i]);
            }
        }

        $this->messages[$queueName] = array_values($messages);
    }

    public function release($queueName, $message, $options = [])
    {
        return;
    }

    public function messages($queueName)
    {
        return isset($this->messages[$queueName]) ? $this->messages[$queueName] : [];
    }

}