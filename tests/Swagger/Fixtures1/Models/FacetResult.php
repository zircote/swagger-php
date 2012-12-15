<?php
namespace SwaggerTests\Fixtures1\Models;

/**
 */
use Swagger\Annotations as SWG;

/**
 * @SWG\Model(id="FacetResult")
 */
class FacetResult
{
    /**
     * @var array
     * @SWG\Property(name="facetSets", type="array", items="$ref:FacetSet")
     */
    protected $facetSets;
}
