<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Console;

use OpenApi\Builder\Mode;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;

class GenerateInput
{
    /**
     * @var array<string>
     */
    public array $paths = [];

    /**
     * @var array<string>
     */
    public array $config = [];

    public bool $defaults = false;

    public ?string $output = null;

    public GenerateFormat $format = GenerateFormat::AUTO;

    /**
     * @var array<string>
     */
    public array $exclude = [];

    public string $pattern = '*.php';

    /**
     * @var array<string>
     */
    public array $bootstrap = [];

    /**
     * @var array<string>
     */
    public array $addProcessor = [];

    /**
     * @var array<string>
     */
    public array $removeProcessor = [];

    public ?string $version = null;

    public Mode $mode = Mode::CLASSIC;

    public bool $debug = false;

    public static function fromInput(InputInterface $input): self
    {
        $generateInput = new self();

        $generateInput->paths = self::strings($input->getArgument('paths'));
        $generateInput->config = self::strings($input->getOption('config'));
        $generateInput->defaults = (bool) $input->getOption('defaults');
        $generateInput->output = self::nullableString($input->getOption('output'));
        $generateInput->format = self::format($input->getOption('format'));
        $generateInput->exclude = self::strings($input->getOption('exclude'));
        $generateInput->pattern = self::string($input->getOption('pattern'), $generateInput->pattern);
        $generateInput->bootstrap = self::strings($input->getOption('bootstrap'));
        $generateInput->addProcessor = self::strings($input->getOption('add-processor'));
        $generateInput->removeProcessor = self::strings($input->getOption('remove-processor'));
        $generateInput->version = self::nullableString($input->getOption('version'));
        $generateInput->mode = self::mode($input->getOption('mode'));
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
     * @return array<string>
     */
    protected static function strings(mixed $value): array
    {
        return is_array($value)
            ? array_values(array_filter($value, is_string(...)))
            : [];
    }

    protected static function string(mixed $value, string $default): string
    {
        return is_string($value) && $value !== '' ? $value : $default;
    }

    protected static function nullableString(mixed $value): ?string
    {
        return is_string($value) && $value !== '' ? $value : null;
    }

    protected static function format(mixed $value): GenerateFormat
    {
        $format = is_string($value) ? GenerateFormat::tryFrom(strtolower($value)) : null;

        return $format ?? throw new InvalidArgumentException(self::invalidValue('--format', $value, array_column(GenerateFormat::cases(), 'value')));
    }

    protected static function mode(mixed $value): Mode
    {
        $mode = is_string($value) ? Mode::tryFrom(strtolower($value)) : null;

        return $mode ?? throw new InvalidArgumentException(self::invalidValue('--mode', $value, array_column(Mode::cases(), 'value')));
    }

    /**
     * @param array<string> $expected
     */
    protected static function invalidValue(string $option, mixed $value, array $expected): string
    {
        return sprintf(
            'Invalid `%s` value: "%s"; expected one of %s',
            $option,
            is_scalar($value) ? (string) $value : get_debug_type($value),
            implode(', ', $expected)
        );
    }
}
