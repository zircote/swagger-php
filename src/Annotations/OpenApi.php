<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Analysis;
use OpenApi\Generator;
use OpenApi\Util;

/**
 * This is the root document object for the API specification.
 *
 * A  "OpenApi Object": https://github.com/OAI/OpenAPI-Specification/blob/OpenAPI.next/versions/3.0.md#openapi-object
 *
 * @Annotation
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class AbstractOpenApi extends AbstractAnnotation
{
    public const DEFAULT_VERSION = '3.0.0';
    /**
     * The semantic version number of the OpenAPI Specification version that the OpenAPI document uses.
     * The openapi field should be used by tooling specifications and clients to interpret the OpenAPI document.
     * This is not related to the API info.version string.
     *
     * @var string
     */
    public $openapi = self::DEFAULT_VERSION;

    /**
     * Provides metadata about the API. The metadata may be used by tooling as required.
     *
     * @var Info
     */
    public $info = Generator::UNDEFINED;

    /**
     * An array of Server Objects, which provide connectivity information to a target server.
     * If the servers property is not provided, or is an empty array, the default value would be a Server Object with a url value of /.
     *
     * @var Server[]
     */
    public $servers = Generator::UNDEFINED;

    /**
     * The available paths and operations for the API.
     *
     * @var PathItem[]
     */
    public $paths = Generator::UNDEFINED;

    /**
     * An element to hold various components for the specification.
     *
     * @var Components
     */
    public $components = Generator::UNDEFINED;

    /**
     * Lists the required security schemes to execute this operation.
     * The name used for each property must correspond to a security scheme declared
     * in the Security Schemes under the Components Object.
     * Security Requirement Objects that contain multiple schemes require that
     * all schemes must be satisfied for a request to be authorized.
     * This enables support for scenarios where multiple query parameters or
     * HTTP headers are required to convey security information.
     * When a list of Security Requirement Objects is defined on the Open API object or
     * Operation Object, only one of Security Requirement Objects in the list needs to
     * be satisfied to authorize the request.
     *
     * @var array
     */
    public $security = Generator::UNDEFINED;

    /**
     * A list of tags used by the specification with additional metadata.
     * The order of the tags can be used to reflect on their order by the parsing tools.
     * Not all tags that are used by the Operation Object must be declared.
     * The tags that are not declared may be organized randomly or based on the tools' logic.
     * Each tag name in the list must be unique.
     *
     * @var Tag[]
     */
    public $tags = Generator::UNDEFINED;

    /**
     * Additional external documentation.
     *
     * @var ExternalDocumentation
     */
    public $externalDocs = Generator::UNDEFINED;

    /**
     * @var Analysis
     */
    public $_analysis = Generator::UNDEFINED;

    /**
     * @inheritdoc
     */
    public static $_required = ['openapi', 'info', 'paths'];

    /**
     * @inheritdoc
     */
    public static $_nested = [
        Info::class => 'info',
        Server::class => ['servers'],
        PathItem::class => ['paths', 'path'],
        Components::class => 'components',
        Tag::class => ['tags'],
        ExternalDocumentation::class => 'externalDocs',
        Attachable::class => ['attachables'],
    ];

    /**
     * @inheritdoc
     */
    public static $_types = [];

    /**
     * @inheritdoc
     */
    public function validate(array $parents = null, array $skip = null, string $ref = ''): bool
    {
        if ($parents !== null || $skip !== null || $ref !== '') {
            $this->_context->logger->warning('Nested validation for ' . $this->identity() . ' not allowed');

            return false;
        }

        return parent::validate([], [], '#');
    }

    /**
     * Save the OpenAPI documentation to a file.
     */
    public function saveAs(string $filename, string $format = 'auto'): void
    {
        if ($format === 'auto') {
            $format = strtolower(substr($filename, -5)) === '.json' ? 'json' : 'yaml';
        }

        if (strtolower($format) === 'json') {
            $content = $this->toJson();
        } else {
            $content = $this->toYaml();
        }

        if (file_put_contents($filename, $content) === false) {
            throw new \Exception('Failed to saveAs("' . $filename . '", "' . $format . '")');
        }
    }

    /**
     * Look up an annotation with a $ref url.
     *
     * @param string $ref The $ref value, for example: "#/components/schemas/Product"
     */
    public function ref(string $ref)
    {
        if (substr($ref, 0, 2) !== '#/') {
            // @todo Add support for external (http) refs?
            throw new \Exception('Unsupported $ref "' . $ref . '", it should start with "#/"');
        }

        return $this->resolveRef($ref, '#/', $this, []);
    }

    /**
     * Recursive helper for ref().
     */
    private static function resolveRef(string $ref, string $resolved, $container, array $mapping)
    {
        if ($ref === $resolved) {
            return $container;
        }
        $path = substr($ref, strlen($resolved));
        $slash = strpos($path, '/');

        $subpath = $slash === false ? $path : substr($path, 0, $slash);
        $property = Util::refDecode($subpath);
        $unresolved = $slash === false ? $resolved . $subpath : $resolved . $subpath . '/';

        if (is_object($container)) {
            if (property_exists($container, $property) === false) {
                throw new \Exception('$ref "' . $ref . '" not found');
            }
            if ($slash === false) {
                return $container->$property;
            }
            $mapping = [];
            if ($container instanceof AbstractAnnotation) {
                foreach ($container::$_nested as $nestedClass => $nested) {
                    if (is_string($nested) === false && count($nested) === 2 && $nested[0] === $property) {
                        $mapping[$nestedClass] = $nested[1];
                    }
                }
            }

            return self::resolveRef($ref, $unresolved, $container->$property, $mapping);
        } elseif (is_array($container)) {
            if (array_key_exists($property, $container)) {
                return self::resolveRef($ref, $unresolved, $container[$property], []);
            }
            foreach ($mapping as $nestedClass => $keyField) {
                foreach ($container as $key => $item) {
                    if (is_numeric($key) && is_object($item) && $item instanceof $nestedClass && (string) $item->$keyField === $property) {
                        return self::resolveRef($ref, $unresolved, $item, []);
                    }
                }
            }
        }

        throw new \Exception('$ref "' . $unresolved . '" not found');
    }
}

if (\PHP_VERSION_ID >= 80100) {
    /**
     * @Annotation
     */
    #[\Attribute(\Attribute::TARGET_CLASS)]
    class OpenApi extends AbstractOpenApi
    {
        public function __construct(
            array $properties = [],
            string $openapi = self::DEFAULT_VERSION,
            ?Info $info = null,
            ?array $servers = null,
            ?array $tags = null,
            ?ExternalDocumentation $externalDocs = null,
            ?array $x = null,
            ?array $attachables = null
        ) {
            parent::__construct($properties + [
                    'openapi' => $openapi,
                    'x' => $x ?? Generator::UNDEFINED,
                    'value' => $this->combine($info, $servers, $tags, $externalDocs, $attachables),
                ]);
        }
    }
} else {
    /**
     * @Annotation
     */
    class OpenApi extends AbstractOpenApi
    {
        public function __construct(array $properties)
        {
            parent::__construct($properties);
        }
    }
}
