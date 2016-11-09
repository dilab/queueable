<?php
// API Design

use Dilab\Queueable\Driver\InMemoryDriver;
use Dilab\Queueable\Worker;

$sendEmailJob = (new SendEmailJob())->toQueue('email');

// Enqueue
$queue->push($sendEmailJob, $payLoad);

// Specs
// max tries before releasing job back to queue
// manual release a job
// view queue jobs

// Usage

// Start worker
$queue = new Queue($queueName, new InMemoryDriver());

Worker::run($queue, $maxTries = 5);

// View jobs inside a queue
Worker::view($queue);



