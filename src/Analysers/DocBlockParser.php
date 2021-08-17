<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Analysers;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\DocParser;
use OpenApi\Context;
use OpenApi\Generator;

if (class_exists(AnnotationRegistry::class, true)) {
    AnnotationRegistry::registerLoader(
        function (string $class): bool {
            if (DocBlockParser::$whitelist === false) {
                $whitelist = ['OpenApi\\Annotations\\'];
            } else {
                $whitelist = DocBlockParser::$whitelist;
            }
            foreach ($whitelist as $namespace) {
                if (strtolower(substr($class, 0, strlen($namespace))) === strtolower($namespace)) {
                    $loaded = class_exists($class);
                    if (!$loaded && $namespace === 'OpenApi\\Annotations\\') {
                        if (in_array(strtolower(substr($class, 20)), ['definition', 'path'])) {
                            // Detected an 2.x annotation?
                            throw new \Exception('The annotation @SWG\\' . substr($class, 20) . '() is deprecated. Found in ' . Generator::$context . "\nFor more information read the migration guide: https://github.com/zircote/swagger-php/blob/master/docs/Migrating-to-v3.md");
                        }
                    }

                    return $loaded;
                }
            }

            return false;
        }
    );
}

/**
 * Extract swagger-php annotations from a [PHPDoc](http://en.wikipedia.org/wiki/PHPDoc) using Doctrine's DocParser.
 */
class DocBlockParser
{
    /**
     * List of namespaces that should be detected by the doctrine annotation parser.
     * Set to false to load all detected classes.
     *
     * @var array|false
     *
     * @deprecated use \OpenApi\Generator::setAliases() instead
     */
    public static $whitelist = ['OpenApi\\Annotations\\'];

    /**
     * Use @OA\* for OpenAPI annotations (unless overwritten by a use statement).
     *
     * @deprecated use \OpenApi\Generator::setNamespaces() instead
     */
    public static $defaultImports = ['oa' => 'OpenApi\\Annotations'];

    /**
     * @var DocParser
     */
    public $docParser;

    public function __construct(?DocParser $docParser = null)
    {
        if ($docParser === null) {
            $docParser = new DocParser();
            $docParser->setIgnoreNotImportedAnnotations(true);
            $docParser->setImports(static::$defaultImports);
        }
        $this->docParser = $docParser;
    }

    /**
     * Use doctrine to parse the comment block and return the detected annotations.
     *
     * @param string  $comment a T_DOC_COMMENT
     * @param Context $context
     *
     * @return array Annotations
     */
    public function fromComment(string $comment, Context $context): array
    {
        $context->comment = $comment;

        try {
            Generator::$context = $context;
            if ($context->is('annotations') === false) {
                $context->annotations = [];
            }

            return $this->docParser->parse($comment);
        } catch (\Exception $e) {
            if (preg_match('/^(.+) at position ([0-9]+) in ' . preg_quote((string) $context, '/') . '\.$/', $e->getMessage(), $matches)) {
                $errorMessage = $matches[1];
                $errorPos = (int) $matches[2];
                $atPos = strpos($comment, '@');
                $context->line += substr_count($comment, "\n", 0, $atPos + $errorPos);
                $lines = explode("\n", substr($comment, $atPos, $errorPos));
                $context->character = strlen(array_pop($lines)) + 1; // position starts at 0 character starts at 1
                $context->logger->error($errorMessage . ' in ' . $context, ['exception' => $e]);
            } else {
                $context->logger->error($e);
            }

            return [];
        } finally {
            Generator::$context = null;
        }
    }
}
