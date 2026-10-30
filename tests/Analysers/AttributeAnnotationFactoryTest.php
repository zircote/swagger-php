<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Analysers;

use OpenApi\Analysers\AttributeAnnotationFactory;
use OpenApi\Annotations as OA;
use OpenApi\Attributes\Property;
use OpenApi\Tests\Fixtures\ClassLevelExternalDocs;
use OpenApi\Tests\Fixtures\ClassLevelExternalDocsForATag;
use OpenApi\Tests\Fixtures\InvalidPropertyAttribute;
use OpenApi\Tests\Fixtures\UsingAttributes;
use OpenApi\Tests\OpenApiTestCase;

final class AttributeAnnotationFactoryTest extends OpenApiTestCase
{
    public function testReturnedAnnotationsCount(): void
    {
        $rc = new \ReflectionClass(UsingAttributes::class);

        $annotations = (new AttributeAnnotationFactory())->build($rc, $this->getContext());
        $this->assertCount(1, $annotations);
    }

    public function testErrorOnInvalidAttribute(): void
    {
        $instance = new InvalidPropertyAttribute();
        $rm = new \ReflectionMethod($instance, 'post');

        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(Property::class . '::__construct(): Argument #9 ($required) must be of type ?array');

        (new AttributeAnnotationFactory())->build($rm, $this->getContext());
    }

    public function testIgnoreOtherAttributes(): void
    {
        $rc = new \ReflectionClass(UsingAttributes::class);

        (new AttributeAnnotationFactory())->build($rc, $context = $this->getContext());
        $this->assertIsArray($context->other);
        $this->assertCount(1, $context->other);

        (new AttributeAnnotationFactory(true))->build($rc, $context = $this->getContext());
        $this->assertNull($context->other);
    }

    public function testAnAnnotationNoParentHasRoomForStaysForTheRoot(): void
    {
        $annotations = (new AttributeAnnotationFactory())->build(new \ReflectionClass(ClassLevelExternalDocs::class), $this->getContext());

        $this->assertCount(3, $annotations);
        [$project, $user, $externalDocs] = $annotations;
        $this->assertInstanceOf(OA\Tag::class, $project);
        $this->assertInstanceOf(OA\Tag::class, $user);
        $this->assertInstanceOf(OA\ExternalDocumentation::class, $externalDocs);
        $this->assertSame('https://root.example', $externalDocs->url);
        $this->assertSame('https://project.example', $project->externalDocs->url);
        $this->assertSame('https://user.example', $user->externalDocs->url);
        $this->assertSame([], $project->_unmerged);
        $this->assertSame([], $user->_unmerged);
    }

    public function testAnAnnotationStillNestsWhereThereIsRoom(): void
    {
        $annotations = (new AttributeAnnotationFactory())->build(new \ReflectionClass(ClassLevelExternalDocsForATag::class), $this->getContext());

        $this->assertCount(2, $annotations);
        [$project, $user] = $annotations;
        $this->assertInstanceOf(OA\Tag::class, $project);
        $this->assertInstanceOf(OA\Tag::class, $user);
        $this->assertSame('https://project.example', $project->externalDocs->url);
        $this->assertSame('https://user.example', $user->externalDocs->url);
        $this->assertSame([], $project->_unmerged);
    }
}
