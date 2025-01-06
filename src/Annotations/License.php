<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

/**
 * License information for the exposed API.
 *
 * @see [License Object](https://spec.openapis.org/oas/v3.1.1.html#license-object)
 *
 * @Annotation
 */
class License extends AbstractAnnotation
{
    /**
     * The license name used for the API.
     *
     * @var string
     */
    public $name = Generator::UNDEFINED;

    /**
     * An SPDX license expression for the API. The `identifier` field is mutually exclusive of the `url` field.
     *
     * @var string
     */
    public $identifier = Generator::UNDEFINED;

    /**
     * An URL to the license used for the API. This MUST be in the form of a URL.
     *
     * The `url` field is mutually exclusive of the `identifier` field.
     *
     * @var string
     */
    public $url = Generator::UNDEFINED;

    /**
     * @inheritdoc
     */
    public static $_types = [
        'name' => 'string',
        'identifier' => 'string',
        'url' => 'string',
    ];

    /**
     * @inheritdoc
     */
    public static $_required = ['name'];

    /**
     * @inheritdoc
     */
    public static $_parents = [
        Info::class,
    ];

    /**
     * @inheritdoc
     */
    public static $_nested = [
        Attachable::class => ['attachables'],
    ];

    /**
     * @inheritdoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = parent::jsonSerialize();

        if ($this->_context->isVersion(OpenApi::VERSION_3_0_0)) {
            unset($data->identifier);
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function validate(array $stack = [], array $skip = [], string $ref = '', $context = null): bool
    {
        $valid = parent::validate($stack, $skip, $ref, $context);

        if ($this->_context->isVersion(OpenApi::VERSION_3_1_0)) {
            if (!Generator::isDefault($this->url) && $this->identifier !== Generator::UNDEFINED) {
                $this->_context->logger->warning($this->identity() . ' url and identifier are mutually exclusive');
                $valid = false;
            }
        }

        return $valid;
    }
}
