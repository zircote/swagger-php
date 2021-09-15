<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Analysers;

use OpenApi\Analysis;
use OpenApi\Context;

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

    public function __construct(array $annotationFactories = [])
    {
        $this->annotationFactories = $annotationFactories;
        if (!$this->annotationFactories) {
            throw new \InvalidArgumentException('Need at least one annotation factory');
        }
    }

    public function fromFile(string $filename, Context $context): Analysis
    {
        $scanner = new TokenScanner();
        $fileDetails = $scanner->scanFile($filename);

        require_once $filename;

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

        require_once $filename;

        $this->analyzeFqdn($fqdn, $analysis, $fileDetails[$fqdn]);

        return $analysis;
    }

    protected function analyzeFqdn(string $fqdn, Analysis $analysis, array $details): Analysis
    {
        // autoload if needed
        if (!class_exists($fqdn) && !interface_exists($fqdn) && !trait_exists($fqdn)) {
            return $analysis;
        }

        $rc = new \ReflectionClass($fqdn);
        $contextType = $this->contextType($rc);
        $context = new Context([$contextType => $rc->getShortName()]);
        if ($namespace = $rc->getNamespaceName()) {
            $context->namespace = $namespace;
        }

        $definition = [
            $contextType => $rc->getShortName(),
            'extends' => null,
            'implements' => [],
            'traits' => [],
            'properties' => [],
            'methods' => [],
            'context' => $context,
        ];
        foreach ($this->annotationFactories as $annotationFactory) {
            $analysis->addAnnotations($annotationFactory->build($rc, $context), $context);
        }

        if ($parentClass = $rc->getParentClass()) {
            $definition['extends'] = '\\' . $parentClass->getName();
        }

        if ($interfaceNames = $rc->getInterfaceNames()) {
            $definition['implements'] = array_map(function ($name) {
                return '\\' . $name;
            }, $interfaceNames);
        }

        if ($traitNames = $rc->getTraitNames()) {
            $definition['traits'] = array_map(function ($name) {
                return '\\' . $name;
            }, $traitNames);
        }

        foreach ($rc->getMethods() as $method) {
            if (in_array($method->name, $details['methods'])) {
                $definition['methods'][$method->getName()] = $ctx = new Context(['method' => $method->getName()], $context);
                foreach ($this->annotationFactories as $annotationFactory) {
                    $buildContext = new Context([], $ctx);
                    $analysis->addAnnotations($annotationFactory->build($method, $buildContext), $buildContext);
                }
            }
        }

        foreach ($rc->getProperties() as $property) {
            if (in_array($property->name, $details['properties'])) {
                $definition['properties'][$property->getName()] = $ctx = new Context(['property' => $property->getName()], $context);
                if ($property->isStatic()) {
                    $ctx->static = true;
                }
                if (\PHP_VERSION_ID >= 70400 && ($type = $property->getType())) {
                    $ctx->nullable = $type->allowsNull();
                    if ($type instanceof \ReflectionNamedType) {
                        $ctx->type = $type->getName();
                    }
                }
                foreach ($this->annotationFactories as $annotationFactory) {
                    $buildContext = new Context([], $ctx);
                    $analysis->addAnnotations($annotationFactory->build($property, $buildContext), $buildContext);
                }
            }
        }

        $addDefinition = 'add' . ucfirst($contextType) . 'Definition';
        $analysis->{$addDefinition}($definition);

        return $analysis;
    }

    protected function contextType(\ReflectionClass $rc): string
    {
        return $rc->isInterface() ? 'interface' : ($rc->isTrait() ? 'trait' : 'class');
    }
}
