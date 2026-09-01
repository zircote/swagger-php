<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Concerns;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

/**
 * Assertions about the diagnostics a build emits.
 *
 * Hand {@see self::trackingLogger()} to `Builder::setLogger()`, then declare what the
 * build is allowed to say:
 *
 *   $this->expectLogEntry('info is required');            // must be logged
 *   $this->allowLogEntry('const is not supported');       // may be logged
 *
 * Strict by default: any entry matching neither is a failure, which is what stops a
 * diagnostic from appearing unnoticed. Use `allowLogEntry()` for diagnostics that are
 * incidental to what the test is about — a fixture that deliberately omits `info` need
 * not pretend to care about the resulting warning.
 *
 * Matching is by substring, and order-independent: expectations are not required to be
 * logged in the order they were declared.
 *
 * The `#[Before]`/`#[After]` hooks run independently of `setUp()`/`tearDown()`, so a test
 * needs nothing beyond `use ExpectsLogEntries;` — no hook methods, and no `parent::` call
 * to forget.
 */
trait ExpectsLogEntries
{
    /** @var list<array{needle: string, level: string|null}> */
    protected array $expectedLogEntries = [];

    /** @var list<string> */
    protected array $allowedLogEntries = [];

    /** @var list<array{level: string, message: string}> */
    protected array $recordedLogEntries = [];

    /**
     * Require an entry containing `$needle` to be logged, optionally at `$level`.
     */
    public function expectLogEntry(string $needle, ?string $level = null): void
    {
        $this->expectedLogEntries[] = ['needle' => $needle, 'level' => $level];
    }

    /**
     * Permit entries containing any of `$needles` without requiring them.
     */
    public function allowLogEntry(string ...$needles): void
    {
        array_push($this->allowedLogEntries, ...$needles);
    }

    /**
     * The logger to hand to `Builder::setLogger()`; forwards to `$delegate` if given.
     */
    public function trackingLogger(?LoggerInterface $delegate = null): LoggerInterface
    {
        $recorder = function (string $level, string $message): void {
            $this->recordedLogEntries[] = ['level' => $level, 'message' => $message];
        };

        return new class ($recorder, $delegate) extends AbstractLogger {
            public function __construct(protected \Closure $recorder, protected ?LoggerInterface $delegate)
            {
            }

            public function log($level, $message, array $context = []): void
            {
                ($this->recorder)((string) $level, (string) $message);

                $this->delegate?->log($level, $message, $context);
            }
        };
    }

    /**
     * @return list<array{level: string, message: string}>
     */
    public function recordedLogEntries(): array
    {
        return $this->recordedLogEntries;
    }

    #[Before]
    protected function resetLogEntryExpectations(): void
    {
        $this->expectedLogEntries = [];
        $this->allowedLogEntries = [];
        $this->recordedLogEntries = [];
    }

    #[After]
    protected function assertLogEntryExpectations(): void
    {
        $unmatched = $this->recordedLogEntries;
        $missing = [];

        foreach ($this->expectedLogEntries as $expected) {
            $index = $this->findLogEntry($unmatched, $expected['needle'], $expected['level']);

            if ($index === null) {
                $missing[] = $expected['level'] === null
                    ? $expected['needle']
                    : "[{$expected['level']}] {$expected['needle']}";
                continue;
            }

            unset($unmatched[$index]);
        }

        $unexpected = [];
        foreach ($unmatched as $entry) {
            if (!$this->isAllowedLogEntry($entry['message'])) {
                $unexpected[] = "[{$entry['level']}] {$entry['message']}";
            }
        }

        // one assertion covering both, so a run reports every problem at once — and so a
        // test whose only assertions are its log expectations is not flagged risky
        $this->assertSame(
            [],
            array_merge($missing, $unexpected),
            $this->describeProblems($missing, $unexpected),
        );
    }

    /**
     * @param array<int,array{level: string, message: string}> $entries
     */
    protected function findLogEntry(array $entries, string $needle, ?string $level): ?int
    {
        foreach ($entries as $index => $entry) {
            if ($level !== null && $entry['level'] !== $level) {
                continue;
            }

            if (str_contains($entry['message'], $needle)) {
                return $index;
            }
        }

        return null;
    }

    protected function isAllowedLogEntry(string $message): bool
    {
        foreach ($this->allowedLogEntries as $needle) {
            if (str_contains($message, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param list<string> $missing
     * @param list<string> $unexpected
     */
    protected function describeProblems(array $missing, array $unexpected): string
    {
        $problems = [];

        if ($missing !== []) {
            // what did arrive is the useful context for an expectation that did not match
            $problems[] = 'Expected log entries were not logged:' . $this->describeList($missing)
                . "\n" . $this->describeRecordedLogEntries();
        }

        if ($unexpected !== []) {
            $problems[] = 'Unexpected log entries:' . $this->describeList($unexpected)
                . "\nDeclare them with expectLogEntry() or allowLogEntry().";
        }

        return implode("\n\n", $problems);
    }

    protected function describeRecordedLogEntries(): string
    {
        if ($this->recordedLogEntries === []) {
            return "\nNothing was logged.";
        }

        return "\nLogged:" . $this->describeList(array_map(
            static fn (array $entry): string => "[{$entry['level']}] {$entry['message']}",
            $this->recordedLogEntries,
        ));
    }

    /**
     * @param list<string> $items
     */
    protected function describeList(array $items): string
    {
        return $items === [] ? ' (none)' : "\n  " . implode("\n  ", $items);
    }
}
