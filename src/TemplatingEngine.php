<?php

namespace Statix\Petals;

use Statix\Petals\Contracts\TemplatingEngine as TemplatingEngineContract;
use Statix\Petals\Directives\Directives;

class TemplatingEngine implements TemplatingEngineContract
{
    use Directives;

    protected array $data = [];

    protected array $directives = [
        '@if' => 'compileIf',
        '@elseif' => 'compileElseIf',
        '@else' => 'compileElse',
        '@endif' => 'compileEndIf',
        '@foreach' => 'compileForeach',
        '@endforeach' => 'compileEndForeach',
        '@for' => 'compileFor',
        '@endfor' => 'compileEndFor',
        '@include' => 'compileInclude',
        '@section' => 'compileSection',
        '@endsection' => 'compileEndSection',
        '@extends' => 'compileExtends',
        '@verbatim' => 'compileVerbatim',
        '{{--' => 'compileComment',
        '{{' => 'compileEcho',
        '{!!' => 'compileRawEcho',
        '@{{' => 'compileVerbatimEcho',
        '@php' => 'compilePhp',
        '@endphp' => 'compileEndPhp',
        '@yield' => 'compileYield',
    ];

    public function __construct(
        private string $templates,
        private string $cachePath,
    ) {
        // ensure that the cache path exists
        if (! file_exists($this->cachePath)) {
            mkdir($this->cachePath, 0777, true);
        }

        // ensure that the cache path is writable
        if (! is_writable($this->cachePath)) {
            throw new \Exception("Cache path {$this->cachePath} is not writable.");
        }
    }

    /**
     * Compile the given template and return the path to the compiled php file.
     *
     * @param  string  $template
     * @return string
     */
    public function compile(string $template): string
    {
        // get the compiled path
        $compiledPath = $this->getCompiledPath($template);

        $templatePath = $this->getTemplatePath($template);

        // if the compiled path does not exist or the template has been modified since the last compile, compile the template
        if (! file_exists($compiledPath) || filemtime($templatePath) > filemtime($compiledPath)) {
            // get the template contents
            $templateContents = file_get_contents($templatePath);

            // compile the template contents
            $compiledContents = $this->compileString($templateContents);

            // append a php comment with the path variable to the compiled template
            $compiledContents .= '<?php /** __template_path__: '.realpath($template).' */ ?>';

            // write the compiled contents to the compiled path
            file_put_contents($compiledPath, trim($compiledContents));
        }

        return $compiledPath;
    }

    /**
     * Compile the given string and return the compiled string.
     *
     * @param  string  $template
     * @return string
     */
    public function compileString(string $template): string
    {
        return trim($this->compileDirectives($template));
    }

    protected function compileDirectives(string $template): string
    {
        // loop through the directives and check if the template contains the directive before compiling it, if it does not contain the directive, continue to the next directive
        foreach ($this->directives as $directive => $method) {
            if (strpos($template, $directive) === false) {
                continue;
            }

            // check if the method is a closure and if it is, call it, elseif check if a method exists for the directive and if it does, call it
            if ($method instanceof \Closure) {
                $template = $method($template);
            } elseif (method_exists($this, $method)) {
                $template = $this->$method($template);
            }
        }

        return $template;
    }

    protected function getCompiledPath(string $template): string
    {
        // generate a unique name for the compiled template that accounts for the template path and the last modified time
        $compiledName = md5($template).'.php';

        // return the compiled path
        return $this->cachePath.DIRECTORY_SEPARATOR.$compiledName;
    }

    protected function getTemplatePath(string $template): string
    {
        // if the given template has no extension, add the default extension which is .blade.php
        if (pathinfo($template, PATHINFO_EXTENSION) === '') {
            $template .= '.blade.php';
        }

        return realpath($this->templates.DIRECTORY_SEPARATOR.$template);
    }

    /**
     * Render the given template with the given data and get the contents back.
     *
     * @param  string  $template
     * @param  array  $data
     * @return string
     */
    public function render(string $template, array $data = []): string
    {
        $templatePath = $this->getTemplatePath($template);

        // if the template does not exist, throw an exception
        if (! file_exists($templatePath)) {
            throw new \Exception("Template {$template} does not exist.");
        }

        // assign the data to the a protected property
        $this->data = $data;

        // compile the template get the compiled path
        $compiledPath = $this->compile($template);

        // render the compiled template and capture the contents
        $contents = $this->renderAndCaptureCompiledFile($compiledPath);

        // return the contents
        return $contents;
    }

    protected function renderAndCaptureCompiledFile(string $compiledPath): string
    {
        // start output buffering
        ob_start();

        // extract the data to variables
        extract($this->data);

        // include the compiled template
        include $compiledPath;

        // get the contents of the output buffer
        $contents = ob_get_contents();

        // end output buffering
        ob_end_clean();

        // return the contents
        return $contents;
    }

    /**
     * Render the given string with the given data and get the contents back.
     *
     * @param  string  $template
     * @param  array  $data
     * @return string
     */
    public function renderString(string $template, array $data = []): string
    {
        // compile the template
        $compiled = $this->compileString($template);

        // write the compiled template to a temporary file
        $compiledPath = tempnam(sys_get_temp_dir(), 'blade');

        // write the compiled template to the temporary file
        file_put_contents($compiledPath, $compiled);

        // assign the data to the a protected property
        $this->data = $data;

        // render the compiled template and capture the contents
        $contents = $this->renderAndCaptureCompiledFile($compiledPath);

        // delete the temporary file
        unlink($compiledPath);

        return $contents;
    }
}
