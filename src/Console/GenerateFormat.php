<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Console;

enum GenerateFormat: string
{
    case JSON = 'json';
    case YAML = 'yaml';
    case AUTO = 'auto';

    public function isJson(): bool
    {
        return $this === GenerateFormat::JSON;
    }

    public function isYAML(): bool
    {
        return $this === GenerateFormat::YAML;
    }

    public function isAuto(): bool
    {
        return $this === GenerateFormat::AUTO;
    }
}
