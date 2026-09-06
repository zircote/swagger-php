<?php declare(strict_types=1);

namespace OpenApi\Snippets\Guide\ExtensionPoints;

#[\Attribute(\Attribute::TARGET_METHOD)]
final class Route
{
    public function __construct(public string $path, public string $method = 'get')
    {
    }
}
