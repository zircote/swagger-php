<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Snippets\Guide\Cookbook\Fragments as Snippet;
use OpenApi\Spec as OA;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

/**
 * Runs the snippets the cookbook's "Build parts of a document for another one" transcludes.
 * `DocSnippetsTest` cannot: they build with their own `Builder` and return parts, not a document.
 */
final class CookbookFragmentsSnippetTest extends TestCase
{
    private const SNIPPETS = __DIR__ . '/../docs/snippets/guide/cookbook/fragments';

    public static function setUpBeforeClass(): void
    {
        foreach (glob(self::SNIPPETS . '/*.php') ?: [] as $snippet) {
            require_once $snippet;
        }
    }

    public function testPartsMatchTheDocumentedOutput(): void
    {
        $expected = Yaml::parseFile(self::SNIPPETS . '/fragments-3.1.0.yaml');

        $this->assertEquals($expected['User'], Snippet\buildModel(Snippet\User::class));
        $this->assertEquals($expected['id'], Snippet\buildPart(
            new OA\Parameter\Path(name: 'id', schema: new OA\Schema(type: 'integer'), component: 'id'),
            'parameters'
        ));
    }
}
