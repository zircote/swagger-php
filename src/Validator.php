<?php declare(strict_types=1);

namespace OpenApi;

use OpenApi\Annotations as OA;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class Validator
{
    /**
     * @var array<class-string<OA\AbstractAnnotation>, array<class-string<ValidatorInterface>>>
     */
    protected array $validatorMap = [
        OA\AbstractAnnotation::class => [Validation\DefaultValidator::class],
        OA\OpenApi::class => [Validation\OpenApiValidator::class],
        OA\License::class => [Validation\LicenseValidator::class],
        OA\Schema::class => [Validation\SchemaValidator::class],
        OA\Response::class => [Validation\ResponseValidator::class],
        OA\Parameter::class => [Validation\ParameterValidator::class],
        OA\Operation::class => [Validation\OperationValidator::class],
    ];

    public function __construct(protected LoggerInterface $logger)
    {

    }

    public function validate(Analysis $analysis, OA\AbstractAnnotation $root): bool
    {
        $isValid = true;
        $context = new \stdClass();

        foreach ($this->collectAnnotations($root) as $annotation) {
            foreach ($this->validatorsFor($annotation) as $validator) {
                $isValid = $validator->validate($analysis, $annotation, $context) && $isValid;
            }
        }

        return $isValid;
    }

    /**
     * @return array<OA\AbstractAnnotation>
     */
    protected function collectAnnotations(OA\AbstractAnnotation $root): array
    {
        $annotations = [$root];

        foreach (get_object_vars($root) as $field => $value) {
            if (null === $value || Generator::isDefault($value) || is_scalar($value) || in_array($field, $root::$_blacklist)) {
                continue;
            }

            if ($value instanceof OA\AbstractAnnotation) {
                $annotations = array_merge($annotations, $this->collectAnnotations($value));
            } elseif (is_array($value)) {
                foreach ($value as $item) {
                    if ($item instanceof OA\AbstractAnnotation) {
                        $annotations = array_merge($annotations, $this->collectAnnotations($item));
                    }
                }
            }
        }

        return $annotations;
    }

    /**
     * @return array<ValidatorInterface>
     * @tod cache
     */
    protected function validatorsFor(OA\AbstractAnnotation $root): array
    {
        $validators = [];

        foreach ($this->validatorMap as $annotation => $annotationValidators) {
            if ($root instanceof $annotation) {
                $validators = array_merge($validators, $annotationValidators);
            }
        }

        $ensureInstance = function ($validator): object {
            $validator = $validator instanceof ValidatorInterface
                ? $validator
                : new $validator();

            if ($validator instanceof LoggerAwareInterface) {
                $validator->setLogger($this->logger);
            }

            return $validator;

        };

        return array_map($ensureInstance, $validators);
    }
}
