<?php declare(strict_types=1);

namespace OpenApi\Snippets\Guide\ExtensionPoints;

use OpenApi\Augmenter\Group;
use OpenApi\Utils\PipeInterface;

final class TagFromController implements PipeInterface
{
    public function __invoke(mixed $specification): mixed
    {
        foreach ($specification->operations as $operation) {
            $reflector = $operation->getReflector();
            if ($reflector instanceof \ReflectionMethod) {
                $controller = $reflector->getDeclaringClass()->getShortName();
                $operation->tags ??= [preg_replace('/Controller$/', '', $controller) . 's'];
            }
        }

        return $specification;
    }

    public function group(): string|\BackedEnum
    {
        return Group::Augment;
    }
}
