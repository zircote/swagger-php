<?php
namespace Facet\Models;

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
