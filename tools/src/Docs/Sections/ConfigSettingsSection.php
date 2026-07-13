<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tools\Docs\Sections;

use OpenApi\Tools\Docs\DocGenerator;

class ConfigSettingsSection implements SectionInterface
{
    public function render(array $data): string
    {
        $options = $data['options'] ?? [];
        if (!$options) {
            return '';
        }

        $configPrefix = $data['configPrefix'] ?? '';

        $out = "#### Config settings\n";

        foreach ($options as $option) {
            $out .= "- **{$configPrefix}{$option['name']}** : `{$option['type']}`";
            $out .= " · default: `{$option['default']}`  \n";

            $desc = ($option['description'] ?? '') ?: DocGenerator::NO_DETAILS_AVAILABLE;
            $out .= '  ' . $desc . "\n";
        }

        return $out;
    }
}
