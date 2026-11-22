<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Compiler;

use OpenApi\Compiler\OpenApi30Compiler;
use OpenApi\Compiler\OpenApi31Compiler;
use OpenApi\Compiler\OpenApi32Compiler;
use OpenApi\Contracts\AttributeInterface;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Tests\Concerns\CollectsSpecClasses;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Every property of every spec attribute either changes the document or is reported, at every version.
 *
 * Each optional constructor argument is set on a minimal document, which is compiled with and
 * without it. A property that changes neither the output nor the log is dropped silently. The
 * fix is to write it in the compile method, or to list it in the compiler's
 * `UNSUPPORTED_PROPERTIES`, which leaves it out with a warning. A property the pipeline consumes
 * before compiling goes in `PIPELINE_PROPERTIES` instead. An attribute whose type fixes a value the
 * compiler lists in `UNSUPPORTED_VALUES`, such as a device authorization flow before 3.2, is
 * left out whole, and only needs a message.
 */
final class PropertyCoverageTest extends TestCase
{
    use CollectsSpecClasses;

    /**
     * Properties no compiler sees, as `property` or `Class::property` => what reads them.
     */
    protected const PIPELINE_PROPERTIES = [
        'attachables' => 'attached to their owner by the Assembler',
        'fan' => 'decides where the Assembler nests the attribute',
        'component' => 'names a component; an inline one is moved to components by the pipeline',
        OA\Operation::class . '::methods' => 'one operation per method, made by the Assembler',
        OA\PathItem::class . '::prefix' => 'joined to the paths below it by the path item hierarchy',
        OA\PathItem::class . '::tags' => "applied to the path's operations by the PathItems augmenter",
        OA\PathItem::class . '::security' => "applied to the path's operations by the PathItems augmenter",
        OA\PathItem::class . '::responses' => "applied to the path's operations by the PathItems augmenter",
        OA\Parameter::class . '::parameter' => 'the component name',
        OA\RequestBody::class . '::request' => 'the component name',
        OA\Schema::class . '::schema' => 'the component name',
        OA\Schema\Ref::class . '::qualifiers' => 'part of the component key',
        OA\MediaType\Json::class . '::ref' => 'turned into the schema by the Shortcuts augmenter',
        OA\MediaType\Json::class . '::type' => 'turned into the schema by the Shortcuts augmenter',
        OA\MediaType\Json::class . '::items' => 'turned into the schema by the Shortcuts augmenter',
        OA\MediaType\Json::class . '::properties' => 'turned into the schema by the Shortcuts augmenter',
        OA\MediaType\Json::class . '::required' => 'turned into the schema by the Shortcuts augmenter',
        OA\MediaType\Xml::class . '::ref' => 'turned into the schema by the Shortcuts augmenter',
        OA\MediaType\Xml::class . '::type' => 'turned into the schema by the Shortcuts augmenter',
        OA\MediaType\Xml::class . '::items' => 'turned into the schema by the Shortcuts augmenter',
        OA\MediaType\Xml::class . '::properties' => 'turned into the schema by the Shortcuts augmenter',
        OA\MediaType\Xml::class . '::required' => 'turned into the schema by the Shortcuts augmenter',
        OA\Property::class . '::required' => "added to the parent schema's required list by the Properties augmenter",
        OA\Property\Encoded::class . '::encoding' => "moved to the media type's encoding by the MediaTypes augmenter",
        OA\Attachable::class . '::x' => 'a custom attribute, written by the code that defines it',
    ];

    /**
     * @return iterable<string, array{class-string<AttributeInterface>, class-string<OpenApi31Compiler>}>
     */
    public static function attributes(): iterable
    {
        foreach (self::allSpecClasses() as $class) {
            foreach ([OpenApi30Compiler::class, OpenApi31Compiler::class, OpenApi32Compiler::class] as $compiler) {
                $version = (new $compiler())->getVersion();
                yield substr($class, strlen('OpenApi\\Spec\\')) . ' ' . $version => [$class, $compiler];
            }
        }
    }

    /**
     * @param class-string<AttributeInterface> $class
     * @param class-string<OpenApi31Compiler>  $compiler
     */
    #[DataProvider('attributes')]
    public function testEveryPropertyIsWrittenOrReported(string $class, string $compiler): void
    {
        $host = $this->hostFor($class);
        $this->assertNotNull($host, "{$class} has no place in a document yet; add one to hosts()");
        [$base, $wrap] = $host;

        $parameters = (new \ReflectionClass($class))->getConstructor()?->getParameters() ?? [];
        $base = array_intersect_key($base, array_flip(array_map(fn (\ReflectionParameter $parameter): string => $parameter->getName(), $parameters)));
        $empty = $this->compile($compiler, null);

        if ($this->carriesUnsupportedValue($compiler, new $class(...$base))) {
            // the version leaves out the whole attribute, for a value its type fixes
            $this->assertNotSame([], array_diff($this->compile($compiler, $wrap(new $class(...$base)))['log'], $empty['log']), "{$class} is left out without a message");

            return;
        }

        $silent = [];
        foreach ($parameters as $parameter) {
            $property = $parameter->getName();
            if (!$parameter->isOptional() || $this->consumedByPipeline($class, $property)) {
                continue;
            }

            $without = $base;
            unset($without[$property]);
            $with = $base;
            $with[$property] = ($base[$property] ?? null) ?: $this->sample($class, $parameter);

            $before = $this->compile($compiler, $wrap(new $class(...$without)));
            $after = $this->compile($compiler, $wrap(new $class(...$with)));

            if ($before['document'] === $empty['document'] && $after['document'] === $empty['document'] && array_diff($before['log'], $empty['log']) !== []) {
                // the version leaves out the whole object, with a message of its own
                continue;
            }

            if ($before['document'] === $after['document'] && array_diff($after['log'], $before['log']) === []) {
                $silent[] = $property;
            }
        }

        $this->assertSame([], $silent, sprintf(
            '%s at %s drops these properties without a message: write them, list them in UNSUPPORTED_PROPERTIES, or add them to PIPELINE_PROPERTIES',
            $class,
            (new $compiler())->getVersion(),
        ));
    }

    /**
     * A minimal document, with the root attribute that carries the attribute under test.
     *
     * @param  class-string<OpenApi31Compiler>            $compiler
     * @return array{document: string, log: list<string>}
     */
    protected function compile(string $compiler, mixed $root): array
    {
        $version = (new $compiler())->getVersion();
        $specification = new Specification();
        $specification->openapi = new OA\OpenApi(version: $version);
        $specification->info = new OA\Info(title: 'Title', version: '1.0');
        if ($root instanceof AttributeInterface) {
            $specification->add($root);
        }
        $specification->openapi->version ??= $version;

        $instance = new $compiler();
        $log = array_column($instance->validate($specification), 'message');

        return ['document' => (string) json_encode($instance->compile($specification)), 'log' => $log];
    }

    protected function consumedByPipeline(string $class, string $property): bool
    {
        if (isset(self::PIPELINE_PROPERTIES[$property])) {
            return true;
        }

        for ($current = $class; $current !== false; $current = get_parent_class($current)) {
            if (isset(self::PIPELINE_PROPERTIES[$current . '::' . $property])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whether the attribute has a value the compiler lists in `UNSUPPORTED_VALUES`.
     *
     * @param class-string<OpenApi31Compiler> $compiler
     */
    protected function carriesUnsupportedValue(string $compiler, AttributeInterface $attribute): bool
    {
        /** @var array<class-string, array<string, list<string>>> $unsupported */
        $unsupported = (new \ReflectionClassConstant($compiler, 'UNSUPPORTED_VALUES'))->getValue();
        foreach ($unsupported as $listed => $properties) {
            if ($attribute instanceof $listed) {
                foreach ($properties as $property => $values) {
                    if (in_array($attribute->{$property}, $values, true)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * The arguments an attribute needs to be written at all, and the root attribute that carries it.
     *
     * @return array{array<string, mixed>, \Closure}|null
     */
    protected function hostFor(string $class): ?array
    {
        $hosts = $this->hosts();
        for ($current = $class; $current !== false; $current = get_parent_class($current)) {
            if (isset($hosts[$current])) {
                return $hosts[$current];
            }
        }

        return null;
    }

    /**
     * @return array<class-string, array{array<string, mixed>, \Closure}>
     */
    protected function hosts(): array
    {
        $ok = fn (): array => [new OA\Response(response: 200, description: 'OK')];
        $post = fn (array $arguments = []): OA\Operation => new OA\Operation(...['path' => '/pets', 'method' => 'post', 'responses' => $ok()] + $arguments);
        $get = fn (OA\Response $response): OA\Operation => new OA\Operation(path: '/pets', method: 'get', responses: [$response]);
        $body = fn (OA\MediaType $mediaType): OA\Operation => $post(['requestBody' => new OA\RequestBody(content: [$mediaType])]);
        $schema = fn (array $arguments): OA\Schema => new OA\Schema(...['component' => 'Pet'] + $arguments);
        $itself = fn (AttributeInterface $attribute): AttributeInterface => $attribute;

        return [
            OA\OpenApi::class => [[], $itself],
            OA\Info::class => [['title' => 'Title', 'version' => '1.0'], $itself],
            OA\Contact::class => [['name' => 'Name'], fn (OA\Contact $contact): OA\Info => new OA\Info(title: 'Title', version: '1.0', contact: $contact)],
            OA\License::class => [['name' => 'MIT'], fn (OA\License $license): OA\Info => new OA\Info(title: 'Title', version: '1.0', license: $license)],
            OA\Server::class => [['url' => 'https://example.com'], $itself],
            OA\ServerVariable::class => [['serverVariable' => 'region', 'default' => 'eu'], fn (OA\ServerVariable $variable): OA\Server => new OA\Server(url: 'https://{region}.example.com', variables: [$variable])],
            OA\Tag::class => [['name' => 'pets'], $itself],
            OA\ExternalDocumentation::class => [['url' => 'https://example.com/docs'], $itself],
            OA\Operation::class => [['path' => '/pets', 'method' => 'post', 'responses' => $ok()], $itself],
            OA\PathItem::class => [[], function (OA\PathItem $pathItem): OA\PathItem {
                $pathItem->path = '/pets';

                return $pathItem;
            }],
            OA\Schema::class => [['component' => 'Pet', 'type' => 'string'], $itself],
            OA\Schema\Items::class => [['type' => 'string'], fn (OA\Schema $items): OA\Schema => $schema(['type' => 'array', 'items' => $items])],
            OA\Schema\AdditionalProperties::class => [['type' => 'string'], fn (OA\Schema $additional): OA\Schema => $schema(['type' => 'object', 'additionalProperties' => $additional])],
            OA\Schema\Ref::class => [['ref' => '#/components/schemas/Owner'], fn (OA\Schema\Ref $ref): OA\Schema => $schema(['ref' => $ref])],
            OA\Property::class => [['property' => 'name', 'schema' => new OA\Schema(type: 'string')], fn (OA\Property $property): OA\Schema => $schema(['type' => 'object', 'properties' => [$property]])],
            OA\Discriminator::class => [['propertyName' => 'kind'], fn (OA\Discriminator $discriminator): OA\Schema => $schema(['discriminator' => $discriminator])],
            OA\Xml::class => [[], fn (OA\Xml $xml): OA\Schema => $schema(['xml' => $xml])],
            OA\Response::class => [['response' => 200, 'description' => 'OK'], $get],
            OA\Parameter::class => [['name' => 'limit', 'in' => 'query'], fn (OA\Parameter $parameter): OA\Operation => $post(['parameters' => [$parameter]])],
            OA\RequestBody::class => [['description' => 'A pet'], fn (OA\RequestBody $requestBody): OA\Operation => $post(['requestBody' => $requestBody])],
            OA\Header::class => [['header' => 'X-Rate', 'schema' => new OA\Schema(type: 'string')], fn (OA\Header $header): OA\Operation => $get(new OA\Response(response: 200, description: 'OK', headers: [$header]))],
            OA\Link::class => [['link' => 'owner', 'operationId' => 'getOwner'], fn (OA\Link $link): OA\Operation => $get(new OA\Response(response: 200, description: 'OK', links: [$link]))],
            OA\Example::class => [['example' => 'cat', 'value' => 'Tom'], fn (OA\Example $example): OA\Operation => $body(new OA\MediaType(mediaType: 'application/json', examples: [$example]))],
            OA\MediaType::class => [['mediaType' => 'application/json'], $body],
            OA\Encoding::class => [['encoding' => 'photo'], fn (OA\Encoding $encoding): OA\Operation => $body(new OA\MediaType(mediaType: 'multipart/form-data', encoding: [$encoding]))],
            OA\Security\Scheme::class => [['securityScheme' => 'basic', 'type' => 'http', 'scheme' => 'basic'], $itself],
            OA\Flow::class => [['flow' => 'implicit', 'authorizationUrl' => 'https://example.com/auth', 'scopes' => []], fn (OA\Flow $flow): OA\Security\Scheme => new OA\Security\Scheme\OAuth2(securityScheme: 'oauth', flows: [$flow])],
            OA\Security\Requirement::class => [['scheme' => 'basic'], fn (OA\Security\Requirement $requirement): OA\OpenApi => new OA\OpenApi(security: [$requirement])],
            OA\Components::class => [[], $itself],
            OA\Attachable::class => [[], $itself],
        ];
    }

    /**
     * A value for an argument, by name where the name decides it, otherwise by type.
     */
    protected function sample(string $class, \ReflectionParameter $parameter): mixed
    {
        $declared = $parameter->getType();
        $types = [];
        foreach ($declared instanceof \ReflectionUnionType ? $declared->getTypes() : [$declared] as $member) {
            $types[] = $member instanceof \ReflectionNamedType ? $member->getName() : 'mixed';
        }

        $named = $this->samplesByName();
        foreach ([$class . '::' . $parameter->getName(), $parameter->getName()] as $key) {
            if (array_key_exists($key, $named) && $this->fits($named[$key], $types)) {
                return $named[$key];
            }
        }

        foreach ($types as $type) {
            $object = match (true) {
                $type === OA\Schema\Ref::class => null,
                is_a($type, OA\Schema::class, true) => new OA\Schema(type: 'integer'),
                is_a($type, OA\ExternalDocumentation::class, true) => new OA\ExternalDocumentation(url: 'https://example.com/more'),
                is_a($type, OA\Contact::class, true) => new OA\Contact(name: 'Support'),
                is_a($type, OA\License::class, true) => new OA\License(name: 'Apache-2.0'),
                is_a($type, OA\Discriminator::class, true) => new OA\Discriminator(propertyName: 'kind'),
                is_a($type, OA\Xml::class, true) => new OA\Xml(name: 'pet'),
                is_a($type, OA\Server::class, true) => new OA\Server(url: 'https://link.example.com'),
                is_a($type, OA\RequestBody::class, true) => new OA\RequestBody(description: 'Body'),
                is_a($type, OA\Encoding::class, true) => new OA\Encoding(contentType: 'text/plain'),
                default => null,
            };
            if ($object !== null) {
                return $object;
            }
        }

        return match (true) {
            in_array('string', $types, true) => 'sample',
            in_array('int', $types, true) => 3,
            in_array('bool', $types, true) => !$parameter->isDefaultValueAvailable() || $parameter->getDefaultValue() !== true,
            in_array('array', $types, true) => ['sample'],
            default => 'sample',
        };
    }

    /**
     * Values a type alone does not give, as `property` or `Class::property` => value.
     *
     * @return array<string, mixed>
     */
    protected function samplesByName(): array
    {
        return [
            'x' => ['key' => 'value'],
            'fan' => OA\Fan::All,
            'attachables' => [new OA\Attachable()],
            'in' => 'header',
            'type' => 'string',
            'format' => 'int32',
            'style' => 'form',
            'method' => 'put',
            'flow' => 'password',
            'tags' => ['pets'],
            'scopes' => ['read' => 'Read pets'],
            'required' => ['name'],
            'enum' => ['eu'],
            'mapping' => ['cat' => '#/components/schemas/Cat'],
            'variables' => [new OA\ServerVariable(serverVariable: 'region', default: 'eu')],
            'servers' => [new OA\Server(url: 'https://other.example.com')],
            'security' => [['basic' => []]],
            'parameters' => [new OA\Parameter(name: 'page', in: 'query')],
            OA\Link::class . '::parameters' => ['id' => '$request.path.id'],
            'responses' => [new OA\Response(response: 201, description: 'Created')],
            'callbacks' => ['onEvent' => ['{$request.body#/url}' => ['post' => ['responses' => ['200' => ['description' => 'OK']]]]]],
            'headers' => [new OA\Header(header: 'X-Trace', schema: new OA\Schema(type: 'string'))],
            'links' => [new OA\Link(link: 'next', operationId: 'getNext')],
            'examples' => [new OA\Example(example: 'dog', value: 'Rex')],
            'encoding' => [new OA\Encoding(encoding: 'file', contentType: 'text/plain')],
            'encodings' => [new OA\Encoding(encoding: 'part', contentType: 'text/plain')],
            'prefixEncoding' => [new OA\Encoding(contentType: 'image/png')],
            'content' => [new OA\MediaType(mediaType: 'text/plain', schema: new OA\Schema(type: 'string'))],
            'flows' => [new OA\Flow(flow: 'implicit', authorizationUrl: 'https://example.com/auth', scopes: [])],
            'properties' => [new OA\Property(property: 'age', schema: new OA\Schema(type: 'integer'))],
            'schemes' => [['basic' => []]],
            'version' => '2.0',
            'response' => 202,
            'mediaType' => 'text/csv',
            'ref' => '#/components/schemas/Owner',
            OA\Components::class . '::schemas' => [new OA\Schema(type: 'string', component: 'Name')],
            OA\Components::class . '::parameters' => [new OA\Parameter(name: 'page', in: 'query', component: 'Page')],
            OA\Components::class . '::responses' => [new OA\Response(description: 'Not found', component: 'NotFound')],
            OA\Components::class . '::requestBodies' => [new OA\RequestBody(description: 'A pet', component: 'Pet')],
            OA\Components::class . '::headers' => [new OA\Header(schema: new OA\Schema(type: 'string'), component: 'Trace')],
            OA\Components::class . '::securitySchemes' => [new OA\Security\Scheme(type: 'http', scheme: 'basic', component: 'basic')],
            OA\Components::class . '::links' => [new OA\Link(operationId: 'getOwner', component: 'Owner')],
            OA\Components::class . '::examples' => [new OA\Example(value: 'Tom', component: 'Cat')],
            OA\Components::class . '::pathItems' => [new OA\PathItem(summary: 'Pets', component: 'Pets')],
            OA\Components::class . '::mediaTypes' => [new OA\MediaType(mediaType: 'application/json', component: 'Json')],
        ];
    }

    /**
     * @param list<string> $types
     */
    protected function fits(mixed $value, array $types): bool
    {
        foreach ($types as $type) {
            $fits = match ($type) {
                'mixed' => true,
                'string' => is_string($value),
                'int' => is_int($value),
                'float' => is_int($value) || is_float($value),
                'bool' => is_bool($value),
                'array' => is_array($value),
                'null' => false,
                default => $value instanceof $type,
            };
            if ($fits) {
                return true;
            }
        }

        return false;
    }
}
