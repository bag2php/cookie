# Bag2\Cookie

## PSR-7

```php
$now = \time();

$cookie = new Bag2\Cookie\Bag(['secure' => true, 'httponly' => true]);
$cookie->add('A', 'value 1', ['expires' => time() + 1200]);
$cookie->add('B', 'value 2', ['expires' => time() + 3600]);

$response = $cookie->setTo($response, $now);
var_dump($response->getHeader('Set-Cookie', $now));
// [
//   'Name1=value; expires=Sunday, 12-Jan-2020 08:25:56 UTC; Max-Age=3600',
//   'Name2=value; expires=Sunday, 12-Jan-2020 08:25:56 UTC; Max-Age=3600',
// ]

$response = $cookie->appendTo($response);
```
