<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Rector\Tests;

use OpenApi\Spec\Schema;
use PHPUnit\Framework\TestCase;

/**
 * Every classic argument the renamed spec class does not take is either rewritten by the set or
 * listed in `migrating-to-spec.md`.
 *
 * Left unhandled, such an argument throws `Unknown named parameter` once the migrated attribute
 * loads. The lists are checked against the constructors, so an argument added on either side
 * shows up here rather than in someone's migration.
 */
final class ClassicToSpecArgumentsTest extends TestCase
{
    /** Classes whose schema keywords `MoveSchemaKeywordsRector` moves into a nested `Schema`. */
    private const SCHEMA_HOLDERS = ['Property', 'JsonContent', 'XmlContent'];

    private const STYLES = ['spaceDelimited', 'pipeDelimited', 'deepObject'];

    private const SCHEMA_ONLY_IN_CLASSIC = ['collectionFormat', 'additionalItems', 'dependencies'];

    /**
     * Rewritten by a rule of the set.
     *
     * @var array<string, list<string>>
     */
    private const REWRITTEN = [
        'Encoding' => ['property'],
        'OpenApi' => ['openapi', 'info', 'servers', 'tags', 'externalDocs'],
        'Parameter' => self::STYLES,
        'PathParameter' => self::STYLES,
        'Property' => ['encoding'],
        'QueryParameter' => self::STYLES,
    ];

    /**
     * Left for the author, and listed in `migrating-to-spec.md`.
     *
     * @var array<string, list<string>>
     */
    private const LEFT = [
        'AdditionalProperties' => self::SCHEMA_ONLY_IN_CLASSIC,
        'Attachable' => ['properties'],
        'Components' => ['callbacks'],
        'CookieParameter' => ['in', 'allowEmptyValue', 'style', 'allowReserved', ...self::STYLES],
        'Get' => ['requestBody'],
        'Head' => ['requestBody'],
        'Header' => ['allowEmptyValue'],
        'HeaderParameter' => ['in', 'allowEmptyValue', 'style', 'allowReserved', ...self::STYLES],
        'Items' => self::SCHEMA_ONLY_IN_CLASSIC,
        'JsonContent' => self::SCHEMA_ONLY_IN_CLASSIC,
        'OpenApi' => ['paths', 'components', 'webhooks'],
        'Options' => ['requestBody'],
        'PathItem' => ['path', 'get', 'put', 'post', 'delete', 'options', 'head', 'patch', 'trace', 'query'],
        'PathParameter' => ['in', 'allowEmptyValue', 'allowReserved'],
        'Property' => self::SCHEMA_ONLY_IN_CLASSIC,
        'QueryParameter' => ['in'],
        'Schema' => self::SCHEMA_ONLY_IN_CLASSIC,
        'ServerVariable' => ['variables'],
        'Trace' => ['requestBody'],
        'XmlContent' => self::SCHEMA_ONLY_IN_CLASSIC,
    ];

    public function testEveryDroppedArgumentIsRewrittenOrListed(): void
    {
        /** @var array<class-string, class-string> $map */
        $map = require __DIR__ . '/../set/classic-to-spec-classes.php';
        $schema = $this->parameters(Schema::class);

        $dropped = [];
        foreach ($map as $classic => $spec) {
            $short = substr($classic, strlen('OpenApi\\Attributes\\'));
            $missing = array_diff($this->parameters($classic), $this->parameters($spec));
            if (in_array($short, self::SCHEMA_HOLDERS, true)) {
                $missing = array_diff($missing, $schema);
            }

            foreach ($missing as $name) {
                $dropped[] = $short . '::' . $name;
            }
        }

        $known = [];
        foreach ([self::REWRITTEN, self::LEFT] as $list) {
            foreach ($list as $short => $names) {
                foreach ($names as $name) {
                    $known[] = $short . '::' . $name;
                }
            }
        }

        sort($dropped);
        sort($known);

        $this->assertSame($known, $dropped, 'Rewrite a classic argument spec does not take in the set, or list it here and in migrating-to-spec.md');
    }

    /**
     * @param class-string $class
     *
     * @return list<string>
     */
    private function parameters(string $class): array
    {
        $constructor = (new \ReflectionClass($class))->getConstructor();

        return $constructor
            ? array_map(static fn (\ReflectionParameter $p): string => $p->getName(), $constructor->getParameters())
            : [];
    }
}
