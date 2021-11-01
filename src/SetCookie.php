<?php

namespace Bag2\Cookie;

use DomainException;
use OutOfRangeException;
use function gmdate;
use function is_int;
use function is_string;
use function max;
use function preg_match;
use function urlencode;
use const DATE_RFC7231;

/**
 * Cookie class for HTTP Set-Cookie header
 *
 * @property-read string $name
 * @property-read string $value
 * @property-read array{expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool,samesite?:string} $options
 */
final class SetCookie
{
    private const KNOWN_OPTIONS = [
        'expires' => 'int',
        'path' => 'string',
        'domain' => 'string',
        'secure' => 'bool',
        'httponly' => 'bool',
        'samesite' => 'bool',
    ];

    private const RE_MALFORMED_PATH = "/[,; \t\r\n\013\014]/";

    /** @var string */
    private $name;
    /** @var string */
    private $value;
    /** @var array{expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool,samesite?:string} */
    private $options;

    /**
     * @param string|int $value
     * @param array{expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool,samesite?:string} $options
     * @phan-suppress PhanAccessReadOnlyMagicProperty
     */
    public function __construct(string $name, $value, array $options = [])
    {
        self::assertOptions($options);

        $this->name = $name;
        $this->value = (string)$value;
        $this->options = $options;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->$name;
    }

    /**
     * @param string $name
     */
    public function __isset($name)
    {
        return isset($this->$name);
    }

    /**
     * @param string $name
     * @param string|int $_value
     * @return void
     */
    public function __set($name, $_value)
    {
        throw new OutOfRangeException();
    }

    /**
     * @param string $name
     */
    public function __unset($name)
    {
        throw new OutOfRangeException();
    }

    /**
     * @param array{expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool,samesite?:string} $options
     */
    public static function assertOptions(array $options): void
    {
        foreach ($options as $name => $_) {
            if (!isset(self::KNOWN_OPTIONS[$name])) {
                throw new DomainException("{$name} in unexpected cookie option.");
            }
        }
    }

    /**
     * Compile cookie header
     *
     * @see https://developer.mozilla.org/docs/Web/HTTP/Headers/Set-Cookie
     * @see https://developer.mozilla.org/docs/Web/HTTP/Headers/Date
     * @phpstan-param 0|positive-int $now
     */
    public function compileHeaderLine(int $now): string
    {
        $line = urlencode($this->name) . '=' . urlencode($this->value);

        $expires = $this->options['expires'] ?? 0;
        if ($expires > 0) {
            assert(is_int($expires));

            $expires_str = gmdate(DATE_RFC7231, $expires);
            $max_age = max(0, $expires - $now);
            $line .= '; Expires=' . $expires_str . '; Max-Age=' . $max_age;
        }

        $path = $this->options['path'] ?? '';
        if ($path !== '') {
            assert(is_string($path));

            if (preg_match(self::RE_MALFORMED_PATH, $path)) {
                throw new DomainException('Cookie paths cannot contain any of the following \',; \\t\\r\\n\\013\\014\'');
            }

            $line .= '; Path=' . $path;
        }

        $domain = $this->options['domain'] ?? '';
        if ($domain !== '') {
            assert(is_string($domain));

            $line .= '; Domain=' . urlencode($domain);
        }

        $secure = $this->options['secure'] ?? false;
        if ($secure) {
            $line .= '; Secure';
        }

        $httponly = $this->options['httponly'] ?? false;
        if ($httponly) {
            $line .= '; HttpOnly';
        }

        $samesite = $this->options['samesite'] ?? '';
        if ($samesite !== '') {
            $line .= '; SameSite=' . urlencode($samesite);
        }

        return $line;
    }

    /**
     * @param array{name:string,value:string,options:array{expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool,samesite?:string}} $data
     */
    public function fromArray(array $data): SetCookie
    {
        return new SetCookie($data['name'], $data['value'], $data['options']);
    }

    /**
     * @return array{name:string,value:string,options:array{expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool,samesite?:string}}
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'value' => $this->value,
            'options' => $this->options,
        ];
    }
}
