<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Analysis;
use OpenApi\Generator;
use OpenApi\OpenApiException;
use OpenApi\Util;

/**
 * This is the root document object for the API specification.
 *
 * @see [OpenApi Object](https://spec.openapis.org/oas/v3.1.1.html#openapi-object)
 *
 * @Annotation
 */
class OpenApi extends AbstractAnnotation
{
    public const VERSION_3_0_0 = '3.0.0';
    public const VERSION_3_1_0 = '3.1.0';
    public const DEFAULT_VERSION = self::VERSION_3_0_0;
    public const SUPPORTED_VERSIONS = [self::VERSION_3_0_0, self::VERSION_3_1_0];

    /**
     * The semantic version number of the OpenAPI Specification version that the OpenAPI document uses.
     *
     * The openapi field should be used by tooling specifications and clients to interpret the OpenAPI document.
     *
     * A version specified via <code>Generator::setVersion()</code> will overwrite this value.
     *
     * This is not related to the API info::version string.
     *
     * @var '3.0.0'|'3.1.0'
     */
    public $openapi = self::DEFAULT_VERSION;

    /**
     * Provides metadata about the API. The metadata may be used by tooling as required.
     *
     * @var Info
     */
    public $info = Generator::UNDEFINED;

    /**
     * An array of <code>@Server</code> objects, which provide connectivity information to a target server.
     *
     * If not provided, or is an empty array, the default value would be a Server Object with an url value of <code>/</code>.
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
     * A declaration of which security mechanisms can be used across the API.
     *
     * The list of values includes alternative security requirement objects that can be used.
     * Only one of the security requirement objects need to be satisfied to authorize a request.
     * Individual operations can override this definition.
     * To make security optional, an empty security requirement (<code>{}</code>) can be included in the array.
     *
     * @var array
     */
    public $security = Generator::UNDEFINED;

    /**
     * A list of tags used by the specification with additional metadata.
     *
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
     * The available webhooks for the API.
     *
     * @var Webhook[]
     */
    public $webhooks = Generator::UNDEFINED;

    /**
     * @var Analysis
     */
    public $_analysis = Generator::UNDEFINED;

    /**
     * @inheritdoc
     */
    public static $_required = ['openapi', 'info'];

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
        Webhook::class => ['webhooks', 'webhook'],
        Attachable::class => ['attachables'],
    ];

    /**
     * @inheritdoc
     */
    public static $_types = [];

    /**
     * @inheritdoc
     */
    public function validate(?array $stack = null, ?array $skip = null, string $ref = '', $context = null): bool
    {
        if ($stack !== null || $skip !== null || $ref !== '') {
            $this->_context->logger->warning('Nested validation for ' . $this->identity() . ' not allowed');

            return false;
        }

        if (!in_array($this->openapi, self::SUPPORTED_VERSIONS)) {
            $this->_context->logger->warning('Unsupported OpenAPI version "' . $this->openapi . '". Allowed versions are: ' . implode(', ', self::SUPPORTED_VERSIONS));

            return false;
        }

        /* paths is optional in 3.1.0 */
        if ($this->openapi === self::VERSION_3_0_0 && Generator::isDefault($this->paths)) {
            $this->_context->logger->warning('Required @OA\PathItem() not found');
        }

        if ($this->openapi === self::VERSION_3_1_0
            && Generator::isDefault($this->paths)
            && Generator::isDefault($this->webhooks)
            && Generator::isDefault($this->components)
        ) {
            $this->_context->logger->warning("At least one of 'Required @OA\PathItem(), @OA\Components() or @OA\Webhook() not found'");

            return false;
        }

        return parent::validate([], [], '#', new \stdClass());
    }

    /**
     * Save the OpenAPI documentation to a file.
     */
    public function saveAs(string $filename, string $format = 'auto'): void
    {
        if ($format === 'auto') {
            $format = strtolower(substr($filename, -5)) === '.json' ? 'json' : 'yaml';
        }

        $content = strtolower($format) === 'json' ? $this->toJson() : $this->toYaml();

        if (file_put_contents($filename, $content) === false) {
            throw new OpenApiException('Failed to saveAs("' . $filename . '", "' . $format . '")');
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
            throw new OpenApiException('Unsupported $ref "' . $ref . '", it should start with "#/"');
        }

        return self::resolveRef($ref, '#/', $this, []);
    }

    /**
     * Recursive helper for ref().
     *
     * @param array|AbstractAnnotation $container
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
            // support use x-* in ref
            $xKey = strpos($property, 'x-') === 0 ? substr($property, 2) : null;
            if ($xKey) {
                if (!is_array($container->x) || !array_key_exists($xKey, $container->x)) {
                    $xKey = null;
                }
            }
            if (property_exists($container, $property) === false && !$xKey) {
                throw new OpenApiException('$ref "' . $ref . '" not found');
            }

            $nextContainer = $xKey ? $container->x[$xKey] : $container->{$property};

            if ($slash === false) {
                return $nextContainer;
            }
            $mapping = [];
            foreach ($container::$_nested as $nestedClass => $nested) {
                if (is_string($nested) === false && count($nested) === 2 && $nested[0] === $property) {
                    $mapping[$nestedClass] = $nested[1];
                }
            }

            return self::resolveRef($ref, $unresolved, $nextContainer, $mapping);
        } elseif (is_array($container)) {
            if (array_key_exists($property, $container)) {
                return self::resolveRef($ref, $unresolved, $container[$property], []);
            }
            foreach ($mapping as $nestedClass => $keyField) {
                foreach ($container as $key => $item) {
                    if (is_numeric($key) && is_object($item) && $item instanceof $nestedClass && (string) $item->{$keyField} === $property) {
                        return self::resolveRef($ref, $unresolved, $item, []);
                    }
                }
            }
        }

        throw new OpenApiException('$ref "' . $unresolved . '" not found');
    }

    /**
     * @inheritdoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = parent::jsonSerialize();

        if (!$this->_context->isVersion(OpenApi::VERSION_3_1_0)) {
            unset($data->webhooks);
        }

        return $data;
    }
}
