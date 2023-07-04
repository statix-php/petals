<?php

use Statix\Petals\TemplatingEngine;

// require the composer autoloader
require __DIR__.'/vendor/autoload.php';

// create a new instance of the templating engine
$engine = new TemplatingEngine(
    templates: [
        __DIR__.'/tests/templates',
    ],
    cachePath: __DIR__.'/tests/cache',
    // cache: false,
);

$engine->clearCache();

$start = microtime(true);

foreach (range(1, 100) as $item) {
    $engine->render('echos', [
        'name' => 'World',
    ]);
}

$end = microtime(true);

echo $end - $start . 's' . PHP_EOL;