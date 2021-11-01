<?php

namespace Bag2\Cookie;

use DomainException;

/**
 * @phpstan-import-type options from CookieEmitter
 */
final class CookieOvenBuilderTest extends TestCase
{
    public function test(): void
    {
        $default_options = [
            'path' => '/',
            'secure' => true,
            'httponly' => true,
        ];

        $subject = new CookieOvenBuilder();

        $this->assertEquals($default_options, $subject->getOptions());

        $this->assertEquals([
            'expires' => 1234567890,
        ] + $default_options, $subject->withExpires(1234567890)->getOptions());

        $this->assertEquals([
            'domain' => 'cookie.example.com',
        ] + $default_options, $subject->withDomain('cookie.example.com')->getOptions());

        $this->assertEquals([
            'secure' => false,
        ] + $default_options, $subject->withSecure(false)->getOptions());

        $this->assertEquals([
            'httponly' => false,
        ] + $default_options, $subject->withHttpOnly(false)->getOptions());

        $this->assertEquals([
            'samesite' => 'Lax',
        ] + $default_options, $subject->withSameSite('Lax')->getOptions());

        $this->assertSame(
            [
                'expires' => 1234567890,
                'domain' => 'cookie.example.com',
                'path' => '/',
                'secure' => false,
                'httponly' => false,
                'samesite' => 'Lax',
            ],
            $subject
                ->withExpires(1234567890)
                ->withDomain('cookie.example.com')
                ->withSecure(false)
                ->withHttpOnly(false)
                ->withSameSite('Lax')
                ->getOptions()
        );

        $this->assertSame(
            [
                'expires' => 1234567890,
                'domain' => 'cookie.example.com',
                'path' => '/',
                'secure' => true,
                'httponly' => false,
            ],
            $subject
                ->withExpires(1234567890)
                ->withDomain('cookie.example.com')
                ->withSecure(false)
                ->withHttpOnly(false)
                ->withSecure(true)
                ->withSameSite('Lax')
                ->withSameSite(null)
                ->getOptions()
        );
    }
}
