<?php

namespace Bag2\Cookie\functions;

use Badoo\SoftMocks;
use Bag2\Cookie\Emitter;
use Bag2\Cookie\Oven;

/**
 * @phpstan-import-type options from Emitter
 */
final class SetCookieTest extends \Bag2\Cookie\TestCase
{
    /**
     * @var ?array<array{name:string,value:string,options:array}>
     * @phpstan-var ?array<array{name:non-empty-string,value:string,options:options}>
     */
    public $received;
    /** @var ?Emitter */
    protected $subject;

    public function setUp(): void
    {
        $this->received = null;
        $received = &$this->received;

        SoftMocks::redefineFunction(
            'Bag2\Cookie\emit',
            '',
            static function (Oven $cookie_oven) use (&$received) {
                $received = $cookie_oven;

                return true;
            }
        );
    }

    public function test(): void
    {
        $expected = (new Oven())->add('Name', '', [
            'expires' => 0,
            'secure' => false,
            'httponly' => false,
        ]);
        $actual = \Bag2\Cookie\setcookie('Name');

        $this->assertTrue($actual);
        $this->assertInstanceOf(Oven::class, $this->received);
        $this->assertEquals($expected, $this->received);
    }
}
