<?php


namespace Dilab\Queueable\Contract;


use Dilab\Queueable\Job\Payload;

interface JobContract
{
    public function handle(Payload $payload);
}