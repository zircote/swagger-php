<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Builder;
use OpenApi\Tests\Concerns\GeneratesTestMatrix;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Log\NullLogger;

/**
 * The form `docs/dev/messages.md` sets for the spec pipeline's messages, checked over what the
 * spec scratch fixtures log. Only the rules a pattern can check: no internal-name prefix (rule 1),
 * and sentence case without a trailing period or an em dash (rule 5). The rest is review.
 */
final class MessageFormTest extends OpenApiTestCase
{
    use GeneratesTestMatrix;

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function specFixtures(): iterable
    {
        foreach (self::discoverFixtures(self::fixture('Scratch') . '/*-spec.php') as $name => $fixture) {
            foreach (self::versions() as $version) {
                yield "{$name}-{$version}" => [$fixture, $version];
            }
        }
    }

    #[DataProvider('specFixtures')]
    public function testMessagesFollowTheForm(string $fixture, string $version): void
    {
        require_once $fixture;

        $result = (new Builder())
            ->setMode(Builder\Mode::SPEC)
            ->addSource($fixture)
            ->setVersion($version)
            ->setLogger(new NullLogger())
            ->build();

        foreach (array_column($result->log(), 'message') as $message) {
            $this->assertDoesNotMatchRegularExpression('/^\w+: /', $message, 'Starts with an internal name');
            $this->assertMatchesRegularExpression('/^[A-Z0-9]/', $message, 'Not in sentence case');
            $this->assertStringEndsNotWith('.', $message, 'Ends with a period');
            $this->assertStringNotContainsString('—', $message, 'Has an em dash');
        }
    }
}
