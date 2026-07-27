<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tools\Docs\Sections;

use OpenApi\Tools\Docs\DocGenerator;

class ParametersSection implements SectionInterface
{
    public function __construct(
        protected string $heading = 'Parameters',
    ) {
    }

    public function render(array $data): string
    {
        $parameters = $data['parameters'] ?? [];
        if (!$parameters) {
            return '';
        }

        $out = "#### {$this->heading}\n";
        $out .= "---\n";

        foreach ($parameters as $param) {
            $type = ($param['type'] ?? '') !== '' ? ' : `' . $param['type'] . '`' : '';
            $out .= '- **' . $param['name'] . '**' . $type . "  \n";

            $desc = ($param['description'] ?? '') ?: DocGenerator::NO_DETAILS_AVAILABLE;
            $out .= '  ' . $desc . "\n";

            $meta = [];
            if (array_key_exists('required', $param)) {
                $meta[] = '*Required*: ' . ($param['required'] ? 'yes' : 'no');
            }

            if (!empty($param['see'])) {
                $links = [];
                foreach ($param['see'] as $see) {
                    if ($link = $this->linkFromMarkup($see)) {
                        $links[] = $link;
                    }
                }
                if ($links) {
                    $meta[] = '*See*: ' . implode(', ', $links);
                }
            }

            if ($meta) {
                $out .= '  ' . implode(' | ', $meta) . "\n";
            }
        }

        return $out;
    }

    protected function linkFromMarkup(string $see): ?string
    {
        preg_match('/\[([^]]+)]\((.*)\)/', $see, $matches);

        return 3 === count($matches) ? '<a href="' . $matches[2] . '">' . $matches[1] . '</a>' : null;
    }
}
