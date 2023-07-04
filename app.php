<?php

use Statix\Petals\TemplatingEngine;

// require the composer autoloader
require __DIR__.'/vendor/autoload.php';

// create a new instance of the templating engine
$engine = new TemplatingEngine(
    templates: [
        __DIR__.'/tests/templates',
        __DIR__.'/tests/templates2',
    ],
    cachePath: __DIR__.'/tests/cache',
    cache: false,
);

$engine->clearCache();

dd($engine->render('t2', [
    'name' => '<p>Hello world</p><script>alert("name")</script>',
    'records' => [],
]));
