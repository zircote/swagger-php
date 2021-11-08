<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

/**
 * An "Info Object": https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#info-object.
 *
 * The object provides metadata about the API.
 * The metadata may be used by the clients if needed, and may be presented in editing or documentation generation tools for convenience.
 *
 * @Annotation
 */
abstract class AbstractInfo extends AbstractAnnotation
{
    /**
     * The title of the application.
     *
     * @var string
     */
    public $title = Generator::UNDEFINED;

    /**
     * A short description of the application. CommonMark syntax may be used for rich text representation.
     *
     * @var string
     */
    public $description = Generator::UNDEFINED;

    /**
     * A URL to the Terms of Service for the API. must be in the format of a url.
     *
     * @var string
     */
    public $termsOfService = Generator::UNDEFINED;

    /**
     * The contact information for the exposed API.
     *
     * @var Contact
     */
    public $contact = Generator::UNDEFINED;

    /**
     * The license information for the exposed API.
     *
     * @var License
     */
    public $license = Generator::UNDEFINED;

    /**
     * The version of the OpenAPI document (which is distinct from the OpenAPI Specification version or the API implementation version).
     *
     * @var string
     */
    public $version = Generator::UNDEFINED;

    /**
     * @inheritdoc
     */
    public static $_required = ['title', 'version'];

    /**
     * @inheritdoc
     */
    public static $_types = [
        'title' => 'string',
        'version' => 'string',
        'description' => 'string',
        'termsOfService' => 'string',
    ];

    /**
     * @inheritdoc
     */
    public static $_nested = [
        Contact::class => 'contact',
        License::class => 'license',
        Attachable::class => ['attachables'],
    ];

    /**
     * @inheritdoc
     */
    public static $_parents = [
        OpenApi::class,
    ];
}

if (\PHP_VERSION_ID >= 80100) {
    /**
     * @Annotation
     */
    #[\Attribute(\Attribute::TARGET_CLASS)]
    class Info extends AbstractInfo
    {
        public function __construct(
            array $properties = [],
            string $version = Generator::UNDEFINED,
            string $description = Generator::UNDEFINED,
            string $title = Generator::UNDEFINED,
            string $termsOfService = Generator::UNDEFINED,
            ?Contact $contact = null,
            ?License $license = null,
            ?array $x = null,
            ?array $attachables = null
        ) {
            parent::__construct($properties + [
                    'version' => $version,
                    'description' => $description,
                    'title' => $title,
                    'termsOfService' => $termsOfService,
                    'x' => $x ?? Generator::UNDEFINED,
                    'value' => $this->combine($contact, $license, $attachables),
                ]);
        }
    }
} else {
    /**
     * @Annotation
     */
    class Info extends AbstractInfo
    {
        public function __construct(array $properties)
        {
            parent::__construct($properties);
        }
    }
}
