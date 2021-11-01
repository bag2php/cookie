<?php

namespace Bag2\Cookie;

use Badoo\SoftMocks;
use function class_exists;

abstract class SoftMocksTestCase extends TestCase
{
    public function setUp(): void
    {
        if (!class_exists(SoftMocks::class)) {
            $this->markTestSkipped('SoftMocks is not activated');
        }
    }

    public function tearDown(): void
    {
        SoftMocks::restoreAll();

        parent::tearDown();
    }
}
