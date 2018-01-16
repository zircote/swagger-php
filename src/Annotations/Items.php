<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace Swagger\Annotations;

use Swagger\Logger;

/**
 * @Annotation
 * The description of an item in a Schema with type "array"
 */
class Items extends Schema
{
    /** @inheritdoc */
    public static $_nested = [
        'Swagger\Annotations\Items' => 'items',
        'Swagger\Annotations\Property' => ['properties', 'property'],
        'Swagger\Annotations\ExternalDocumentation' => 'externalDocs',
        'Swagger\Annotations\Xml' => 'xml'
    ];

    /** @inheritdoc */
    public static $_parents = [
        'Swagger\Annotations\Property',
        'Swagger\Annotations\AdditionalProperties',
        'Swagger\Annotations\Schema',
        'Swagger\Annotations\JsonContent',
        'Swagger\Annotations\XmlContent',
        'Swagger\Annotations\Items'
    ];

    /** @inheritdoc */
    public function validate($parents = [], $skip = [], $ref = '')
    {
        if (in_array($this, $skip, true)) {
            return true;
        }
        $valid = parent::validate($parents, $skip);
        if (!$this->ref) {
            if ($this->type === 'array' && $this->items === null) {
                Logger::notice('@OAS\Items() is required when ' . $this->identity() . ' has type "array" in ' . $this->_context);
                $valid = false;
            }
            $parent = end($parents);
            if (is_object($parent) && ($parent instanceof Parameter && $parent->in !== 'body' || $parent instanceof Header)) {
                // This is a "Items Object" https://github.com/OAI/OpenAPI-Specification/blob/OpenAPI.next/versions/3.0.md#items-object
                // A limited subset of JSON-Schema's items object.
                $allowedTypes = ['string', 'number', 'integer', 'boolean', 'array'];
                if (in_array($this->type, $allowedTypes) === false) {
                    Logger::notice('@OAS\Items()->type="'.$this->type.'" not allowed inside a '.$parent->_identity([]).' must be "'.implode('", "', $allowedTypes).'" in ' . $this->_context);
                    $valid = false;
                }
            }
        }
        return $valid;
        // @todo Additional validation when used inside a Header or Parameter context.
    }
}
