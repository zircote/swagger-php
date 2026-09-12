<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tools\Docs\Sections;

use OpenApi\Tools\Docs\DocGenerator;

/**
 * Renders parameters as a definition list.
 *
 * Both halves of an entry carry content this section does not control: a description is
 * prose with its own paragraph breaks, and a type arrives already HTML-escaped. The markup
 * is HTML so that both reach the page as written.
 */
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
        $out .= "<dl>\n";

        foreach ($parameters as $param) {
            $type = ($param['type'] ?? '') !== ''
                ? ' : <span style="font-family: monospace;">' . $param['type'] . '</span>'
                : '';

            $out .= '  <dt><strong>' . $param['name'] . '</strong>' . $type . "</dt>\n";
            $out .= '  <dd>';

            $desc = ($param['description'] ?? '') ?: DocGenerator::NO_DETAILS_AVAILABLE;
            $out .= '<p>' . nl2br($desc) . '</p>';

            $rows = [];
            if (array_key_exists('required', $param)) {
                $rows[] = '<tr><td><i>Required</i>:</td><td style="padding-left: 0;"><b>' . ($param['required'] ? 'yes' : 'no') . '</b></td></tr>';
            }

            if (!empty($param['see'])) {
                $links = [];
                foreach ($param['see'] as $see) {
                    if ($link = $this->linkFromMarkup($see)) {
                        $links[] = $link;
                    }
                }
                if ($links) {
                    $rows[] = '<tr><td style="padding-left: 0;"><i>See</i>:</td><td style="padding-left: 0;">' . implode(', ', $links) . '</td></tr>';
                }
            }

            // spec attributes carry no required flag, and a parameter may have no reference
            if ($rows !== []) {
                $out .= '<table class="table-plain"><tbody>' . implode('', $rows) . '</tbody></table>';
            }

            $out .= "</dd>\n";
        }

        return $out . "</dl>\n";
    }

    protected function linkFromMarkup(string $see): ?string
    {
        preg_match('/\[([^]]+)]\((.*)\)/', $see, $matches);

        return 3 === count($matches) ? '<a href="' . $matches[2] . '">' . $matches[1] . '</a>' : null;
    }
}
