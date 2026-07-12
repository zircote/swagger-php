<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Compiler;

use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Undefined;

/**
 * Compiles a Specification into an OpenAPI 3.0.x document array.
 *
 * Extends the 3.1 compiler and overrides version-specific methods:
 * - JSON Schema draft-04 subset: type is always a string (not array)
 * - nullable is a separate boolean keyword
 * - exclusiveMinimum/Maximum are booleans (used alongside minimum/maximum)
 * - No $ref siblings (summary/description stripped from $ref objects)
 * - No webhooks
 * - No const, no examples array on Schema (only singular example)
 * - No prefixItems, unevaluatedItems, unevaluatedProperties
 * - No if/then/else
 * - No contentMediaType/contentEncoding on Schema
 * - No dependentRequired/dependentSchemas
 * - No propertyNames, contains, minContains, maxContains
 * - License: no identifier field (only url)
 */
class OpenApi30Compiler extends OpenApi31Compiler
{
    protected const VERSIONS = ['3.0.0', '3.0.1', '3.0.2', '3.0.3', '3.0.4'];

    public function getVersion(): string
    {
        return '3.0.0';
    }

    public function validate(Specification $specification): array
    {
        $diagnostics = parent::validate($specification);

        $hasPaths = (bool) array_filter($specification->operations, fn (OA\Operation $op): bool => $op->path !== null);
        if (!$hasPaths) {
            $diagnostics[] = ['level' => 'warning', 'message' => 'paths is required in OpenAPI 3.0'];
        }

        $hasWebhooks = (bool) array_filter($specification->operations, fn (OA\Operation $op): bool => $op->webhook !== null);
        if ($hasWebhooks) {
            $diagnostics[] = ['level' => 'warning', 'message' => 'webhooks are not supported in OpenAPI 3.0 and will be omitted'];
        }

        if ($specification->info?->license instanceof OA\License) {
            $license = $specification->info->license;
            if ($license->identifier !== null) {
                $diagnostics[] = ['level' => 'warning', 'message' => 'License identifier is not supported in OpenAPI 3.0, use url instead'];
            }
        }

        $this->validateSchemas($specification, $diagnostics);

        return $diagnostics;
    }

    protected function compileWebhooks(array $operations): array
    {
        return [];
    }

    protected function compileInfo(OA\Info $info): array
    {
        return $this->filter([
            'title' => $info->title,
            'description' => $info->description,
            'termsOfService' => $info->termsOfService,
            'contact' => $info->contact instanceof OA\Contact ? $this->compileContact($info->contact) : null,
            'license' => $info->license instanceof OA\License ? $this->compileLicense($info->license) : null,
            'version' => $info->version,
        ], $info);
    }

    protected function compileLicense(OA\License $license): array
    {
        return $this->filter([
            'name' => $license->name,
            'url' => $license->url,
        ], $license);
    }

    /**
     * Compile schema using OAS 3.0 / JSON Schema draft-04 semantics.
     */
    protected function compileSchema(OA\Schema|string $schema): array
    {
        if (is_string($schema)) {
            return ['$ref' => $schema];
        }

        if ($schema->ref !== null) {
            return ['$ref' => $schema->ref];
        }

        $type = $schema->type;
        if (is_array($type)) {
            $type = array_values(array_filter($type, fn (string $t): bool => $t !== 'null'));
            $type = count($type) === 1 ? $type[0] : ($type[0] ?? null);
        }

        $nullable = $schema->nullable;
        if ($nullable === null && $schema->type !== null) {
            $typeArray = (array) $schema->type;
            if (in_array('null', $typeArray, true)) {
                $nullable = true;
            }
        }

        $exclusiveMinimum = null;
        $exclusiveMaximum = null;
        $minimum = $schema->minimum;
        $maximum = $schema->maximum;

        if ($schema->exclusiveMinimum !== null) {
            if (is_bool($schema->exclusiveMinimum)) {
                $exclusiveMinimum = $schema->exclusiveMinimum ?: null;
            } else {
                $minimum = $schema->exclusiveMinimum;
                $exclusiveMinimum = true;
            }
        }

        if ($schema->exclusiveMaximum !== null) {
            if (is_bool($schema->exclusiveMaximum)) {
                $exclusiveMaximum = $schema->exclusiveMaximum ?: null;
            } else {
                $maximum = $schema->exclusiveMaximum;
                $exclusiveMaximum = true;
            }
        }

        $result = $this->filter([
            'type' => $type,
            'format' => $schema->format,
            'title' => $schema->title,
            'description' => $schema->description,
            'nullable' => $nullable,
            'enum' => $schema->enum,

            // String
            'minLength' => $schema->minLength,
            'maxLength' => $schema->maxLength,
            'pattern' => $schema->pattern,

            // Numeric
            'minimum' => $minimum,
            'maximum' => $maximum,
            'exclusiveMinimum' => $exclusiveMinimum,
            'exclusiveMaximum' => $exclusiveMaximum,
            'multipleOf' => $schema->multipleOf,

            // Array
            'items' => $schema->items !== null ? $this->compileSchema($schema->items) : null,
            'minItems' => $schema->minItems,
            'maxItems' => $schema->maxItems,
            'uniqueItems' => $schema->uniqueItems,

            // Object
            'properties' => $schema->properties !== null ? $this->compileProperties($schema->properties) : null,
            'required' => $schema->required,
            'additionalProperties' => $schema->additionalProperties !== null ? (is_bool($schema->additionalProperties) ? $schema->additionalProperties : $this->compileSchema($schema->additionalProperties)) : null,
            'minProperties' => $schema->minProperties,
            'maxProperties' => $schema->maxProperties,

            // Composition
            'allOf' => $schema->allOf !== null ? array_map($this->compileSchema(...), $schema->allOf) : null,
            'anyOf' => $schema->anyOf !== null ? array_map($this->compileSchema(...), $schema->anyOf) : null,
            'oneOf' => $schema->oneOf !== null ? array_map($this->compileSchema(...), $schema->oneOf) : null,
            'not' => $schema->not instanceof OA\Schema ? $this->compileSchema($schema->not) : null,

            // Meta
            'deprecated' => $schema->deprecated,
            'readOnly' => $schema->readOnly,
            'writeOnly' => $schema->writeOnly,

            // OpenAPI extensions on schema
            'discriminator' => $schema->discriminator instanceof OA\Discriminator ? $this->compileDiscriminator($schema->discriminator) : null,
            'externalDocs' => $schema->externalDocs instanceof OA\ExternalDocumentation ? $this->compileExternalDocs($schema->externalDocs) : null,
            'xml' => $schema->xml instanceof OA\Xml ? $this->compileXml($schema->xml) : null,
        ], $schema);

        if ($schema->default !== Undefined::UNDEFINED) {
            $result['default'] = $schema->default;
        }
        if ($schema->example !== Undefined::UNDEFINED) {
            $result['example'] = $schema->example;
        } elseif ($schema->examples !== null && $schema->examples !== []) {
            $result['example'] = $schema->examples[0];
        }
        // const is not supported in 3.0 — fall back to enum
        if ($schema->const !== Undefined::UNDEFINED && !isset($result['enum'])) {
            $result['enum'] = [$schema->const];
        }

        return $result;
    }

    protected function validateSchemas(Specification $specification, array &$diagnostics): void
    {
        $allSchemas = $this->collectSchemas($specification);

        foreach ($allSchemas as $schema) {
            $type = $schema->type;
            if (is_array($type)) {
                $type = array_filter($type, fn (string $t): bool => $t !== 'null');
                $type = reset($type) ?: null;
            }

            if ($type === 'array' && $schema->items === null) {
                $diagnostics[] = [
                    'level' => 'warning',
                    'message' => 'Schema' . ($schema->schema ? " \"$schema->schema\"" : '') . ' has type "array" but no items',
                ];
            }

            if ($schema->prefixItems !== null) {
                $diagnostics[] = [
                    'level' => 'warning',
                    'message' => 'Schema' . ($schema->schema ? " \"$schema->schema\"" : '') . ': prefixItems is not supported in OpenAPI 3.0',
                ];
            }

            if ($schema->unevaluatedProperties !== null) {
                $diagnostics[] = [
                    'level' => 'warning',
                    'message' => 'Schema' . ($schema->schema ? " \"$schema->schema\"" : '') . ': unevaluatedProperties is not supported in OpenAPI 3.0',
                ];
            }

            if ($schema->unevaluatedItems !== null) {
                $diagnostics[] = [
                    'level' => 'warning',
                    'message' => 'Schema' . ($schema->schema ? " \"$schema->schema\"" : '') . ': unevaluatedItems is not supported in OpenAPI 3.0',
                ];
            }

            if ($schema->if instanceof OA\Schema || $schema->then instanceof OA\Schema || $schema->else instanceof OA\Schema) {
                $diagnostics[] = [
                    'level' => 'warning',
                    'message' => 'Schema' . ($schema->schema ? " \"$schema->schema\"" : '') . ': if/then/else is not supported in OpenAPI 3.0',
                ];
            }

            if ($schema->const !== Undefined::UNDEFINED) {
                $diagnostics[] = [
                    'level' => 'warning',
                    'message' => 'Schema' . ($schema->schema ? " \"$schema->schema\"" : '') . ': const is not supported in OpenAPI 3.0, using enum fallback',
                ];
            }

            if ($schema->examples !== null) {
                $diagnostics[] = [
                    'level' => 'warning',
                    'message' => 'Schema' . ($schema->schema ? " \"$schema->schema\"" : '') . ': examples array is not supported in OpenAPI 3.0, using first value as example',
                ];
            }
        }
    }
}
