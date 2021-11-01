<?php

namespace Bag2\Cookie;

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
 * @implements IteratorAggregate<string,SetCookie>
 * @phpstan-import-type options from CookieEmitter
 */
class Oven implements IteratorAggregate, Countable
{
    /** @var array<string,SetCookie> */
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
    public function count()
    {
        return count($this->bag);
    }

    /**
     * @phpstan-impure
     * @psalm-mutation-free
     * @return $this
     */
    public function delete(string $name): self
    {
        unset($this->bag[$name]);

        return $this;
    }

    /**
     * @psalm-mutation-free
     */
    public function has(string $name): bool
    {
        return isset($this->bag[$name]);
    }

    /**
     * @psalm-mutation-free
     */
    public function get(string $name): SetCookie
    {
        return $this->bag[$name];
    }

    /**
     * @return ArrayIterator<string,SetCookie>
     */
    public function getIterator()
    {
        return new ArrayIterator($this->bag);
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
     * @phpstan-param ?positive-int $now
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

        return $response->withHeader('Set-Cookie', array_values($cookie_lines));
    }
}
