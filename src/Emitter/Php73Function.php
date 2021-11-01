<?php

namespace Bag2\Cookie\Emitter;

use Bag2\Cookie\CookieEmitter;
use function setcookie;
use const PHP_VERSION_ID;

/**
 * @phpstan-import-type options from CookieEmitter
 */
final class Php73Function implements CookieEmitter
{
    /**
     * @deprecated Use {@see CookieEmitter::emitCookie()}
     * @phpstan-param non-empty-string $name
     * @param array{expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool,samesite?:string} $options
     * @phpstan-param options $options
     */
    public function __invoke(string $name, string $value, array $options): bool
    {
        return $this->emitCookie($name, $value, $options);
    }

    public function emitCookie(string $name, string $value, array $options): bool
    {
        assert(PHP_VERSION_ID >= 70300);

        /** @psalm-suppress InvalidArgument */
        return setcookie($name, $value, $options);
    }
}
