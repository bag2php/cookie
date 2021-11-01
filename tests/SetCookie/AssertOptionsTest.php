<?php

namespace Bag2\Cookie\SetCookie;

use Bag2\Cookie\SetCookie;
use Bag2\Cookie\TestCase;
use DomainException;

final class AssertOptionsTest extends TestCase
{
    public function test(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('foo is unexpected cookie option.');

        SetCookie::assertOptions(['foo' => 'bar']);
    }
}
