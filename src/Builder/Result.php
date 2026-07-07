<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Builder;

use OpenApi\Annotations\OpenApi;

/**
 * Result container for a build operation.
 */
class Result
{
    /**
     * @param list<string>                                $files
     * @param list<array{level: string, message: string}> $log
     */
    public function __construct(
        protected array $files,
        protected ?OpenApi $openApi,
        protected array $log = [],
    ) {
    }

    /**
     * @return list<string>
     */
    public function files(): array
    {
        return $this->files;
    }

    public function isValid(): bool
    {
        return $this->openApi instanceof OpenApi;
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
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        if (!$this->openApi instanceof OpenApi) {
            return [];
        }

        return json_decode($this->openApi->toJson(), true) ?? [];
    }

    public function toJson(int $flags = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE): string
    {
        if (!$this->openApi instanceof OpenApi) {
            return '{}';
        }

        return $this->openApi->toJson();
    }

    public function toYaml(int $inline = 10, int $indent = 4): string
    {
        if (!$this->openApi instanceof OpenApi) {
            return '';
        }

        return $this->openApi->toYaml();
    }
}
