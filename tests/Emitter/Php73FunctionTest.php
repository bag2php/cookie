<?php

namespace Bag2\Cookie\Emitter;

use AssertionError;
use Badoo\SoftMocks;
use Bag2\Cookie\TestCase;

final class Php73FunctionTest extends TestCase
{
    /** @var Emitter */
    private $subject;
    /** @var ?array */
    private $receive;

    public function setUp()
    {
        parent::setUp();

        SoftMocks::redefineConstant('PHP_VERSION_ID', 70300);

        $receive = &$this->receive;
        SoftMocks::redefineFunction('setcookie', '', static function ($name, $value, $options) use (&$receive) {
            $receive = [
                'name' => $name,
                'value' => $value,
                'options' => $options,
            ];

            return true;
        });

        $this->subject = new Php73Function();
    }

    public function tearDown()
    {
        SoftMocks::restoreAll();

        parent::tearDown();
    }

    public function test()
    {
        $expected = [
            'name' => 'name',
            'value' => 'val',
            'options' => ['expires' => 0],
        ];
        $subject = $this->subject;

        $this->assertTrue($subject('name', 'val', ['expires' => 0]));
        $this->assertSame($expected, $this->receive);
    }


    public function test_raise_AssertionError()
    {
        $this->expectException(AssertionError::class);

        SoftMocks::redefineConstant('PHP_VERSION_ID', 70100);

        $subject = $this->subject;
        $subject('name', 'val', ['expires' => 0]);
    }
}
