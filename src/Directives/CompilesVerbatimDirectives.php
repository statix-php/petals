<?php

namespace Statix\Petals\Directives;

trait CompilesVerbatimDirectives
{
    protected function bootCompilesVerbatimDirectives(): void
    {
        $directives = [
            '@verbatim' => 'compileVerbatim',
        ];

        foreach ($directives as $directive => $method) {
            $this->directive($directive, [$this, $method]);
        }
    }

    protected function compileVerbatim(string $template): string
    {
        // use a regex to find all @verbatim directives and capture all content between the @verbatim and @endverbatim directives, allowing the expression to span multiple lines
        preg_match_all('/@verbatim\s*([\s\S]*?)\s*@endverbatim/s', $template, $matches);

        // now loop over all the matches and replace the @verbatim directives with the captured content
        for ($i = 0; $i < count($matches[0]); $i++) {
            $template = str_replace(
                $matches[0][$i],
                preg_replace('/(?<!@){{\s*(.*?)\s*}}/', '@{{ $1 }}', $matches[1][$i]),
                $template
            );
        }

        return trim($template);
    }
}
