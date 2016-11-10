<?php

// Create your custom job
class SendEmailJob implements JobContract
{
    public function handle(Payload $payload)
    {
        return 'Sending an email to user ' . $payload->get('name');
    }

}

// Create a Queue instance
$driver = new InMemoryDriver();

$queue = new Queue('email', $driver);

// Enqueue a job
$queue->push(
    new SendEmailJob(),
    new Payload(['name' => 'Xu'])
);

// Create a Worker instance
$worker = new Worker($queue);

// Run worker
$worker->work();



