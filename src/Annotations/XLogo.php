<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

/**
 * License information for the exposed API.
 *
 * A "License Object": https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#license-object
 *
 * @Annotation
 */
class XLogo extends AbstractAnnotation
{

    /**
     * A URL to the logo to be used for the API. This MUST be in the form of a URL.
     *
     * @var string
     */
    public $url = Generator::UNDEFINED;

    /**
     * A backgroundColor to the logo to be used for the API. This MUST be in the form of a valid CSS color name or #value.
     *
     * @var string
     */
    public $backgroundColor = Generator::UNDEFINED;

    /**
     * A altText to the logo to be used for the API. This MUST be a string.
     *
     * @var string
     */
    public $altText = Generator::UNDEFINED;

    /**
     * @inheritdoc
     */
    public static $_types = [
        'url' => 'string',
        'backgroundColor' => 'string',
        'altText' => 'string',
    ];

    /**
     * @inheritdoc
     */
    public static $_required = ['url'];

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
    public function validate(array $parents = [], array $skip = [], string $ref = ''): bool
    {
        $valid = parent::validate($parents, $skip);

        if ($this->_context->isVersion(OpenApi::VERSION_3_1_0)) {
            if (!Generator::isDefault($this->url) && $this->identifier !== Generator::UNDEFINED) {
                $this->_context->logger->warning($this->identity() . ' url and identifier are mutually exclusive');
                $valid = false;
            }
        }

        return $valid;
    }
}
