<?php

namespace Bag2\Cookie\Emitter;

use Bag2\Cookie\Emitter;
use function setcookie;
use const PHP_VERSION_ID;

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
