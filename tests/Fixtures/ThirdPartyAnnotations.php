<?php declare(strict_types=1);

namespace OpenApiTests\Fixtures;

/**
 * @OA\Info(title="Fixture for ParserTest", version="test")
 * Based on the examplefrom http://framework.zend.com/manual/current/en/modules/zend.form.quick-start.html
 */
use Laminas\Form\Annotation;

/**
 * @Annotation\Name("user")
 * @Annotation\Hydrator("Laminas\Stdlib\Hydrator\ObjectProperty")
 */
class ThirdPartyAnnotations
{
    /**
     * @Annotation\Exclude()
     */
    public $id;

    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^[a-zA-Z][a-zA-Z0-9_-]{0,24}$/"}})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Username:"})
     */
    public $username;

    /**
     * @Annotation\Type("Laminas\Form\Element\Email")
     * @Annotation\Options({"label":"Your email address:"})
     */
    public $email;

    /**
     * @OA\Get(path="api/3rd-party", @OA\Response(response="200", description="a response"))
     */
    public function methodWithOpenApiAnnotation()
    {
    }
}
