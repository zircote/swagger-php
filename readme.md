# Swagger-php

This is very new and not ready for consumption. Please note it is very much in initial stages of development.

For more info on swagger see: http://swagger.wordnik.com/

Still cleaning alot up woth the result and how it will produce that result but I expect something usable soon.

Current Sample is Generating the following output with models being next on my list:

## Tags:
### Resource Tags:

   * `@ApiResource`
   * `@Api`
   * `@ApiProduces`

#### Example Use:

```php
<?php
/**
 * @apiresource(
 *     basePath="http://org.local/v1",
 *     swaggerVersion="0.1a",
 *     apiVersion="1"
 * )
 * @Api (
 *     path="/leadresponder",
 *     value="Gets collection of leadresponders",
 *     description="This is a long description of what it does"
 *     )
 * @ApiProduces (
 *     'application/json',
 *     'application/json+hal',
 *     'application/json-p',
 *     'application/json-p+hal',
 *     'application/xml',
 *     'application/xml',
 *     'application/xml+hal'
 *     )
 *
 * @category   Organic
 * @package    Organic_V1
 * @subpackage Controller
 */
class LeadResponder_RoutesController
{
}
```

### Operation Tags:

   * `@GET`
   * `@PUT`
   * `@POST`
   * `@DELETE`
   * `@ApiPath`
   * `@ApiOperation`
   * `@ApiError`
   * `@ApiParam`

#### Example Use:

```php
<?php
// ....
class LeadResponder_RoutesController
{
    /**
     *
     * @PUT
     * @ApiPath /{leadresponder_id}
     * @ApiOperation(
     *     value="Updates the existing leadresponder designated by the {leadresponder_id}",
     *     responseClass="leadresonder_route",
     *     multiValueResponse=false,
     *     tags="MLR"
     * )
     * @ApiError(code=400,reason="Invalid ID Provided")
     * @ApiError(code=403,reason="User Not Authorized")
     * @ApiError(code=404,reason="Lead Responder Not Found")
     * @ApiParam(
     *     description="ID of the leadresponder being requested",
     *     required=true,
     *     allowMultiple=false,
     *     dataType="integer",
     *     name="leadresponder_id",
     *     paramType="path"
     * )
     * @ApiParam(
     *     description="leadresponder_route being updated",
     *     required=true,
     *     allowMultiple=false,
     *     dataType="leadresponder_route",
     *     name="leadresponder_route",
     *     paramType="body"
     * )
     * @see Ifbyphone_Rest_AbstractController::postAction()
     */
    public function putAction()
    {
    }
}
```
### Model Tags:

 * `@ApiModel`
 * `@ApiModelProp`

#### Example Use:

```php
<?php
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
 * @property integer $usr_mlr_route_id
 * @property string  $route
 * @property string  $createdDate
 * @property string  $tag
 *
 */
class Model_LeadResponder_Route
{
// .....
}

```

### Resource Listing:

```php
<?php
$swagger = Swagger::discover($projectPath);
echo $swagger->getResource('http://org.local/v1');

```
_Outputs:_

```javascript
{
    "apis":[
        {
            "path":"http:\/\/org.local\/v1\/leadresponder",
            "description":"Gets collection of leadresponders"
        }
    ],
    "basePath":"http:\/\/org.local\/v1",
    "swaggerVersion":"0.1a",
    "apiVersion":"1"
}
```
### Operations Listing:

```php
<?php
$swagger = Swagger::discover($projectPath);
echo $swagger->getApi('http://org.local/v1', '/leadresponder');

```

_Outputs:_

```javascript
{
    "models":[
        {
            "id":"leadresonder_route",
            "description":"some long description of the model",
            "properties":[
                {
                    "name":"usr_mlr_route_id",
                    "type":"integer",
                    "description":"some long winded description"
                },
                {
                    "name":"route",
                    "type":"string"
                },
                {
                    "name":"createdDate",
                    "type":"Date"
                },
                {
                    "name":"tag",
                    "type":"string"
                },
                {
                    "name":"enumVal",
                    "type":"string",
                    "enum":[
                        "item1",
                        "item2",
                        "item"
                    ]
                },
                {
                    "name":"arrayItem",
                    "type":"array",
                    "items":{
                        "type":"string"
                    }
                },
                {
                    "name":"refArr",
                    "type":"array",
                    "items":{
                        "$ref":"ref_item"
                    }
                }
            ]
        }
    ],
    "operations":[
        {
            "tags":[
                "MLR"
            ],
            "errorResponses":[
                {
                    "code":"403",
                    "reason":"User Not Authorized"
                }
            ],
            "parameters":[

            ],
            "httpMethod":"GET",
            "responseClass":"List[leadresonder_route]",
            "summary":"Fetches the leadresponder corresponding the the provided ID",
            "path":"http:\/\/org.local\/v1\/leadresponder"
        },
        {
            "tags":[
                "MLR"
            ],
            "errorResponses":[
                {
                    "code":"403",
                    "reason":"User Not Authorized"
                }
            ],
            "parameters":[
                {
                    "description":"leadresponder_route being created",
                    "required":"true",
                    "allowMultiple":"false",
                    "dataType":"leadresponder_route",
                    "name":"leadresponder_route",
                    "paramType":"body"
                }
            ],
            "httpMethod":"POST",
            "responseClass":"leadresonder_route",
            "summary":"Creates a new leadresponder",
            "path":"http:\/\/org.local\/v1\/leadresponder"
        },
        {
            "tags":[
                "MLR"
            ],
            "errorResponses":[
                {
                    "code":"400",
                    "reason":"Invalid ID Provided"
                },
                {
                    "code":"403",
                    "reason":"User Not Authorized"
                },
                {
                    "code":"404",
                    "reason":"Lead Responder Not Found"
                }
            ],
            "parameters":[
                {
                    "description":"ID of the leadresponder being requested",
                    "required":"true",
                    "allowMultiple":"false",
                    "dataType":"integer",
                    "name":"leadresponder_id",
                    "paramType":"path"
                },
                {
                    "description":"leadresponder_route being updated",
                    "required":"true",
                    "allowMultiple":"false",
                    "dataType":"leadresponder_route",
                    "name":"leadresponder_route",
                    "paramType":"body"
                }
            ],
            "httpMethod":"PUT",
            "path":"http:\/\/org.local\/v1\/leadresponder\/{leadresponder_id}",
            "responseClass":"leadresonder_route",
            "summary":"Updates the existing leadresponder designated by the {leadresponder_id}"
        }
    ],
    "basePath":"http:\/\/org.local\/v1",
    "swaggerVersion":"0.1a",
    "apiVersion":"1",
    "path":"\/leadresponder",
    "value":"Gets collection of leadresponders",
    "description":"This is a long description of what it does",
    "produces":[
        "application\/json",
        "application\/json+hal",
        "application\/json-p",
        "application\/json-p+hal",
        "application\/xml",
        "application\/xml",
        "application\/xml+hal"
    ]
}
```