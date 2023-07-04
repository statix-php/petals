<?php

namespace Statix\Petals;

use Statix\Petals\Directives\Directives;
use Statix\Petals\Directives\CompilesCustomElements;
use Statix\Petals\Directives\CompilesEchoDirectives;
use Statix\Petals\Directives\CompilesForLoopDirectives;
use Statix\Petals\Directives\CompilesVerbatimDirectives;
use Statix\Petals\Directives\CompilesConditionalDirectives;
use Statix\Petals\Contracts\TemplatingEngine as TemplatingEngineContract;

class TemplatingEngine implements TemplatingEngineContract
{
    use Directives,
        CompilesVerbatimDirectives,
        CompilesConditionalDirectives,
        CompilesEchoDirectives,
        CompilesForLoopDirectives,
        CompilesCustomElements;

    protected array $sharedData = [];

    protected array $data = [];

    protected array $directives = [
        '@include' => 'compileInclude',
        '@section' => 'compileSection',
        '@endsection' => 'compileEndSection',
        '@extends' => 'compileExtends',
        '@php' => 'compilePhp',
        '@endphp' => 'compileEndPhp',
        '@yield' => 'compileYield',
    ];

    public function __construct(
        private string|array $templates,
        private string $cachePath,
        private string $extension = '.blade.php',
        private bool $cache = true,
    ) {
        // ensure that the cache path exists
        if (! file_exists($this->cachePath)) {
            mkdir($this->cachePath, 0777, true);
        }

        // ensure that the cache path is writable
        if (! is_writable($this->cachePath)) {
            throw new \Exception("Cache path {$this->cachePath} is not writable.");
        }

        // loop through the traits and call the boot method for each trait
        foreach (class_uses($this) as $trait) {
            $method = is_object($trait) ? get_class($trait) : $trait;
            $method = 'boot'.basename(str_replace('\\', '/', $method));

            if (method_exists($this, $method)) {
                $this->{$method}();
            }
        }
    }

    public function clearCache(): void
    {
        $files = glob($this->cachePath.'/*');

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    /**
     * Compile the given template and return the path to the compiled php file.
     *
     * @throws \Exception
     */
    public function compile(string $template): string
    {
        $templatePath = $this->getTemplatePath($template);

        // get the compiled path
        $compiledPath = $this->getCompiledPath($template);

        // if the compiled path does not exist or the template has been modified since the last compile, compile the template
        if (! file_exists($compiledPath) || filemtime($templatePath) > filemtime($compiledPath) || ! $this->cache) {
            // get the template contents
            $templateContents = file_get_contents($templatePath);

            // compile the template contents
            $compiledContents = $this->compileString($templateContents);

            // append a php comment with the path variable to the compiled template
            $compiledContents .= '<?php /** __template_path__: '.realpath($templatePath).' */ ?>';

            // write the compiled contents to the compiled path
            file_put_contents($compiledPath, trim($compiledContents));
        }

        return $compiledPath;
    }

    /**
     * Compile the given string and return the compiled string.
     *
     * @throws \Exception
     */
    public function compileString(string $template): string
    {
        return trim($this->compileDirectives($template));
    }

    /**
     * Compile the given template and return the compiled string.
     */
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

    /**
     * Get the path where the compiled template or string will be created.
     * The path is based on the template path and the last modified time of the template.
     * If the template does not exist, the last modified time is the current time.
     */
    public function getCompiledPath(string $template): string
    {
        $templatePath = $this->getTemplatePath($template);

        // generate a unique name for the compiled template that accounts for the template path and the last modified time
        $compiledName = md5($templatePath.'-'.filemtime($templatePath)).'.php';

        return $this->cachePath.DIRECTORY_SEPARATOR.$compiledName;
    }

    public function directive(string $name, $handler): void
    {
        // if the handler is an array, then the first item is the class and the second item is the method
        if (is_array($handler)) {
            $handler = function ($template) use ($handler) {
                return $handler[0]->{$handler[1]}($template);
            };
        }

        $this->directives[$name] = $handler;
    }

    /**
     * Get the path to the given template.
     *
     * If the given template does not have an extension, the default extension which is .blade.php will be added.
     */
    public function getTemplatePath(string $template): string
    {
        // if the given template has no extension, add the default extension which is .blade.php
        if (pathinfo($template, PATHINFO_EXTENSION) === '') {
            $template .= $this->extension;
        }

        // if the templates directory is an array, loop through the templates directories and check if the template exists in any of the directories, if it does, return the path to the template
        if (is_array($this->templates)) {
            foreach ($this->templates as $templatesDirectory) {
                $templatePath = realpath($templatesDirectory.DIRECTORY_SEPARATOR.$template);

                if ($templatePath !== false) {
                    return $templatePath;
                }
            }

            // if the template does not exist in any of the templates directories, throw an exception
            throw new \Exception("Template {$template} does not exist.");
        }

        $templatePath = realpath($this->templates.DIRECTORY_SEPARATOR.$template);

        // if the template does not exist, throw an exception
        if ($templatePath === false) {
            throw new \Exception("Template {$template} does not exist.");
        }

        return $templatePath;
    }

    /**
     * Render the given template with the given data and get the contents back.
     */
    public function render(string $template, array $data = []): string
    {
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

        (function () use ($compiledPath) {
            // dd(array_merge($this->sharedData, $this->data));

            // extract the data to variables
            extract(array_merge($this->sharedData, $this->data));


            // include the compiled template
            include $compiledPath;
        })();

        // get the contents of the output buffer and end the buffer
        $contents = ob_get_clean();

        // return the contents
        return $contents;
    }

    /**
     * Render the given string with the given data and get the contents back.
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

    /**
     * Render the given template with the given data and write the contents to the given path.
     * If the path does not exist, it will be created.
     * If the path exists, it will be overwritten.
     * Returns the path.
     */
    public function renderTo(string $path, string $template, array $data = []): string
    {
        $content = $this->render($template, $data);

        // make sure the path exists and if it does not, create it and any parent directories
        if (! file_exists(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        // write the contents to the path
        file_put_contents($path, $content);

        return $path;
    }

    /**
     * Render the given string with the given data and write the contents to the given path.
     * If the path does not exist, it will be created.
     * If the path exists, it will be overwritten.
     * Returns the path.
     */
    public function renderStringTo(string $path, string $template, array $data = []): string
    {
        $content = $this->renderString($template, $data);

        // make sure the path exists and if it does not, create it and any parent directories
        if (! file_exists(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        // write the contents to the path
        file_put_contents($path, $content);

        return $path;
    }

    /**
     * Share data with all templates.
     */
    public function share(array $data): void
    {
        $this->sharedData = array_merge($this->sharedData, $data);
    }

    /**
     * Check if the given template exists.
     */
    public function templateExists(string $template): bool
    {
        return file_exists($this->getTemplatePath($template));
    }
}
