<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Analysers;

use OpenApi\Analysis;
use OpenApi\Annotations\Property;
use OpenApi\Context;
use OpenApi\Generator;

/**
 * OpenApi analyser using reflection.
 *
 * Can read either PHP `DocBlock`s or `Attribute`s.
 *
 * Due to the nature of reflection this requires all related classes
 * to be auto-loadable.
 */
class ReflectionAnalyser implements AnalyserInterface
{
    /** @var AnnotationFactoryInterface[] */
    protected $annotationFactories;

    /** @var Generator|null */
    protected $generator;

    public function __construct(array $annotationFactories = [])
    {
        $this->annotationFactories = $annotationFactories;
        if (!$this->annotationFactories) {
            throw new \InvalidArgumentException('Need at least one annotation factory');
        }
    }

    public function setGenerator(Generator $generator): void
    {
        $this->generator = $generator;

        foreach ($this->annotationFactories as $annotationFactory) {
            $annotationFactory->setGenerator($generator);
        }
    }

    public function fromFile(string $filename, Context $context): Analysis
    {
        $scanner = new TokenScanner();
        $fileDetails = $scanner->scanFile($filename);

        $analysis = new Analysis([], $context);
        foreach ($fileDetails as $fqdn => $details) {
            $this->analyzeFqdn($fqdn, $analysis, $details);
        }

        return $analysis;
    }

    public function fromFqdn(string $fqdn, Analysis $analysis): Analysis
    {
        $fqdn = ltrim($fqdn, '\\');

        $rc = new \ReflectionClass($fqdn);
        if (!$filename = $rc->getFileName()) {
            return $analysis;
        }

        $scanner = new TokenScanner();
        $fileDetails = $scanner->scanFile($filename);

        $this->analyzeFqdn($fqdn, $analysis, $fileDetails[$fqdn]);

        return $analysis;
    }

    protected function analyzeFqdn(string $fqdn, Analysis $analysis, array $details): Analysis
    {
        if (!class_exists($fqdn) && !interface_exists($fqdn) && !trait_exists($fqdn) && (!function_exists('enum_exists') || !enum_exists($fqdn))) {
            $analysis->context->logger->warning('Skipping unknown ' . $fqdn);

            return $analysis;
        }

        $rc = new \ReflectionClass($fqdn);
        $contextType = $rc->isInterface() ? 'interface' : ($rc->isTrait() ? 'trait' : ((method_exists($rc, 'isEnum') && $rc->isEnum()) ? 'enum' : 'class'));
        $context = new Context([
            $contextType => $rc->getShortName(),
            'namespace' => $rc->getNamespaceName() ?: Generator::UNDEFINED,
            'comment' => $rc->getDocComment() ?: Generator::UNDEFINED,
            'filename' => $rc->getFileName() ?: Generator::UNDEFINED,
            'line' => $rc->getStartLine(),
            'annotations' => [],
            'scanned' => $details,
        ], $analysis->context);

        $definition = [
            $contextType => $rc->getShortName(),
            'extends' => null,
            'implements' => [],
            'traits' => [],
            'properties' => [],
            'methods' => [],
            'context' => $context,
        ];
        $normaliseClass = function (string $name): string {
            return '\\' . $name;
        };
        if ($parentClass = $rc->getParentClass()) {
            $definition['extends'] = $normaliseClass($parentClass->getName());
        }
        $definition[$contextType == 'class' ? 'implements' : 'extends'] = array_map($normaliseClass, $details['interfaces']);
        $definition['traits'] = array_map($normaliseClass, $details['traits']);

        foreach ($this->annotationFactories as $annotationFactory) {
            $analysis->addAnnotations($annotationFactory->build($rc, $context), $context);
        }

        foreach ($rc->getMethods() as $method) {
            if (in_array($method->name, $details['methods'])) {
                $definition['methods'][$method->getName()] = $ctx = new Context([
                    'method' => $method->getName(),
                    'comment' => $method->getDocComment() ?: Generator::UNDEFINED,
                    'filename' => $method->getFileName() ?: Generator::UNDEFINED,
                    'line' => $method->getStartLine(),
                    'annotations' => [],
                ], $context);
                foreach ($this->annotationFactories as $annotationFactory) {
                    $analysis->addAnnotations($annotationFactory->build($method, $ctx), $ctx);
                }
            }
        }

        foreach ($rc->getProperties() as $property) {
            if (in_array($property->name, $details['properties'])) {
                $definition['properties'][$property->getName()] = $ctx = new Context([
                    'property' => $property->getName(),
                    'comment' => $property->getDocComment() ?: Generator::UNDEFINED,
                    'annotations' => [],
                ], $context);
                if ($property->isStatic()) {
                    $ctx->static = true;
                }
                if (\PHP_VERSION_ID >= 70400 && ($type = $property->getType())) {
                    $ctx->nullable = $type->allowsNull();
                    if ($type instanceof \ReflectionNamedType) {
                        $ctx->type = $type->getName();
                        // Context::fullyQualifiedName(...) expects this
                        if (class_exists($absFqn = '\\' . $ctx->type)) {
                            $ctx->type = $absFqn;
                        }
                    }
                }
                foreach ($this->annotationFactories as $annotationFactory) {
                    $analysis->addAnnotations($annotationFactory->build($property, $ctx), $ctx);
                }
            }
        }

        foreach ($rc->getReflectionConstants() as $constant) {
            foreach ($this->annotationFactories as $annotationFactory) {
                $definition['constants'][$constant->getName()] = $ctx = new Context([
                    'constant' => $constant->getName(),
                    'comment' => $constant->getDocComment() ?: Generator::UNDEFINED,
                    'annotations' => [],
                ], $context);
                foreach ($annotationFactory->build($constant, $ctx) as $annotation) {
                    if ($annotation instanceof Property) {
                        if (Generator::isDefault($annotation->property)) {
                            $annotation->property = $constant->getName();
                        }
                        if (Generator::isDefault($annotation->const)) {
                            $annotation->const = $constant->getValue();
                        }
                        $analysis->addAnnotation($annotation, $ctx);
                    }
                }
            }
        }

        $addDefinition = 'add' . ucfirst($contextType) . 'Definition';
        $analysis->{$addDefinition}($definition);

        return $analysis;
    }
}
