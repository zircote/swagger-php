<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Benchmarks\Fixtures;

use OpenApi\Augmenter;
use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Builder\Result;
use OpenApi\Generator;
use OpenApi\Specification;
use OpenApi\Utils\PipeInterface;
use OpenApi\Utils\Pipeline;
use Psr\Log\LoggerInterface;

/**
 * A generated API on disk, and how to build a document from it.
 */
final class Fixture
{
    private bool $registered = false;

    public function __construct(
        public readonly string $dir,
        public readonly string $namespace,
        public readonly Shape $shape,
        public readonly Flavour $flavour,
    ) {
    }

    /**
     * The modes that read this flavour: classic and hybrid read classic attributes, spec reads spec
     * attributes.
     *
     * @return list<Mode>
     */
    public function modes(): array
    {
        return match ($this->flavour) {
            Flavour::Classic => [Mode::CLASSIC, Mode::HYBRID],
            Flavour::Spec => [Mode::SPEC],
        };
    }

    /**
     * The controllers, and the class holding `Info`: enough to seed a build that does not scan.
     *
     * @return list<string>
     */
    public function entryClasses(): array
    {
        $this->register();

        $classes = [$this->namespace . '\\Api'];
        for ($c = 0; $c < $this->shape->controllers(); $c++) {
            $classes[] = $this->namespace . '\\Controllers\\C' . $c;
        }

        return $classes;
    }

    /**
     * Builds the document, scanning the directory or seeded with `entryClasses()`.
     */
    public function build(Mode $mode, bool $cleanup = true, bool $scan = true, string $version = '3.1.0', ?LoggerInterface $logger = null): Result
    {
        if (!in_array($mode, $this->modes(), true)) {
            throw new \InvalidArgumentException(sprintf('A %s fixture is not read by %s mode', $this->flavour->value, $mode->value));
        }

        $this->register();

        $builder = (new Builder(logger: $logger))
            ->setMode($mode)
            ->setVersion($version);

        if ($scan) {
            $builder->addSource($this->dir);
        } else {
            foreach ($this->entryClasses() as $class) {
                if (!class_exists($class)) {
                    throw new \RuntimeException(sprintf('%s is missing from %s', $class, $this->dir));
                }
                $builder->addSource(new \ReflectionClass($class));
            }
        }

        if ($mode === Mode::CLASSIC) {
            $builder->withGenerator(fn (Generator $generator): Generator => $generator->setConfig(['cleanUnusedComponents' => ['enabled' => $cleanup]]));
        } elseif (!$cleanup) {
            $builder->withAugmenters(fn (Pipeline $augmenters): Pipeline => $augmenters->remove(Augmenter\Cleanup::class));
        }

        return $builder->build();
    }

    /**
     * Builds in spec mode and stops right before `Augmenter\Cleanup`, so it can run on its own.
     *
     * The pipeline is cut short by an exception rather than by removing the later augmenters,
     * since the augmenters run grouped and their order is the pipeline's to decide.
     */
    public function specificationBeforeCleanup(string $version = '3.1.0'): Specification
    {
        $this->register();

        $stop = new class () implements PipeInterface {
            public function __invoke(mixed $payload): mixed
            {
                throw new StopBeforeCleanup($payload);
            }

            public function group(): string|\BackedEnum
            {
                return (new Augmenter\Cleanup())->group();
            }
        };

        try {
            (new Builder())
                ->setMode(Mode::SPEC)
                ->setVersion($version)
                ->addSource($this->dir)
                ->withAugmenters(fn (Pipeline $augmenters): Pipeline => $augmenters->insert($stop, Augmenter\Cleanup::class))
                ->build();
        } catch (StopBeforeCleanup $stopBeforeCleanup) {
            return $stopBeforeCleanup->specification;
        }

        throw new \LogicException('The build did not reach Augmenter\Cleanup');
    }

    /**
     * Loads the generated classes on demand, registering the autoloader once per instance.
     */
    public function register(): void
    {
        if ($this->registered) {
            return;
        }

        $prefix = $this->namespace . '\\';
        $dir = $this->dir;
        spl_autoload_register(static function (string $class) use ($prefix, $dir): void {
            if (str_starts_with($class, $prefix)) {
                $file = $dir . '/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
                if (is_file($file)) {
                    require $file;
                }
            }
        });

        $this->registered = true;
    }
}
