<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Console;

use OpenApi\Builder\Mode;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputInterface;

class GenerateInput
{
    /** @var array<string> */
    public array $paths = [];

    /** @var array<string> */
    public array $config = [];

    public bool $defaults = false;

    public ?string $output = null;

    public GenerateFormat $format = GenerateFormat::AUTO;

    /** @var array<string> */
    public array $exclude = [];

    public string $pattern = '*.php';

    /** @var array<string> */
    public array $bootstrap = [];

    /** @var array<string> */
    public array $addProcessor = [];

    /** @var array<string> */
    public array $removeProcessor = [];

    public ?string $version = null;

    public Mode $mode = Mode::CLASSIC;

    public bool $debug = false;

    /**
     * Map the console input onto this data object.
     *
     * The console definition lives in {@see GenerateCommand::configure()}; the two
     * are aligned by hand. Option names arrive kebab-cased, properties are camelCase.
     */
    public static function fromInput(InputInterface $input): self
    {
        $generateInput = new self();

        $generateInput->paths = $input->getArgument('paths');
        $generateInput->config = $input->getOption('config');
        $generateInput->defaults = (bool) $input->getOption('defaults');
        $generateInput->output = $input->getOption('output');
        $generateInput->format = self::enum(GenerateFormat::class, 'format', $input->getOption('format'));
        $generateInput->exclude = $input->getOption('exclude');
        $generateInput->pattern = $input->getOption('pattern');
        $generateInput->bootstrap = $input->getOption('bootstrap');
        $generateInput->addProcessor = $input->getOption('add-processor');
        $generateInput->removeProcessor = $input->getOption('remove-processor');
        $generateInput->version = $input->getOption('version');
        $generateInput->mode = self::enum(Mode::class, 'mode', $input->getOption('mode'));
        $generateInput->debug = (bool) $input->getOption('debug');

        return $generateInput;
    }

    /**
     * @return iterable<string>
     */
    public function getBootstrapFilenames(): iterable
    {
        foreach ($this->bootstrap as $bootstrapPattern) {
            $filenames = glob($bootstrapPattern);

            if (!$filenames) {
                throw new InvalidArgumentException('Invalid `--bootstrap` value: "' . $bootstrapPattern . '"');
            }

            yield from $filenames;
        }
    }

    /**
     * Resolve a backed enum option, reporting unknown values the way Symfony does.
     *
     * @template T of \BackedEnum
     *
     * @param class-string<T> $enum
     *
     * @return T
     */
    protected static function enum(string $enum, string $name, string $value): \BackedEnum
    {
        $case = $enum::tryFrom($value);

        if ($case === null) {
            throw new InvalidOptionException(sprintf(
                'The value "%s" is not valid for the "%s" option. Supported values are "%s".',
                $value,
                $name,
                implode('", "', array_column($enum::cases(), 'value'))
            ));
        }

        return $case;
    }
}
