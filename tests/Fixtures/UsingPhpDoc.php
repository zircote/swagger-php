<?php declare(strict_types=1);
namespace SwaggerFixtures;

/**
 * @OAS\Info(title="Fixture for AugmentOperationTest", version="test")
 */
class UsingPhpDoc
{
    /**
     * Example summary
     *
     * Example description...
     * More description...
     *
     * @OAS\Get(path="api/test1", @OAS\Response(response="200", description="a response"))
     */
    public function methodWithDescription()
    {
    }

    /**
     * Example summary
     *
     * @OAS\Get(path="api/test2", @OAS\Response(response="200", description="a response"))
     */
    public function methodWithSummary()
    {
    }
}
