<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace Swagger\Annotations;

use \Swagger\Logger;

/**
 * @Annotation
 * [A "Parameter Object": https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#parameter-object
 * Describes a single operation parameter.
 * A unique parameter is defined by a combination of a name and location.
 */
class Parameter extends AbstractAnnotation
{
    /**
     * $ref See http://json-schema.org/latest/json-schema-core.html#rfc.section.7
     * @var string
     */
    public $ref;

    /**
     * The key into Components->parameters or Path->parameters array.
     * @var string
     */
    public $parameter;

    /**
     * The name of the parameter.
     * Parameter names are case sensitive.
     * If in is "path", the name field must correspond to the associated path segment from the path field in the Paths Object.
     * If in is "header" and the name field is "Accept", "Content-Type" or "Authorization", the parameter definition shall be ignored.
     * For all other cases, the name corresponds to the parameter name used by the in property.
     *
     * @var string
     */
    public $name;

    /**
     * The location of the parameter.
     * Possible values are "query", "header", "path" or "cookie".
     *
     * @var string
     */
    public $in;

    /**
     * A brief description of the parameter.
     * This could contain examples of use.
     * CommonMark syntax may be used for rich text representation.
     *
     * @var string
     */
    public $description;

    /**
     * Determines whether this parameter is mandatory.
     * If the parameter location is "path", this property is required and its value must be true.
     * Otherwise, the property may be included and its default value is false
     *
     * @var boolean
     */
    public $required;

    /**
     * Specifies that a parameter is deprecated and should be transitioned out of usage.
     *
     * @var boolean
     */
    public $deprecated;

    /**
     * Sets the ability to pass empty-valued parameters.
     * This is valid only for query parameters and allows sending a parameter with an empty value.
     * Default value is false. If style is used, and if behavior is n/a (cannot be serialized), the value of allowEmptyValue shall be ignored.
     *
     * @var boolean
     */
    public $allowEmptyValue;

    
    /**
     * Describes how the parameter value will be serialized depending on the type of the parameter value.
     * Default values (based on value of in): for query - form; for path - simple; for header - simple; for cookie - form.
     *
     * @var string
     */
    public $style;

    /**
     * When this is true, parameter values of type array or object generate separate parameters for each value of the array or key-value pair of the map.
     * For other types of parameters this property has no effect.
     * When style is form, the default value is true.
     * For all other styles, the default value is false.
     *
     * @var boolean
     */
    public $explode;

    /**
     * Determines whether the parameter value should allow reserved characters, as defined by RFC3986 :/?#[]@!$&'()*+,;= to be included without percent-encoding.
     * This property only applies to parameters with an in value of query.
     * The default value is false.
     *
     * @var boolean
     */
    public $allowReserved;

    /**
     * The schema defining the type used for the parameter.
     *
     * @var Schema
     */
    public $schema;

    /**
     * Example of the media type.
     * The example should match the specified schema and encoding properties if present.
     * The example object is mutually exclusive of the examples object.
     * Furthermore, if referencing a schema which contains an example, the example value shall override the example provided by the schema.
     * To represent examples of media types that cannot naturally be represented in JSON or YAML, a string value can contain the example with escaping where necessary.
     */
    public $example;

    /**
     * Examples of the media type.
     * Each example should contain a value in the correct format as specified in the parameter encoding.
     * The examples object is mutually exclusive of the example object.
     * Furthermore, if referencing a schema which contains an example, the examples value shall override the example provided by the schema.
     *
     * @var array
     */
    public $examples;

    /**
     * A map containing the representations for the parameter.
     * The key is the media type and the value describes it.
     * The map must only contain one entry.
     *
     * @var MediaType[]
     */
    public $content;

    /**
     * Path-style parameters defined by https://tools.ietf.org/html/rfc6570#section-3.2.7
     */
    public $matrix;

    /**
     * Label style parameters defined by https://tools.ietf.org/html/rfc6570#section-3.2.5
     */
    public $label;

    /**
     * Form style parameters defined by https://tools.ietf.org/html/rfc6570#section-3.2.8
     * This option replaces collectionFormat with a csv (when explode is false) or multi (when explode is true) value from OpenAPI 2.0.
     */
    public $form;

    /**
     * Simple style parameters defined by https://tools.ietf.org/html/rfc6570#section-3.2.2
     * This option replaces collectionFormat with a csv value from OpenAPI 2.0.
     *
     * @var array
     */
    public $simple;

    /**
     * Space separated array values.
     * This option replaces collectionFormat equal to ssv from OpenAPI 2.0.
     *
     * @var array
     */
    public $spaceDelimited;

    /**
     * Pipe separated array values.
     * This option replaces collectionFormat equal to pipes from OpenAPI 2.0.
     *
     * @var array
     */
    public $pipeDelimited;

    /**
     * Provides a simple way of rendering nested objects using form parameters.
     */
    public $deepObject;

    /** @inheritdoc */
    public static $_required = ['name', 'in'];

    /** @inheritdoc */
    public static $_types = [
        'name' => 'string',
        'in' => ['query', 'header', 'path', 'cookie'],
        'description' => 'string',
        'style' => ['matrix', 'label', 'form', 'simple', 'spaceDelimited', 'pipeDelimited', 'deepObject'],
        'required' => 'boolean',
    ];

    /** @inheritdoc */
    public static $_nested = [
        'Swagger\Annotations\Schema' => 'schema',
        'Swagger\Annotations\Examples' => ['examples'],
    ];

    /** @inheritdoc */
    public static $_parents = [
        'Swagger\Annotations\Components',
        'Swagger\Annotations\PathItem',
        'Swagger\Annotations\Operation',
        'Swagger\Annotations\Get',
        'Swagger\Annotations\Post',
        'Swagger\Annotations\Put',
        'Swagger\Annotations\Delete',
        'Swagger\Annotations\Patch',
        'Swagger\Annotations\Head',
        'Swagger\Annotations\Options',
        'Swagger\Annotations\Trace',
    ];

    /** @inheritdoc */
    public function validate($parents = [], $skip = [], $ref = '')
    {
        if (in_array($this, $skip, true)) {
            return true;
        }
        $valid = parent::validate($parents, $skip, $ref);
        if (empty($this->ref)) {
            if ($this->in === 'body') {
                if ($this->schema === null) {
                    Logger::notice('Field "schema" is required when ' . $this->identity() . ' is in "' . $this->in . '" in ' . $this->_context);
                    $valid = false;
                }
            } else {
                //                $validTypes = ['string', 'number', 'integer', 'boolean', 'array', 'file'];
//                if ($this->type === null) {
//                    Logger::notice($this->identity() . '->type is required when ' . $this->_identity([]) . '->in == "' . $this->in . '" in ' . $this->_context);
//                    $valid = false;
//                } elseif ($this->type === 'array' && $this->items === null) {
//                    Logger::notice($this->identity() . '->items required when ' . $this->_identity([]) . '->type == "array" in ' . $this->_context);
//                    $valid = false;
//                } elseif (in_array($this->type, $validTypes) === false) {
//                    $valid = false;
//                    Logger::notice($this->identity() . '->type must be "' . implode('", "', $validTypes) . '" when ' . $this->_identity([]) . '->in != "body" in ' . $this->_context);
//                } elseif ($this->type === 'file' && $this->in !== 'formData') {
//                    Logger::notice($this->identity() . '->in must be "formData" when ' . $this->_identity([]) . '->type == "file" in ' . $this->_context);
//                    $valid = false;
//                }
            }
        }
        return $valid;
    }

    /** @inheritdoc */
    public function identity()
    {
        return parent::_identity(['name', 'in']);
    }
}
