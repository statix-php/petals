<?php

use Statix\Petals\Contracts\TemplatingEngine as TemplatingEngineContract;
use Statix\Petals\TemplatingEngine;

it('it can be created', function () {
    $engine = new TemplatingEngine(
        templates: __DIR__.'/templates',
        cachePath: __DIR__.'/cache',
    );

    expect($engine)->toBeInstanceOf(TemplatingEngine::class);
});

it('creates the cache path if it does not exist', function () {
    $cachePath = __DIR__.'/does-not-exist-cache';

    expect(file_exists($cachePath))->toBeFalse();

    $engine = new TemplatingEngine(
        templates: __DIR__.'/templates',
        cachePath: $cachePath,
    );

    expect(file_exists($cachePath))->toBeTrue();

    // delete the cache path
    rmdir($cachePath);
});

// test it implements the TemplatingEngineContract
it('implements the TemplatingEngineContract', function () {
    $engine = new TemplatingEngine(
        templates: __DIR__.'/templates',
        cachePath: __DIR__.'/cache',
    );

    expect($engine)->toBeInstanceOf(TemplatingEngineContract::class);
});

// test it has a public compile method
it('has 4 public methods required to render or compile', function () {
    $engine = new TemplatingEngine(
        templates: __DIR__.'/templates',
        cachePath: __DIR__.'/cache',
    );

    $methods = [
        'compile',
        'compileString',
        'render',
        'renderString',
    ];

    foreach ($methods as $method) {
        expect(method_exists($engine, $method))->toBeTrue();
    }
});

// test when compiling a template that doesn't exist, it throws an exception
it('throws an exception when compiling a template that does not exist', function () {
    $engine = new TemplatingEngine(
        templates: __DIR__.'/templates',
        cachePath: __DIR__.'/cache',
    );

    $engine->compile('does-not-exist');
})->throws(\Exception::class);

// test the compiled file name is the md5 hash of the template name and the last modified time of the template
it('the compiled file name is the md5 hash of the template name and the last modified time of the template', function () {
    $engine = new TemplatingEngine(
        templates: __DIR__.'/templates',
        cachePath: __DIR__.'/cache',
    );

    $templatePath = $engine->getTemplatePath('base');

    $lastModified = filemtime($templatePath);

    $compiledName = md5($templatePath.'-'.$lastModified).'.php';

    $compiledPath = str_replace('/', '\\', $engine->getCompiledPath('base'));

    expect($compiledPath)->toBe(realpath(__DIR__.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.$compiledName));
});

// test when compiling a template that exists, it returns the path to the compiled file
it('returns the path to the compiled file when compiling a template that exists', function () {
    $engine = new TemplatingEngine(
        templates: __DIR__.'/templates',
        cachePath: __DIR__.'/cache',
    );

    $compiledPath = $engine->compile('base');

    expect(file_exists($compiledPath))->toBe(true);
});

// test it can compile a template string
it('can compile a template string', function () {
    $engine = new TemplatingEngine(
        templates: __DIR__.'/templates',
        cachePath: __DIR__.'/cache',
    );

    $compiled = $engine->compileString('Hello {{ $name }}');

    expect($compiled)->toBe(
        'Hello <?php echo @htmlspecialchars($name); ?>'
    );
});

// test it can render a template string
it('can render a template string', function () {
    $engine = new TemplatingEngine(
        templates: __DIR__.'/templates',
        cachePath: __DIR__.'/cache',
    );

    $rendered = $engine->renderString('Hello {{ $name }}', ['name' => 'World']);

    expect($rendered)->toBe('Hello World');
});

// test it can render a string with no directives
it('can render a string with no directives', function () {
    $engine = new TemplatingEngine(
        templates: __DIR__.'/templates',
        cachePath: __DIR__.'/cache',
    );

    $rendered = $engine->renderString('Hello World');

    expect($rendered)->toBe('Hello World');
});

// test it can render @if directives
it('can render @if directives', function () {
    $engine = new TemplatingEngine(
        templates: __DIR__.'/templates',
        cachePath: __DIR__.'/cache',
    );

    // test if directive with no else
    $rendered = $engine->renderString('@if(true) Hello @endif');

    expect($rendered)->toBe(' Hello ');

    // test if directive with else
    $rendered = $engine->renderString('@if(false) Hello @else World @endif');

    expect($rendered)->toBe(' World ');

    // test if directive with elseif
    $rendered = $engine->renderString('@if(false) Hello @elseif(true) World @endif');

    expect($rendered)->toBe(' World ');

    // test if directive with elseif and else
    $rendered = $engine->renderString('@if(false) Hello @elseif(false) World @else Goodbye @endif');

    expect($rendered)->toBe(' Goodbye ');
});

// test it can use variables in the @if directives to determine truthiness
it('can use variables in the @if directives to determine truthiness', function () {
    $engine = new TemplatingEngine(
        templates: __DIR__.'/templates',
        cachePath: __DIR__.'/cache',
    );

    // test if directive with no else
    $rendered = $engine->renderString('@if($name) Hello @endif', ['name' => 'World']);

    expect($rendered)->toBe(' Hello ');

    // test if directive with else
    $rendered = $engine->renderString('@if(!$name) Hello @else World @endif', ['name' => 'World']);

    expect($rendered)->toBe(' World ');

    // test if directive with elseif
    $rendered = $engine->renderString('@if(!$name) Hello @elseif($name) World @endif', ['name' => 'World']);

    expect($rendered)->toBe(' World ');

    // test if directive with elseif and else
    $rendered = $engine->renderString('@if(!$name) Hello @elseif(!$name) World @else Goodbye @endif', ['name' => 'World']);

    expect($rendered)->toBe(' Goodbye ');
});

// test @if directives can be nested
it('can nest @if directives', function () {
    $engine = new TemplatingEngine(
        templates: __DIR__.'/templates',
        cachePath: __DIR__.'/cache',
    );

    // test if directive with no else
    $rendered = $engine->renderString('@if(true) @if(true) Hello @endif @endif');

    expect($rendered)->toBe('  Hello  ');

    // test if directive with else
    $rendered = $engine->renderString('@if(true) @if(false) Hello @else World @endif @endif');

    expect($rendered)->toBe('  World  ');

    // test if directive with elseif
    $rendered = $engine->renderString('@if(true) @if(false) Hello @elseif(true) World @endif @endif');

    expect($rendered)->toBe('  World  ');

    // test if directive with elseif and else
    $rendered = $engine->renderString('@if(true) @if(false) Hello @elseif(false) World @else Goodbye @endif @endif');

    expect($rendered)->toBe('  Goodbye  ');
});

// test @if directives can accept multiline conditions
it('can accept multiline conditions in @if directives', function () {
    $engine = new TemplatingEngine(
        templates: __DIR__.'/templates',
        cachePath: __DIR__.'/cache',
    );

    // test if directive with no else
    $rendered = $engine->renderString('@if(
        true
    ) Hello @endif');

    expect($rendered)->toBe(' Hello ');

    // test if directive with else
    $rendered = $engine->renderString('@if(
        false
    ) Hello @else World @endif');

    expect($rendered)->toBe(' World ');

    // test if directive with elseif
    $rendered = $engine->renderString('@if(
        false
    ) Hello @elseif(
        true
    ) World @endif');

    expect($rendered)->toBe(' World ');

    // test if directive with elseif and else
    $rendered = $engine->renderString('@if(
        false
    ) Hello @elseif(
        false
    ) World @else Goodbye @endif');

    expect($rendered)->toBe(' Goodbye ');
});

// test it wont recomplie a template if it hasnt changed
it('wont recomplie a template if it hasnt changed', function () {
    $engine = new TemplatingEngine(
        templates: __DIR__.'/templates',
        cachePath: __DIR__.'/cache',
    );

    $compiledPath = $engine->compile('base');

    $lastModified = filemtime($compiledPath);

    $engine->compile('base');

    expect(filemtime($compiledPath))->toBe($lastModified);
});

// test we can set shared data to be included in all templates
it('can set shared data to be included in all templates', function () {
    $engine = new TemplatingEngine(
        templates: __DIR__.'/templates',
        cachePath: __DIR__.'/cache',
    );

    $engine->share(['name' => 'World']);

    $time = time();

    $rendered = $engine->renderString('Hello {{ $name }}, the time is: {{ $time }}', ['time' => $time]);

    expect($rendered)->toBe('Hello World, the time is: '.$time);
});