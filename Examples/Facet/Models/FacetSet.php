<?php
namespace Facet\Models;

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
     * @SWG\Property(name="facets", type="array", items="$ref: Facet")
     */
    public $facets;
}
