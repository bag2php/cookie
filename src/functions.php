<?php

/**
 * Cookie functions
 */

namespace Bag2\Cookie
{
    use Bag2\Cookie\Emitter\Php73Function;
    use Bag2\Cookie\Emitter\PhpLegacyFunction;
    use const PHP_VERSION_ID;

    /**
     * @param array{expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool,samesite?:string} $default_options
     */
    function oven(array $default_options = []): Oven
    {
        return new Oven($default_options);
    }

    function create_emitter(): Emitter
    {
        if (PHP_VERSION_ID < 70300) {
            return new PhpLegacyFunction();
        }

        return new Php73Function();
    }

    function emit(Oven $cookie_oven): bool
    {
        $emitter = create_emitter();

        $success = true;

        foreach ($cookie_oven as $cookie) {
            [$name, $value, $options] = [$cookie->name, $cookie->value, $cookie->options];
            $success = $success && $emitter($name, $value, $options);
        }

        return $success;
    }

    /**
     * Send a cookie by legacy style \setcookie() like function
     */
    function setcookie(
        string $name,
        string $value = '',
        int $expires = 0,
        string $path = '',
        string $domain = '',
        bool $secure = false,
        bool $httponly = false,
        string $samesite = ''
    ): bool {
        return emit(oven()->add($name, $value, [
            'expires' => $expires,
            'path' => $path,
            'domain' => $domain,
            'secure' => $secure,
            'httponly' => $httponly,
            'samesite' => $samesite,
        ]));
    }
}
