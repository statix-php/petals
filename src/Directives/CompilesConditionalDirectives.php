<?php

namespace Statix\Petals\Directives;

trait CompilesConditionalDirectives
{
    protected function bootCompilesConditionalDirectives(): void
    {
        $directives = [
            '@if' => 'compileIf',
            '@elseif' => 'compileElseIf',
            '@else' => 'compileElse',
            '@endif' => 'compileEndIf',
            '@unless' => 'compileUnless',
            '@endunless' => 'compileEndUnless',
            '@isset' => 'compileIsset',
            '@endisset' => 'compileEndIsset',
            '@empty' => 'compileEmpty',
            '@endempty' => 'compileEndEmpty',
        ];

        foreach ($directives as $directive => $method) {
            $this->directive($directive, [$this, $method]);
        }
    }

    protected function compileIf(string $expression): string
    {
        // use a regex to find all @if() and @if () and replace them with <?php if ():, allow expressions to be mutliple lines
        $expression = trim(preg_replace('/@if\s*\(([^()]*(?:\([^()]*\))*[^()]*)\)/s', '<?php if ($1): ?>', $expression));

        return $expression;
    }

    protected function compileElse(string $expression): string
    {
        // use a regex to find all @else() statements and replace them with <?php else:
        $expression = trim(preg_replace('/@else/', '<?php else: ?>', $expression));

        return $expression;
    }

    /**
     * Compile @elseif() statements into <?php elseif (): ?> statements.
     */
    protected function compileElseIf(string $expression): string
    {
        // use a regex to find all @elseif() and @elseif () and replace them with <?php elseif ():, allow expressions to be mutliple lines
        $expression = trim(preg_replace('/@elseif\s*\(([^()]*(?:\([^()]*\))*[^()]*)\)/s', '<?php elseif ($1): ?>', $expression));

        return $expression;
    }

    /**
     * Compile @endif() statements into <?php endif; ?> statements.
     */
    protected function compileEndIf(string $expression): string
    {
        // use a regex to find all @endif statements and replace them with <?php endif;
        $expression = trim(preg_replace('/@endif/', '<?php endif; ?>', $expression));

        return $expression;
    }

    /**
     * Compile @unless() statements into <?php if (!): ?> statements.
     */
    protected function compileUnless(string $expression): string
    {
        // use a regex to find all @unless() and @unless () and replace them with <?php if (!):
        $expression = trim(preg_replace('/@unless\s*\(([^()]*(?:\([^()]*\))*[^()]*)\)/s', '<?php if (!$1): ?>', $expression));

        return $expression;
    }

    /**
     * Compile @endunless() statements into <?php endif; ?> statements.
     */
    protected function compileEndUnless(string $expression): string
    {
        // use a regex to find all @endunless statements and replace them with <?php endif;
        $expression = trim(preg_replace('/@endunless/', '<?php endif; ?>', $expression));

        return $expression;
    }

    /**
     * Compile @isset() statements into <?php if (isset()): ?> statements.
     */
    protected function compileIsset(string $expression): string
    {
        // use a regex to find all @isset() and @isset () and replace them with <?php if (isset()):
        $expression = trim(preg_replace('/@isset\s*\(([^()]*(?:\([^()]*\))*[^()]*)\)/s', '<?php if (isset($1)): ?>', $expression));

        return $expression;
    }

    /**
     * Compile @endisset() statements into <?php endif; ?> statements.
     */
    protected function compileEndIsset(string $expression): string
    {
        // use a regex to find all @endisset statements and replace them with <?php endif;
        $expression = trim(preg_replace('/@endisset/', '<?php endif; ?>', $expression));

        return $expression;
    }

    /**
     * Compile @empty() statements into <?php if (empty()): ?> statements.
     */
    protected function compileEmpty(string $expression): string
    {
        // use a regex to find all @empty() and @empty () and replace them with <?php if (empty()):
        $expression = trim(preg_replace('/@empty\s*\(([^()]*(?:\([^()]*\))*[^()]*)\)/s', '<?php if (empty($1)): ?>', $expression));

        return $expression;
    }

    /**
     * Compile @endempty() statements into <?php endif; ?> statements.
     */
    protected function compileEndEmpty(string $expression): string
    {
        // use a regex to find all @endempty statements and replace them with <?php endif;
        $expression = trim(preg_replace('/@endempty/', '<?php endif; ?>', $expression));

        return $expression;
    }
}
