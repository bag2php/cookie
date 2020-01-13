<?php

namespace Bag2\Cookie\functions;

use Bag2\Cookie\Oven;

final class OvenTest extends \Bag2\Cookie\TestCase
{
    public function test(): void
    {
        $this->assertInstanceOf(Oven::class, \Bag2\Cookie\oven());
    }
}
