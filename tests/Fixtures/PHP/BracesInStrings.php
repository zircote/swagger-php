<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP;

trait BracedTrait
{
    public string $after = '';
}

/**
 * Braces that are text, in strings and a heredoc, before a member and a class.
 */
class BracesInStrings
{
    use BracedTrait;

    public function json(int $id): string
    {
        return "{\"id\": {$id}}" . <<<JSON
            {"id": {$id}}}
            JSON;
    }

    public string $after = '';
}

class AfterBraces
{
}
