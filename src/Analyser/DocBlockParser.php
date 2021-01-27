<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Analyser;

use Doctrine\Common\Annotations\DocParser;
use OpenApi\Context;
use OpenApi\Logger;
use Psr\Log\LoggerInterface;

/**
 * PHP doc block parser.
 *
 * @see http://en.wikipedia.org/wiki/PHPDoc
 */
class DocBlockParser
{
    /**
     * Current context.
     *
     * This is used/referenced  when doctrine instantiates annotation classes as there is no mechanism
     * to pass the context into doctrine.
     *
     * @var null|Context
     */
    public static $context;

    /** @var DocParser The doctrine doc parser. */
    public $docParser;

    /** @var LoggerInterface A logger. */
    protected $logger;

    public function __construct(array $imports = [], ?LoggerInterface $logger = null)
    {
        $docParser = new DocParser();
        $docParser->setIgnoreNotImportedAnnotations(true);
        $docParser->setImports($imports);
        $this->docParser = $docParser;
        $this->logger = $logger ?: Logger::psrInstance();
    }

    /**
     * Use doctrine to parse the comment block and return the detected annotations.
     *
     * @param string  $comment a T_DOC_COMMENT
     * @param Context $context
     *
     * @return array Annotations
     */
    public function fromComment(string $comment, ?Context $context = null): array
    {
        $context = $context ?: new Context(['logger' => $this->logger]);
        $context->comment = $comment;

        $context->logger = $context->logger ?: $this->logger;

        try {
            // share context with AbstractAnnotation constructor...
            self::$context = $context;
            if ($context->is('annotations') === false) {
                $context->annotations = [];
            }
            $annotations = $this->docParser->parse($comment, $context);
            self::$context = null;

            return $annotations;
        } catch (\Exception $e) {
            self::$context = null;
            if (preg_match('/^(.+) at position ([0-9]+) in '.preg_quote((string) $context, '/').'\.$/', $e->getMessage(), $matches)) {
                $errorMessage = $matches[1];
                $errorPos = (int) $matches[2];
                $atPos = strpos($comment, '@');
                $context->line += substr_count($comment, "\n", 0, $atPos + $errorPos);
                $lines = explode("\n", substr($comment, $atPos, $errorPos));
                $context->character = strlen(array_pop($lines)) + 1; // position starts at 0 character starts at 1
                $this->logger->warning(new \Exception($errorMessage.' in '.$context, $e->getCode(), $e));
            } else {
                $this->logger->warning($e);
            }

            return [];
        }
    }
}
