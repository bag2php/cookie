<?php

namespace Bag2\Cookie;

use DomainException;

/**
 * @phpstan-import-type options from CookieEmitter
 */
final class SetCookieTest extends TestCase
{
    private const NOW = 1578813956;

    /**
     * @dataProvider cookieProvider
     * @param string $name
     * @phpstan-param non-empty-string $name
     * @param string|int $value
     * @param array{expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool,samesite?:string} $options
     * @phpstan-param options $options
     * @param array{name:string,value:string,options:array{expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool,samesite?:string}} $expected_array
     * @param string $expected_line
     */
    public function test($name, $value, $options, $expected_array, $expected_line): void
    {
        $subject = new SetCookie($name, $value, $options);

        $this->assertSame($expected_array, $subject->toArray());
        $this->assertSame($expected_line, $subject->compileHeaderLine(self::NOW));
    }

    /**
     * @return array<array<mixed>>
     */
    public function cookieProvider(): array
    {
        return [
            [
                'name' => 'Name',
                'value' => 'Value',
                'options' => [],
                'expected_array' => [
                    'name' => 'Name',
                    'value' => 'Value',
                    'options' => [],
                ],
                'expected_line' => 'Name=Value',
            ],
            [
                'name' => 'Number',
                'value' => 12345,
                'options' => [],
                'expected_array' => [
                    'name' => 'Number',
                    'value' => '12345',
                    'options' => [],
                ],
                'expected_line' => 'Number=12345',
            ],
            [
                'name' => 'Full',
                'value' => 'Option',
                'options' => [
                    'expires' => self::NOW + 3600,
                    'path' => '/dir/',
                    'domain' => 'cookie.example.net',
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => 'Strict',
                ],
                'expected_array' => [
                    'name' => 'Full',
                    'value' => 'Option',
                    'options' => [
                        'expires' => self::NOW + 3600,
                        'path' => '/dir/',
                        'domain' => 'cookie.example.net',
                        'secure' => true,
                        'httponly' => true,
                        'samesite' => 'Strict',
                    ],
                ],
                'expected_line' => 'Full=Option; Expires=Sun, 12 Jan 2020 08:25:56 GMT; Max-Age=3600; Path=/dir/; Domain=cookie.example.net; Secure; HttpOnly; SameSite=Strict',
            ],
            [
                'name' => 'Expires',
                'value' => 'is zero.',
                'options' => [
                    'expires' => 0,
                ],
                'expected_array' => [
                    'name' => 'Expires',
                    'value' => 'is zero.',
                    'options' => [
                        'expires' => 0,
                    ],
                ],
                'expected_line' => 'Expires=is%20zero.',
            ],
            [
                'name' => 'Expires',
                'value' => 'is one.',
                'options' => [
                    'expires' => 1,
                ],
                'expected_array' => [
                    'name' => 'Expires',
                    'value' => 'is one.',
                    'options' => [
                        'expires' => 1,
                    ],
                ],
                'expected_line' => 'Expires=is%20one.; Expires=Thu, 01 Jan 1970 00:00:01 GMT; Max-Age=0',
            ],
            [
                'name' => 'Expires',
                'value' => 'is now.',
                'options' => [
                    'expires' => self::NOW,
                ],
                'expected_array' => [
                    'name' => 'Expires',
                    'value' => 'is now.',
                    'options' => [
                        'expires' => self::NOW,
                    ],
                ],
                'expected_line' => 'Expires=is%20now.; Expires=Sun, 12 Jan 2020 07:25:56 GMT; Max-Age=0',
            ],
            [
                'name' => 'Expires',
                'value' => 'is past.',
                'options' => [
                    'expires' => self::NOW - 1,
                ],
                'expected_array' => [
                    'name' => 'Expires',
                    'value' => 'is past.',
                    'options' => [
                        'expires' => self::NOW - 1,
                    ],
                ],
                'expected_line' => 'Expires=is%20past.; Expires=Sun, 12 Jan 2020 07:25:55 GMT; Max-Age=0',
            ],
            [
                'name' => 'Remove',
                'value' => '',
                'options' => [
                    'expires' => self::NOW - 1,
                ],
                'expected_array' => [
                    'name' => 'Remove',
                    'value' => '',
                    'options' => [
                        'expires' => self::NOW - 1,
                    ],
                ],
                'expected_line' => 'Remove=; Expires=Sun, 12 Jan 2020 07:25:55 GMT; Max-Age=0',
            ],
        ];
    }

    /**
     * @dataProvider illegalOptionsProvider
     * @phpstan-param array{0:non-empty-string,1:int|string,2:options} $args
     */
    public function test_illegalOptions(array $args): void
    {
        $this->expectException(DomainException::class);

        $_ = new SetCookie(...$args);
    }

    /**
     * @phpstan-return array<array{0:array{0:string,1:string,2:array<string,string>}}>
     */
    public function illegalOptionsProvider(): array
    {
        return [
            [
                ['name', 'value', ['foo' => 'bar']],
            ],
            [
                ['', 'value', ['foo' => 'bar']],
            ],
            [
                ['name', 'value', ['foo' => 'bar']],
            ],
        ];
    }
}
