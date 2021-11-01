<?php

namespace Bag2\Cookie;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseFactoryInterface;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    public function createResponseFactory(): ResponseFactoryInterface
    {
        return new Psr17Factory();
    }
}
