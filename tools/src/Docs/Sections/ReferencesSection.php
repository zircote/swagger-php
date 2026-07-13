<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tools\Docs\Sections;

class ReferencesSection implements SectionInterface
{
    public function render(array $data): string
    {
        $links = $data['see'] ?? [];
        if (!$links) {
            return '';
        }

        $out = "#### Reference\n";
        $out .= "---\n";
        foreach ($links as $link) {
            $out .= '- ' . $link . " ↗\n";
        }

        return $out;
    }
}
