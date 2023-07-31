<?php
namespace SwaggerFixtures;

class GrandAncestor
{

    /**
     * @SWG\Property();
     * @var string
     */
    public $firstname;

    /**
     * @SWG\Property(property="lastname");
     * @var string
     */
    public $lastname;
}
