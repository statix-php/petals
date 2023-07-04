<?php

use Statix\Petals\TemplatingEngine;

// require the composer autoloader
require __DIR__.'/vendor/autoload.php';

// create a new instance of the templating engine
$engine = new TemplatingEngine(__DIR__.'/tests/templates', __DIR__.'/tests/cache');

$content = $engine->render('app', [
    'name' => 'Welcome',
    'nickname' => 'World',
    'email' => 'user@email.com',
]);

$path = __DIR__.'/tests/cache/rendered/output.html';

// make sure the file exists
if (! file_exists($path)) {
    touch($path);
}

// put the contents into a file
file_put_contents($path, $content);
