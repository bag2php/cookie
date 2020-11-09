<?php

namespace Bag2\Cookie;

use function count;

final class OvenTest extends TestCase
{
    private const NOW = 1578813956;

    /**
     * @dataProvider bagProvider
     * @param array{expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool,samesite?:string} $default_options
     * @param array<array{0:string,1:string,2:array{expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool,samesite?:string}}> $cookies
     * @param SetCookie[] $expected_cookies
     */
    public function test($default_options, $cookies, $expected_cookies): void
    {
        $subject = new Oven($default_options);

        foreach ($cookies as [$name, $value, $options]) {
            $subject->add($name, $value, $options);
        }

        foreach ($subject as $cookie) {
            $this->assertInstanceOf(SetCookie::class, $cookie);
            $this->assertTrue($subject->has($cookie->name));
            $this->assertSame($cookie, $subject->get($cookie->name));
        }

        $this->assertFalse($subject->has(''));

        foreach ($expected_cookies as $expected) {
            $actual = $subject->get($expected->name);
            $this->assertSame($expected->name, $actual->name);
            $this->assertSame($expected->value, $actual->value);
            $this->assertSame($expected->options, $actual->options);
        }

        $this->assertSame(count($expected_cookies), count($subject));
    }

    /**
     * @return array<array>
     */
    public function bagProvider(): array
    {
        $now = time();

        return [
            [
                'default_options' => [],
                'cookies' => [],
                'expected_cookies' => [],
            ],
            [
                'default_options' => [],
                'cookies' => [
                    ['Name', 'Value', ['expires' => $now + 120]],
                ],
                'expected_cookies' => [
                    new SetCookie('Name', 'Value', ['expires' => $now + 120]),
                ],
            ],
            [
                'default_options' => ['httponly' => true, 'samesite' => 'Strict'],
                'cookies' => [
                    ['Name', 'Value', ['expires' => $now + 120]],
                ],
                'expected_cookies' => [
                    new SetCookie('Name', 'Value', [
                        'expires' => $now + 120,
                        'httponly' => true,
                        'samesite' => 'Strict',
                    ]),
                ],
            ],
        ];
    }

    public function test_setTo(): void
    {
        $response = $this->createResponseFactory()->createResponse();

        $subject = (new Oven())
            ->add('Name1', 'value', ['expires' => self::NOW + 3600])
            ->add('Name2', 'value', ['expires' => self::NOW + 3600]);

        $expected = [
            'Name1=value; Expires=Sun, 12 Jan 2020 08:25:56 GMT; Max-Age=3600',
            'Name2=value; Expires=Sun, 12 Jan 2020 08:25:56 GMT; Max-Age=3600',
        ];

        $actual = $subject->setTo($response, self::NOW);
        $this->assertSame($expected, $actual->getHeader('Set-Cookie'));

        $subject->add('Name1', 'VALUE', ['expires' => 0]);

        $expected = [
            'Name1=VALUE',
            'Name2=value; Expires=Sun, 12 Jan 2020 08:25:56 GMT; Max-Age=3600',
        ];

        $actual = $subject->setTo($actual, self::NOW);
        $this->assertSame($expected, $actual->getHeader('Set-Cookie'));

        $subject->delete('Name2');

        $expected = [
            'Name1=VALUE',
            'Name2=value; Expires=Sun, 12 Jan 2020 08:25:56 GMT; Max-Age=3600',
        ];

        $actual = $subject->appendTo($actual, self::NOW);
        $this->assertSame($expected, $actual->getHeader('Set-Cookie'));

        $expected = [
            'Name1=VALUE',
        ];

        $actual = $subject->setTo($actual, self::NOW);
        $this->assertSame($expected, $actual->getHeader('Set-Cookie'));
    }
}
