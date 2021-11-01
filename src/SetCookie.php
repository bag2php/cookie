<?php

namespace Bag2\Cookie;

use DomainException;
use OutOfRangeException;
use function gmdate;
use function in_array;
use function is_int;
use function is_string;
use function max;
use function preg_match;
use function rawurlencode;
use function strpbrk;
use const DATE_RFC7231;

/**
 * Set-Cookie entry class
 *
 * @psalm-external-mutation-free
 * @phpstan-import-type options from CookieEmitter
 * @property-read non-empty-string $name
 * @property-read string $value
 * @property-read array{expires?:0|positive-int,path?:non-empty-string,domain?:non-empty-string,secure?:bool,httponly?:bool,samesite?:'Lax'|'None'|'Strict'} $options
 */
final class SetCookie
{
    private const KNOWN_OPTIONS = [
        'expires' => 'int',
        'path' => 'string',
        'domain' => 'string',
        'secure' => 'bool',
        'httponly' => 'bool',
        'samesite' => ['Lax', 'None', 'Strict'],
    ];

    protected const ILLEGAL_COOKIE_CHARACTER = '",", ";", " ", "\t", "\r", "\n", "\013", or "\014"';
    protected const ILLEGAL_OPTION_FORMAT = 'Cookie "%s" option cannot contain ' . self::ILLEGAL_COOKIE_CHARACTER;

    /**
     * @var string
     * @phpstan-var non-empty-string
     */
    private $name;
    /** @var string */
    private $value;
    /**
     * @var array{expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool,samesite?:string}
     * @phpstan-var options
     */
    private $options;

    /**
     * @phpstan-param non-empty-string $name
     * @param string|int $value
     * @param array{expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool,samesite?:string} $options
     * @phpstan-param options $options
     * @phan-suppress PhanAccessReadOnlyMagicProperty
     */
    final public function __construct(string $name, $value, array $options = [])
    {
        self::assertName($name);
        self::assertValue($value);
        self::assertOptions($options);

        $this->name = $name;
        $this->value = (string)$value;
        /** @psalm-suppress InvalidPropertyAssignmentValue */
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
     * @phpstan-return never
     */
    public function __set($name, $_value)
    {
        throw new OutOfRangeException();
    }

    /**
     * @param string $name
     * @phpstan-return never
     */
    public function __unset($name)
    {
        throw new OutOfRangeException();
    }

    /**
     * @psalm-assert non-empty-string $name
     */
    public static function assertName(string $name): void
    {
        if ($name === '') {
            throw new DomainException('Cookie names must not be empty');
        }

        if (strpbrk($name, "=,; \t\r\n\013\014") !== false) {
            throw new DomainException('Cookie names cannot contain "=", ' . self::ILLEGAL_COOKIE_CHARACTER);
        }
    }

    /**
     * @param int|string $value
     * @psalm-assert int|string $value
     */
    public static function assertValue($value): void
    {
        // noop
    }

    /**
     * @pure
     * @param array{expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool,samesite?:string} $options
     * @psalm-param array<mixed> $options
     * @psalm-assert options $options
     */
    public static function assertOptions(array $options): void
    {
        foreach ($options as $name => $_) {
            if (!isset(self::KNOWN_OPTIONS[$name])) {
                throw new DomainException("{$name} is unexpected cookie option.");
            }
        }

        $path = $options['path'] ?? '';
        if (!is_string($path) || strpbrk($path, ",; \t\r\n\013\014")) {
            throw new DomainException(sprintf(self::ILLEGAL_OPTION_FORMAT, 'path'));
        }

        $domain = $options['domain'] ?? '';
        if (!is_string($domain) || strpbrk($domain, ",; \t\r\n\013\014")) {
            throw new DomainException(sprintf(self::ILLEGAL_OPTION_FORMAT, 'domain'));
        }

        if (isset($options['secure']) && !is_bool($options['secure'])) {
            throw new DomainException('Cookie "secure" option accept only bool');
        }

        if (isset($options['samesite']) &&
            !in_array($options['samesite'], self::KNOWN_OPTIONS['samesite'], true)
        ) {
            throw new DomainException('Cookie "secure" option allows only "Lax", "None" and "Strict"');
        }
    }

    /**
     * Compile cookie header
     *
     * @see https://developer.mozilla.org/docs/Web/HTTP/Headers/Set-Cookie
     * @see https://developer.mozilla.org/docs/Web/HTTP/Headers/Date
     * @phpstan-param positive-int $now
     */
    public function compileHeaderLine(int $now): string
    {
        $line = "{$this->name}=" . $this->encodeValue($this->value);

        $expires = $this->options['expires'] ?? 0;
        if ($expires > 0) {
            assert(is_int($expires));

            $expires_str = gmdate(DATE_RFC7231, $expires);
            $max_age = max(0, $expires - $now);
            $line .= "; Expires={$expires_str}; Max-Age={$max_age}";
        }

        $path = $this->options['path'] ?? '';
        if ($path !== '') {
            $line .= '; Path=' . $path;
        }

        $domain = $this->options['domain'] ?? '';
        if ($domain !== '') {
            $line .= '; Domain=' . rawurlencode($domain);
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
            $line .= '; SameSite=' . rawurlencode($samesite);
        }

        return $line;
    }

    /**
     * @param int|string $value
     */
    public function encodeValue($value): string
    {
        return rawurlencode((string)$value);
    }

    /**
     * @param array{name:string,value:string,options:array{expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool,samesite?:string}} $data
     * @phpstan-param array{name:non-empty-string,value:string,options:options} $data
     * @return static
     */
    public function fromArray(array $data): SetCookie
    {
        return new SetCookie($data['name'], $data['value'], $data['options']);
    }

    /**
     * @return array{name:string,value:string,options:array{expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool,samesite?:string}}
     * @phpstan-return array{name:non-empty-string,value:string,options:options}
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
