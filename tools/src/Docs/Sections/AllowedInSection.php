<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tools\Docs\Sections;

class AllowedInSection implements SectionInterface
{
    public function render(array $data): string
    {
        $parents = $data['parents'] ?? [];
        if (!$parents) {
            return '';
        }

        $out = "#### Allowed in\n";
        $out .= "---\n";
        $links = array_map(
            fn (array $p): string => '<a href="#' . $p['anchor'] . '">' . $p['name'] . '</a>',
            $parents,
        );

        return $out . implode(', ', $links) . "\n";
    }
}
