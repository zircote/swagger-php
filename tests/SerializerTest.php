<?php declare(strict_types=1);

namespace Swaggertests;

use Swagger\Annotations;
use Swagger\Serializer;

class SerializerTest extends SwaggerTestCase
{
    private function getExpected()
    {
        $path = new Annotations\PathItem([]);
        $path->path = '/products';
        $path->post = new Annotations\Post([]);
        $path->post->tags = ['products'];
        $path->post->summary = 's1';
        $path->post->description = 'd1';
        $path->post->requestBody = new Annotations\RequestBody([]);
        $mediaType = new Annotations\MediaType([]);
        $mediaType->mediaType = 'application/json';
        $mediaType->schema = new Annotations\Schema([]);
        $mediaType->schema->type = 'object';
        $path->post->requestBody->content = [$mediaType];
        $path->post->requestBody->description = 'data in body';
        $path->post->requestBody->x = [];
        $path->post->requestBody->x['repository'] = 'def';

        $resp = new Annotations\Response([]);
        $resp->response = '200';
        $resp->description = 'Success';
        $content = new Annotations\MediaType([]);
        $content->mediaType = 'application/json';
        $content->schema = new Annotations\Schema([]);
        $content->schema->ref = '#/components/schemas/Pet';
        $resp->content = [$content];
        $resp->x = [];
        $resp->x['repository'] = 'def';
        $path->post->responses = [$resp];

        $expected = new Annotations\OpenApi([]);
        $expected->openapi = '3.0.0';
        $expected->paths = [
            $path,
        ];

        $info = new Annotations\Info([]);
        $info->title = 'Pet store';
        $info->version = '1.0';
        $expected->info = $info;

        $schema = new Annotations\Schema([]);
        $schema->schema = 'Pet';
        $schema->required = ['name', 'photoUrls'];

        $expected->components = new Annotations\Components([]);
        $expected->components->schemas = [$schema];

        return $expected;
    }

    public function testDeserializeAnnotation()
    {
        $serializer = new Serializer();

        $json = <<<JSON
{
	"openapi": "3.0.0",
	"info": {
		"title": "Pet store",
		"version": "1.0"
	},
	"paths": {
		"/products": {
			"post": {
				"tags": [
					"products"
				],
				"summary": "s1",
				"description": "d1",
				"requestBody": {
					"description": "data in body",
					"content": {
						"application/json": {
							"schema": {
								"type": "object"
							}
						}
					},
					"x-repository": "def"
				},
				"responses": {
					"200": {
						"description": "Success",
						"content": {
							"application/json": {
								"schema": {
									"\$ref": "#/components/schemas/Pet"
								}
							}
						},
						"x-repository": "def"
					}
				}
			}
		}
	},
	"components": {
		"schemas": {
			"Pet": {
				"required": [
					"name",
					"photoUrls"
				]
			}
		}
	}
}
JSON;

//        $this->markTestSkipped('@todo');
        $annotation = $serializer->deserialize($json, 'Swagger\Annotations\OpenApi');

        $this->assertInstanceOf('Swagger\Annotations\OpenApi', $annotation);
        $this->assertJsonStringEqualsJsonString(
            $annotation->__toString(),
            $this->getExpected()->__toString()
        );
    }

    public function testPetstoreExample()
    {
        $serializer = new Serializer();
        $openapi = $serializer->deserializeFile(__DIR__.'/ExamplesOutput/petstore.swagger.io.json');
        $this->assertInstanceOf('Swagger\Annotations\OpenApi', $openapi);
        $this->assertSwaggerEqualsFile(__DIR__.'/ExamplesOutput/petstore.swagger.io.json', $openapi);
    }
}
