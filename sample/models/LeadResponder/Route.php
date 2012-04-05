<?php
/**
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * Copyright [2012] [Robert Allen]
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * @category Organic
 * @package Organic
 * @subpackage Model
 */
/**
 * @ApiModel(
 *     id="leadresonder_route",
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