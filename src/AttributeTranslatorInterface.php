<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

use ReflectionAttribute;
use Reflector;

/**
 * Contract for creating raw attributes and translating them into `AttributeInterface` instances.
 */
interface AttributeTranslatorInterface
{
    /**
     * @return array<ReflectionAttribute>
     */
    public function getAttributes(Reflector $reflector): array;

    /**
     * @return array<AttributeInterface>
     */
    public function translate(object $instance): array;
}
