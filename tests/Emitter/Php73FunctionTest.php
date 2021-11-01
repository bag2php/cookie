<?php

namespace Bag2\Cookie\Emitter;

use AssertionError;
use Badoo\SoftMocks;
use Bag2\Cookie\Emitter;
use Bag2\Cookie\TestCase;

/**
 * @phpstan-import-type options from Emitter
 */
final class Php73FunctionTest extends TestCase
{
    /** @var ?Php73Function */
    private $subject;
    /**
     * @var ?array{name:string,value:string,options:array}
     * @phpstan-var ?array{name:non-empty-string,value:string,options:options}
     */
    private $receive;

    public function setUp(): void
    {
        parent::setUp();

        SoftMocks::redefineConstant('PHP_VERSION_ID', 70300);

        $receive = &$this->receive;
        /**
         * @param string $name
         * @param string $value
         * @param array{expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool,samesite?:string} $options $options
         */
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

    public function test(): void
    {
        $expected = [
            'name' => 'name',
            'value' => 'val',
            'options' => ['expires' => 0],
        ];
        $subject = $this->subject;
        assert($subject !== null);

        $this->assertTrue($subject('name', 'val', ['expires' => 0]));
        $this->assertSame($expected, $this->receive);
    }

    public function test_raise_AssertionError(): void
    {
        $this->expectException(AssertionError::class);

        SoftMocks::redefineConstant('PHP_VERSION_ID', 70100);

        $subject = $this->subject;
        assert($subject !== null);
        $subject('name', 'val', ['expires' => 0]);
    }
}
