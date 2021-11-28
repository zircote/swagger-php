<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

/**
 * License information for the exposed API.
 *
 * A "License Object": https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#license-object
 *
 * @Annotation
 */
abstract class AbstractLicense extends AbstractAnnotation
{
    /**
     * The license name used for the API.
     *
     * @var string
     */
    public $name = Generator::UNDEFINED;

    /**
     * A URL to the license used for the API.
     *
     * @var string
     */
    public $url = Generator::UNDEFINED;

    /**
     * @inheritdoc
     */
    public static $_types = [
        'name' => 'string',
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
}

if (\PHP_VERSION_ID >= 80100) {
    /**
     * @Annotation
     */
    #[\Attribute(\Attribute::TARGET_CLASS)]
    class License extends AbstractLicense
    {
        public function __construct(
            array $properties = [],
            string $name = Generator::UNDEFINED,
            string $url = Generator::UNDEFINED,
            ?array $x = null,
            ?array $attachables = null
        ) {
            parent::__construct($properties + [
                    'name' => $name,
                    'url' => $url,
                    'x' => $x ?? Generator::UNDEFINED,
                    'value' => $this->combine($attachables),
                ]);
        }
    }
} else {
    /**
     * @Annotation
     */
    class License extends AbstractLicense
    {
        public function __construct(array $properties)
        {
            parent::__construct($properties);
        }
    }
}
