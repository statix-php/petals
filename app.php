<?php

use Statix\Petals\TemplatingEngine;

// require the composer autoloader
require __DIR__.'/vendor/autoload.php';

// create a new instance of the templating engine
$engine = new TemplatingEngine(
    templates: __DIR__.'/tests/templates',
    cachePath: __DIR__.'/tests/cache',
    cache: false,
);

dd($engine->render('app', [
    'name' => '<p>Hello world</p><script>alert("name")</script>',
    'records' => [],
]));
