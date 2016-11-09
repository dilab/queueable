<?php


namespace Dilab\Queueable\Job;


use PHPUnit\Framework\TestCase;

class PayloadTest extends TestCase
{

    public function testDataProvider()
    {
        return [
            [
                '', ['name' => 'Xu', 'dob' => '1986-05-07'], 'occupation', ''
            ],
            [
                'Engineer', ['name' => 'Xu', 'dob' => '1986-05-07'], 'occupation', 'Engineer'
            ],
            [
                'Xu', ['name' => 'Xu', 'dob' => '1986-05-07'], 'name', ''
            ],
            [
                ['name' => 'Xu', 'dob' => '1986-05-07'], ['name' => 'Xu', 'dob' => '1986-05-07'], '', ''
            ]
        ];
    }

    /**
     * @dataProvider testDataProvider
     */
    public function testData($expected, $data, $key, $default)
    {
        $payLoad = new Payload($data);
        $result = $payLoad->data($key, $default);
        $this->assertEquals($expected, $result);
    }
}
