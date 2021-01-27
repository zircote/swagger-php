<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

use Doctrine\Common\Annotations\AnnotationRegistry;
use OpenApi\Analyser\DocBlockParser;
use Psr\Log\LoggerInterface;

if (class_exists(AnnotationRegistry::class, true)) {
    AnnotationRegistry::registerLoader(
        function (string $class): bool {
            if (Analyser::$whitelist === false) {
                $whitelist = ['OpenApi\\Annotations\\'];
            } else {
                $whitelist = Analyser::$whitelist;
            }
            foreach ($whitelist as $namespace) {
                if (strtolower(substr($class, 0, strlen($namespace))) === strtolower($namespace)) {
                    $loaded = class_exists($class);
                    if (!$loaded && $namespace === 'OpenApi\\Annotations\\') {
                        if (in_array(strtolower(substr($class, 20)), ['definition', 'path'])) {
                            // Detected an 2.x annotation?
                            throw new OpenApiException('The annotation @SWG\\'.substr($class, 20).'() is deprecated. Found in '.Analyser::$context."\nFor more information read the migration guide: https://github.com/zircote/swagger-php/blob/master/docs/Migrating-to-v3.md");
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
 * @deprecated
 */
class Analyser extends DocBlockParser
{
    /**
     * List of namespaces that should be detected by the doctrine annotation parser.
     * Set to false to load all detected classes.
     *
     * @var array|false
     *
     * @deprecated
     */
    public static $whitelist = ['OpenApi\\Annotations\\'];

    /**
     * Use @OA\* for OpenAPI annotations (unless overwritten by a use statement).
     *
     * @deprecated
     */
    public static $defaultImports = ['oa' => 'OpenApi\\Annotations'];

    public function __construct(?LoggerInterface $logger = null)
    {
        parent::__construct(static::$defaultImports, $logger);
    }
}
