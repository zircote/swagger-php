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
class DocBlockParser
{
    /**
     * @var DocParser
     */
    protected $docParser;

    /**
     * @param array<string, class-string> $aliases
     */
    public function __construct(array $aliases = [])
    {
        $docParser = new DocParser();
        $docParser->setIgnoreNotImportedAnnotations(true);
        $docParser->setImports($aliases);
        $this->docParser = $docParser;
    }

    /**
     * @param array<string, class-string> $aliases
     */
    public function setAliases(array $aliases): void
    {
        $this->docParser->setImports($aliases);
    }

    /**
     * Use doctrine to parse the comment block and return the detected annotations.
     *
     * @param string  $comment a T_DOC_COMMENT
     * @param Context $context
     *
     * @return array<OA\AbstractAnnotation>
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
        } catch (\Exception $e) {
            if (preg_match('/^(.+) at position ([0-9]+) in ' . preg_quote((string) $context, '/') . '\.$/', $e->getMessage(), $matches)) {
                $errorMessage = $matches[1];
                $errorPos = (int) $matches[2];
                $atPos = strpos($comment, '@');
                $context->line -= substr_count($comment, "\n", $atPos + $errorPos) + 1;
                $lines = explode("\n", substr($comment, $atPos, $errorPos));
                $context->character = strlen(array_pop($lines)) + 1; // position starts at 0 character starts at 1
                $context->logger->error($errorMessage . ' in ' . $context, ['exception' => $e]);
            } else {
                $context->logger->error(
                    $e->getMessage() . ($context->filename ? ('; file=' . $context->filename) : ''),
                    ['exception' => $e]
                );
            }

            return [];
        } finally {
            Generator::$context = null;
        }
    }
}
