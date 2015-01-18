<?php

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

use PHPUnit_Framework_TestCase;
use Swagger\Annotations\AbstractAnnotation;
use Swagger\Context;
use Swagger\Parser;

class SwaggerTestCase extends PHPUnit_Framework_TestCase {

    /**
     *
     * @param string $comment Contents of a comment block
     * @return AbstractAnnotation[]
     */
    protected function parseComment($comment) {
        $parser = new Parser();
        $caller = Context::detect(1);
        $context = Context::detect(2);
        $context->line = -2;
        $context->filename = $caller->filename . ':' . $caller->line;
        return $parser->parseContents("<?php\n/**\n * " . implode("\n * ", explode("\n", $comment)) . "\n*/", $context);
    }

}
