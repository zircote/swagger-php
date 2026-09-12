<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tools\Docs;

class Renderer
{
    public function preamble(string $title, ?string $snippetContent = null): string
    {
        $out = "# {$title} Reference\n";
        $out .= "\n";
        $out .= "This page is generated automatically from the `swagger-php` sources.\n";
        $out .= "\n";
        $out .= "For improvements head over to [GitHub](https://github.com/zircote/swagger-php) and create a PR ;)\n";

        if ($snippetContent) {
            $out .= "\n" . rtrim($snippetContent) . "\n";
        }

        return $out;
    }

    public function sectionHeader(string $title, int $level = 2): string
    {
        return str_repeat('#', $level) . " {$title}\n";
    }

    public function classHeader(string $name, string $namespace): string
    {
        return "### [{$name}](https://github.com/zircote/swagger-php/tree/master/src/{$namespace}/{$name}.php)\n";
    }

    public function exampleSection(string $name, ?string $readme, array $files): string
    {
        $out = $readme ? rtrim($readme) . "\n" : '## ' . $name . "\n";

        foreach ($files as $relFilename => $details) {
            $out .= "\n";
            $out .= '### ' . $relFilename . "\n";

            if (!empty($details['attributes'])) {
                $out .= "\n";
                $out .= "<codeblock id=\"{$name}-{$details['basename']}\">\n";
                $out .= "  <template v-slot:at>\n";
                $out .= "\n";
                $out .= "<<< @/examples/specs/{$name}/attributes/{$relFilename}\n";
                $out .= "\n";
                $out .= "  </template>\n";
                $out .= "  <template v-slot:an>\n";
                $out .= "\n";
                $out .= "<<< @/examples/specs/{$name}/annotations/{$relFilename}\n";
                $out .= "\n";
                $out .= "  </template>\n";
                $out .= "  <template v-slot:spec>\n";
                $out .= "\n";
                $out .= "<<< @/examples/specs/{$name}/spec/{$relFilename}\n";
                $out .= "\n";
                $out .= "  </template>\n";
                $out .= "</codeblock>\n";
            }
        }

        return $out;
    }
}
