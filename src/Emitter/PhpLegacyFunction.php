<?php

namespace Bag2\Cookie\Emitter;

use Bag2\Cookie\Emitter;
use function setcookie;
use function urlencode;
use const PHP_VERSION_ID;

/**
 * @phpstan-import-type options from \Bag2\Cookie\Emitter
 */
final class PhpLegacyFunction implements Emitter
{
    /**
     * @deprecated Use {@see Emitter::emitCookie()}
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
        assert(PHP_VERSION_ID < 70300);

        $path = $options['path'] ?? '';

        if (isset($options['samesite'])) {
            if ($path === '') {
                $path = '/; SameSite=' . urlencode($options['samesite']);
            } else {
                $path .= '; SameSite=' . urlencode($options['samesite']);
            }
        }

        return setcookie(
            $name,
            $value,
            $options['expires'] ?? 0,
            $path,
            $options['domain'] ?? '',
            $options['secure'] ?? false,
            $options['httponly'] ?? false
        );
    }
}
