<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\AttributeInterface;
use OpenApi\PipeInterface;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Undefined;
use OpenApi\Utils\DocBlockParser;

/**
 * Fills summary, description, and deprecated from PHP docblock comments.
 *
 * Walks all attributes in the specification that have summary/description
 * properties and populates them from the reflector's docblock when not
 * explicitly set.
 *
 * @implements PipeInterface<Specification>
 */
class Docblock implements PipeInterface
{
    protected DocBlockParser $parser;

    public function __construct()
    {
        $this->parser = new DocBlockParser();
    }

    public function group(): string|\BackedEnum
    {
        return Group::Augment;
    }

    public function __invoke(mixed $payload): mixed
    {
        foreach ($payload->operations as $operation) {
            $this->augmentSummaryAndDescription($operation);
            $this->augmentDeprecated($operation);
            $this->augmentOperationParameters($operation);
        }

        foreach ($payload->schemas as $schema) {
            $this->augmentDescription($schema);
            $this->augmentDeprecated($schema);
            $this->augmentProperties($schema);
        }

        foreach ($payload->parameters as $parameter) {
            $this->augmentParameterDescription($parameter);
        }

        return null;
    }

    protected function augmentSummaryAndDescription(OA\Operation $operation): void
    {
        if ($operation->summary !== null && $operation->description !== null) {
            return;
        }

        $docblock = $this->getDocComment($operation);
        if ($docblock === null) {
            return;
        }

        $content = $this->parser->parseDocblock($docblock);
        if ($content === '' || Undefined::isDefault($content)) {
            return;
        }

        if ($operation->summary === null && $operation->description === null) {
            $operation->summary = $this->extractSummary($content);
            $operation->description = $this->extractDescription($content);
        } elseif ($operation->summary === null) {
            $operation->summary = $content;
        } elseif ($operation->description === null) {
            $operation->description = $content;
        }
    }

    protected function augmentDescription(OA\Schema $schema): void
    {
        if ($schema->description !== null) {
            return;
        }

        $docblock = $this->getDocComment($schema);
        if ($docblock === null) {
            return;
        }

        $content = $this->parser->parseDocblock($docblock);
        if ($content === '' || Undefined::isDefault($content)) {
            return;
        }

        $schema->description = $content;
    }

    protected function augmentDeprecated(OA\Operation|OA\Schema $attribute): void
    {
        if ($attribute->deprecated !== null) {
            return;
        }

        $docblock = $this->getDocComment($attribute);
        if ($docblock === null) {
            return;
        }

        if ($this->parser->isDeprecated($docblock)) {
            $attribute->deprecated = true;
        }
    }

    protected function augmentOperationParameters(OA\Operation $operation): void
    {
        if (!$operation->parameters) {
            return;
        }

        $methodDoc = $this->getDocComment($operation);
        $paramTags = [];
        if ($methodDoc !== null) {
            $this->parser->parseDocblock($methodDoc, $paramTags);
        }
        $paramDescriptions = $paramTags['param'] ?? [];

        foreach ($operation->parameters as $parameter) {
            $this->augmentParameterDescription($parameter, $paramDescriptions);
        }
    }

    /**
     * @param array<string, array{type: ?string, description: ?string}> $parentParamTags
     */
    protected function augmentParameterDescription(OA\Parameter $parameter, array $parentParamTags = []): void
    {
        if ($parameter->description !== null) {
            return;
        }

        $reflector = $parameter->getReflector();

        if ($reflector instanceof \ReflectionParameter) {
            $name = $reflector->getName();
            if (isset($parentParamTags[$name]) && $parentParamTags[$name]['description'] !== null) {
                $parameter->description = $parentParamTags[$name]['description'];

                return;
            }

            $method = $reflector->getDeclaringFunction();
            if ($method instanceof \ReflectionMethod || $method instanceof \ReflectionFunction) {
                $tags = [];
                $doc = $method->getDocComment();
                if ($doc !== false) {
                    $this->parser->parseDocblock($doc, $tags);
                    $params = $tags['param'] ?? [];
                    if (isset($params[$name]) && $params[$name]['description'] !== null) {
                        $parameter->description = $params[$name]['description'];
                    }
                }
            }
        }
    }

    protected function augmentProperties(OA\Schema $schema): void
    {
        if (!$schema->properties) {
            return;
        }

        foreach ($schema->properties as $property) {
            if (!$property instanceof OA\Property) {
                continue;
            }

            if ($property->schema instanceof OA\Schema) {
                $this->augmentDescription($property->schema);
            }
        }
    }

    protected function getDocComment(AttributeInterface $attribute): ?string
    {
        $reflector = $attribute->getReflector();
        if (!$reflector instanceof \Reflector) {
            return null;
        }

        if (!method_exists($reflector, 'getDocComment')) {
            return null;
        }

        $doc = $reflector->getDocComment();

        return $doc !== false ? $doc : null;
    }

    protected function extractSummary(string $content): ?string
    {
        $summary = $this->parser->extractCommentSummary($content);

        return Undefined::isDefault($summary) ? null : $summary;
    }

    protected function extractDescription(string $content): ?string
    {
        $description = $this->parser->extractCommentDescription($content);

        return Undefined::isDefault($description) ? null : $description;
    }
}
