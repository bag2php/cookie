<?php

namespace Bag2\Cookie\Oven;

use Bag2\Cookie\Oven;
use Bag2\Cookie\TestCase;
use DomainException;
use PHPUnit\Framework\Attributes\DataProvider;

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
    #[DataProvider('linesProvider')]
    public function test(array $input, array $expected): void
    {
        $this->assertEquals($expected, $this->subject->parseLines($input));
    }

    /**
     * @phpstan-return array<array{0:list<string>,1:array<string>}>
     */
    public static function linesProvider(): array
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
