<?php

use Statix\Petals\Contracts\TemplatingEngine as TemplatingEngineContract;
use Statix\Petals\TemplatingEngine;

// test it implements the TemplatingEngineContract
it('implements the TemplatingEngineContract', function () {
    $engine = new TemplatingEngine(
        templates: __DIR__.'/templates',
        cachePath: __DIR__.'/cache',
    );

    expect($engine)->toBeInstanceOf(TemplatingEngineContract::class);
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
        'getTemplatePath',
        'getCompiledPath',
        'clearCache',
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

// test it compiles @verbatim directives
it('compiles @verbatim directives', function () {
    $engine = new TemplatingEngine(
        templates: __DIR__.'/templates',
        cachePath: __DIR__.'/cache',
    );

    $compiled = $engine->compileString('@verbatim Hello {{ $name }} @endverbatim');

    expect($compiled)->toBe(
        'Hello {{ $name }}'
    );
});

// test it compiles multiline @verbatim directives
it('compiles multiline @verbatim directives', function () {
    $engine = new TemplatingEngine(
        templates: __DIR__.'/templates',
        cachePath: __DIR__.'/cache',
    );

    $compiled = $engine->compileString('@verbatim
        <div class="container">
            Hello, {{ name }}.
        </div>
    @endverbatim');

    expect($compiled)->toBe(
        '<div class="container">
            Hello, {{ name }}.
        </div>'
    );
});

// test it compiles @if directives
it('compiles @if directives', function () {
    $engine = new TemplatingEngine(
        templates: __DIR__.'/templates',
        cachePath: __DIR__.'/cache',
    );

    $compiled = $engine->compileString('@if(true) Hello @endif');

    expect($compiled)->toBe(
        '<?php if(true): ?> Hello <?php endif; ?>'
    );

    $compiled = $engine->compileString('@if(false) Hello @else World @endif');

    expect($compiled)->toBe(
        '<?php if(false): ?> Hello <?php else: ?> World <?php endif; ?>'
    );

    $compiled = $engine->compileString('@if(false) Hello @elseif(true) World @endif');

    expect($compiled)->toBe(
        '<?php if(false): ?> Hello <?php elseif(true): ?> World <?php endif; ?>'
    );

    $compiled = $engine->compileString('@if(false) Hello @elseif(false) World @else Goodbye @endif');

    expect($compiled)->toBe(
        '<?php if(false): ?> Hello <?php elseif(false): ?> World <?php else: ?> Goodbye <?php endif; ?>'
    );

    $compiled = $engine->compileString("
        @if (count(\$records) === 1)
            I have one record!
        @elseif (count(\$records) > 1)
            I have multiple records!
        @else
            I don't have any records!
        @endif
    ");

    expect($compiled)->toBe(
        "<?php if(count(\$records) === 1): ?>
            I have one record!
        <?php elseif(count(\$records) > 1): ?>
            I have multiple records!
        <?php else: ?>
            I don't have any records!
        <?php endif; ?>"
    );

    // test it compiles @if directives with multiline conditions
    $compiled = $engine->compileString('@if(
        true
    ) Hello @endif');

    expect($compiled)->toBe(
        '<?php if(
        true
    ): ?> Hello <?php endif; ?>'
    );
});

// it renderes @if directives with multiline conditions
it('renders @if directives with multiline conditions', function () {
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

// it can compile @unless directives
it('can compile @unless directives', function () {
    $engine = new TemplatingEngine(
        templates: __DIR__.'/templates',
        cachePath: __DIR__.'/cache',
    );
    $compiled = $engine->compileString('@unless(true) Hello @endunless');

    expect($compiled)->toBe(
        '<?php if(!true): ?> Hello <?php endif; ?>'
    );

    $compiled = $engine->compileString('@unless(false) Hello @else World @endunless');

    expect($compiled)->toBe(
        '<?php if(!false): ?> Hello <?php else: ?> World <?php endif; ?>'
    );

    $compiled = $engine->compileString('@unless(false) Hello @elseif(true) World @endunless');

    expect($compiled)->toBe(
        '<?php if(!false): ?> Hello <?php elseif(true): ?> World <?php endif; ?>'
    );

    $compiled = $engine->compileString('@unless(false) Hello @elseif(false) World @else Goodbye @endunless');

    expect($compiled)->toBe(
        '<?php if(!false): ?> Hello <?php elseif(false): ?> World <?php else: ?> Goodbye <?php endif; ?>'
    );

    $compiled = $engine->compileString("
        @unless (count(\$records) === 1)
            I have one record!
        @elseif (count(\$records) > 1)
            I have multiple records!
        @else
            I don't have any records!
        @endunless
    ");

    // dd($compiled);

    expect($compiled)->toBe(
        "<?php if(!count(\$records) === 1): ?>
            I have one record!
        <?php elseif(count(\$records) > 1): ?>
            I have multiple records!
        <?php else: ?>
            I don't have any records!
        <?php endif; ?>"
    );

    // test it compiles @unless directives with multiline conditions
    $compiled = $engine->compileString('@unless(
        true
    ) Hello @endunless');

    expect($compiled)->toBe(
        '<?php if(!
        true
    ): ?> Hello <?php endif; ?>'
    );
});

// it compiles @isset directives
it('compiles @isset directives', function () {
    $engine = new TemplatingEngine(
        templates: __DIR__.'/templates',
        cachePath: __DIR__.'/cache',
    );
    $compiled = $engine->compileString('@isset($name) Hello @endisset');

    expect($compiled)->toBe(
        '<?php if(isset($name)): ?> Hello <?php endif; ?>'
    );

    $compiled = $engine->compileString('@isset($name) Hello @else World @endisset');

    expect($compiled)->toBe(
        '<?php if(isset($name)): ?> Hello <?php else: ?> World <?php endif; ?>'
    );

    // test it compiles @isset directives with multiline conditions
    $compiled = $engine->compileString('@isset(
        $name
    ) Hello @endisset');

    expect($compiled)->toBe(
        '<?php if(isset(
        $name
    )): ?> Hello <?php endif; ?>'
    );
});

// it compiles @empty directives
it('compiles @empty directives', function () {
    $engine = new TemplatingEngine(
        templates: __DIR__.'/templates',
        cachePath: __DIR__.'/cache',
    );
    $compiled = $engine->compileString('@empty($name) Hello @endempty');

    expect($compiled)->toBe(
        '<?php if(empty($name)): ?> Hello <?php endif; ?>'
    );

    $compiled = $engine->compileString('@empty($name) Hello @else World @endempty');

    expect($compiled)->toBe(
        '<?php if(empty($name)): ?> Hello <?php else: ?> World <?php endif; ?>'
    );

    // test it compiles @empty directives with multiline conditions
    $compiled = $engine->compileString('@empty(
        $name
    ) Hello @endempty');

    expect($compiled)->toBe(
        '<?php if(empty(
        $name
    )): ?> Hello <?php endif; ?>'
    );
});
