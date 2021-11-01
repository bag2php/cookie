<?php

namespace Bag2\Cookie\Oven;

use Bag2\Cookie\Oven;
use Bag2\Cookie\TestCase;
use DomainException;

final class ParseLineTest extends TestCase
{
    /** @var Oven */
    private $subject;

    public function setUp(): void
    {
        $this->subject = new Oven();
    }

    /**
     * @dataProvider linesProvider
     * @phpstan-param list<string> $input
     * @param array<string> $expected
     */
    public function test(array $input, array $expected): void
    {
        $this->assertEquals($expected, $this->subject->parseLines($input));
    }

    /**
     * @phpstan-return array<array{0:list<string>,1:array<string>}>
     */
    public function linesProvider(): array
    {
        return [
            [
                [],
                [],
            ],
            [
                [
                    'Name=Value',
                ],
                [
                    'Name' => 'Name=Value',
                ],
            ],
            [
                [
                    'Name=Value1',
                    'Name=Value2',
                ],
                [
                    'Name' => 'Name=Value2',
                ],
            ],
            [
                [
                    'Value',
                ],
                [
                    'Value',
                ],
            ],
            [
                [
                    'Value1',
                    'Value2',
                ],
                [
                    'Value1',
                    'Value2',
                ],
            ],
        ];
    }
}
