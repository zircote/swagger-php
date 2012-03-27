# Swagger-php

This is very new and not ready for consumption. Please note it is very much in initial stages of development.

For more info on swagger see: http://swagger.wordnik.com/

Still cleaning alot up woth the result and how it will produce that result but I expect something usable soon.

Current Sample is Generating the following output with models being next on my list:

### Resource Listing:

```php
<?php
$swagger = Zircote_Swagger::discover($projectPath);
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
$swagger = Zircote_Swagger::discover($projectPath);
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
            "httpMethod":"GET",
            "open":false,
            "deprecated":false,
            "tags":[
                "MLR"
            ],
            "path":null,
            "summary":"Fetches the leadresponder corresponding the the provided ID",
            "errorResponses":[
                {
                    "code":"403",
                    "reason":"User Not Authorized"
                }
            ],
            "parameters":[

            ],
            "responseClass":"List[leadresonder_route]"
        },
        {
            "httpMethod":"POST",
            "open":false,
            "deprecated":false,
            "tags":[
                "MLR"
            ],
            "path":null,
            "summary":"Creates a new leadresponder",
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
            "responseClass":"leadresonder_route"
        },
        {
            "httpMethod":"PUT",
            "open":false,
            "deprecated":false,
            "tags":[
                "MLR"
            ],
            "path":"\/{leadresponder_id}",
            "summary":"Updates the existing leadresponder designated by the {leadresponder_id}",
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
            "responseClass":"leadresonder_route"
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