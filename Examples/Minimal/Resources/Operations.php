<?php
namespace Minimal\Resources;

// swagger-php uses the @SWG namespace by default. `use Swagger\Annotations as SWG;` is optional

/**
 * @SWG\Resource(
 *  basePath="http://example.com/api"
 * )
 *  resourcePath is detected as "/operations" based on the class-name.
 */
class Operations
{
    /**
     * @SWG\Api(
     *   @SWG\Operation(
     *     summary="Retrieve all pets",
     *     method="GET",
     *     type="array[Pet]",
     *     @SWG\Parameter(name="name",type="string"),
     *     @SWG\Parameter(name="status",type="string")
     *   ),
     *   @SWG\Operation(
     *     summary="Register a new pet",
     *     method="POST",
     *     type="Pet"
     *   )
     * )
     */
    public function pets($pets)
    {
        // Autodetected:
        // @SWG\Api->path is detected as "/operation/pets"  based on the resoursePath and method-name.
        // @SWG\Operation->nickname are generated based on the method name.
        // When a a api has multiple operations the generated nicknames are suffixed with _$index. Example "pets_0" and "pets_1"
        // A @SWG\Operation is used directly inside the @SWG\Api (Wrapping @SWG\Operation() in @SWG\Operations() is optional)
        // A @SWG\Parameter is used directly inside the @SWG\Operation (Wrapping @SWG\Parameter() in @SWG\Parameters() is optional)

    }
}
