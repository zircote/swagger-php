<?php
namespace Swagger\Annotations;

/**
 * @package
 * @category
 * @subpackage
 */
/**
 * @package
 * @category
 * @subpackage
 *
 * @Annotation
 * @Target({"ALL"})
 */
class ContentType extends AbstractAnnotation
{
    /**
     * @var string
     */
    protected $type;

    public function __construct($value = array())
    {
        $this->type = $value["value"];
    }

    public function __toString()
    {
        return (string) $this->type;
    }
}

