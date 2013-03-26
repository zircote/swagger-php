<?php
namespace SwaggerTests\Fixtures1\Models;

/**
 */
use Swagger\Annotations as SWG;

/**
 * @SWG\Model(id="FacetSet")
 */
class FacetSet
{
    /**
     * @var array
     * @SWG\Property(name="facets", type="Array", items="$ref:Facet")
     */
    public $facets;
}
