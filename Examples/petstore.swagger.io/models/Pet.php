<?php

namespace PetstoreIO;

/**
 * @OAS\Schema(required={"name", "photoUrls"}, type="object", @OAS\Xml(name="Pet"))
 */
class Pet
{

    /**
     * @OAS\Property(format="int64")
     * @var int
     */
    public $id;

    /**
     * @OAS\Property(example="doggie")
     * @var string
     */
    public $name;

    /**
     * @var Category
     * @OAS\Property()
     */
    public $category;

    /**
     * @var string[]
     * @OAS\Property(@OAS\Xml(name="photoUrl", wrapped=true))
     */
    public $photoUrls;

    /**
     * @var Tag[]
     * @OAS\Property(@OAS\Xml(name="tag", wrapped=true))
     */
    public $tags;

    /**
     * pet status in the store
     * @var string
     * @OAS\Property(enum={"available", "pending", "sold"})
     */
    public $status;
}
