<?php

declare(strict_types=1);

namespace Bag2\Cookie;

/**
 * An immutable object-oriented builder class for Oven
 *
 * This class has some *recommended* options:
 *
 * - path: '/'
 * - secure: true
 * - httponly: true
 *
 * Generally use the `with` method to disable them.
 *
 * NOTICE: According to general security knowledge, it is not recommended to
 * specify anything other than '/' for the cookie "path".
 * Therefore, the withPath() method is not intentionally implemented.
 *
 * @psalm-immutable
 * @psalm-external-mutation-free
 * @phpstan-import-type options from CookieEmitter
 */
class CookieOvenBuilder
{
    /** @phpstan-var 0|positive-int|null */
    private $expires;
    /** @phpstan-var non-empty-string */
    private $path;
    /** @phpstan-var non-empty-string|null */
    private $domain;
    /** @var bool */
    private $secure;
    /** @var bool */
    private $httponly;
    /** @phpstan-var 'Lax'|'None'|'Strict'|null */
    private $samesite;

    /**
     * @phpstan-param 0|positive-int|null $expires
     * @phpstan-param non-empty-string|null $domain
     * @phpstan-param non-empty-string $path
     * @phpstan-param 'Lax'|'None'|'Strict'|null $samesite
     */
    final public function __construct(
        ?int $expires = null,
        ?string $domain = null,
        string $path = '/',
        bool $secure = true,
        bool $httponly = true,
        ?string $samesite = null
    ) {
        $this->expires = $expires;
        $this->domain = $domain;
        $this->path = $path;
        $this->secure = $secure;
        $this->httponly = $httponly;
        $this->samesite = $samesite;
    }

    /**
     * @phpstan-pure
     */
    public function build(): Oven
    {
        return new Oven($this->getOptions());
    }

    /**
     * @phpstan-return options
     */
    public function getOptions(): array
    {
        $options = [
            'expires' => $this->expires,
            'domain' => $this->domain,
            'path' => $this->path,
            'secure' => $this->secure,
            'httponly' => $this->httponly,
            'samesite' => $this->samesite,
        ];

        if ($options['expires'] === null) {
            unset($options['expires']);
        }
        if ($options['domain'] === null) {
            unset($options['domain']);
        }
        if ($options['samesite'] === null) {
            unset($options['samesite']);
        }

        return $options;
    }

    /**
     * @phpstan-param 0|positive-int $expires
     * @return static
     */
    public function withExpires(int $expires): self
    {
        return new static($expires, $this->domain, $this->path, $this->secure, $this->httponly, $this->samesite);
    }

    /**
     * @phpstan-param non-empty-string $domain
     * @return static
     */
    public function withDomain(string $domain): self
    {
        return new static($this->expires, $domain, $this->path, $this->secure, $this->httponly, $this->samesite);
    }

    /**
     * @return static
     */
    public function withSecure(bool $secure): self
    {
        return new static($this->expires, $this->domain, $this->path, $secure, $this->httponly, $this->samesite);
    }

    /**
     * @return static
     */
    public function withHttpOnly(bool $httponly): self
    {
        return new static($this->expires, $this->domain, $this->path, $this->secure, $httponly, $this->samesite);
    }

    /**
     * @phpstan-param 'Lax'|'None'|'Strict'|null $samesite
     * @return static
     */
    public function withSameSite(?string $samesite): self
    {
        return new static($this->expires, $this->domain, $this->path, $this->secure, $this->httponly, $samesite);
    }
}
