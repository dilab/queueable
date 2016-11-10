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

    public function push($queueName, array $body, $options = [])
    {
        if (!isset($this->messages[$queueName])) {
            $this->messages[$queueName] = [];
        }

        array_push($this->messages[$queueName], $body);
    }

    public function delete($queueName, array $message)
    {
        $messages = $this->messages($queueName);

        foreach ($messages as $i => $message) {
            if ($message['id'] == $message['id']) {
                unset($messages[$i]);
            }
        }

        $this->messages[$queueName] = array_values($messages);
    }

    public function release($queueName, array $message, $options = [])
    {
        return;
    }

    public function messages($queueName)
    {
        return isset($this->messages[$queueName]) ? $this->messages[$queueName] : [];
    }

}