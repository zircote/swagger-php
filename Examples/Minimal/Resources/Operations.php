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
        // @Api->path is detected as "/operation/pets"  based on the resoursePath and method-name.
        // @Operation->nickname are generated based on the method name.
        // When a a api has multiple operations the generated nicknames are suffixed with _$index. Example "pets_0" and "pets_1"
        // A @Operation is used directly inside the @Api (Wrapping @Operation() in @Operations() is optional)
        // A @Parameter is used directly inside the @Operation (Wrapping @Parameter() in @Parameters() is optional)

    }
}
