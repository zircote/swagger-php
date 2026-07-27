<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tools\Docs\Sections;

class DescriptionSection implements SectionInterface
{
    public function render(array $data): string
    {
        $description = $data['description'] ?? '';

        return $description !== '' ? $description . "\n" : '';
    }
}
