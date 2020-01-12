<?php

namespace Bag2\Cookie;

final class CookieTest extends TestCase
{
    /**
     * @dataProvider cookieProvider
     * @param string $name
     * @param string|int $value
     * @param array{expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool,samesite?:string} $options
     * @param array{name:string,value:string,options:array{expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool,samesite?:string}} $expected_array
     */
    public function test($name, $value, array $options, array $expected_array): void
    {
        $subject = new Cookie($name, $value, $options);

        $this->assertSame($expected_array, $subject->toArray());
    }

    /**
     * @return array<array>
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
            ],
        ];
    }
}
