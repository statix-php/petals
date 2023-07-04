<?php

namespace Statix\Petals\Directives;

trait CompilesCustomElements
{
    protected function bootCompilesCustomElements(): void
    {
        $directives = [
            '<x-' => 'compileCustomElements',
        ];

        foreach ($directives as $directive => $method) {
            $this->directive($directive, [$this, $method]);
        }
    }

    protected function compileCustomElements(string $template): string
    {
        // todoooo....

        return $template;
    }
}
