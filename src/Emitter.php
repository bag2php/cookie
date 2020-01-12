<?php

namespace Bag2\Cookie;

interface Emitter
{
    /**
     * @param array{expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool,samesite?:string} $options
     */
    public function __invoke(string $name, string $value, array $options): bool;
}
