<?php
namespace Dilab\Queueable\Driver;

abstract class Driver
{
    public abstract function connect($queueName);

    /**
     * Pop the next message off of the queue.
     *
     * @param array $options an array of options for popping a message from the queue
     *
     * @return array an array of item data
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
    public abstract function push($queueName, $message, $options = []);

    /**
     * Delete a single message from the queue.
     *
     * @param array $message an item payload
     *
     * @return boolean
     **/
    public abstract function delete($queueName, $message);

    /**
     * Release the message back into the queue.
     *
     * @param  array $message an array of item data
     *
     * @return boolean
     */
    public abstract function release($queueName, $message, $options = []);

    /**
     * List messages inside a queue.
     *
     * @param  string name of the queue
     *
     * @return array
     */
    public abstract function messages($queueName);
}