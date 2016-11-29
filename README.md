# Queueable: Framework Agnostic Queue/Worker System

## Install

```
composer require dilab/queueable
```


## Usage


### Create your custom job
```

class SendEmailJob implements JobContract
{
    public function handle(Payload $payload)
    {
        return 'Sending an email to user ' . $payload->get('name');
    }

}
```

### Create a Queue instance

```
$driver = new InMemoryDriver();

$queue = new Queue('email', $driver);
```

### Enqueue a job

```
$queue->push(
    new SendEmailJob(),
    new Payload(['name' => 'Xu'])
);
```

### Create a Worker instance

```
$worker = new Worker($queue);
```

### Put worker to work
```
$worker->work();
```

### Current Drivers:
+ AWS SQS
+ RabbitMQ

## Notes
Some general notes when developing this package
+ Driver works with message(raw data, mostly in array format)
+ Queue translates message to Job
+ Job works with message
+ Worker works with Job & Queue objects
