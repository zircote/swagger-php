<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Utils\Config;
use OpenApi\Utils\PipeInterface;

/**
 * Sets the media type of every `MediaType` that has none.
 *
 * That is a bare `Schema` given as `content`, or a `MediaType` written without `mediaType`.
 * A media type that is set is left alone, so an explicit one is how a single response or
 * request body departs from the default.
 *
 * @implements PipeInterface<Specification>
 */
class DefaultMediaType implements PipeInterface
{
    public function __construct(
        #[Config('The media type given to content that names none.')]
        protected string $mediaType = 'application/json',
    ) {
    }

    public function __invoke(mixed $payload): mixed
    {
        $payload->getWalker()->visit(OA\MediaType::class, function (OA\MediaType $mediaType): void {
            $mediaType->mediaType ??= $this->mediaType;
        });

        return null;
    }

    public function setMediaType(string $mediaType): static
    {
        $this->mediaType = $mediaType;

        return $this;
    }

    public function group(): string|\BackedEnum
    {
        return Group::Resolve;
    }
}
