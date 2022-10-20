<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tools\Docs;

abstract class DocGenerator
{
    public const NO_DETAILS_AVAILABLE = 'No details available.';

    protected $projectRoot;

    public function __construct($projectRoot)
    {
        $this->projectRoot = realpath($projectRoot);
    }

    public function docPath(string $relativeName): string
    {
        return $this->projectRoot . '/docs/' . $relativeName;
    }

    public function formatClassHeader(string $name, string $namespace): string
    {
        return <<< EOT
## [$name](https://github.com/zircote/swagger-php/tree/master/src/$namespace/$name.php)


EOT;
    }

    public function preamble(string $type): string
    {
        return <<< EOT
# $type

This page is generated automatically from the `swagger-php` sources.

For improvements head over to [GitHub](https://github.com/zircote/swagger-php) and create a PR ;)


EOT;
    }
}
