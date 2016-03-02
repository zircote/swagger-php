<?php
namespace SwaggerFixtures;

/**
 * @SWG\Info(title="Fixture for AugmentOperationTest", version="test")
 */
class UsingPhpDoc
{
    /**
     * Get protected item
     *
     * Example description...
     * More description...
     *
     * @SWG\Get(path="api/test", @SWG\Response(response="200", description="a response"))
     */
    public function methodWithSwaggerAnnotation()
    {
    }
}
