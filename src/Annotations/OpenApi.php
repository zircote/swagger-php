<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace Swagger\Annotations;

use Exception;
use Swagger\Analysis;
use Swagger\Context;
use Swagger\Logger;

/**
 * @Annotation
 * This is the root document object for the API specification.
 *
 * A  "OpenApi Object": https://github.com/OAI/OpenAPI-Specification/blob/OpenAPI.next/versions/3.0.md#openapi-object
 */
class OpenApi extends AbstractAnnotation
{
    /**
     * The semantic version number of the OpenAPI Specification version that the OpenAPI document uses.
     * The openapi field should be used by tooling specifications and clients to interpret the OpenAPI document.
     * This is not related to the API info.version string.
     *
     * @var string
     */
    public $openapi = '3.0.0';

    /**
     * Provides metadata about the API. The metadata may be used by tooling as required.
     *
     * @var Info
     */
    public $info;

    /**
     * An array of Server Objects, which provide connectivity information to a target server.
     * If the servers property is not provided, or is an empty array, the default value would be a Server Object with a url value of /.
     *
     * @var Server[]
     */
    public $servers;

    /**
     * The available paths and operations for the API.
     *
     * @var Path[]
     */
    public $paths = [];

    /**
     * An element to hold various components for the specification.
     *
     * @var Components
     */
    public $components;


    /**
     * A declaration of which security mechanisms can be used across the API.
     * The list of values includes alternative security requirement objects that can be used.
     * Only one of the security requirement objects need to be satisfied to authorize a request.
     * Individual operations can override this definition.
     *
     * @var \Swagger\Annotations\SecurityScheme
     */
    public $security;

    /**
     * A list of tags used by the specification with additional metadata.
     * The order of the tags can be used to reflect on their order by the parsing tools.
     * Not all tags that are used by the Operation Object must be declared.
     * The tags that are not declared may be organized randomly or based on the tools' logic.
     * Each tag name in the list must be unique.
     *
     * @var Tag[]
     */
    public $tags;

    /**
     * Additional external documentation.
     *
     * @var ExternalDocumentation
     */
    public $externalDocs;

    /**
     * @var Analysis
     */
    public $_analysis;

    /**
     * Schemes
     *
     * @var array
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public $schemes;

    /** @inheritdoc */
    public static $_blacklist = ['_context', '_unmerged', '_analysis', 'security'];

    /** @inheritdoc */
    public static $_required = ['openapi', 'info', 'paths'];

    /** @inheritdoc */
    public static $_nested = [
        'Swagger\Annotations\Info' => 'info',
        'Swagger\Annotations\SecurityScheme' => 'security',
        'Swagger\Annotations\Server' => ['servers'],
        'Swagger\Annotations\PathItem' => ['paths', 'path'],
        'Swagger\Annotations\Components' => 'components',
        'Swagger\Annotations\Tag' => ['tags'],
        'Swagger\Annotations\ExternalDocumentation' => 'externalDocs',
    ];

    /** @inheritdoc */
    public static $_types = [];

    /** @inheritdoc */
    public function validate($parents = null, $skip = null, $ref = null)
    {
        if ($parents !== null || $skip !== null || $ref !== null) {
            Logger::notice('Nested validation for '.$this->identity().' not allowed');
            return false;
        }
        return parent::validate([], [], '#');
    }
    /**
     * Save the OpenAPI documentation to a file.
     * @param string $filename
     * @throws Exception
     */
    public function saveAs($filename)
    {
        if (file_put_contents($filename, $this) === false) {
            throw new Exception('Failed to saveAs("' . $filename . '")');
        }
    }

    /**
     * Look up an annotation with a $ref url.
     *
     * @param string $ref The $ref value, for example: "#/definitions/Product"
     * @throws Exception
     */
    public function ref($ref)
    {
        if (substr($ref, 0, 2) !== '#/') {
            // @todo Add support for external (http) refs?
            throw new Exception('Unsupported $ref "' . $ref . '", it should start with "#/"');
        }
        return $this->resolveRef($ref, '#/', $this, []);
    }

    /**
     * Recursive helper for ref()
     *
     * @param string $prefix The resolved path of the ref.
     * @param string $path A partial ref
     * @param * $container the container to resolve the ref in.
     * @param Array $mapping
     */
    private static function resolveRef($ref, $resolved, $container, $mapping)
    {
        if ($ref === $resolved) {
            return $container;
        }
        $path = substr($ref, strlen($resolved));
        $slash = strpos($path, '/');

        $subpath = $slash === false ? $path : substr($path, 0, $slash);
        $property = urldecode($subpath);
        $unresolved = $slash === false ? $resolved . $subpath : $resolved . $subpath . '/';

        if (is_object($container)) {
            if (property_exists($container, $property) === false) {
                throw new Exception('$ref "' . $unresolved . '" ');
            }
            if ($slash === false) {
                return $container->$property;
            }
            $mapping = [];
            if ($container instanceof AbstractAnnotation) {
                foreach ($container::$_nested as $className => $nested) {
                    if (is_string($nested) === false && count($nested) === 2 && $nested[0] === $property) {
                        $mapping[$className] = $nested[1];
                    }
                }
            }
            return self::resolveRef($ref, $unresolved, $container->$property, $mapping);
        } elseif (is_array($container)) {
            if (array_key_exists($property, $container)) {
                return self::resolveRef($ref, $unresolved, $container[$property], []);
            }
            foreach ($mapping as $className => $keyField) {
                foreach ($container as $key => $item) {
                    if (is_numeric($key) && is_object($item) && $item instanceof $className && $item->$keyField === $property) {
                        return self::resolveRef($ref, $unresolved, $item, []);
                    }
                }
            }
        }
        throw new Exception('$ref "' . $unresolved . '" not found');
    }
}
