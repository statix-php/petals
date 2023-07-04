<?php

namespace Statix\Petals\Directives;

trait CompilesEchoDirectives
{
    protected function bootCompilesEchoDirectives(): void
    {
        $directives = [
            '{{--' => 'compileComment',
            '{{' => 'compileEcho',
            '{!!' => 'compileRawEcho',
            '@{{' => 'compileVerbatimEcho',
        ];

        foreach ($directives as $directive => $method) {
            $this->directive($directive, [$this, $method]);
        }
    }

    protected function compileComment(string $template): string
    {
        // use a regex to find all {{--}} statements and replace them with empty string
        // revise the regex to ensure that statements that start with @{{ are not replaced
        $template = trim(preg_replace('/{{--\s*(?!@)(.*?)\s*--}}/', '', $template));

        return $template;
    }

    protected function compileEcho(string $template): string
    {
        // use a regex to find all {{ }} statements and replace them with <?php echo htmlspecialchars($1);
        $template = trim(preg_replace('/(?<!@){{\s*(.*?)\s*}}/', '<?php echo @htmlspecialchars($1); ?>', $template));

        return $template;
    }

    protected function compileRawEcho(string $template): string
    {
        // use a regex to find all {{!! !!}} statements and replace them with <?php echo $1;
        $template = trim(preg_replace('/{!!\s*(.*?)\s*!!}/', '<?php echo $1; ?>', $template));

        return $template;
    }

    protected function compileVerbatimEcho(string $template): string
    {
        $template = trim(preg_replace('/@{{\s*([\s\S]*?)\s*}}/s', '{{ $1 }}', $template));

        return $template;
    }
}
