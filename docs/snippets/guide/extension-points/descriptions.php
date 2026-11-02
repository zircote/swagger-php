<?php declare(strict_types=1);

namespace OpenApi\Snippets\Guide\ExtensionPoints;

use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Builder\Result;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Specification\ComponentKey;

final class Author
{
    public int $id;

    public string $name;

    public string $email;
}

#[OA\Schema]
final class Article
{
    #[OA\Property]
    public int $id;

    #[OA\Property]
    public Author $author;

    #[OA\Schema\Ref(ref: Author::class, qualifiers: ['groups' => ['summary']])]
    public Author $reviewer;
}

/**
 * `Author` described twice: in full, and with only the fields of its `summary` group.
 */
function buildWithDescriptions(): Result
{
    return (new Builder())
        ->setMode(Mode::SPEC)
        ->addSource(new \ReflectionClass(Article::class))
        ->withSpecification(function (Specification $specification): void {
            $specification->add(
                new OA\Info(title: 'Articles', version: '1.0.0'),
                new OA\Operation\Get(path: '/articles', responses: [
                    new OA\Response(response: 200, description: 'OK', content: [
                        new OA\MediaType\Json(schema: new OA\Schema(ref: Article::class)),
                    ]),
                ]),
            );

            $full = new OA\Schema(component: 'Author', properties: [
                'id' => OA\Type::Integer,
                'name' => OA\Type::String,
                'email' => OA\Type::String,
            ]);
            $summary = new OA\Schema(component: 'AuthorSummary', properties: [
                'id' => OA\Type::Integer,
                'name' => OA\Type::String,
            ]);
            $summary->setComponentKey(new ComponentKey(Author::class, ['groups' => ['summary']]));

            foreach ([$full, $summary] as $schema) {
                $specification->add($schema->setReflector(new \ReflectionClass(Author::class)));
            }
        })
        ->build();
}
