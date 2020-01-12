<?php

/**
 * Cookie functions
 */
namespace Bag2\Cookie
{
    use const PHP_VERSION_ID;
    use Bag2\Cookie\Emitter\Php73Function;
    use Bag2\Cookie\Emitter\PhpLegacyFunction;

    /**
     * @param array{expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool,samesite?:string} $default_options
     */
    function bag(array $default_options = []): Bag
    {
        return new Bag($default_options);
    }

    function create_emitter(): Emitter
    {
        if (PHP_VERSION_ID < 70300) {
            return new PhpLegacyFunction();
        }

        return new Php73Function();
    }

    function emit(Bag $cookie_bag): void
    {
        $emitter = create_emitter();

        foreach ($cookie_bag as $cookie) {
            $emitter($cookie->name, $cookie->value, $cookie->options);
        }
    }
}
