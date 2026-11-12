<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

use OpenApi\Contracts\ParserInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * Reads an OpenAPI document into a Specification, with the parser for the version in its `openapi` field.
 *
 * A document of a version no parser supports, or of none, is read by the last parser, with a warning.
 */
class Parser
{
    protected LoggerInterface $logger;

    /**
     * @var non-empty-list<ParserInterface>
     */
    protected array $parsers;

    /**
     * @param non-empty-list<ParserInterface>|null $parsers the parsers to choose from, in order; by default one per version read
     */
    public function __construct(?LoggerInterface $logger = null, ?array $parsers = null)
    {
        $this->logger = $logger ?? new NullLogger();
        $this->parsers = $parsers ?? [new Compiler\OpenApi31Parser($this->logger)];
    }

    /**
     * A JSON or YAML file, by its extension; anything but `.json` is read as YAML.
     */
    public function parseFile(string $filename): Specification
    {
        $contents = is_file($filename) ? file_get_contents($filename) : false;
        if ($contents === false) {
            throw new OpenApiException(sprintf('Cannot read "%s"', $filename));
        }

        try {
            $document = str_ends_with(strtolower($filename), '.json')
                ? json_decode($contents, true, flags: JSON_THROW_ON_ERROR)
                : Yaml::parse($contents);
        } catch (\JsonException|ParseException $exception) {
            throw new OpenApiException(sprintf('Cannot parse "%s": %s', $filename, $exception->getMessage()), $exception->getCode(), previous: $exception);
        }

        if (!is_array($document)) {
            throw new OpenApiException(sprintf('"%s" does not hold an OpenAPI document', $filename));
        }

        return $this->parse($document);
    }

    /**
     * @param array<string, mixed> $document the document as decoded from JSON or YAML, with maps as arrays
     */
    public function parse(array $document): Specification
    {
        $version = $document['openapi'] ?? null;

        if (is_string($version)) {
            foreach ($this->parsers as $parser) {
                if ($parser->supports($version)) {
                    return $parser->parse($document);
                }
            }
        }

        $parser = $this->parsers[array_key_last($this->parsers)];
        $this->logger->warning(is_string($version)
            ? sprintf('No parser supports OpenAPI %s, read with %s', $version, $parser::class)
            : sprintf('The document has no OpenAPI version, read with %s', $parser::class));

        return $parser->parse($document);
    }
}
