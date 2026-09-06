<?php declare(strict_types=1);

namespace OpenApi\Snippets\Guide\ExtensionPoints;

use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Builder\Result;
use OpenApi\Utils\AttributeFactory;
use OpenApi\Utils\Pipeline;
use OpenApi\Utils\TypedList;

function buildSpec(): Result
{
    return (new Builder())
        ->setMode(Mode::SPEC)
        ->addSource(new \ReflectionClass(PetController::class))
        ->withAttributeFactory(fn (AttributeFactory $factory) => $factory->withTranslators(
            fn (TypedList $translators) => $translators->add(new RouteTranslator())
        ))
        ->withAugmenters(fn (Pipeline $augmenters) => $augmenters->add(new TagFromController()))
        ->build();
}
