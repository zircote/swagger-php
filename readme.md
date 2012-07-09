# Swagger-php

[![Build Status](https://secure.travis-ci.org/zircote/swagger-php.png)](http://travis-ci.org/zircote/swagger-php)

Swagger-PHP library implementing the swagger.wordnik.com specification to describe 
web services, operations/actions and models enabling a uniform means of producing, 
consuming, and visualizing RESTful web services. 

 More on Swagger:
  * http://swagger.wordnik.com/
  * https://github.com/outeredge/SwaggerModule a ZF2 Module implementing swagger-php

_Installation_

### Composer:

#### Outside of a project:
```sh
git clone git@github.com:zircote/swagger-php.git swagger
cd swagger
php composer.phar install
```
#### As a project depenency:

Add the following snippet to your require section of you `composer.json` and
run composer install|update

**Get Composer: http://getcomposer.org**

```json
    {"zircote/swagger-php": "master-dev"}
```

_Examples:_

## Tags:
### Resource Tags:

   * `@SwaggerResource`
   * `@Swagger`
   * `@SwaggerProduces`

#### Example Use:

Generating static `json` documents:

```sh
$ ./bin/swagger --project-path /my/project/ --output-path /tmp/swagger
$ /tmp/swagger/resources.json created
$ /tmp/swagger/pets.json created
$ /tmp/swagger/users.json created
```

Dynamic examples:

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
   * `@ResponseTypeInternal`

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
     * @responseTypeInternal Model_LeadResponder_Route
     */
    public function putAction()
    {
    }
}
```
### Model Tags:

 * `@SwaggerModel`

### Complex Types via Annotations:

Besides the basic primitive type definitions in models you may also define the following:


**Javascript Reference**

 - `@property array<ref:tag> $tags this is a reference to tag`


**Array Member Types**

 - __string__

   - `@property array<string> $arrayItem This is an array of strings`

 - __integer__

   - `@property array<integer> $refArr This is an array of integers.`

**enum**

 - `@property string<'Two Pigs','Duck', 'And 1 Cow'> $enumVal This is an enum value.`

#### Example Use:

```php
<?php
/**
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
 * @property string<'Two Pigs','Duck', 'And 1 Cow'> $enumVal This is an enum value.
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
            "path":"http://org.local/v1/leadresponder",
            "description":"Gets collection of leadresponders"
        }
    ],
    "basePath":"http://org.local/v1",
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
            "properties":{
                "usr_mlr_route_id":{
                    "type":"integer",
                    "description":"some long winded description."
                },
                "route":{
                    "type":"string",
                    "description":"some long description of the model."
                },
                "createdDate":{
                    "type":"string",
                    "description":""
                },
                "tags":{
                    "type":"array",
                    "description":"this is a reference to `tag`",
                    "items" : {
                        "$ref": "tag"
                    }
                },
                "arrayItem":{
                    "type":"array",
                    "description":"This is an array of strings",
                    "items" : {
                        "type": "string"
                    }
                },
                "refArr":{
                    "type":"array",
                    "description":"This is an array of integers.",
                    "items" : {
                        "type": "integer"
                    }
                },
                "enumVal":{
                    "type":"string",
                    "description":"This is an enum value.",
                    "enum": ["Two Pigs","Duck","And 1 Cow"]
                },
                "integerParam":{
                    "description":"This is an integer Param",
                    "type":"integer"
                }
            }
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
            "path":"http://org.local/v1/leadresponder",
            "responseTypeInternal": "Model_LeadResponder_RouteCollection"
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
            "path":"http://org.local/v1/leadresponder"
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
            "path":"http://org.local/v1/leadresponder/{leadresponder_id}",
            "responseClass":"leadresonder_route",
            "responseTypeInternal" : "Model_LeadResponder_Route",
            "summary":"Updates the existing leadresponder designated by the {leadresponder_id}"
        }
    ],
    "basePath":"http://org.local/v1",
    "swaggerVersion":"1.0",
    "apiVersion":"1",
    "path":"/leadresponder",
    "value":"Gets collection of leadresponders",
    "description":"This is a long description of what it does",
    "produces":[
        "application/json",
        "application/json+hal",
        "application/json-p",
        "application/json-p+hal",
        "application/xml",
        "application/xml",
        "application/xml+hal"
    ]
}
```
 

