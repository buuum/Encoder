<?php

namespace Test;

use Buuum\Encoder\Encoder;
use PHPUnit\Framework\TestCase;

class EncoderTest extends TestCase
{

    /**
     * @var Encoder
     */
    protected static $encoder;

    public static function setUpBeforeClass()
    {
        self::$encoder = new Encoder('my_secret_key');
    }

    public function testEncoder()
    {
        $encoder = self::$encoder;

        $data = [
            'id'   => 123,
            'text' => 'text'
        ];
        $encode_string = $encoder->encode($data);
        $decode = $encoder->decode($encode_string);

        $this->assertEquals($data['id'], $decode->id);
        $this->assertEquals($data['text'], $decode->text);

    }

    public function testEncodeWithExpires()
    {
        $encoder = self::$encoder;

        $data = [
            'id'   => 123,
            'text' => 'text'
        ];

        $headers = [
            'expires' => 2
        ];
        $encode_string = $encoder->encode($data, $headers);
        $decode = $encoder->decode($encode_string);

        $this->assertEquals($data['id'], $decode->id);
        $this->assertEquals($data['text'], $decode->text);

    }

    /**
     * @expectedException \Buuum\Encoder\Exception\ExpiresException
     */
    public function testCreateExpiresException()
    {
        $encoder = self::$encoder;

        $data = [
            'id'   => 123,
            'text' => 'text'
        ];

        $headers = [
            'expires' => 1
        ];
        $encode_string = $encoder->encode($data, $headers);
        sleep(2);
        $decode = $encoder->decode($encode_string);
    }

    /**
     * @expectedException \Buuum\Encoder\Exception\DelayException
     */
    public function testCreateDelayException()
    {
        $encoder = self::$encoder;

        $data = [
            'id'   => 123,
            'text' => 'text'
        ];

        $headers = [
            'delay' => 10
        ];
        $encode_string = $encoder->encode($data, $headers);
        $decode = $encoder->decode($encode_string);
    }

    public function testCreateWithDelay()
    {
        $encoder = self::$encoder;

        $data = [
            'id'   => 123,
            'text' => 'text'
        ];

        $headers = [
            'delay' => 1
        ];
        $encode_string = $encoder->encode($data, $headers);
        sleep(2);
        $decode = $encoder->decode($encode_string);

        $this->assertEquals($data['id'], $decode->id);
        $this->assertEquals($data['text'], $decode->text);
    }

}