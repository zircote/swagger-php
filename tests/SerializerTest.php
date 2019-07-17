<?php

namespace Swaggertests;

use Swagger\Annotations;
use Swagger\Serializer;

class SerializerTest extends SwaggerTestCase
{
    private function getExpected()
    {
        $path = new Annotations\Path([]);
        $path->path = '/products';
        $path->post = new Annotations\Post([]);
        $path->post->tags = ['products'];
        $path->post->summary = 's1';
        $path->post->description = 'd1';
        $path->post->consumes = ['application/json'];
        $path->post->produces = ['application/json'];

        $param = new Annotations\Parameter([]);
        $param->in = 'body';
        $param->description = 'data in body';
        $param->required = true;
        $param->type = 'object';
        $param->x = [];
        $param->x['repository'] = 'abc';
        $path->post->parameters = [$param];

        $resp = new Annotations\Response([]);
        $resp->response = '200';
        $resp->x = [];
        $resp->x['repository'] = 'def';
        $schema = new Annotations\Schema([]);
        $schema->ref = '#/definitions/Pet';
        $resp->schema = $schema;
        $path->post->responses = [$resp];

        $expected = new Annotations\Swagger([]);
        $expected->swagger = '2.0';
        $expected->paths = [
            $path,
        ];

        $definition = new Annotations\Definition([]);
        $definition->definition = 'Pet';
        $definition->required = ['name', 'photoUrls'];

        $expected->definitions = [$definition];

        return $expected;
    }

    public function testDeserializeAnnotation()
    {
        $serializer = new Serializer();

        $json = <<<JSON
{
  "swagger": "2.0",
  "paths": {
    "/products": {
      "post": {
        "tags": [
          "products"
        ],
        "summary": "s1",
        "description": "d1",
        "consumes": [
          "application/json"
        ],
        "produces": [
          "application/json"
        ],
        "parameters": [
          {
            "in": "body",
            "description": "data in body",
            "required": true,
            "type": "object",
            "x-repository": "abc"
          }
        ],
        "responses": {
          "200": {
            "x-repository": "def",
            "schema": {
                "\$ref": "#/definitions/Pet"
            }
          }
        }
      }
    }
  },
  "definitions": {
    "Pet": {
      "required": [
        "name",
        "photoUrls"
      ]
    }
  }
}
JSON;

        $annotation = $serializer->deserialize($json, 'Swagger\Annotations\Swagger');

        $this->assertInstanceOf('Swagger\Annotations\Swagger', $annotation);
        $this->assertJsonStringEqualsJsonString(
            $annotation->__toString(),
            $this->getExpected()->__toString()
        );
    }

    public function testPetstoreExample()
    {
        $serializer = new Serializer();
        $swagger = $serializer->deserializeFile(__DIR__.'/ExamplesOutput/petstore.swagger.io.json');
        $this->assertInstanceOf('Swagger\Annotations\Swagger', $swagger);
        $this->assertSwaggerEqualsFile(__DIR__ . '/ExamplesOutput/petstore.swagger.io.json', $swagger);
    }

    /**
     * Test for correct deserialize schemas 'allOf' property.
     * @throws \Exception
     */
    public function testDeserializeAllOfProperty()
    {
        $serializer = new Serializer();
        $json = <<<JSON
{
  "swagger": "2.0",
  "definitions": {
   "Pet": {
     "type": "object",
      "required": [
        "name",
        "photoUrls"
      ]
    },
    "Dog": {
      "type": "object",
      "allOf": [{
        "\$ref": "#/definitions/Pet"
                
      }, {
        "properties": {
          "name": {
            "type": "string"
          }
        }
      }]
    }
  }
}
JSON;

        /** @var Annotations\Swagger $swagger */
        $swagger = $serializer->deserialize($json, 'Swagger\Annotations\Swagger');

        $this->assertNotEmpty($swagger->definitions['Dog']->allOf);

        foreach ($swagger->definitions['Dog']->allOf as $schemaObject) {
            $this->assertInstanceOf(Annotations\Schema::class, $schemaObject);
        }
    }
}
