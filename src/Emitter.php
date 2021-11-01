<?php

namespace Bag2\Cookie;

/**
 * @phpstan-type options array{expires?:int,path?:non-empty-string,domain?:non-empty-string,secure?:bool,httponly?:bool,samesite?:'Lax'|'None'|'Strict'}
 */
interface Emitter
{
    /**
     * @phpstan-param non-empty-string $name
     * @param array{expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool,samesite?:string} $options
     * @phpstan-param options $options
     */
    public function emitCookie(string $name, string $value, array $options): bool;
}
