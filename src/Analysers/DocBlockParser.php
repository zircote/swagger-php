<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Analysers;

use Doctrine\Common\Annotations\DocParser;
use OpenApi\Annotations as OA;
use OpenApi\Context;
use OpenApi\Generator;

/**
 * Extract swagger-php annotations from a [PHPDoc](http://en.wikipedia.org/wiki/PHPDoc) using Doctrine's DocParser.
 */
/**
 * @deprecated since 6.11, removed in 8.0 - use attributes instead
 */
class DocBlockParser
{
    protected DocParser $docParser;

    /**
     * @param array<string, string> $aliases
     */
    public function __construct(array $aliases = [])
    {
        if (DocBlockParser::isEnabled()) {
            $docParser = new DocParser();
            $docParser->setIgnoreNotImportedAnnotations(true);
            /** @var array<string, class-string> $importedAliases doctrine wants class-string; ours may be bare namespaces too */
            $importedAliases = $aliases;
            $docParser->setImports($importedAliases);
            $this->docParser = $docParser;
        }
    }

    /**
     * Check if we can process annotations.
     */
    public static function isEnabled(): bool
    {
        return class_exists('Doctrine\\Common\\Annotations\\DocParser');
    }

    /**
     * @param array<string, string> $aliases
     */
    public function setAliases(array $aliases): void
    {
        /** @var array<string, class-string> $importedAliases doctrine wants class-string; ours may be bare namespaces too */
        $importedAliases = $aliases;
        $this->docParser->setImports($importedAliases);
    }

    /**
     * Use doctrine to parse the comment block and return the detected annotations.
     *
     * @param string $comment a T_DOC_COMMENT
     *
     * @return list<OA\AbstractAnnotation|object>
     */
    public function fromComment(string $comment, Context $context): array
    {
        $context->comment = $comment;

        try {
            Generator::$context = $context;
            if ($context->is('annotations') === false) {
                $context->annotations = [];
            }

            return $this->docParser->parse($comment, $context->getDebugLocation());
        } catch (\Exception $exception) {
            if (preg_match('/^(.+) at position ([0-9]+) in ' . preg_quote((string) $context, '/') . '\.$/', $exception->getMessage(), $matches)) {
                $errorMessage = $matches[1];
                $errorPos = (int) $matches[2];
                $atPos = strpos($comment, '@') ?: 0;
                $context->line -= substr_count($comment, "\n", $atPos + $errorPos) + 1;
                $lines = explode("\n", substr($comment, $atPos, $errorPos));
                $context->character = strlen(array_pop($lines)) + 1; // position starts at 0 character starts at 1
                $context->logger->error($errorMessage . ' in ' . $context, ['exception' => $exception]);
            } else {
                $context->logger->error(
                    $exception->getMessage() . ($context->filename ? ('; file=' . $context->filename) : ''),
                    ['exception' => $exception]
                );
            }

            return [];
        } finally {
            Generator::$context = null;
        }
    }
}
