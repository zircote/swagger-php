<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Contracts\AttributeInterface;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Undefined;
use OpenApi\Utils\DocBlockParser;
use OpenApi\Utils\PipeInterface;

/**
 * Fills summary, description, and deprecated from PHP docblock comments.
 *
 * Walks all attributes in the specification that have summary/description
 * properties and populates them from the reflector's docblock when not
 * explicitly set.
 *
 * @implements PipeInterface<Specification>
 */
class Docblocks implements PipeInterface
{
    public function __construct(
        protected DocBlockParser $parser = new DocBlockParser(),
    ) {
    }

    public function __invoke(mixed $payload): mixed
    {
        $payload->getWalker()->visit(AttributeInterface::class, function (AttributeInterface $attribute): void {
            if ($attribute instanceof OA\Property) {
                $this->augmentProperty($attribute);
            } elseif ($attribute instanceof OA\Schema) {
                $this->augmentSchema($attribute);
            } elseif ($attribute instanceof OA\Parameter) {
                $this->augmentParameterDescription($attribute);
            } elseif ($attribute instanceof OA\Header) {
                $this->augmentHeader($attribute);
            }
        });

        foreach ($payload->operations as $operation) {
            $this->augmentSummaryAndDescription($operation);
            $this->augmentDeprecated($operation);
            $this->augmentOperationParameters($operation);
        }

        return null;
    }

    /**
     * Override the docblock parser used to extract summaries and descriptions.
     */
    public function setParser(DocBlockParser $parser): static
    {
        $this->parser = $parser;

        return $this;
    }

    public function group(): string|\BackedEnum
    {
        return Group::Augment;
    }

    protected function augmentSummaryAndDescription(OA\Operation $operation): void
    {
        if (!Undefined::isDefault($operation->summary) && !Undefined::isDefault($operation->description)) {
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

        if (Undefined::isDefault($operation->summary) && Undefined::isDefault($operation->description)) {
            $operation->summary = $this->extractSummary($content);
            $operation->description = $this->extractDescription($content);
        } elseif (Undefined::isDefault($operation->summary)) {
            $operation->summary = $content;
        } elseif (Undefined::isDefault($operation->description)) {
            $operation->description = $content;
        }
    }

    protected function augmentDescription(OA\Schema $schema): void
    {
        if (!Undefined::isDefault($schema->description)) {
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
            if (Undefined::isDefault($parameter->description)
                && $parameter->name !== null
                && isset($paramDescriptions[$parameter->name])
                && $paramDescriptions[$parameter->name]['description'] !== null
            ) {
                $parameter->description = $paramDescriptions[$parameter->name]['description'];
            }
        }
    }

    protected function augmentProperty(OA\Property $property): void
    {
        $property->schema ??= new OA\Schema();
        if ($this->getPropertyDocComment($property->schema) !== null) {
            return;
        }

        $docblock = $this->getDocComment($property->schema) ?? $this->getDocComment($property);
        if ($docblock !== null) {
            $this->augmentPropertyDocumentation($property->schema, $docblock);
        }
    }

    protected function augmentSchema(OA\Schema $schema): void
    {
        if (($docblock = $this->getPropertyDocComment($schema)) !== null) {
            $this->augmentPropertyDocumentation($schema, $docblock);
        } elseif ($schema->getReflector() instanceof \ReflectionClass) {
            $this->augmentDescription($schema);
            $this->augmentDeprecated($schema);
        }
    }

    protected function augmentHeader(OA\Header $header): void
    {
        if (($docblock = $this->getPropertyDocComment($header)) !== null) {
            $this->augmentPropertyDocumentation($header, $docblock);
        }
    }

    protected function augmentPropertyDocumentation(OA\Parameter|OA\Header|OA\Schema $attribute, string $docblock): void
    {
        if (Undefined::isDefault($attribute->description)) {
            $content = $this->parser->parseDocblock($docblock);
            if ($content !== '' && !Undefined::isDefault($content)) {
                $attribute->description = $content;
            } else {
                // Descriptions embedded in @var tags need parseVarLine();
                // parseDocblock() only extracts free text before tags.
                $varLine = $this->parser->parseVarLine($docblock);
                if ($varLine['description'] !== null) {
                    $attribute->description = $varLine['description'];
                }
            }
        }

        if ($attribute->deprecated === null && $this->parser->isDeprecated($docblock)) {
            $attribute->deprecated = true;
        }

        if (Undefined::isDefault($attribute->example) && $attribute->examples === null) {
            $schema = $attribute instanceof OA\Schema ? $attribute : ($attribute->schema ?? new OA\Schema());
            $attribute->example = $this->extractExample($schema, $docblock);
        }
    }

    protected function augmentParameterDescription(OA\Parameter $parameter): void
    {
        if (($docblock = $this->getPropertyDocComment($parameter)) !== null) {
            $this->augmentPropertyDocumentation($parameter, $docblock);
        }

        if (!Undefined::isDefault($parameter->description)) {
            return;
        }

        $reflector = $parameter->getReflector();

        if ($reflector instanceof \ReflectionParameter) {
            $name = $reflector->getName();

            // PHP 8.6+ exposes docblocks on parameters directly
            if (method_exists($reflector, 'getDocComment') && ($doc = $reflector->getDocComment()) !== false) {
                $varLine = $this->parser->parseVarLine($doc);
                if ($varLine['description'] !== null) {
                    $parameter->description = $varLine['description'];

                    return;
                }
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

    protected function getPropertyDocComment(AttributeInterface $attribute): ?string
    {
        $reflector = $attribute->getReflector();
        if (!$reflector instanceof \ReflectionProperty && (!$reflector instanceof \ReflectionParameter || !$reflector->isPromoted())) {
            return null;
        }

        return $this->getDocComment($attribute);
    }

    protected function getDocComment(AttributeInterface $attribute): ?string
    {
        $reflector = $attribute->getReflector();
        if (!$reflector instanceof \Reflector) {
            return null;
        }

        if ($reflector instanceof \ReflectionParameter && $reflector->isPromoted()) {
            $function = $reflector->getDeclaringFunction();
            $class = $function instanceof \ReflectionMethod ? $function->getDeclaringClass() : null;
            if ($class?->hasProperty($reflector->getName())) {
                $reflector = $class->getProperty($reflector->getName());
            }
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

    protected function extractExample(OA\Schema $schema, string $docblock): mixed
    {
        $example = $this->parser->extractExample($docblock);
        if ($example === null) {
            return Undefined::UNDEFINED;
        }

        return $this->parser->castExample($example, (array) $schema->type, $schema->nullable === true);
    }
}
