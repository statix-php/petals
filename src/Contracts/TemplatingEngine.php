<?php

namespace Statix\Petals\Contracts;

interface TemplatingEngine
{
    /**
     * Compile the given template and return the path to the compiled php file.
     *
     * @param  string  $template
     * @return string
     */
    public function compile(string $template): string;

    /**
     * Compile the given string and return the path to the compiled php file.
     *
     * @param  string  $template
     * @return string
     */
    public function compileString(string $template): string;

    /**
     * Render the given template with the given data and get the contents back.
     *
     * @param  string  $template
     * @param  array  $data
     * @return string
     */
    public function render(string $template, array $data = []): string;

    /**
     * Render the given string with the given data and get the contents back.
     *
     * @param  string  $template
     * @param  array  $data
     * @return string
     */
    public function renderString(string $template, array $data = []): string;
}
