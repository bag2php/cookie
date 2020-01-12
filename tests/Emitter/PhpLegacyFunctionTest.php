<?php

namespace Bag2\Cookie\Emitter;

use AssertionError;
use Badoo\SoftMocks;
use Bag2\Cookie\TestCase;

final class PhpLegacyFunctionTest extends TestCase
{
    /** @var Emitter */
    private $subject;
    /** @var ?array */
    private $receive;

    public function setUp()
    {
        parent::setUp();

        $receive = &$this->receive;
        SoftMocks::redefineFunction('setcookie', '', static function ($name, $value, $expires, $path, $domain, $secure, $httponly) use (&$receive) {
            $receive = [
                'name' => $name,
                'value' => $value,
                'expires' => $expires,
                'path' => $path,
                'domain' => $domain,
                'secure' => $secure,
                'httponly' => $httponly,
            ];

            \ksort($receive);

            return true;
        });

        $this->subject = new PhpLegacyFunction();
    }

    public function tearDown()
    {
        SoftMocks::restoreAll();

        parent::tearDown();
    }

    /**
     * @dataProvider cookieInputProvider
     */
    public function test(array $input)
    {
        SoftMocks::redefineConstant('PHP_VERSION_ID', 70100);

        $expected = $input + [
            'name' => 'name',
            'value' => 'val',
            'expires' => 0,
            'path' => '',
            'domain' => '',
            'secure' => false,
            'httponly' => false,
        ];

        \ksort($expected);

        $subject = $this->subject;

        $this->assertTrue($subject('name', 'val', $input));
        $this->assertSame($expected, $this->receive);
    }

    public function cookieInputProvider()
    {
        $options = [
            'expires' => 0,
            'path' => '',
            'domain' => '',
            'secure' => false,
            'httponly' => false,
        ];

        foreach ($options as $name => $value) {
            yield [[$name => $value]];
        }
    }

    public function test_raise_AssertException()
    {
        $this->expectException(AssertionError::class);

        SoftMocks::redefineConstant('PHP_VERSION_ID', 70300);

        $subject = $this->subject;
        $subject('name', 'val', ['expires' => 0]);
    }
}
