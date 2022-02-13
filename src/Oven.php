<?php

namespace Bag2\Cookie;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Psr\Http\Message\ResponseInterface;
use function array_values;
use function count;
use function explode;
use function strpos;
use function time;

/**
 * Cookie Oven
 *
 * @implements ArrayAccess<non-empty-string, SetCookie>
 * @implements IteratorAggregate<non-empty-string, SetCookie>
 * @phpstan-import-type options from CookieEmitter
 */
class Oven implements ArrayAccess, Countable, IteratorAggregate
{
    /** @phpstan-var array<non-empty-string,SetCookie> */
    private $bag = [];

    /**
     * @var array{expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool,samesite?:string}
     * @phpstan-var options
     */
    private $default_options;

    /**
     * @param array{expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool,samesite?:string} $default_options
     * @phpstan-param options $default_options
     */
    public function __construct(array $default_options = [])
    {
        SetCookie::assertOptions($default_options);

        /** @psalm-suppress InvalidPropertyAssignmentValue */
        $this->default_options = $default_options;
    }

    /**
     * @phpstan-impure
     * @phpstan-param non-empty-string $name
     * @param string|int $value
     * @param array{expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool,samesite?:string} $options
     * @phpstan-param options $options
     * @return $this
     */
    public function add(string $name, $value, array $options = []): self
    {
        $this->bag[$name] = new SetCookie($name, $value, $options + $this->default_options);

        return $this;
    }

    /**
     * Append cookies to PSR-7 HTTP Response Set-Cookie header
     *
     * @phpstan-pure
     * @template T of ResponseInterface
     * @phpstan-param T $response
     * @phpstan-param ?positive-int $now
     * @phpstan-return T
     */
    public function appendTo(ResponseInterface $response, ?int $now = null): ResponseInterface
    {
        // Return directly when bag is empty
        if (empty($this->bag)) {
            return $response;
        }

        $cookie_lines = $this->parseLines($response->getHeader('Set-Cookie'));

        if ($now === null) {
            /** @phpstan-var positive-int */
            $now = time();
        }

        foreach ($this->bag as $name => $cookie) {
            $cookie_lines[$name] = $cookie->compileHeaderLine($now);
        }

        return $response->withHeader('Set-Cookie', array_values($cookie_lines));
    }

    /**
     * @psalm-mutation-free
     * @return int
     */
    #[\ReturnTypeWillChange]
    public function count()
    {
        return count($this->bag);
    }

    /**
     * @phpstan-impure
     * @psalm-external-mutation-free
     * @phpstan-param non-empty-string $offset
     */
    public function offsetUnset($offset): void
    {
        unset($this->bag[$offset]);
    }

    /**
     * @psalm-mutation-free
     * @phpstan-param non-empty-string $offset
     */
    public function offsetExists($offset): bool
    {
        return isset($this->bag[$offset]);
    }

    /**
     * @psalm-mutation-free
     * @phpstan-param non-empty-string $offset
     */
    public function offsetGet($offset): SetCookie
    {
        return $this->bag[$offset];
    }

    /**
     * @phpstan-impure
     * @psalm-external-mutation-free
     * @phpstan-param ?non-empty-string $offset
     * @param SetCookie $value
     */
    public function offsetSet($offset, $value): void
    {
        /** @phpstan-ignore-next-line */
        assert($value instanceof SetCookie);
        $this->bag[$offset ?? $value->name] = $value;
    }

    /**
     * @phpstan-return ArrayIterator<non-empty-string, SetCookie>
     */
    #[\ReturnTypeWillChange]
    public function getIterator()
    {
        /** @phpstan-var ArrayIterator<non-empty-string, SetCookie> $iter */
        $iter = new ArrayIterator($this->bag);

        return $iter;
    }

    /**
     * @pure
     * @phpstan-param list<string> $cookie_lines
     * @phpstan-return array<array-key,string>
     */
    public function parseLines(array $cookie_lines): array
    {
        $parsed = [];
        foreach ($cookie_lines as $i => $line) {
            if (strpos($line, '=') !== false) {
                $name = explode('=', $line, 2)[0];
                $parsed[$name] = $line;
            } else {
                $parsed[] = $line;
            }
        }

        return $parsed;
    }

    /**
     * Set cookies to PSR-7 HTTP Response Set-Cookie header
     *
     * @phpstan-pure
     * @template T of ResponseInterface
     * @phpstan-param T $response
     * @phpstan-param ?positive-int $now
     * @phpstan-return T
     */
    public function setTo(ResponseInterface $response, ?int $now = null): ResponseInterface
    {
        if ($now === null) {
            /** @var positive-int */
            $now = time();
        }

        $cookie_lines = [];
        foreach ($this->bag as $name => $cookie) {
            $cookie_lines[$name] = $cookie->compileHeaderLine($now);
        }

        return $response->withHeader('Set-Cookie', $cookie_lines);
    }
}
