<?php

/**
 * Cookie functions
 */
namespace Bag2\Cookie
{
    use const PHP_VERSION_ID;
    use Bag2\Cookie\Emitter\Php73Function;
    use Bag2\Cookie\Emitter\PhpLegacyFunction;

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
