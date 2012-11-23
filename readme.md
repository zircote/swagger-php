swagger-php
============

[![Build Status](https://secure.travis-ci.org/zircote/swagger-php.png)](http://travis-ci.org/zircote/swagger-php)

Swagger-PHP library implementing the swagger.wordnik.com specification to describe
web services, operations/actions and models enabling a uniform means of producing,
consuming, and visualizing RESTful web services.

 More on Swagger:
  * http://swagger.wordnik.com/
  * https://github.com/wordnik/swagger-core/wiki
  * https://github.com/outeredge/SwaggerModule a ZF2 Module implementing swagger-php

__Installation__

=============================
Composer:
=============================

---------------------
Outside of a project:
---------------------

```sh
git clone git@github.com:zircote/swagger-php.git swagger
cd swagger
php composer.phar install
```

=============================
External Dependencies:
=============================
 - Doctrine/Common
 - PHP 5.3/ PHP 5.4

Dev Dependencies (For building the phar):
 - Symfony/Finder
 - Symfony/Process

---------------------------
As a project depenency:
---------------------------

Add the following snippet to your require section of you `composer.json` and
run composer install|update

**Get Composer: http://getcomposer.org**

```json

    {"zircote/swagger-php": "master-dev"}

```

=============================
Example Use:
=============================
---------
CLI
--------

```
php swagger.phar -h

 Usage: swagger --project-path PATH [--output-path PATH]...
     Generate Swagger JSON documents for a project.
         Mandatory argument[s]:
             -p, --project-path    base path of the project to perform swagger discovery
         Optional arguments:
             -i, --include-path    Optional bootstrap file for additional include path support
                                     ex: --include-path Zend:/usr/local/share/pear
             -o, --output-path     directory to store the generated json documents
             -f, --format          format JSON output in readable formatting.
             -h, --help            generates this help message

php swagger.phar -p <project-path> \
  -o <output-path> -f \
  --include-path Zend:/usr/local/shar/pear,Rediska:/usr/local/share/pear

 > /tmp/swagger/resources.json created
 > /tmp/swagger/pets.json created
 > /tmp/swagger/users.json created

bin/swagger --project-path /my/project/ --output-path /tmp/swagger -f
> /tmp/swagger/resources.json created
> /tmp/swagger/pets.json created
> /tmp/swagger/users.json created
```

=============================
Tags:
=============================

--------------
Example Model:
--------------

```php
<?php
namespace SwaggerTests\Fixtures\Models;

/**
 *
 * @package
 * @category
 * @subpackage
 */
use Swagger\Annotations\Property;
use Swagger\Annotations\AllowableValues;
use Swagger\Annotations\Model;
use Swagger\Annotations\Items;

/**
 * @package
 * @category
 * @subpackage
 *
 * @Model(id="Pet")
 */
class Pet
{
    /**
     * @var array<Tags>
     *
     * @Property(name="tags",type="array", items="$ref:Tag")
     */
    protected $tags = array();

    /**
     * @var int
     *
     * @Property(name="id",type="long")
     */
    protected $id;

    /**
     * @var Category
     *
     * @Property(name="category",type="Category")
     */
    protected $category;

    /**
     *
     *
     * @var string
     *
     * @Property(
     *      name="status",type="string",
     *      @allowableValues(
     *          valueType="LIST",
     *          values="['available', 'pending', 'sold']"
     *      ),
     *      description="pet status in the store")
     */
    protected $status;

    /**
     * @var string
     *
     * @Property(name="name",type="string")
     */
    protected $name;

    /**
     * @var array<string>
     *
     * @Property(name="photoUrls",type="array", @items(type="string"))
     */
    protected $photoUrls = array();
}


```

-----------------------
Example Resource:
-----------------------

```php
<?php
namespace SwaggerTests\Fixtures\Resources;

/**
 * @package
 * @category
 * @subpackage
 */
use Swagger\Annotations\Operation;
use Swagger\Annotations\Operations;
use Swagger\Annotations\Parameter;
use Swagger\Annotations\Parameters;
use Swagger\Annotations\Api;
use Swagger\Annotations\ErrorResponse;
use Swagger\Annotations\ErrorResponses;
use Swagger\Annotations\Resource;
use Swagger\Annotations\AllowableValues;

/**
 * @package
 * @category
 * @subpackage
 *
 * @Resource(
 *  apiVersion="0.2",
 *  swaggerVersion="1.1",
 *  resourcePath="/pet",
 *  basePath="http://petstore.swagger.wordnik.com/api"
 * )
 */
class Pet
{

    /**
     *
     * @Api(
     *   path="/pet.{format}/findByTags",
     *   description="Operations about pets",
     *   @operations(
     *     @operation(
     *       httpMethod="GET",
     *       summary="Finds Pets by tags",
     *       notes="Muliple tags can be provided with comma seperated strings. Use tag1, tag2, tag3 for testing.",
     *       deprecated=true,
     *       responseClass="List[Pet]",
     *       nickname="findPetsByTags",
     *       @parameters(
     *         @parameter(
     *           name="tags",
     *           description="Tags to filter by",
     *           paramType="query",
     *           required=true,
     *           allowMultiple=true,
     *           dataType="string"
     *         )
     *       ),
     *       @errorResponses(
     *          @errorResponse(
     *            code="400",
     *            reason="Invalid tag value"
     *          )
     *       )
     *     )
     *   )
     * )
     */
    public function findPetsByTags()
    {
    }
}

```


=============================
Resource Listing:
=============================

```php
<?php
$swagger = Swagger::discover($path);
echo $swagger->jsonEncode($swagger->registry, true);

// Alternate
echo $swagger->jsonEncode($swagger->registry['/pet'], true);

```


__Output__


```json
{
    "/pet":{
        "apiVersion":"0.2",
        "swaggerVersion":"1.1",
        "basePath":"http://petstore.swagger.wordnik.com/api",
        "resourcePath":"/pet",
        "apis":[
            {
                "path":"/pet.{format}/{petId}",
                "description":"Operations about pets",
                "resourcePath":"/pet",
                "operations":{
                    "allowMultiple":true,
                    "nickname":"getPetById",
                    "responseClass":"Pet",
                    "summary":"Find pet by ID",
                    "httpMethod":"GET",
                    "parameters":{
                        "description":"ID of pet that needs to be fetched",
                        "dataType":"string",
                        "name":"petId",
                        "paramType":"path",
                        "required":"true"
                    },
                    "errorResponses":[
                        {
                            "code":"400",
                            "reason":"Invalid ID supplied"
                        },
                        {
                            "code":"404",
                            "reason":"Pet not found"
                        }
                    ],
                    "notes":"Returns a pet based on ID"
                }
            },
            {
                "path":"/pet.{format}",
                "description":"Operations about pets",
                "resourcePath":"/pet",
                "operations":[
                    {
                        "allowMultiple":true,
                        "nickname":"addPet",
                        "responseClass":"void",
                        "summary":"Add a new pet to the store",
                        "httpMethod":"GET",
                        "parameters":{
                            "description":"Pet object that needs to be added to the store",
                            "dataType":"Pet",
                            "paramType":"body",
                            "required":"true"
                        },
                        "notes":"<pre>\nsome inline html note\n\n</pre>"
                    },
                    {
                        "allowMultiple":true,
                        "nickname":"updatePet",
                        "responseClass":"void",
                        "summary":"Update an existing pet",
                        "httpMethod":"PUT",
                        "parameters":{
                            "description":"Pet object that needs to be updated to the store",
                            "dataType":"Pet",
                            "paramType":"body",
                            "required":"true"
                        },
                        "errorResponses":[
                            {
                                "code":"405",
                                "reason":"Invalid input"
                            },
                            {
                                "code":"400",
                                "reason":"Invalid ID supplied"
                            },
                            {
                                "code":"404",
                                "reason":"Pet not found"
                            }
                        ]
                    }
                ]
            }
        ]
    }
}
```



