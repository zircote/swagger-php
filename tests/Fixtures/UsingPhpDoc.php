<?php
namespace SwaggerFixtures;

/**
 * @SWG\Info(title="Fixture for AugmentOperationTest", version="test")
 */
class UsingPhpDoc
{
    /**
     * Example summary
     *
     * Example description...
     * More description...
     *
     * @SWG\Get(path="api/test1", @SWG\Response(response="200", description="a response"))
     */
    public function methodWithDescription()
    {
    }

    /**
     * Example summary
     *
     * @SWG\Get(path="api/test2", @SWG\Response(response="200", description="a response"))
     */
    public function methodWithSummary()
    {
    }
}
