<?php

namespace Statix\Petals\Directives;

trait CompilesIfDirectives
{
    protected function bootCompilesIfDirectives(): void
    {
        $directives = [
            '@if' => 'compileIf',
            '@elseif' => 'compileElseIf',
            '@else' => 'compileElse',
            '@endif' => 'compileEndIf',
        ];

        foreach ($directives as $directive => $method) {
            $this->directive($directive, [$this, $method]);
        }
    }

    protected function compileIf(string $content): string
    {
        // use a regex to find all @if() and @if () and replace them with <?php if ():, allow expressions to be mutliple lines
        $content = trim(preg_replace('/@if\s*\(([^()]*(?:\([^()]*\))*[^()]*)\)/s', '<?php if ($1): ?>', $content));

        return $content;
    }

    protected function compileElse(string $content): string
    {
        // use a regex to find all @else() statements and replace them with <?php else:
        $content = trim(preg_replace('/@else/', '<?php else: ?>', $content));

        return $content;
    }

    /**
     * Compile @elseif() statements into <?php elseif (): ?> statements.
     */
    protected function compileElseIf(string $content): string
    {
        // use a regex to find all @elseif() and @elseif () and replace them with <?php elseif ():, allow expressions to be mutliple lines
        $content = trim(preg_replace('/@elseif\s*\(([^()]*(?:\([^()]*\))*[^()]*)\)/s', '<?php elseif ($1): ?>', $content));

        return $content;
    }

    /**
     * Compile @endif() statements into <?php endif; ?> statements.
     */
    protected function compileEndIf(string $content): string
    {
        // use a regex to find all @endif statements and replace them with <?php endif;
        $content = trim(preg_replace('/@endif/', '<?php endif; ?>', $content));

        return $content;
    }
}
