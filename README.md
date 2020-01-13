# Bag2\Cookie

## Motivation

We have some problems with HTTP `Set-Cookie` response header in PHP.

 * PHP's `setcookie()` function started supporting ['SameSite' cookie attribute](https://caniuse.com/#feat=same-site-cookie-attribute) from 7.3.0
 * Sending `SameSite` cookies under PHP 7.3.0 requires an unusual hack
 * PSR-7 does not provide high-level functions for Cookies
 * The `setcookie()` function is useless for the era of PSR-7

This package provides common features whether your project is **PSR-7 based** or **vanilla PHP** (direct call to `setcookie()` function).

## Examples

### Create Cookie Oven

**CookieOven** is an object that can hold multiple cookies.

```php
<?php

$now = \time();

$cookie = Bag2\Cookie\oven(['secure' => true, 'httponly' => true, 'samesite' => 'Strict']);
$cookie->add('NameA', 'value 1', ['expires' => $now + 1200]);
$cookie->add('NameB', 'value 2', ['expires' => $now + 3600]);
```

CookieOven manages cookies by key-value. Please note that CookieOven can only have one cookie with the same name.

`$default_options` passed to the CookieOven constructor is combined with `$option` passed to the `CookieOven::add()` method.

The `$options` received in the 3rd argument is compatible with `setcookie()` function added in PHP 7.3.  Pleese see [PHP: setcookie - Manual](https://www.php.net/setcookie).  All option names are lowercase.


### PSR-7

[PSR-7](https://www.php-fig.org/psr/psr-7/) is a HTTP message interfaces defined by [PHP-FIG](https://www.php-fig.org/).

```php
$response = $cookie->appendTo($response, $now);

// var_dump($response->getHeader('Set-Cookie'));
// => [
//   'Name1=value; expires=Sunday, 12-Jan-2020 08:25:56 UTC; Max-Age=3600',
//   'Name2=value; expires=Sunday, 12-Jan-2020 08:25:56 UTC; Max-Age=3600',
// ]
```

PSR-7 HTTP message objects are immutable.  If you are writing code on a PSR-7 compatible framework, you will probably just `return` this value.

**Tips for unit testing**: `Oven::appendTo()` and `Oven::setTo()` receive unixtime of the current time for `SetCookie::compileHeaderLine()`.  The reason is that the current time affects the cookie output.  The argument is optional, but if you want strict output value validation, inject the time externally.

### PHP `setcookie()` wrapper

If your project allows you to call the `setcookie()`, `header()` functions directly, it is a kind of **vanilla PHP**.

```php
Bag2\Cookie\emit($cookie);
```

Inside this function, the Emitter for the PHP version is selected, and the `setcookie()` function that matches the version specification is called.

## Copyright

This package is licenced under [Apache License 2.0][Apache-2.0].

> Copyright 2020 Baguette HQ
>
> Licensed under the Apache License, Version 2.0 (the "License");
> you may not use this file except in compliance with the License.
> You may obtain a copy of the License at
>
>     http://www.apache.org/licenses/LICENSE-2.0
>
> Unless required by applicable law or agreed to in writing, software
> distributed under the License is distributed on an "AS IS" BASIS,
> WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
> See the License for the specific language governing permissions and
> limitations under the License.

[Apache-2.0]: https://www.apache.org/licenses/LICENSE-2.0
