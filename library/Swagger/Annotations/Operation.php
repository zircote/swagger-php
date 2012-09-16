<?php
namespace Swagger\Annotations;

/**
 * @package
 * @category
 * @subpackage
 */
use Swagger\Annotations\Parameters;
use Swagger\Annotations\ErrorResponses;

/**
 * @package
 * @category
 * @subpackage
 *
 * @Annotation
 */
class Operation extends AbstractAnnotation
{
    /**
     * @var string
     */
    public $description;
    /**
     * @var bool
     */
    public $require = false;
    /**
     * @var string
     */
    public $dataType;
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $paramType;

    /**
     * @var string
     */
    public $nickname;

    /**
     * @var string
     */
    public $responseClass;

    /**
     * @var string
     */
    public $summary;

    /**
     * @var string
     */
    public $httpMethod;

    /**
     * @var Parameters
     */
    public $parameters;

    /**
     * @var ErrorResponses
     */
    public $errorResponses;

    /**
     * @var string
     */
    public $notes;

    /**
     * @var bool
     */
    public $deprecated;

    /**
     * @param array $values
     */
    public function __construct($values)
    {
        parent::__construct($values);
        if (isset($values['value'])) {
            foreach ($values['value'] as $value) {
                switch ($value) {
                    case ($value instanceof Parameters):
                        $this->parameters = $value->toArray();
                        break;
                    case ($value instanceof ErrorResponses):
                        $this->errorResponses = $value->toArray();
                        break;
                }
            }
        }
        $this->notes = $this->removePreamble($this->notes);
    }
}

