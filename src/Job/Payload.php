<?php


namespace Dilab\Queueable\Job;


class Payload
{
    private $data;

    /**
     * Payload constructor.
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * @param $key
     * @param string $default
     * @return mixed|string
     *
     * Safe data retrieve
     */
    public function data($key = '', $default = '')
    {
        if (empty($key)) {
            return $this->data;
        }

        if (!isset($this->data[$key])) {
            return $default;
        }

        return $this->data[$key];
    }

}