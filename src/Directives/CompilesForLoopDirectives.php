<?php

namespace Statix\Petals\Directives;

trait CompilesForLoopDirectives
{
    protected function bootCompilesForLoopDirectives(): void
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

    protected function compileForeach(string $template): string
    {
        $template = trim(preg_replace('/@foreach\s*\(([^()]*(?:\([^()]*\))*[^()]*)\)/s', '<?php foreach ($1): ?>', $template));

        // $template = trim(preg_replace('/@@foreach/', '@foreach', $template));

        return $template;
    }

    protected function compileEndForeach(string $template): string
    {
        // use a regex to find all @endforeach() statements and replace them with <?php endforeach;
        $template = trim(preg_replace('/@endforeach/', '<?php endforeach; ?>', $template));

        // $template = trim(preg_replace('/@@endforeach/', '@endforeach', $template));

        return $template;
    }

    protected function compileFor(string $template): string
    {
        // use a regex to find all @for() statements and replace them with <?php for ():
        $template = trim(preg_replace('/@for\s*\(([^()]*(?:\([^()]*\))*[^()]*)\)/s', '<?php for ($1): ?>', $template));

        $template = trim(preg_replace('/@@for/', '@for', $template));

        return $template;
    }

    protected function compileEndFor(string $template): string
    {
        // use a regex to find all @endfor() statements and replace them with <?php endfor;
        $template = trim(preg_replace('/@endfor/', '<?php endfor; ?>', $template));

        // $template = trim(preg_replace('/@@endfor/', '@endfor', $template));

        return $template;
    }
}
