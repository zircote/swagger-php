# Swagger-php

Swagger-PHP library implementing the swagger.wordnik.com specification to describe 
web services, operations/actions and models enabling a uniform means of producing, 
consuming, and visualizing RESTful web services. 

 More on Swagger:
  * http://swagger.wordnik.com/
  * https://github.com/outeredge/SwaggerModule a ZF2 Module implementing swagger-php
  
 
_Examples:_

## Tags:
### Resource Tags:

   * `@SwaggerResource`
   * `@Swagger`
   * `@SwaggerProduces`

#### Example Use:

```php
<?php
/**
 *@Swaggerresource(
 *     basePath="http://org.local/v1",
 *     swaggerVersion="1.0",
 *     apiVersion="1"
 * )
 *@Swagger (
 *     path="/leadresponder",
 *     value="Gets collection of leadresponders",
 *     description="This is a long description of what it does"
 *     )
 *@SwaggerProduces (
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
   * `@SwaggerPath`
   * `@SwaggerOperation`
   * `@SwaggerError`
   * `@SwaggerParam`

#### Example Use:

```php
<?php
// ....
class LeadResponder_RoutesController
{
    /**
     *
     * @PUT
     *@SwaggerPath /{leadresponder_id}
     *@SwaggerOperation(
     *     value="Updates the existing leadresponder designated by the {leadresponder_id}",
     *     responseClass="leadresonder_route",
     *     multiValueResponse=false,
     *     tags="MLR"
     * )
     *@SwaggerError(code=400,reason="Invalid ID Provided")
     *@SwaggerError(code=403,reason="User Not Authorized")
     *@SwaggerError(code=404,reason="Lead Responder Not Found")
     *@SwaggerParam(
     *     description="ID of the leadresponder being requested",
     *     required=true,
     *     allowMultiple=false,
     *     dataType="integer",
     *     name="leadresponder_id",
     *     paramType="path"
     * )
     *@SwaggerParam(
     *     description="leadresponder_route being updated",
     *     required=true,
     *     allowMultiple=false,
     *     dataType="leadresponder_route",
     *     name="leadresponder_route",
     *     paramType="body"
     * )
     */
    public function putAction()
    {
    }
}
```
### Model Tags:

 * `@SwaggerModel`

#### Example Use:

```php
<?php
/**
 *@SwaggerModel(
 *     id="leadresonder_route",
 *     description="some long description of the model"
 * )
 * @property integer $usr_mlr_route_id description of blah
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
$swagger = \Swagger\Swagger::discover($projectPath);
echo $swagger->getResource('http://org.local/v1');

```
_Outputs:_

```json
{
    "apis":[
        {
            "path":"http:\/\/org.local\/v1\/leadresponder",
            "description":"Gets collection of leadresponders"
        }
    ],
    "basePath":"http:\/\/org.local\/v1",
    "swaggerVersion":"1.0",
    "apiVersion":"1"
}
```
### Operations Listing:

```php
<?php
$swagger = \Swagger\Swagger::discover($projectPath);
echo $swagger->getApi('http://org.local/v1', '/leadresponder');

```

_Outputs:_

```json
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
    "swaggerVersion":"1.0",
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
