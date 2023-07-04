<?php

use Statix\Petals\TemplatingEngine;

// require the composer autoloader
require __DIR__.'/vendor/autoload.php';

// create a new instance of the templating engine
$engine = new TemplatingEngine(
    __DIR__.'/tests/templates',
    __DIR__.'/tests/cache',
    cache: false,
);

$path = __DIR__.'/tests/cache/rendered/output.html';

dd($engine->render('app', [
    'name' => 'wyatt',
]));
