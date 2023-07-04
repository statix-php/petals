<?php

namespace Statix\Petals\Directives;

trait CompilesForLoopDirectives
{
    public function bootCompilesForLoopDirectives(): void
    {
        $directives = [
            '@foreach' => 'compileForeach',
            '@endforeach' => 'compileEndForeach',
            '@for' => 'compileFor',
            '@endfor' => 'compileEndFor',
        ];

        foreach ($directives as $directive => $method) {
            $this->directive($directive, [$this, $method]);
        }
    }

    public function compileForeach(string $template): string
    {
        // use a regex to find all @foreach() statements and replace them with <?php foreach ():
        $template = trim(preg_replace('/@foreach\s*\(([^()]*(?:\([^()]*\))*[^()]*)\)/s', '<?php foreach ($1): ?>', $template));

        return $template;
    }

    public function compileEndForeach(string $template): string
    {
        // use a regex to find all @endforeach() statements and replace them with <?php endforeach;
        $template = trim(preg_replace('/@endforeach/', '<?php endforeach; ?>', $template));

        return $template;
    }

    public function compileFor(string $template): string
    {
        // use a regex to find all @for() statements and replace them with <?php for ():
        $template = trim(preg_replace('/@for\s*\(([^()]*(?:\([^()]*\))*[^()]*)\)/s', '<?php for ($1): ?>', $template));

        return $template;
    }

    public function compileEndFor(string $template): string
    {
        // use a regex to find all @endfor() statements and replace them with <?php endfor;
        $template = trim(preg_replace('/@endfor/', '<?php endfor; ?>', $template));

        return $template;
    }
}
