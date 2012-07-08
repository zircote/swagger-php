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
 *
 * @category Organic
 * @package Organic
 * @subpackage Model
 */
/**
 * @category Organic
 * @package Organic
 * @subpackage Model
 * @SwaggerModel(
 *     id="leadresonder_route",
 *     description="some long description of the model"
 * )
 *
 * @property integer $usr_mlr_route_id some long winded description.
 * @property string $route some long description of the model.
 * @property string $createdDate
 * @property array<ref:tag> $tags this is a reference to `tag`
 * @property array<string> $arrayItem This is an array of strings
 * @property array<integer> $refArr This is an array of integers.
 * @property string<'Two Pigs','One Duck', 'And a Cow'> $enumVal This is an enum value.
 *
 */
class Model_Organic_Route
{
    /**
     * This is an integer Param
     * @var integer
     */
    public $integerParam;
}
