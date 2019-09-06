<?php declare(strict_types=1);

namespace OpenApiTests;

use OpenApi\Annotations;
use OpenApi\Serializer;
use const OpenApi\UNDEFINED;

class SerializerTest extends OpenApiTestCase
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
    
        $respRange = new Annotations\Response([]);
        $respRange->response = '4XX';
        $respRange->description = 'Client error response';
        
        $path->post->responses = [$resp, $respRange];

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
					},
					"4XX": {
						"description": "Client error response"
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

        $annotation = $serializer->deserialize($json, 'OpenApi\Annotations\OpenApi');

        $this->assertInstanceOf('OpenApi\Annotations\OpenApi', $annotation);
        $this->assertJsonStringEqualsJsonString(
            $annotation->toJson(),
            $this->getExpected()->toJson()
        );
    }

    public function testPetstoreExample()
    {
        $serializer = new Serializer();
        $openapi = $serializer->deserializeFile(__DIR__.'/ExamplesOutput/petstore.swagger.io.json');
        $this->assertInstanceOf('OpenApi\Annotations\OpenApi', $openapi);
        $this->assertOpenApiEqualsFile(__DIR__.'/ExamplesOutput/petstore.swagger.io.json', $openapi);
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
            	"openapi": "3.0.0",
            	"info": {
            		"title": "Pet store",
            		"version": "1.0"
            	},
            	"components": {
            		"schemas": {
            			"Dog": {
            				"allOf": [{
            					"\$ref": "#/components/schemas/SomeSchema"
            				}]
            			},
            			"Cat": {
            				"allOf": [{
            					"\$ref": "#/components/schemas/SomeSchema"
            				}]
            			}
            		}
            	}
            }
JSON;
        /* @var $annotation Annotations\OpenApi */
        $annotation = $serializer->deserialize($json, Annotations\OpenApi::class);

        foreach ($annotation->components->schemas as $schemaObject) {
            $this->assertObjectHasAttribute('allOf', $schemaObject);
            $this->assertNotSame($schemaObject->allOf, UNDEFINED);
            $this->assertIsArray($schemaObject->allOf);
            $allOfItem = current($schemaObject->allOf);
            $this->assertIsObject($allOfItem);
            $this->assertInstanceOf(Annotations\Schema::class, $allOfItem);
            $this->assertObjectHasAttribute('ref', $allOfItem);
            $this->assertNotSame($allOfItem->ref, UNDEFINED);
            $this->assertSame('#/components/schemas/SomeSchema', $allOfItem->ref);
        }
    }
}
