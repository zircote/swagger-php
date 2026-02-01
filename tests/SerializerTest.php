<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Annotations as OA;
use OpenApi\Generator;
use OpenApi\Serializer;
use OpenApi\Tests\Concerns\UsesExamples;
use PHPUnit\Framework\Attributes\DataProvider;
use OpenApi\Annotations\OpenApi;

final class SerializerTest extends OpenApiTestCase
{
    use UsesExamples;

    private function getExpected(): OpenApi
    {
        $path = new OA\PathItem(['_context' => $this->getContext()]);
        $path->path = '/products';
        $path->post = new OA\Post(['_context' => $this->getContext()]);
        $path->post->tags = ['products'];
        $path->post->summary = 's1';
        $path->post->description = 'd1';
        $path->post->requestBody = new OA\RequestBody(['_context' => $this->getContext()]);
        $mediaType = new OA\MediaType(['_context' => $this->getContext()]);
        $mediaType->mediaType = 'application/json';
        $mediaType->schema = new OA\Schema(['_context' => $this->getContext()]);
        $mediaType->schema->type = 'object';
        $mediaType->schema->additionalProperties = true;
        $path->post->requestBody->content = [$mediaType];
        $path->post->requestBody->description = 'data in body';
        $path->post->requestBody->x = [];
        $path->post->requestBody->x['repository'] = 'def';

        $resp = new OA\Response(['_context' => $this->getContext()]);
        $resp->response = '200';
        $resp->description = 'Success';
        $content = new OA\MediaType(['_context' => $this->getContext()]);
        $content->mediaType = 'application/json';
        $content->schema = new OA\Schema(['_context' => $this->getContext()]);
        $content->schema->ref = '#/components/schemas/Pet';
        $resp->content = [$content];
        $resp->x = [];
        $resp->x['repository'] = 'def';

        $respRange = new OA\Response(['_context' => $this->getContext()]);
        $respRange->response = '4XX';
        $respRange->description = 'Client error response';

        $path->post->responses = [$resp, $respRange];

        $expected = new OpenApi(['_context' => $this->getContext()]);
        $expected->openapi = OpenApi::VERSION_3_0_0;
        $expected->paths = [
            $path,
        ];

        $info = new OA\Info(['_context' => $this->getContext()]);
        $info->title = 'Pet store';
        $info->version = '1.0';
        $expected->info = $info;

        $schema = new OA\Schema(['_context' => $this->getContext()]);
        $schema->schema = 'Pet';
        $schema->required = ['name', 'photoUrls'];

        $expected->components = new OA\Components(['_context' => $this->getContext()]);
        $expected->components->schemas = [$schema];

        return $expected;
    }

    public function testDeserializeAnnotation(): void
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
								"type": "object",
								"additionalProperties": true
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

        /** @var OpenApi $annotation */
        $annotation = $serializer->deserialize($json, OpenApi::class);

        $this->assertInstanceOf(OpenApi::class, $annotation);
        $this->assertJsonStringEqualsJsonString(
            $annotation->toJson(),
            $this->getExpected()->toJson()
        );

        $schema = $annotation->paths['/products']->post->requestBody->content['application/json']->schema;
        $this->assertTrue($schema->additionalProperties);
    }

    public function testPetstoreExample(): void
    {
        $serializer = new Serializer();
        $spec = self::examplePath('petstore/petstore-3.0.0.yaml');
        $openapi = $serializer->deserializeFile($spec, 'yaml');
        $this->assertInstanceOf(OpenApi::class, $openapi);
        $this->assertSpecEquals(file_get_contents($spec), $openapi->toYaml());
    }

    /**
     * Test for correct deserialize schemas 'allOf' property.
     *
     * @throws \Exception
     */
    public function testDeserializeAllOfProperty(): void
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
        /** @var OpenApi $annotation */
        $annotation = $serializer->deserialize($json, OpenApi::class);

        foreach ($annotation->components->schemas as $schemaObject) {
            $this->assertIsObject($schemaObject);
            $this->assertTrue(property_exists($schemaObject, 'allOf'));
            $this->assertNotSame(Generator::UNDEFINED, $schemaObject->allOf);
            $this->assertIsArray($schemaObject->allOf);
            $allOfItem = current($schemaObject->allOf);
            $this->assertIsObject($allOfItem);
            $this->assertInstanceOf(OA\Schema::class, $allOfItem);
            $this->assertNotSame(Generator::UNDEFINED, $allOfItem->ref);
            $this->assertSame('#/components/schemas/SomeSchema', $allOfItem->ref);
        }
    }

    #[DataProvider('allAnnotationClasses')]
    public function testValidAnnotationsListComplete(string $annotation): void
    {
        $staticProperties = (new \ReflectionClass((Serializer::class)))->getStaticProperties();
        $this->assertArrayHasKey($annotation, array_flip($staticProperties['VALID_ANNOTATIONS']));
    }
}
