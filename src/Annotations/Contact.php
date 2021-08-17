<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

/**
 * A "Contact Object": https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#contact-object.
 *
 * Contact information for the exposed API.
 *
 * @Annotation
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
abstract class AbstractContact extends AbstractAnnotation
{
    /**
     * The identifying name of the contact person/organization.
     *
     * @var string
     */
    public $name = Generator::UNDEFINED;

    /**
     * The URL pointing to the contact information.
     *
     * @var string
     */
    public $url = Generator::UNDEFINED;

    /**
     * The email address of the contact person/organization.
     *
     * @var string
     */
    public $email = Generator::UNDEFINED;

    /**
     * @inheritdoc
     */
    public static $_types = [
        'name' => 'string',
        'url' => 'string',
        'email' => 'string',
    ];

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
    class Contact extends AbstractContact
    {
        public function __construct(
            array $properties = [],
            $x = Generator::UNDEFINED
        ) {
            parent::__construct($properties + [
                    'x' => $x,
                ]);
        }
    }
} else {
    /**
     * @Annotation
     */
    class Contact extends AbstractContact
    {
        public function __construct(array $properties)
        {
            parent::__construct($properties);
        }
    }
}
