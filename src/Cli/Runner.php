<?php
namespace Dilab\Queueable\Cli;

use League\CLImate\CLImate;

/**
 * Created by xu
 * Date: 8/11/16
 * Time: 10:02 AM
 */
class Runner
{
    /**
     * @var CLImate
     */
    private $cli;

    /**
     * Runner constructor.
     */
    public function __construct()
    {
        $this->cli = new CLImate();
    }

    public function enqueue()
    {

    }

    public function run()
    {
        $this->cli->arguments->add([
            'queue' => [
                'prefix' => 'q',
                'longPrefix' => 'queue',
                'description' => 'Queue Name',
                'required' => true
            ],
            'verbose' => [
                'prefix' => 'v',
                'longPrefix' => 'verbose',
                'description' => 'Verbose output',
                'noValue' => true,
            ],
            'help' => [
                'longPrefix' => 'help',
                'description' => 'Prints a usage statement',
                'noValue' => true,
            ],
        ]);

        try {

            $this->cli->arguments->parse();

            $queue = $this->cli->arguments->get('queue');

        } catch (\Exception $e) {
            $this->cli->usage();
        }
    }
}