<?php
namespace SwaggerFixtures;

/**
 * @SWG\Info(title="Fixture for ParserTest", version="test")
 */
use AnotherNamespace\Annotations as Annotation;

/**
 * @Annotation\Unrelated("user")
 */
class ThirdPartyAnnotations
{
    /**
     * @Annotation\Unrelated()
     */
    public $id;

    /**
     * @Annotation\Unrelated("user")
     */
    public $username;

    /**
     * @Annotation\Unrelated("email")
     */
    public $email;

    /**
     * @SWG\Get(path="api/3rd-party", @SWG\Response(response="200", description="a response"))
     */
    public function methodWithSwaggerAnnotation()
    {
    }
}
