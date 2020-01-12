<?php

namespace Bag2\Cookie\Emitter;

use AssertionError;
use Badoo\SoftMocks;
use Bag2\Cookie\TestCase;

final class PhpLegacyFunctionTest extends TestCase
{
    /** @var PhpLegacyFunction */
    private $subject;
    /** @var ?array{name:string,value:string,expires:int,path:string,domain:string,secure:bool,httponly:bool} */
    private $receive;

    public function setUp(): void
    {
        parent::setUp();

        $receive = &$this->receive;

        /**
         * @param string $name
         * @param string $value
         * @param int $expires
         * @param string $path
         * @param string $domain
         * @param bool $secure
         * @param bool $httponly
         */
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

    /**
     * @dataProvider cookieInputProvider
     * @param array{expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool,samesite?:string} $input
     * @param ?array{name:string,value:string,expires:int,path:string,domain:string,secure:bool,httponly:bool} $expected
     */
    public function test(array $input, $expected = null): void
    {
        SoftMocks::redefineConstant('PHP_VERSION_ID', 70100);

        if ($expected === null) {
            $expected = $input + [
                'name' => 'name',
                'value' => 'val',
                'expires' => 0,
                'path' => '',
                'domain' => '',
                'secure' => false,
                'httponly' => false,
            ];
        }

        \ksort($expected);

        $subject = $this->subject;

        $this->assertTrue($subject('name', 'val', $input));
        $this->assertSame($expected, $this->receive);
    }

    /**
     * @return array<array{0:array{expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool,samesite?:string},1?:array{name:string,value:string,expires:int,path:string,domain:string,secure:bool,httponly:bool}}>
     */
    public function cookieInputProvider()
    {
        return [
            [['expires' => 3600]],
            [['path' => '/']],
            [['domain' => 'cookie.example.com']],
            [['secure' => true]],
            [['httponly' => true]],
            [
                ['path' => '/', 'samesite' => 'Lax'],
                [
                    'name' => 'name',
                    'value' => 'val',
                    'expires' => 0,
                    'path' => '/; SameSite=Lax',
                    'domain' => '',
                    'secure' => false,
                    'httponly' => false,
                ]
            ],
        ];
    }

    public function test_raise_AssertException(): void
    {
        $this->expectException(AssertionError::class);

        SoftMocks::redefineConstant('PHP_VERSION_ID', 70300);

        $subject = $this->subject;
        $subject('name', 'val', ['expires' => 0]);
    }
}
