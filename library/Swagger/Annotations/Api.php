<?php
namespace Swagger\Annotations;

/**
 * @package
 * @category
 * @subpackage
 */
use Swagger\Annotations\Operations;

/**
 * @package
 * @category
 * @subpackage
 *
 * @Annotation
 */
class Api extends AbstractAnnotation
{
    /**
     * @var string
     */
    public $path;
    /**
     * @var string
     */
    public $operations;
    /**
     * @var string
     */
    public $description;

    public function __construct($values)
    {
        parent::__construct($values);
        if (isset($values['value'])) {
            switch ($values['value']) {
                case ($values['value'] instanceof Operations):
                    $this->operations[] = $values['value'];
                    break;
            }
        }
    }
}

