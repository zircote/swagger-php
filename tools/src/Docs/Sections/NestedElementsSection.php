<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tools\Docs\Sections;

class NestedElementsSection implements SectionInterface
{
    public function render(array $data): string
    {
        $nested = $data['nested'] ?? [];
        if (!$nested) {
            return '';
        }

        $out = "#### Nested elements\n";
        $out .= "---\n";
        $links = array_map(
            fn (array $n): string => '<a href="#' . $n['anchor'] . '">' . $n['name'] . '</a>',
            $nested,
        );

        return $out . implode(', ', $links) . "\n";
    }
}
