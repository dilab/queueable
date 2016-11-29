<?php
namespace Dilab\Queueable\Driver;

abstract class Driver
{
    public abstract function name();

    public function connect($queueName)
    {
        return true;
    }

    /**
     * Release the message back into the queue.
     *
     * @param  array $message an array of item data
     *
     * @return boolean
     */
    public function release($queueName, array $message, $options = [])
    {
        return true;
    }

    /**
     * Pop the next message off of the queue.
     *
     * @param array $options an array of options for popping a message from the queue
     *
     * @return array an array of message
     */
    public abstract function pop($queueName, $options = []);

    /**
     * Push a single message onto the queue.
     *
     * @param array $message an item payload
     * @param array $options an array of options for publishing the message
     *
     * @return boolean
     **/
    public abstract function push($queueName, array $body, $options = []);

    /**
     * Delete a single message from the queue.
     *
     * @param array $message an item payload
     *
     * @return boolean
     **/
    public abstract function delete($queueName, array $message);

    /**
     * List messages inside a queue.
     *
     * @param  string name of the queue
     *
     * @return array
     */
    public function messages($queueName)
    {
        return [];
    }

}