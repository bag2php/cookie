<?php

namespace Bag2\Cookie\Emitter;

use const PHP_VERSION_ID;
use function setcookie;
use Bag2\Cookie\Emitter;

final class PhpLegacyFunction implements Emitter
{
    /**
     * @param array{expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool,samesite?:string} $options
     */
    public function __invoke(string $name, string $value, array $options): bool
    {
        assert(PHP_VERSION_ID < 70300);

        return setcookie(
            $name,
            $value,
            $options['expires'] ?? 0,
            $options['path'] ?? '',
            $options['domain'] ?? '',
            $options['secure'] ?? false,
            $options['httponly'] ?? false,
        );
    }
}
