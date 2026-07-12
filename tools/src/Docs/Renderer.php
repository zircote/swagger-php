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

    public function classDescription(string $content): string
    {
        if ($content === '') {
            return '';
        }

        return $content . "\n";
    }

    /**
     * @param list<array{name: string, anchor: string}> $parents
     */
    public function allowedIn(array $parents): string
    {
        if (!$parents) {
            return '';
        }

        $out = "#### Allowed in\n";
        $out .= "---\n";
        $links = array_map(
            fn (array $p): string => '<a href="#' . $p['anchor'] . '">' . $p['name'] . '</a>',
            $parents,
        );

        return $out . (implode(', ', $links) . "\n");
    }

    /**
     * @param list<array{name: string, anchor: string}> $nested
     */
    public function nestedElements(array $nested): string
    {
        if (!$nested) {
            return '';
        }

        $out = "#### Nested elements\n";
        $out .= "---\n";
        $links = array_map(
            fn (array $n): string => '<a href="#' . $n['anchor'] . '">' . $n['name'] . '</a>',
            $nested,
        );

        return $out . (implode(', ', $links) . "\n");
    }

    /**
     * @param list<array{name: string, type: string, description: string, required: bool, see: list<string>}> $parameters
     */
    public function parameters(array $parameters, string $heading = 'Parameters'): string
    {
        if (!$parameters) {
            return '';
        }

        $out = "#### {$heading}\n";
        $out .= "---\n";
        $out .= "<dl>\n";

        foreach ($parameters as $param) {
            $typeHtml = '';
            if ($param['type'] !== '') {
                $typeHtml = ' : <span style="font-family: monospace;">' . $param['type'] . '</span>';
            }

            $out .= '  <dt><strong>' . $param['name'] . '</strong>' . $typeHtml . "</dt>\n";
            $out .= '  <dd>';

            $desc = $param['description'] ?: DocGenerator::NO_DETAILS_AVAILABLE;
            $out .= '<p>' . nl2br($desc) . '</p>';

            $out .= '<table class="table-plain"><tbody>';
            $out .= '<tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>' . ($param['required'] ? 'yes' : 'no') . '</b></td></tr>';

            if (!empty($param['see'])) {
                $links = [];
                foreach ($param['see'] as $see) {
                    if ($link = $this->linkFromMarkup($see)) {
                        $links[] = $link;
                    }
                }
                if ($links) {
                    $out .= '<tr><td style="padding-left: 0;"><i>See</i>:</td><td style="padding-left: 0;">' . implode(', ', $links) . '</td></tr>';
                }
            }

            $out .= '</tbody></table>';
            $out .= "</dd>\n";
        }

        return $out . "</dl>\n";
    }

    /**
     * @param list<string> $links
     */
    public function references(array $links): string
    {
        if (!$links) {
            return '';
        }

        $out = "#### Reference\n";
        $out .= "---\n";
        foreach ($links as $link) {
            $out .= '- ' . $link . "\n";
        }

        return $out;
    }

    /**
     * @param list<array{name: string, type: string, default: string, description: string}> $options
     */
    public function processorOptions(array $options, string $configPrefix): string
    {
        if (!$options) {
            return '';
        }

        $out = "#### Config settings\n";

        foreach ($options as $option) {
            $out .= "**{$configPrefix}{$option['name']}**\n";
            $out .= ': <span style="font-family: monospace;">' . $option['type'] . "</span>\n<br>";
            $out .= "**default**\n";
            $out .= ': <span style="font-family: monospace;">' . $option['default'] . "</span>\n";
            $out .= "\n";

            $desc = $option['description'] ?: DocGenerator::NO_DETAILS_AVAILABLE;
            $out .= $this->indentedBr($desc) . "\n";
        }

        return $out;
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
                $out .= "</codeblock>\n";
            }
        }

        return $out;
    }

    protected function linkFromMarkup(string $see): ?string
    {
        preg_match('/\[([^]]+)]\((.*)\)/', $see, $matches);

        return 3 === count($matches) ? '<a href="' . $matches[2] . '">' . $matches[1] . '</a>' : null;
    }

    protected function indentedBr(string $text): string
    {
        $lines = explode("\n", $text);
        $processed = [];
        $inBlock = false;

        foreach ($lines as $line) {
            $blockStart = !$inBlock && str_contains($line, '```');
            if ($blockStart) {
                $inBlock = true;
            }
            if (!$inBlock) {
                $processed[] = '&nbsp;&nbsp;&nbsp;&nbsp;' . $line . '<br>';
            } else {
                $processed[] = $line;
                if ('```' === $line) {
                    $inBlock = false;
                }
            }
        }

        return implode("\n", $processed);
    }
}
