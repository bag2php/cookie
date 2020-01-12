<?php

namespace Bag2\Cookie\functions;

use Bag2\Cookie\Bag;

final class BagTest extends \Bag2\Cookie\TestCase
{
    public function test()
    {
        $this->assertInstanceOf(Bag::class, \Bag2\Cookie\bag());
    }
}
