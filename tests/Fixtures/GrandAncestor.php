<?php declare(strict_types=1);
namespace SwaggerFixtures;

class GrandAncestor
{

    /**
     * @OAS\Property();
     * @var string
     */
    public $firstname;

    /**
     * @OAS\Property(property="lastname");
     * @var string
     */
    public $lastname;
}
