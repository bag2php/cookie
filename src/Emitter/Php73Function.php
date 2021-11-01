<?php

namespace Bag2\Cookie\Emitter;

use Bag2\Cookie\Emitter;
use const PHP_VERSION_ID;
use function setcookie;

final class Php73Function implements Emitter
{
    /**
     * @param array{expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool,samesite?:string} $options
     */
    public function __invoke(string $name, string $value, array $options): bool
    {
        assert(PHP_VERSION_ID >= 70300);

        /** @psalm-suppress InvalidArgument */
        return setcookie($name, $value, $options);
    }
}
