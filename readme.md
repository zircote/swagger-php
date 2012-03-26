# Swagger-php

This is very new and not ready for consumption. Please note it is very much in initial stages of development.

For more info on swagger see: http://swagger.wordnik.com/


Current Sample is Generating the following output with models being next on my list:

```javascript
{
    "apis":[
        {
            "path":"http:\/\/org.local\/v1\/leadresponder",
            "value":"Gets collection of leadresponders",
            "description":"This is a long description of what it does"
        }
    ],
    "basePath":"http:\/\/org.local\/v1",
    "swagrVersion":"0.1a",
    "apiVersion":"1.0.1a"
}
{
    "operations":[
        {
            "httpMethod":"GET",
            "open":false,
            "deprecated":false,
            "tags":[
                "MLR"
            ],
            "path":"http:\/\/org.local\/v1\/leadresponder",
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
            "path":"http:\/\/org.local\/v1\/leadresponder",
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
            "path":"http:\/\/org.local\/v1\/leadresponder\/{leadresponder_id}",
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
    "path":"http:\/\/org.local\/v1\/leadresponder",
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