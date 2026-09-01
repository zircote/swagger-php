<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Concerns;

use OpenApi\Builder;
use OpenApi\Builder\Mode;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;

final class ExpectsLogEntriesTest extends TestCase
{
    use ExpectsLogEntries;

    public function testExpectedEntryIsSatisfied(): void
    {
        $this->expectLogEntry('hello');

        $this->trackingLogger()->warning('well, hello there');

        // the #[After] hook asserts this; reaching it without failure is the assertion
        $this->assertCount(1, $this->recordedLogEntries());
    }

    public function testExpectationsAreOrderIndependent(): void
    {
        $this->expectLogEntry('second');
        $this->expectLogEntry('first');

        $logger = $this->trackingLogger();
        $logger->warning('the first one');
        $logger->warning('the second one');

        $this->assertCount(2, $this->recordedLogEntries());
    }

    public function testAllowedEntryIsToleratedButNotRequired(): void
    {
        $this->allowLogEntry('tolerated', 'also tolerated');

        $this->trackingLogger()->warning('a tolerated diagnostic');

        // 'also tolerated' was never logged, and that is not a failure
        $this->assertCount(1, $this->recordedLogEntries());
    }

    public function testLevelIsMatchedWhenGiven(): void
    {
        $this->expectLogEntry('pinned', 'error');

        $this->trackingLogger()->error('a pinned diagnostic');

        $this->assertCount(1, $this->recordedLogEntries());
    }

    public function testDelegateReceivesEntries(): void
    {
        $received = [];
        $delegate = new class ($received) extends AbstractLogger {
            /**
             * @param list<string> $received
             */
            public function __construct(public array &$received)
            {
            }

            public function log($level, $message, array $context = []): void
            {
                $this->received[] = (string) $message;
            }
        };

        $this->allowLogEntry('forwarded');
        $this->trackingLogger($delegate)->warning('forwarded message');

        $this->assertSame(['forwarded message'], $received);
    }

    public function testUnmetExpectationFails(): void
    {
        $this->expectLogEntry('never logged');

        $this->assertHookFailsWith('never logged');
    }

    public function testWrongLevelFailsTheExpectation(): void
    {
        $this->expectLogEntry('pinned', 'error');
        $this->trackingLogger()->warning('a pinned diagnostic');

        $this->assertHookFailsWith('[error] pinned');
    }

    public function testUnexpectedEntryFails(): void
    {
        $this->trackingLogger()->warning('nobody asked for this');

        $this->assertHookFailsWith('nobody asked for this');
    }

    public function testAnExpectationIsConsumedOnlyOnce(): void
    {
        $this->expectLogEntry('repeated');

        $logger = $this->trackingLogger();
        $logger->warning('repeated once');
        $logger->warning('repeated twice');

        // the second entry matches no remaining expectation, so it is unexpected
        $this->assertHookFailsWith('repeated twice');
    }

    public function testAgainstARealBuild(): void
    {
        // the diagnostic this test is about ...
        $this->expectLogEntry('info is required', 'error');
        // ... and one that merely comes with an empty source set
        $this->allowLogEntry('At least one of paths, webhooks, or components is required');

        (new Builder())
            ->setMode(Mode::SPEC)
            ->setSources([])
            ->setLogger($this->trackingLogger())
            ->build();

        $this->assertNotEmpty($this->recordedLogEntries());
    }

    /**
     * Run the `#[After]` hook now, assert it failed, then clear state so the automatic
     * invocation does not fail the test for the same reason.
     */
    protected function assertHookFailsWith(string $expectedMessage): void
    {
        try {
            $this->assertLogEntryExpectations();
            $failure = null;
        } catch (ExpectationFailedException $expectationFailedException) {
            $failure = $expectationFailedException;
        } finally {
            $this->resetLogEntryExpectations();
        }

        $this->assertInstanceOf(ExpectationFailedException::class, $failure, 'the hook should have failed');
        $this->assertStringContainsString($expectedMessage, $failure->getMessage());
    }
}
