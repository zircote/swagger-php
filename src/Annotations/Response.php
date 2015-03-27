<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Annotations;

use Swagger\Logger;

/**
 * @Annotation
 *
 * A Swagger "Response Object": https://github.com/swagger-api/swagger-spec/blob/master/versions/2.0.md#responseObject
 */
class Response extends AbstractAnnotation {

    /**
     * The key into Operations->reponses array.
     * 
     * @var string a HTTP Status Code or "default" 
     */
    public $status;

    /**
     * A short description of the response. GFM syntax can be used for rich text representation.
     * @var string
     */
    public $description;

    /**
     * A definition of the response structure. It can be a primitive, an array or an object. If this field does not exist, it means no content is returned as part of the response. As an extension to the Schema Object, its root type value may also be "file". This SHOULD be accompanied by a relevant produces mime-type.
     * @var Schema
     */
    public $schema;

    /**
     * A list of headers that are sent with the response.
     * @var Header[]
     */
    public $headers;

    /**
     * An example of the response message.
     * @var array
     */
    public $examples;

    /** @inheritdoc */
    public static $_key = 'status';

    /** @inheritdoc */
    public static $_required = ['description'];

    /** @inheritdoc */
    public static $_types = [
        'description' => 'string',
    ];

    /** @inheritdoc */
    public static $_nested = [
        'Swagger\Annotations\Schema' => 'schema',
        'Swagger\Annotations\Header' => 'headers[]'
    ];

    /** @inheritdoc */
    public static $_parents = [
        'Swagger\Annotations\Operation',
        'Swagger\Annotations\Get',
        'Swagger\Annotations\Post',
        'Swagger\Annotations\Put',
        'Swagger\Annotations\Patch',
        'Swagger\Annotations\Delete',
        'Swagger\Annotations\Swagger'
    ];

    public function validate($skip = array()) {
        if (in_array($this, $skip, true)) {
            return true;
        }
        $valid = parent::validate($skip);
        if ($this->status !== null && $this->status !== 'default' && preg_match('/^[12345]{1}[0-9]{2}$/', $this->status) === 0) {
            Logger::notice('Invalid value "' . $this->status . '" for ' . $this->_identity([]) . '->status, expecting "default" or a HTTP Status Code in '.$this->_context);
        }
        return $valid;

    }

}
