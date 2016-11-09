<?php


namespace Dilab\Queueable\Driver;


use PHPUnit\Framework\TestCase;

class InMemoryDriverTest extends TestCase
{
    /**
     * @var InMemoryDriver
     */
    public $inMemoryDriver;

    public function setUp()
    {
        parent::setUp();
        $this->inMemoryDriver = new InMemoryDriver();
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->inMemoryDriver);
    }

    public function testPop()
    {
        $queueName = 'test';
        $this->assertSame([], $this->inMemoryDriver->pop($queueName));
    }

    public function testPush()
    {
        $queueName = 'test';

        $this->inMemoryDriver->push($queueName, $messageOne = ['Message' => ['id' => 1], 'id' => 1]);
        $this->assertSame($messageOne, $this->inMemoryDriver->pop($queueName));

        $this->inMemoryDriver->push($queueName, $messageTwo = ['Message' => ['id' => 2], 'id' => 2]);
        $this->assertSame($messageOne, $this->inMemoryDriver->pop($queueName));

        $queueName = 'test2';
        $this->inMemoryDriver->push($queueName, $messageThree = ['Message' => ['id' => 3], 'id' => 3]);
        $this->assertSame($messageThree, $this->inMemoryDriver->pop($queueName));
    }

    public function testDelete()
    {
        $queueName = 'test';

        $this->inMemoryDriver->push($queueName, $messageOne = ['Message' => ['id' => 1], 'id' => 1]);
        $this->inMemoryDriver->delete($queueName, ['Message' => ['id' => 1], 'id' => 1]);
        $result = $this->inMemoryDriver->pop($queueName);
        $this->assertSame([], $result);

        $this->inMemoryDriver->push($queueName, $messageOne = ['Message' => ['id' => 1], 'id' => 1]);
        $this->inMemoryDriver->delete('another-queue', ['Message' => ['id' => 1], 'id' => 1]);
        $result = $this->inMemoryDriver->pop($queueName);
        $this->assertNotEquals([], $result);
    }

    public function testMessages()
    {
        $queueName = 'test';

        $result = $this->inMemoryDriver->messages($queueName);
        $this->assertSame([], $result);

        $this->inMemoryDriver->push($queueName, $messageOne = ['Message' => ['id' => 1], 'id' => 1]);
        $result = $this->inMemoryDriver->messages($queueName);
        $this->assertSame([$messageOne], $result);
    }


}
