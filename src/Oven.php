<?php

namespace Bag2\Cookie;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Psr\Http\Message\ResponseInterface;

/**
 * Cookie Oven
 *
 * @implements IteratorAggregate<string,SetCookie>
 */
class Oven implements IteratorAggregate, Countable
{
    /** @var array<string,SetCookie> */
    private $bag = [];
    /** @var array{expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool,samesite?:string} */
    private $default_options;

    /**
     * @param array{expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool,samesite?:string} $default_options
     */
    public function __construct(array $default_options = [])
    {
        SetCookie::assertOptions($default_options);

        $this->default_options = $default_options;
    }

    /**
     * @param string|int $value
     * @param array{expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool,samesite?:string} $options
     * @return $this
     */
    public function add(string $name, $value, array $options = []): self
    {
        $this->bag[$name] = new SetCookie($name, $value, $options + $this->default_options);

        return $this;
    }

    /**
     * Append cookies to PSR-7 HTTP Response Set-Cookie header
     */
    public function appendTo(ResponseInterface $response, ?int $now = null): ResponseInterface
    {
        $cookie_lines = $this->parseLines($response->getHeader('Set-Cookie'));

        if ($now === null) {
            $now = \time();
        }

        foreach ($this->bag as $name => $cookie) {
            $cookie_lines[$name] = $cookie->compileHeaderLine($now);
        }

        return $response->withHeader('Set-Cookie', \array_values($cookie_lines));
    }

    /**
     * @return int
     */
    public function count()
    {
        return \count($this->bag);
    }

    /**
     * @return $this
     */
    public function delete(string $name): self
    {
        unset($this->bag[$name]);

        return $this;
    }

    public function has(string $name): bool
    {
        return isset($this->bag[$name]);
    }

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
     * @param string[] $cookie_lines
     * @return array<string,string>
     */
    public function parseLines(array $cookie_lines): array
    {
        $parsed = [];
        foreach ($cookie_lines as $i => $line) {
            $name = \explode('=', $line, 2)[0] ?? (string)$i;
            $parsed[$name] = $line;
        }

        return $parsed;
    }

    /**
     * Set cookies to PSR-7 HTTP Response Set-Cookie header
     */
    public function setTo(ResponseInterface $response, ?int $now = null): ResponseInterface
    {
        if ($now === null) {
            $now = \time();
        }

        $cookie_lines = [];
        foreach ($this->bag as $name => $cookie) {
            $cookie_lines[$name] = $cookie->compileHeaderLine($now);
        }

        return $response->withHeader('Set-Cookie', \array_values($cookie_lines));
    }
}
