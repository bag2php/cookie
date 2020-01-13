<?php

declare(strict_types=1);

use Bag2\Cookie\Oven;
use Nyholm\Psr7\Factory\Psr17Factory;

require_once __DIR__ . '/../vendor/autoload.php';

$factory = new Psr17Factory;
$response = $factory->createResponse()
    ->withHeader('Content-Type', 'text/html; charset=UTF-8')
    ->withBody($factory->createStream((function () {
        \ob_start() ?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>Bag2\Cookie</title>
    </head>
    <body>
        <h1>Bag2\Cookie Sample page</h1>

    </body>
</html>
<?php return ob_get_clean(); })()));

$now = new DateTimeImmutable();
$cookie = new Oven(['path' => '/', 'httponly' => true, 'samesite' => 'Strict']);
$cookie->add('Name', 'John', ['expires' => $now->getTimestamp() + 120]);

//Bag2\Cookie\emit($cookie);

$response = $cookie->setTo($response, $now->getTimestamp());

(new \Laminas\HttpHandlerRunner\Emitter\SapiEmitter())->emit($response);
