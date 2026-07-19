<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Builder;

use OpenApi\Annotations\OpenApi;
use OpenApi\OpenApiException;
use Symfony\Component\Yaml\Yaml;

/**
 * Result container for a build operation.
 */
class Result
{
    /**
     * @param list<string>                                $files
     * @param list<array{level: string, message: string}> $log
     * @param array<string,mixed>|null                    $specOutput
     */
    protected function __construct(
        protected array $files,
        protected ?OpenApi $openApi,
        protected array $log = [],
        protected ?array $specOutput = null,
    ) {
    }

    /**
     * Create a Result from the classic annotation pipeline.
     *
     * @param list<string>                                $files
     * @param list<array{level: string, message: string}> $log
     */
    public static function fromClassic(array $files, ?OpenApi $openApi, array $log = []): self
    {
        return new self($files, $openApi, $log);
    }

    /**
     * Create a Result from the spec attribute pipeline.
     *
     * @param list<string>                                $files
     * @param array<string,mixed>                         $output
     * @param list<array{level: string, message: string}> $log
     */
    public static function fromSpec(array $files, array $output, array $log = []): self
    {
        return new self($files, null, $log, $output);
    }

    /**
     * @return list<string>
     */
    public function files(): array
    {
        return $this->files;
    }

    public function openApi(): ?OpenApi
    {
        return $this->openApi;
    }

    public function isValid(): bool
    {
        return $this->openApi instanceof OpenApi || $this->specOutput !== null;
    }

    /**
     * @return list<array{level: string, message: string}>
     */
    public function log(): array
    {
        return $this->log;
    }

    /**
     * @return list<string>
     */
    public function warnings(): array
    {
        return array_column(
            array_filter($this->log, fn (array $entry): bool => $entry['level'] === 'warning'),
            'message'
        );
    }

    /**
     * @return list<string>
     */
    public function errors(): array
    {
        return array_column(
            array_filter($this->log, fn (array $entry): bool => $entry['level'] === 'error'),
            'message'
        );
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        if ($this->specOutput !== null) {
            return $this->specOutput;
        }

        if (!$this->openApi instanceof OpenApi) {
            return [];
        }

        return json_decode($this->openApi->toJson(), true) ?? [];
    }

    public function toJson(int $flags = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE): string
    {
        if ($this->specOutput !== null) {
            return json_encode($this->specOutput, $flags) ?: '{}';
        }

        if (!$this->openApi instanceof OpenApi) {
            return '{}';
        }

        return $this->openApi->toJson();
    }

    public function toYaml(int $inline = 10, int $indent = 2): string
    {
        if ($this->specOutput !== null) {
            return Yaml::dump($this->specOutput, $inline, $indent, Yaml::DUMP_OBJECT_AS_MAP | Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE);
        }

        if (!$this->openApi instanceof OpenApi) {
            return '';
        }

        return $this->openApi->toYaml();
    }

    public function saveAs(string $filename, string $format = 'auto'): void
    {
        if ($format === 'auto') {
            $format = strtolower(substr($filename, -5)) === '.json' ? 'json' : 'yaml';
        }

        $content = strtolower($format) === 'json' ? $this->toJson() : $this->toYaml();

        if (file_put_contents($filename, $content) === false) {
            throw new OpenApiException('Failed to save to "' . $filename . '"');
        }
    }
}
