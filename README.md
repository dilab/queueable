# Queueable: Framework Agnostic Queue/Worker System

## Install

```
composer require dilab/queueable
```


## Usage

### Job & Queue

+ Create a Job

```

class SendEmailJob implements JobContract
{
    public function handle(Payload $payload)
    {
        return 'Sending an email to user ' . $payload->get('name');
    }

}
```

+ Create a Queue

```
$driver = new InMemoryDriver();

$queue = new Queue('email', $driver);
```

+ Enqueue a job

```
$queue->push(
    new SendEmailJob(),
    new Payload(['name' => 'Xu'])
);
```

### Worker

+ Create a Worker instance

```
$worker = new Worker($queue);
```

+ Put worker to work
```
$worker->work();
```

## Callbacks

+ beforeFetchJob: It is called before trying to fetch a job from the queue

```php
$worker->attach('heartbeat', function () use ($queueName) {
    $cronitorUrlId = Configure::read('Cronitor.queue.' . $queueName);
    if (!empty($cronitorUrlId)) {
        file_get_contents('https://cronitor.link/' . $cronitorUrlId . '/complete');
    }
});
```

+ beforeCompleteJob: It is called before a job is completed  

```php
$worker->attach('beforeCompleteJob', function () {
    // do something
});
```

+ afterCompleteJob: It is called after a job is completed  

```php
$worker->attach('afterCompleteJob', function () {
    // do something
});
```



## Current Drivers:
+ [x] AWS SQS

## Notes
Some general notes when developing this package
+ Driver works with message(raw data, mostly in array format)
+ Queue translates message to Job
+ Job works with message
+ Worker works with Job & Queue objects