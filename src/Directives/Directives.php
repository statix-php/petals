<?php

namespace Statix\Petals\Directives;

trait Directives
{
    protected function compileInclude(string $template): string
    {
        // use a regex to find all @include statements and then loop over the matches using a for loop and allow the include to be multiline
        preg_match_all('/@include\s*\((.*?)\)/s', $template, $matches);

        // loop over the matches
        for ($i = 0; $i < count($matches[0]); $i++) {
            // if the include statement has a comma in it then we need split the match into two parts, the template and the array of data
            if (strpos($matches[1][$i], ',') !== false) {
                $parts = explode(',', $matches[1][$i]);

                // implode all the parts of the array except the first one
                $data = implode(',', array_slice($parts, 1));

                $template = str_replace(
                    $matches[0][$i],
                    '<?php echo $this->render('.$parts[0].', array_merge($this->data, '.$data.')); ?>',
                    $template
                );
            } else {
                $template = str_replace($matches[0][$i], '<?php echo $this->render('.$matches[1][$i].', $this->data)); ?>', $template);
            }
        }

        return $template;
    }

    protected function compileExtends(string $template): string
    {
        // use a regex to find all @extends statements and then loop over the matches using a for loop
        preg_match_all('/@extends\s*\((.*?)\)/', $template, $matches);

        // throw an exception if there is more than one @extends statement
        if (count($matches[0]) > 1) {
            throw new \Exception('You can only have one @extends statement per template');
        }

        // if there is a match then remove the @extends statement from the template
        if (count($matches[0]) === 1) {
            $template = str_replace($matches[0][0], '', $template);
            // append the extends call to the end of the template
            $template .= '<?php $this->extends('.$matches[1][0].'); ?>';
        }

        return trim($template);
    }

    protected function startSection(string $name): void
    {
        // set the current section name
        $this->currentSection = $name;

        // start output buffering
        ob_start();
    }

    protected function endSection(): void
    {
        // get the contents of the output buffer
        $sectionContent = ob_get_clean();

        // set the section in the sections array
        $this->sections[$this->currentSection] = $sectionContent;

        // reset the current section
        $this->currentSection = null;
    }

    protected function compileSection(string $template): string
    {
        // use a regex to find all @section statements and then loop over the matches using a for loop
        preg_match_all('/@section\s*\((.*?)\)/', $template, $matches);

        // loop over the matches using a for loop
        for ($i = 0; $i < count($matches[0]); $i++) {
            // get the section name and trim any single quotes
            // $name = trim($matches[1][$i], "'");
            $name = $matches[1][$i];

            // replace the @section statement with <?php $this->startSection($name); using preg_replace
            $template = preg_replace('/@section\s*\((.*?)\)/', '<?php $this->startSection('.$name.'); ?>', $template, 1);
        }

        return trim($template);
    }

    protected function compileEndSection(string $template): string
    {
        // use a regex to find all @endsection statements and then loop over the matches using a for loop
        preg_match_all('/@endsection/', $template, $matches);

        // loop over the matches using a for loop
        for ($i = 0; $i < count($matches[0]); $i++) {
            // replace the @endsection statement with <?php $this->endSection(); using preg_replace
            $template = preg_replace('/@endsection/', '<?php $this->endSection(); ?>', $template);
        }

        return trim($template);
    }

    protected function extends(string $template): void
    {
        echo $this->render($template, $this->data);
    }

    protected function compilePhp(string $template): string
    {
        // use a regex to find only @php statements, ignoring @endphp
        preg_match_all('/@php/', $template, $matches);

        // loop over the matches using a for loop
        for ($i = 0; $i < count($matches[0]); $i++) {
            // replace the @php statement with <?php
            $template = preg_replace('/@php/', '<?php ', $template);
        }

        return trim($template);
    }

    protected function compileEndPhp(string $template): string
    {
        // use a regex to find all @end statements and then loop over the matches using a for loop
        preg_match_all('/@endphp/', $template, $matches);

        // loop over the matches using a for loop
        for ($i = 0; $i < count($matches[0]); $i++) {
            // replace the @end statement with <?php
            $template = preg_replace('/@endphp/', ' ?>', $template);
        }

        return trim($template);
    }

    protected function compileYield(string $template): string
    {
        // use a regex to find all @yield statements and then loop over the matches using a for loop
        preg_match_all('/@yield\s*\((.*?)\)/', $template, $matches);

        // loop over the matches using a for loop
        for ($i = 0; $i < count($matches[0]); $i++) {
            // get the section name
            $name = $matches[1][$i];

            // replace the @yield statement with <?php echo $this->sections[$name] ?? '';
            $template = preg_replace('/@yield\s*\((.*?)\)/', '<?php echo $this->sections['.$name.'] ?? ""; ?>', $template, 1);
        }

        return trim($template);
    }
}
