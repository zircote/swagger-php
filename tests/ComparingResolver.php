<?php declare(strict_types=1);

namespace OpenApi\Tests;

use OpenApi\Context;
use OpenApi\Type\LegacyTypeResolver;
use OpenApi\Type\TypeInfoTypeResolver;
use OpenApi\TypeResolverInterface;
use Symfony\Component\Yaml\Yaml;

class ComparingResolver implements TypeResolverInterface
{
    protected OpenApiTestCase $testCase;
    protected LegacyTypeResolver $legacyTypeResolver;
    protected ?TypeInfoTypeResolver $typeInfoResolver = null;

    public function __construct(OpenApiTestCase $testCase, ?Context $context = null)
    {
        $this->testCase = $testCase;

        $this->legacyTypeResolver = new LegacyTypeResolver($context);
        if (class_exists(\Radebatz\TypeInfoExtras\TypeResolver\StringTypeResolver::class)) {
            $this->typeInfoResolver = new TypeInfoTypeResolver();
        }
    }

    public function setContext(Context $context): ComparingResolver
    {
        $this->legacyTypeResolver = new LegacyTypeResolver($context);

        return $this;
    }

    /**
     * @param \ReflectionParameter|\ReflectionProperty|\ReflectionMethod $reflector
     */
    public function getReflectionTypeDetails(\Reflector $reflector): \stdClass
    {
        $legacyDetails = $this->legacyTypeResolver->getReflectionTypeDetails($reflector);
        $typeInfoDetails = $this->typeInfoResolver
            ? $this->typeInfoResolver->getReflectionTypeDetails($reflector)
            : null;

        $this->compare($legacyDetails, $typeInfoDetails);

        return $typeInfoDetails ?? $legacyDetails;
    }

    /**
     * @param \ReflectionParameter|\ReflectionProperty|\ReflectionMethod $reflector
     */
    public function getDocblockTypeDetails(\Reflector $reflector): \stdClass
    {
        $legacyDetails = $this->legacyTypeResolver->getDocblockTypeDetails($reflector);
        $typeInfoDetails = $this->typeInfoResolver
            ? $this->typeInfoResolver->getDocblockTypeDetails($reflector)
            : null;

        // $this->compare($legacyDetails, $typeInfoDetails);

        return $typeInfoDetails ?? $legacyDetails;
    }

    protected function compare(\stdClass $legacyDetails, ?\stdClass $typeInfoDetails): void
    {
        if ($typeInfoDetails) {
            // var_dump(json_encode($typeInfoDetails, JSON_PRETTY_PRINT));
            // var_dump(json_encode($legacyDetails, JSON_PRETTY_PRINT));
            $this->testCase->assertSpecEquals(
                Yaml::dump((array) $typeInfoDetails),
                Yaml::dump((array) $legacyDetails)
            );
        }
    }
}
