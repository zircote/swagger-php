<?php
/**
 * filecomment
 * package_declaration
 */
/**
 * @ApiModel(
 *     id="leadresponder_route",
 *     description="some long description of the model"
 * )
 * @ApiModelProp(
 *     name=usr_mlr_route_id,
 *     type=integer,
 *     description="some long winded description"
 * )
 * @ApiModelProp(
 *     name=route,
 *     type=string
 * )
 * @ApiModelProp(
 *     name=createdDate,
 *     type=Date
 * )
 * @ApiModelProp(
 *     name=tag,
 *     type=string
 * )
 * @ApiModelProp(
 *     name=enumVal,
 *     type=string,
 *     enum="item1,item2,item"
 * )
 * @ApiModelProp(
 *     name=arrayItem,
 *     type=array,
 *     items=type:string
 * )
 * @ApiModelProp(
 *     name=refArr,
 *     type=array,
 *     items=$ref:ref_item
 * )
 * @category
 * @package
 * @subpackage
 *
 * @property integer $usr_mlr_route_id
 * @property string  $route
 * @property string  $createdDate
 * @property string  $tag
 *
 */
class Model_LeadResponder_Route
{

}