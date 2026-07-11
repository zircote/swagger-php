<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

/**
 * Converts classic annotations into a Specification by iterating the Analysis directly.
 *
 * Expects only MergeJsonContent/MergeXmlContent to have run (unwraps pseudo-annotations).
 * All enrichment (types, refs, descriptions, hierarchy) is left to the spec augmenter chain.
 */
class HybridBridge
{
    public function fromAnalysis(Analysis $analysis): Specification
    {
        $spec = new Specification();

        $classSchemas = [];
        $memberProperties = [];

        foreach ($analysis->annotations as $annotation) {
            if (!$annotation->_context->reflector instanceof \Reflector) {
                continue;
            }

            match (true) {
                $annotation instanceof Annotations\OpenApi => $this->convertOpenApiMeta($annotation, $spec),
                $annotation instanceof Annotations\Info => $spec->info = $this->convertInfo($annotation),
                $annotation instanceof Annotations\Server => $spec->servers[] = $this->convertServer($annotation),
                $annotation instanceof Annotations\Tag => $spec->tags[] = $this->convertTag($annotation),
                $annotation instanceof Annotations\SecurityScheme => $spec->securitySchemes[] = $this->convertSecurityScheme($annotation),
                $annotation instanceof Annotations\PathItem && !$annotation->_context->is('nested') && !Undefined::isDefault($annotation->path) && !($annotation instanceof Annotations\Webhook) => $spec->pathItems[] = $this->convertPathItem($annotation),
                $annotation instanceof Annotations\Components && !$annotation->_context->is('nested') => $this->convertComponents($annotation, $spec),
                $annotation instanceof Annotations\Operation => $spec->operations[] = $this->convertOperation($annotation, $this->val($annotation->path), $this->methodFromAnnotation($annotation)),
                $annotation instanceof Annotations\Schema && $annotation->_context->reflector instanceof \ReflectionClass && !$annotation->_context->is('nested') => $classSchemas[$annotation->_context->reflector->getName()] = $annotation,
                $annotation instanceof Annotations\Property && $this->isClassMember($annotation) => $memberProperties[] = $annotation,
                $annotation instanceof Annotations\Response && !$annotation->_context->is('nested') && !Undefined::isDefault($annotation->response) => $spec->responses[] = $this->convertResponse($annotation),
                $annotation instanceof Annotations\RequestBody && !$annotation->_context->is('nested') && !Undefined::isDefault($annotation->request) => $spec->requestBodies[] = $this->convertRequestBody($annotation),
                $annotation instanceof Annotations\Parameter && !$annotation->_context->is('nested') && !Undefined::isDefault($annotation->parameter) => $spec->parameters[] = $this->convertParameter($annotation),
                $annotation instanceof Annotations\Header && !$annotation->_context->is('nested') && !Undefined::isDefault($annotation->header) => $spec->headers[] = $this->convertHeader($annotation),
                $annotation instanceof Annotations\Link && !$annotation->_context->is('nested') => $spec->links[] = $this->convertLink($annotation),
                $annotation instanceof Annotations\ExternalDocumentation && !$annotation->_context->is('nested') => $spec->externalDocs[] = $this->convertExternalDocs($annotation),
                default => null,
            };
        }

        foreach ($classSchemas as $className => $schema) {
            $converted = $this->convertSchemaWithMembers($schema, $className, $classSchemas, $memberProperties);
            $spec->schemas[] = $converted;
        }

        $spec->openapi ??= new Spec\OpenApi();

        return $spec;
    }

    protected function isClassMember(Annotations\Property $property): bool
    {
        $reflector = $property->_context->reflector;

        return $reflector instanceof \ReflectionProperty
            || $reflector instanceof \ReflectionParameter
            || $reflector instanceof \ReflectionClassConstant
            || $reflector instanceof \ReflectionMethod;
    }

    /**
     * @param array<string, Annotations\Schema> $classSchemas
     * @param list<Annotations\Property>        $allMembers
     */
    protected function convertSchemaWithMembers(Annotations\Schema $schema, string $className, array $classSchemas, array $allMembers): Spec\Schema
    {
        $converted = $this->convertSchema($schema);

        $ownClasses = $this->getOwnClasses($className, $classSchemas);
        $ownProperties = [];
        foreach ($allMembers as $property) {
            $declarer = $this->getDeclaringClass($property);
            if ($declarer !== null && in_array($declarer, $ownClasses, true)) {
                $ownProperties[] = $this->convertProperty($property);
            }
        }

        if ($ownProperties !== []) {
            $converted->properties = [...$ownProperties, ...($converted->properties ?? [])];
        }

        return $converted;
    }

    /**
     * Get the class itself plus non-schema ancestors and interfaces (they contribute properties to this schema).
     *
     * @param array<string, Annotations\Schema> $classSchemas
     *
     * @return list<string>
     */
    protected function getOwnClasses(string $className, array $classSchemas): array
    {
        $classes = [$className];

        if (!class_exists($className) && !interface_exists($className) && !trait_exists($className)) {
            return $classes;
        }

        $ref = new \ReflectionClass($className);

        $parent = $ref->getParentClass();
        while ($parent !== false) {
            if (isset($classSchemas[$parent->getName()])) {
                break;
            }
            $classes[] = $parent->getName();
            foreach ($parent->getTraits() as $trait) {
                if (!isset($classSchemas[$trait->getName()])) {
                    $classes[] = $trait->getName();
                }
            }
            $parent = $parent->getParentClass();
        }

        foreach ($ref->getInterfaces() as $interface) {
            if (!isset($classSchemas[$interface->getName()])) {
                $classes[] = $interface->getName();
            }
        }

        foreach ($ref->getTraits() as $trait) {
            if (!isset($classSchemas[$trait->getName()])) {
                $classes[] = $trait->getName();
            }
        }

        return $classes;
    }

    protected function getDeclaringClass(Annotations\Property $property): ?string
    {
        $reflector = $property->_context->reflector;

        if ($reflector instanceof \ReflectionProperty || $reflector instanceof \ReflectionClassConstant) {
            return $reflector->getDeclaringClass()->getName();
        }
        if ($reflector instanceof \ReflectionMethod) {
            return $reflector->getDeclaringClass()->getName();
        }
        if ($reflector instanceof \ReflectionParameter) {
            $method = $reflector->getDeclaringFunction();
            if ($method instanceof \ReflectionMethod) {
                return $method->getDeclaringClass()->getName();
            }
        }

        return null;
    }

    protected function convertOpenApiMeta(Annotations\OpenApi $openApi, Specification $spec): void
    {
        $spec->openapi = new Spec\OpenApi(
            version: $this->val($openApi->openapi),
        );

        if (!Undefined::isDefault($openApi->security)) {
            $spec->openapi->security = $this->convertSecurityRequirements($openApi->security);
        }

        if (!Undefined::isDefault($openApi->externalDocs)) {
            $spec->externalDocs[] = $this->convertExternalDocs($openApi->externalDocs);
        }

        if (!Undefined::isDefault($openApi->webhooks)) {
            foreach ($openApi->webhooks as $webhook) {
                $this->convertWebhook($webhook, $spec);
            }
        }
    }

    protected function methodFromAnnotation(Annotations\Operation $op): string
    {
        return match (true) {
            $op instanceof Annotations\Get => 'get',
            $op instanceof Annotations\Post => 'post',
            $op instanceof Annotations\Put => 'put',
            $op instanceof Annotations\Delete => 'delete',
            $op instanceof Annotations\Patch => 'patch',
            $op instanceof Annotations\Head => 'head',
            $op instanceof Annotations\Options => 'options',
            $op instanceof Annotations\Trace => 'trace',
            default => strtolower($this->val($op->method) ?? 'get'),
        };
    }

    protected function convertInfo(Annotations\Info $info): Spec\Info
    {
        $contact = null;
        if (!Undefined::isDefault($info->contact)) {
            $contact = new Spec\Contact(
                name: $this->val($info->contact->name),
                url: $this->val($info->contact->url),
                email: $this->val($info->contact->email),
            );
        }

        $license = null;
        if (!Undefined::isDefault($info->license)) {
            $license = new Spec\License(
                name: $this->val($info->license->name),
                identifier: $this->val($info->license->identifier),
                url: $this->val($info->license->url),
            );
        }

        return new Spec\Info(
            title: $this->val($info->title),
            description: $this->val($info->description),
            termsOfService: $this->val($info->termsOfService),
            version: $this->val($info->version),
            contact: $contact,
            license: $license,
            x: $this->extensions($info),
        );
    }

    protected function convertServer(Annotations\Server $server): Spec\Server
    {
        $variables = null;
        if (!Undefined::isDefault($server->variables)) {
            $variables = [];
            foreach ($server->variables as $variable) {
                $variables[] = new Spec\ServerVariable(
                    serverVariable: $this->val($variable->serverVariable),
                    default: $this->val($variable->default),
                    description: $this->val($variable->description),
                    enum: Undefined::isDefault($variable->enum) ? null : $variable->enum,
                    x: $this->extensions($variable),
                );
            }
        }

        return new Spec\Server(
            url: $this->val($server->url),
            description: $this->val($server->description),
            variables: $variables,
            x: $this->extensions($server),
        );
    }

    protected function convertTag(Annotations\Tag $tag): Spec\Tag
    {
        return new Spec\Tag(
            name: $this->val($tag->name),
            description: $this->val($tag->description),
            externalDocs: Undefined::isDefault($tag->externalDocs)
                ? null
                : $this->convertExternalDocs($tag->externalDocs),
            x: $this->extensions($tag),
        );
    }

    protected function convertExternalDocs(Annotations\ExternalDocumentation $docs): Spec\ExternalDocumentation
    {
        return new Spec\ExternalDocumentation(
            url: $this->val($docs->url),
            description: $this->val($docs->description),
            x: $this->extensions($docs),
        );
    }

    protected function convertWebhook(Annotations\PathItem $webhook, Specification $spec): void
    {
        $methods = ['get', 'put', 'post', 'delete', 'options', 'head', 'patch', 'trace'];
        $name = $webhook instanceof Annotations\Webhook
            ? $this->val($webhook->webhook)
            : $this->val($webhook->path);

        foreach ($methods as $method) {
            if (!Undefined::isDefault($webhook->{$method})) {
                $operation = $this->convertOperation($webhook->{$method}, null, $method);
                $operation->webhook = $name;
                $spec->operations[] = $operation;
            }
        }
    }

    protected function convertPathItem(Annotations\PathItem $pathItem): Spec\PathItem
    {
        $parameters = null;
        if (!Undefined::isDefault($pathItem->parameters)) {
            $parameters = [];
            foreach ($pathItem->parameters as $param) {
                $parameters[] = $this->convertParameter($param);
            }
        }

        $result = new Spec\PathItem(
            ref: $this->val($pathItem->ref),
            summary: $this->val($pathItem->summary),
            description: $this->val($pathItem->description),
            parameters: $parameters,
            servers: Undefined::isDefault($pathItem->servers)
                ? null
                : array_map($this->convertServer(...), $pathItem->servers),
            x: $this->extensions($pathItem),
        );
        $result->path = $this->val($pathItem->path);
        $this->copyReflector($pathItem, $result);

        return $result;
    }

    protected function convertOperation(Annotations\Operation $op, ?string $path, string $method): Spec\Operation
    {
        $parameters = null;
        if (!Undefined::isDefault($op->parameters)) {
            $parameters = [];
            foreach ($op->parameters as $param) {
                $parameters[] = $this->convertParameter($param);
            }
        }

        $responses = null;
        if (!Undefined::isDefault($op->responses)) {
            $responses = [];
            foreach ($op->responses as $response) {
                $responses[] = $this->convertResponse($response);
            }
        }

        $requestBody = null;
        if (!Undefined::isDefault($op->requestBody)) {
            $requestBody = $this->convertRequestBody($op->requestBody);
        }

        $callbacks = null;
        if (!Undefined::isDefault($op->callbacks)) {
            $callbacks = $this->convertCallbacks($op->callbacks);
        }

        $security = null;
        if (!Undefined::isDefault($op->security)) {
            $security = $this->convertSecurityRequirements($op->security);
        }

        $result = new Spec\Operation(
            path: $path,
            method: $method,
            operationId: $this->val($op->operationId),
            summary: $this->val($op->summary),
            description: $this->val($op->description),
            tags: Undefined::isDefault($op->tags) ? null : $op->tags,
            parameters: $parameters,
            requestBody: $requestBody,
            responses: $responses,
            callbacks: $callbacks,
            deprecated: $this->val($op->deprecated),
            security: $security,
            servers: Undefined::isDefault($op->servers)
                ? null
                : array_map($this->convertServer(...), $op->servers),
            externalDocs: Undefined::isDefault($op->externalDocs)
                ? null
                : $this->convertExternalDocs($op->externalDocs),
            x: $this->extensions($op),
        );
        $this->copyReflector($op, $result);

        return $result;
    }

    protected function convertParameter(Annotations\Parameter $param): Spec\Parameter
    {
        $result = new Spec\Parameter(
            parameter: $this->val($param->parameter),
            name: $this->val($param->name),
            in: $this->val($param->in),
            description: $this->val($param->description),
            required: $this->val($param->required),
            deprecated: $this->val($param->deprecated),
            allowEmptyValue: $this->val($param->allowEmptyValue),
            ref: $this->val($param->ref),
            style: $this->val($param->style),
            explode: $this->val($param->explode),
            allowReserved: $this->val($param->allowReserved),
            schema: Undefined::isDefault($param->schema) ? null : $this->convertSchema($param->schema),
            example: Undefined::isDefault($param->example) ? Undefined::UNDEFINED : $param->example,
            examples: Undefined::isDefault($param->examples)
                ? null
                : array_map($this->convertExample(...), $param->examples),
            content: Undefined::isDefault($param->content)
                ? null
                : array_map($this->convertMediaType(...), $param->content),
            x: $this->extensions($param),
        );
        $this->copyReflector($param, $result);

        return $result;
    }

    protected function convertResponse(Annotations\Response $response): Spec\Response
    {
        $headers = null;
        if (!Undefined::isDefault($response->headers)) {
            $headers = [];
            foreach ($response->headers as $header) {
                $headers[] = $this->convertHeader($header);
            }
        }

        $links = null;
        if (!Undefined::isDefault($response->links)) {
            $links = [];
            foreach ($response->links as $link) {
                $links[] = $this->convertLink($link);
            }
        }

        $result = new Spec\Response(
            response: $this->val($response->response),
            description: $this->val($response->description),
            ref: $this->val($response->ref),
            headers: $headers,
            content: Undefined::isDefault($response->content)
                ? null
                : array_map($this->convertMediaType(...), $response->content),
            links: $links,
            x: $this->extensions($response),
        );
        $this->copyReflector($response, $result);

        return $result;
    }

    protected function convertRequestBody(Annotations\RequestBody $body): Spec\RequestBody
    {
        $result = new Spec\RequestBody(
            request: $this->val($body->request),
            description: $this->val($body->description),
            required: $this->val($body->required),
            ref: $this->val($body->ref),
            content: Undefined::isDefault($body->content)
                ? null
                : array_map($this->convertMediaType(...), $body->content),
            x: $this->extensions($body),
        );
        $this->copyReflector($body, $result);

        return $result;
    }

    protected function convertMediaType(Annotations\MediaType $mediaType): Spec\MediaType
    {
        $encoding = null;
        if (!Undefined::isDefault($mediaType->encoding)) {
            $encoding = [];
            foreach ($mediaType->encoding as $enc) {
                $encoding[] = $this->convertEncoding($enc);
            }
        }

        return new Spec\MediaType(
            mediaType: $this->val($mediaType->mediaType),
            schema: Undefined::isDefault($mediaType->schema) ? null : $this->convertSchema($mediaType->schema),
            example: Undefined::isDefault($mediaType->example) ? Undefined::UNDEFINED : $mediaType->example,
            examples: Undefined::isDefault($mediaType->examples)
                ? null
                : array_map($this->convertExample(...), $mediaType->examples),
            encoding: $encoding,
            x: $this->extensions($mediaType),
        );
    }

    protected function convertHeader(Annotations\Header $header): Spec\Header
    {
        $result = new Spec\Header(
            header: $this->val($header->header),
            description: $this->val($header->description),
            required: $this->val($header->required),
            deprecated: $this->val($header->deprecated),
            ref: $this->val($header->ref),
            schema: Undefined::isDefault($header->schema) ? null : $this->convertSchema($header->schema),
            x: $this->extensions($header),
        );
        $this->copyReflector($header, $result);

        return $result;
    }

    protected function convertLink(Annotations\Link $link): Spec\Link
    {
        $result = new Spec\Link(
            link: $this->val($link->link),
            operationRef: $this->val($link->operationRef),
            operationId: $this->val($link->operationId),
            parameters: Undefined::isDefault($link->parameters) ? null : $link->parameters,
            requestBody: $this->val($link->requestBody),
            description: $this->val($link->description),
            ref: $this->val($link->ref),
            server: Undefined::isDefault($link->server) ? null : $this->convertServer($link->server),
            x: $this->extensions($link),
        );
        $this->copyReflector($link, $result);

        return $result;
    }

    protected function convertEncoding(Annotations\Encoding $encoding): Spec\Encoding
    {
        return new Spec\Encoding(
            encoding: $this->val($encoding->property),
            contentType: $this->val($encoding->contentType),
            style: $this->val($encoding->style),
            explode: $this->val($encoding->explode),
            allowReserved: $this->val($encoding->allowReserved),
            x: $this->extensions($encoding),
        );
    }

    protected function convertSchema(Annotations\Schema $schema): Spec\Schema
    {
        $properties = null;
        if (!Undefined::isDefault($schema->properties)) {
            $properties = [];
            foreach ($schema->properties as $prop) {
                $properties[] = $this->convertProperty($prop);
            }
        }

        $result = new Spec\Schema(
            schema: $this->val($schema->schema),
            title: $this->val($schema->title),
            description: $this->val($schema->description),
            ref: $this->val($schema->ref) ?? $this->typeAsRef($schema->type),
            type: Undefined::isDefault($schema->type) ? null : $this->filterType($schema->type),
            format: $this->val($schema->format),
            nullable: $this->val($schema->nullable),
            minLength: $this->val($schema->minLength),
            maxLength: $this->val($schema->maxLength),
            pattern: $this->val($schema->pattern),
            minimum: $this->val($schema->minimum),
            maximum: $this->val($schema->maximum),
            exclusiveMinimum: $this->val($schema->exclusiveMinimum),
            exclusiveMaximum: $this->val($schema->exclusiveMaximum),
            multipleOf: $this->val($schema->multipleOf),
            items: Undefined::isDefault($schema->items) ? null : $this->convertSchema($schema->items),
            minItems: $this->val($schema->minItems),
            maxItems: $this->val($schema->maxItems),
            uniqueItems: $this->val($schema->uniqueItems),
            properties: $properties,
            required: Undefined::isDefault($schema->required) ? null : $schema->required,
            additionalProperties: $this->convertAdditionalProperties($schema),
            minProperties: $this->val($schema->minProperties),
            maxProperties: $this->val($schema->maxProperties),
            allOf: Undefined::isDefault($schema->allOf) ? null : array_map($this->convertSchema(...), $schema->allOf),
            anyOf: Undefined::isDefault($schema->anyOf) ? null : array_map($this->convertSchema(...), $schema->anyOf),
            oneOf: Undefined::isDefault($schema->oneOf) ? null : array_map($this->convertSchema(...), $schema->oneOf),
            not: Undefined::isDefault($schema->not) ? null : $this->convertSchema($schema->not),
            enum: Undefined::isDefault($schema->enum) ? null : $schema->enum,
            const: Undefined::isDefault($schema->const) ? Undefined::UNDEFINED : $schema->const,
            example: Undefined::isDefault($schema->example) ? Undefined::UNDEFINED : $schema->example,
            deprecated: $this->val($schema->deprecated),
            readOnly: $this->val($schema->readOnly),
            writeOnly: $this->val($schema->writeOnly),
            default: Undefined::isDefault($schema->default) ? Undefined::UNDEFINED : $schema->default,
            discriminator: Undefined::isDefault($schema->discriminator)
                ? null
                : $this->convertDiscriminator($schema->discriminator),
            externalDocs: Undefined::isDefault($schema->externalDocs)
                ? null
                : $this->convertExternalDocs($schema->externalDocs),
            xml: Undefined::isDefault($schema->xml) ? null : $this->convertXml($schema->xml),
            x: $this->extensions($schema),
        );
        $this->copyReflector($schema, $result);

        return $result;
    }

    protected function convertProperty(Annotations\Property $prop): Spec\Property
    {
        $schema = $this->convertSchema($prop);

        $result = new Spec\Property(
            property: $this->val($prop->property),
            schema: $schema,
        );
        $this->copyReflector($prop, $result);

        return $result;
    }

    protected function convertAdditionalProperties(Annotations\Schema $schema): Spec\Schema|bool|null
    {
        if (Undefined::isDefault($schema->additionalProperties)) {
            return null;
        }

        if (is_bool($schema->additionalProperties)) {
            return $schema->additionalProperties;
        }

        return $this->convertSchema($schema->additionalProperties);
    }

    protected function convertDiscriminator(Annotations\Discriminator $disc): Spec\Discriminator
    {
        return new Spec\Discriminator(
            propertyName: $this->val($disc->propertyName),
            mapping: Undefined::isDefault($disc->mapping) ? null : $disc->mapping,
            x: $this->extensions($disc),
        );
    }

    protected function convertXml(Annotations\Xml $xml): Spec\Xml
    {
        return new Spec\Xml(
            name: $this->val($xml->name),
            namespace: $this->val($xml->namespace),
            prefix: $this->val($xml->prefix),
            attribute: $this->val($xml->attribute),
            wrapped: $this->val($xml->wrapped),
            x: $this->extensions($xml),
        );
    }

    protected function convertSecurityScheme(Annotations\SecurityScheme $scheme): Spec\Security\Scheme
    {
        $flows = null;
        if (!Undefined::isDefault($scheme->flows)) {
            $flows = [];
            foreach ($scheme->flows as $flow) {
                $flows[] = $this->convertFlow($flow);
            }
        }

        $result = new Spec\Security\Scheme(
            securityScheme: $this->val($scheme->securityScheme),
            type: $this->val($scheme->type),
            description: $this->val($scheme->description),
            name: $this->val($scheme->name),
            in: $this->val($scheme->in),
            scheme: $this->val($scheme->scheme),
            bearerFormat: $this->val($scheme->bearerFormat),
            openIdConnectUrl: $this->val($scheme->openIdConnectUrl),
            flows: $flows,
            ref: $this->val($scheme->ref),
            x: $this->extensions($scheme),
        );
        $this->copyReflector($scheme, $result);

        return $result;
    }

    protected function convertFlow(Annotations\Flow $flow): Spec\Flow
    {
        return new Spec\Flow(
            flow: $this->val($flow->flow),
            authorizationUrl: $this->val($flow->authorizationUrl),
            tokenUrl: $this->val($flow->tokenUrl),
            refreshUrl: $this->val($flow->refreshUrl),
            scopes: Undefined::isDefault($flow->scopes) ? null : (array) $flow->scopes,
            x: $this->extensions($flow),
        );
    }

    protected function convertExample(Annotations\Examples $example): Spec\Example
    {
        $key = $this->val($example->example);

        $result = new Spec\Example(
            example: $key !== null ? (string) $key : null,
            summary: $this->val($example->summary),
            description: $this->val($example->description),
            value: Undefined::isDefault($example->value) ? null : $example->value,
            externalValue: $this->val($example->externalValue),
            ref: $this->val($example->ref),
            x: $this->extensions($example),
        );
        $this->copyReflector($example, $result);

        return $result;
    }

    /**
     * @return list<Spec\Security\Requirement>
     */
    protected function convertSecurityRequirements(array $security): array
    {
        $requirements = [];
        foreach ($security as $item) {
            if (is_array($item)) {
                $requirements[] = new Spec\Security\Requirement(schemes: $item);
            }
        }

        return $requirements;
    }

    protected function convertComponents(Annotations\Components $components, Specification $spec): void
    {
        if (!Undefined::isDefault($components->schemas)) {
            foreach ($components->schemas as $schema) {
                $spec->schemas[] = $this->convertSchema($schema);
            }
        }

        if (!Undefined::isDefault($components->responses)) {
            foreach ($components->responses as $response) {
                $spec->responses[] = $this->convertResponse($response);
            }
        }

        if (!Undefined::isDefault($components->parameters)) {
            foreach ($components->parameters as $parameter) {
                $spec->parameters[] = $this->convertParameter($parameter);
            }
        }

        if (!Undefined::isDefault($components->requestBodies)) {
            foreach ($components->requestBodies as $body) {
                $spec->requestBodies[] = $this->convertRequestBody($body);
            }
        }

        if (!Undefined::isDefault($components->headers)) {
            foreach ($components->headers as $header) {
                $spec->headers[] = $this->convertHeader($header);
            }
        }

        if (!Undefined::isDefault($components->securitySchemes)) {
            foreach ($components->securitySchemes as $scheme) {
                $spec->securitySchemes[] = $this->convertSecurityScheme($scheme);
            }
        }

        if (!Undefined::isDefault($components->links)) {
            foreach ($components->links as $link) {
                $spec->links[] = $this->convertLink($link);
            }
        }

        if (!Undefined::isDefault($components->examples)) {
            foreach ($components->examples as $example) {
                $spec->examples[] = $this->convertExample($example);
            }
        }
    }

    protected function convertCallbacks(array $callbacks): array
    {
        $result = [];
        foreach ($callbacks as $key => $value) {
            if ($value instanceof Annotations\PathItem) {
                $path = $this->val($value->path);
                $methods = ['get', 'put', 'post', 'delete', 'options', 'head', 'patch', 'trace'];
                foreach ($methods as $method) {
                    if (!Undefined::isDefault($value->{$method})) {
                        $result[$key][$path][$method] = $this->convertOperation($value->{$method}, $path, $method);
                    }
                }
            } else {
                $result[$key] = $this->convertCallbackValue($value);
            }
        }

        return $result;
    }

    protected function convertCallbackValue(mixed $value): mixed
    {
        if ($value instanceof Annotations\Operation) {
            return $this->convertOperation($value, null, $this->methodFromAnnotation($value));
        }
        if ($value instanceof Annotations\RequestBody) {
            return $this->convertRequestBody($value);
        }
        if ($value instanceof Annotations\Response) {
            return $this->convertResponse($value);
        }
        if ($value instanceof Annotations\Schema) {
            return $this->convertSchema($value);
        }
        if ($value instanceof Annotations\MediaType) {
            return $this->convertMediaType($value);
        }
        if (is_array($value)) {
            $result = [];
            foreach ($value as $k => $v) {
                $result[$k] = $this->convertCallbackValue($v);
            }

            return $result;
        }

        return $value;
    }

    protected function val(mixed $value): mixed
    {
        return Undefined::isDefault($value) ? null : $value;
    }

    protected function typeAsRef(mixed $type): ?string
    {
        if (is_string($type) && !Undefined::isDefault($type) && str_contains($type, '\\')) {
            return $type;
        }

        return null;
    }

    protected function filterType(mixed $type): string|array|null
    {
        if (Undefined::isDefault($type)) {
            return null;
        }

        if (is_string($type) && str_contains($type, '\\')) {
            return null;
        }

        return $type;
    }

    /**
     * @return array<string,mixed>|null
     */
    protected function extensions(Annotations\AbstractAnnotation $annotation): ?array
    {
        if (Undefined::isDefault($annotation->x)) {
            return null;
        }

        return is_array($annotation->x) ? $annotation->x : null;
    }

    protected function copyReflector(Annotations\AbstractAnnotation $from, Spec\AbstractAttribute $to): void
    {
        if ($from->_context->reflector !== null && $from->_context->reflector instanceof \Reflector) {
            $to->setReflector($from->_context->reflector);
        }
    }
}
