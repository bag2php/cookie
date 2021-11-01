<?php

namespace Bag2\Cookie\SetCookie;

use Bag2\Cookie\SetCookie;
use Bag2\Cookie\TestCase;
use DomainException;

final class AssertOptionsTest extends TestCase
{
    /**
     * @dataProvider optionsProvider
     * @phpstan-param array<string,mixed> $input
     */
    public function test(array $input, string $expected_message): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage($expected_message);

        SetCookie::assertOptions($input);
    }

    /**
     * @return array<array{0:array<string,string>,1:string}>
     */
    public function optionsProvider(): array
    {
        return [
            [
                ['foo' => 'bar'],
                'foo is unexpected cookie option.',
            ],
            [
                ['path' => "\t"],
                'Cookie "path" option cannot contain ",", ";", " ", "\t", "\r", "\n", "\013", or "\014"',
            ],
            [
                ['domain' => "\t"],
                'Cookie "domain" option cannot contain ",", ";", " ", "\t", "\r", "\n", "\013", or "\014"',
            ],
            [
                ['secure' => "\t"],
                'Cookie "secure" option accept only bool',
            ],
            [
                ['samesite' => 'hoge'],
                'Cookie "secure" option allows only "Lax", "None" and "Strict"',
            ],
        ];
    }
}
