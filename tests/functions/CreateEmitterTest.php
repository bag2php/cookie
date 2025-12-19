<?php

namespace Bag2\Cookie\functions;

use Badoo\SoftMocks;
use Bag2\Cookie\Emitter\Php73Function;
use Bag2\Cookie\Emitter\PhpLegacyFunction;
use PHPUnit\Framework\Attributes\DataProvider;

final class CreateEmitterTest extends \Bag2\Cookie\SoftMocksTestCase
{
    /**
     * @dataProvider versionsProvider
     * @param class-string $expected
     */
    #[DataProvider('versionsProvider')]
    public function test(int $php_version_id, string $expected): void
    {
        SoftMocks::redefineConstant('PHP_VERSION_ID', $php_version_id);

        $this->assertInstanceOf($expected, \Bag2\Cookie\create_emitter());
    }

    /**
     * @return iterable<list{int, class-string}>
     */
    public static function versionsProvider()
    {
        yield '7.3.0' => [70300, Php73Function::class];
        yield '7.4.0' => [70400, Php73Function::class];
    }
}
