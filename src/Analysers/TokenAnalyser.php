<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Analysers;

use OpenApi\Analysis;
use OpenApi\Context;
use OpenApi\Generator;

/**
 * Extracts swagger-php annotations from php code using static analysis.
 */
class TokenAnalyser implements AnalyserInterface
{
    /** @var Generator|null */
    protected $generator;

    public function setGenerator(Generator $generator): void
    {
        $this->generator = $generator;
    }

    /**
     * Extract and process all doc-comments from a file.
     *
     * @param string $filename path to a php file
     */
    public function fromFile(string $filename, Context $context): Analysis
    {
        if (function_exists('opcache_get_status') && function_exists('opcache_get_configuration')) {
            if (empty($GLOBALS['openapi_opcache_warning'])) {
                $GLOBALS['openapi_opcache_warning'] = true;
                $status = opcache_get_status();
                $config = opcache_get_configuration();
                if (is_array($status) && $status['opcache_enabled'] && $config['directives']['opcache.save_comments'] == false) {
                    $context->logger->error("php.ini \"opcache.save_comments = 0\" interferes with extracting annotations.\n[LINK] https://www.php.net/manual/en/opcache.configuration.php#ini.opcache.save-comments");
                }
            }
        }
        $tokens = token_get_all(file_get_contents($filename));

        return $this->fromTokens($tokens, new Context(['filename' => $filename], $context));
    }

    /**
     * Extract and process all doc-comments from the contents.
     *
     * @param string  $code    PHP code. (including <?php tags)
     * @param Context $context the original location of the contents
     */
    public function fromCode(string $code, Context $context): Analysis
    {
        $tokens = token_get_all($code);

        return $this->fromTokens($tokens, $context);
    }

    /**
     * Shared implementation for parseFile() & parseContents().
     *
     * @param array $tokens The result of a token_get_all()
     */
    protected function fromTokens(array $tokens, Context $parseContext): Analysis
    {
        $generator = $this->generator ?: new Generator();
        $analysis = new Analysis([], $parseContext);
        $docBlockParser = new DocBlockParser($generator->getAliases());

        reset($tokens);
        $token = '';

        $aliases = $generator->getAliases();

        $parseContext->uses = [];
        // default to parse context to start with
        $schemaContext = $parseContext;

        $classDefinition = false;
        $interfaceDefinition = false;
        $traitDefinition = false;
        $enumDefinition = false;
        $comment = false;

        $line = 0;
        $lineOffset = $parseContext->line ?: 0;

        while ($token !== false) {
            $previousToken = $token;
            $token = $this->nextToken($tokens, $parseContext);

            if (is_array($token) === false) {
                // Ignore tokens like "{", "}", etc
                continue;
            }

            if (defined('T_ATTRIBUTE') && $token[0] === T_ATTRIBUTE) {
                // consume
                $this->parseAttribute($tokens, $token, $parseContext);
                continue;
            }

            if ($token[0] === T_DOC_COMMENT) {
                if ($comment) {
                    // 2 Doc-comments in succession?
                    $this->analyseComment($analysis, $docBlockParser, $comment, new Context(['line' => $line], $schemaContext));
                }
                $comment = $token[1];
                $line = $token[2] + $lineOffset;
                continue;
            }

            if (in_array($token[0], [T_ABSTRACT, T_FINAL])) {
                // skip
                $token = $this->nextToken($tokens, $parseContext);
            }

            if ($token[0] === T_CLASS) {
                // Doc-comment before a class?
                if (is_array($previousToken) && $previousToken[0] === T_DOUBLE_COLON) {
                    // php 5.5 class name resolution (i.e. ClassName::class)
                    continue;
                }

                $token = $this->nextToken($tokens, $parseContext);

                if (is_string($token) && ($token === '(' || $token === '{')) {
                    // php7 anonymous classes (i.e. new class() { public function foo() {} };)
                    continue;
                }

                if (is_array($token) && ($token[1] === 'extends' || $token[1] === 'implements')) {
                    // php7 anonymous classes with extends (i.e. new class() extends { public function foo() {} };)
                    continue;
                }

                if (!is_array($token)) {
                    // PHP 8 named argument
                    continue;
                }

                $interfaceDefinition = false;
                $traitDefinition = false;
                $enumDefinition = false;

                $schemaContext = new Context(['class' => $token[1], 'line' => $token[2]], $parseContext);
                if ($classDefinition) {
                    $analysis->addClassDefinition($classDefinition);
                }
                $classDefinition = [
                    'class' => $token[1],
                    'extends' => null,
                    'properties' => [],
                    'methods' => [],
                    'context' => $schemaContext,
                ];

                $token = $this->nextToken($tokens, $parseContext);

                if ($token[0] === T_EXTENDS) {
                    $schemaContext->extends = $this->parseNamespace($tokens, $token, $parseContext);
                    $classDefinition['extends'] = $schemaContext->fullyQualifiedName($schemaContext->extends);
                }

                if ($token[0] === T_IMPLEMENTS) {
                    $schemaContext->implements = $this->parseNamespaceList($tokens, $token, $parseContext);
                    $classDefinition['implements'] = array_map([$schemaContext, 'fullyQualifiedName'], $schemaContext->implements);
                }

                if ($comment) {
                    $schemaContext->line = $line;
                    $this->analyseComment($analysis, $docBlockParser, $comment, $schemaContext);
                    $comment = false;
                    continue;
                }

                // @todo detect end-of-class and reset $schemaContext
            }

            if ($token[0] === T_INTERFACE) { // Doc-comment before an interface?
                $classDefinition = false;
                $traitDefinition = false;
                $enumDefinition = false;

                $token = $this->nextToken($tokens, $parseContext);

                if (!is_array($token)) {
                    // PHP 8 named argument
                    continue;
                }

                $schemaContext = new Context(['interface' => $token[1], 'line' => $token[2]], $parseContext);
                if ($interfaceDefinition) {
                    $analysis->addInterfaceDefinition($interfaceDefinition);
                }
                $interfaceDefinition = [
                    'interface' => $token[1],
                    'extends' => null,
                    'properties' => [],
                    'methods' => [],
                    'context' => $schemaContext,
                ];

                $token = $this->nextToken($tokens, $parseContext);

                if ($token[0] === T_EXTENDS) {
                    $schemaContext->extends = $this->parseNamespaceList($tokens, $token, $parseContext);
                    $interfaceDefinition['extends'] = array_map([$schemaContext, 'fullyQualifiedName'], $schemaContext->extends);
                }

                if ($comment) {
                    $schemaContext->line = $line;
                    $this->analyseComment($analysis, $docBlockParser, $comment, $schemaContext);
                    $comment = false;
                    continue;
                }

                // @todo detect end-of-interface and reset $schemaContext
            }

            if ($token[0] === T_TRAIT) {
                $classDefinition = false;
                $interfaceDefinition = false;
                $enumDefinition = false;

                $token = $this->nextToken($tokens, $parseContext);

                if (!is_array($token)) {
                    // PHP 8 named argument
                    continue;
                }

                $schemaContext = new Context(['trait' => $token[1], 'line' => $token[2]], $parseContext);
                if ($traitDefinition) {
                    $analysis->addTraitDefinition($traitDefinition);
                }
                $traitDefinition = [
                    'trait' => $token[1],
                    'properties' => [],
                    'methods' => [],
                    'context' => $schemaContext,
                ];

                if ($comment) {
                    $schemaContext->line = $line;
                    $this->analyseComment($analysis, $docBlockParser, $comment, $schemaContext);
                    $comment = false;
                    continue;
                }

                // @todo detect end-of-trait and reset $schemaContext
            }

            if (defined('T_ENUM') && $token[0] === T_ENUM) {
                $classDefinition = false;
                $interfaceDefinition = false;
                $traitDefinition = false;

                $token = $this->nextToken($tokens, $parseContext);

                if (!is_array($token)) {
                    // PHP 8 named argument
                    continue;
                }

                $schemaContext = new Context(['enum' => $token[1], 'line' => $token[2]], $parseContext);
                if ($enumDefinition) {
                    $analysis->addEnumDefinition($enumDefinition);
                }
                $enumDefinition = [
                    'enum' => $token[1],
                    'properties' => [],
                    'methods' => [],
                    'context' => $schemaContext,
                ];

                if ($comment) {
                    $schemaContext->line = $line;
                    $this->analyseComment($analysis, $docBlockParser, $comment, $schemaContext);
                    $comment = false;
                    continue;
                }

                // @todo detect end-of-trait and reset $schemaContext
            }

            if ($token[0] === T_STATIC) {
                $token = $this->nextToken($tokens, $parseContext);
                if ($token[0] === T_VARIABLE) {
                    // static property
                    $propertyContext = new Context(
                        [
                            'property' => substr($token[1], 1),
                            'static' => true,
                            'line' => $line,
                        ],
                        $schemaContext
                    );

                    if ($classDefinition) {
                        $classDefinition['properties'][$propertyContext->property] = $propertyContext;
                    }
                    if ($traitDefinition) {
                        $traitDefinition['properties'][$propertyContext->property] = $propertyContext;
                    }
                    if ($comment) {
                        $this->analyseComment($analysis, $docBlockParser, $comment, $propertyContext);
                        $comment = false;
                    }
                    continue;
                }
            }

            if (in_array($token[0], [T_PRIVATE, T_PROTECTED, T_PUBLIC, T_VAR])) { // Scope
                [$type, $nullable, $token] = $this->parseTypeAndNextToken($tokens, $parseContext);
                if ($token[0] === T_VARIABLE) {
                    // instance property
                    $propertyContext = new Context(
                        [
                            'property' => substr($token[1], 1),
                            'type' => $type,
                            'nullable' => $nullable,
                            'line' => $line,
                        ],
                        $schemaContext
                    );

                    if ($classDefinition) {
                        $classDefinition['properties'][$propertyContext->property] = $propertyContext;
                    }
                    if ($interfaceDefinition) {
                        $interfaceDefinition['properties'][$propertyContext->property] = $propertyContext;
                    }
                    if ($traitDefinition) {
                        $traitDefinition['properties'][$propertyContext->property] = $propertyContext;
                    }
                    if ($comment) {
                        $this->analyseComment($analysis, $docBlockParser, $comment, $propertyContext);
                        $comment = false;
                    }
                } elseif ($token[0] === T_FUNCTION) {
                    $token = $this->nextToken($tokens, $parseContext);
                    if ($token[0] === T_STRING) {
                        $methodContext = new Context(
                            [
                                'method' => $token[1],
                                'line' => $line,
                            ],
                            $schemaContext
                        );

                        if ($classDefinition) {
                            $classDefinition['methods'][$token[1]] = $methodContext;
                        }
                        if ($interfaceDefinition) {
                            $interfaceDefinition['methods'][$token[1]] = $methodContext;
                        }
                        if ($traitDefinition) {
                            $traitDefinition['methods'][$token[1]] = $methodContext;
                        }
                        if ($comment) {
                            $this->analyseComment($analysis, $docBlockParser, $comment, $methodContext);
                            $comment = false;
                        }
                    }
                }
                continue;
            } elseif ($token[0] === T_FUNCTION) {
                $token = $this->nextToken($tokens, $parseContext);
                if ($token[0] === T_STRING) {
                    $methodContext = new Context(
                        [
                            'method' => $token[1],
                            'line' => $line,
                        ],
                        $schemaContext
                    );

                    if ($classDefinition) {
                        $classDefinition['methods'][$token[1]] = $methodContext;
                    }
                    if ($interfaceDefinition) {
                        $interfaceDefinition['methods'][$token[1]] = $methodContext;
                    }
                    if ($traitDefinition) {
                        $traitDefinition['methods'][$token[1]] = $methodContext;
                    }
                    if ($comment) {
                        $this->analyseComment($analysis, $docBlockParser, $comment, $methodContext);
                        $comment = false;
                    }
                }
            }

            if (in_array($token[0], [T_NAMESPACE, T_USE]) === false) {
                // Skip "use" & "namespace" to prevent "never imported" warnings)
                if ($comment) {
                    // Not a doc-comment for a class, property or method?
                    $this->analyseComment($analysis, $docBlockParser, $comment, new Context(['line' => $line], $schemaContext));
                    $comment = false;
                }
            }

            if ($token[0] === T_NAMESPACE) {
                $parseContext->namespace = $this->parseNamespace($tokens, $token, $parseContext);
                $aliases['__NAMESPACE__'] = $parseContext->namespace;
                $docBlockParser->setAliases($aliases);
                continue;
            }

            if ($token[0] === T_USE) {
                $statements = $this->parseUseStatement($tokens, $token, $parseContext);
                foreach ($statements as $alias => $target) {
                    if ($classDefinition) {
                        // class traits
                        $classDefinition['traits'][] = $schemaContext->fullyQualifiedName($target);
                    } elseif ($traitDefinition) {
                        // trait traits
                        $traitDefinition['traits'][] = $schemaContext->fullyQualifiedName($target);
                    } else {
                        // not a trait use
                        $parseContext->uses[$alias] = $target;

                        $namespaces = $generator->getNamespaces();
                        if (null === $namespaces) {
                            $aliases[strtolower($alias)] = $target;
                        } else {
                            foreach ($namespaces as $namespace) {
                                if (strcasecmp(substr($target . '\\', 0, strlen($namespace)), $namespace) === 0) {
                                    $aliases[strtolower($alias)] = $target;
                                    break;
                                }
                            }
                        }
                        $docBlockParser->setAliases($aliases);
                    }
                }
            }
        }

        // cleanup final comment and definition
        if ($comment) {
            $this->analyseComment($analysis, $docBlockParser, $comment, new Context(['line' => $line], $schemaContext));
        }
        if ($classDefinition) {
            $analysis->addClassDefinition($classDefinition);
        }
        if ($interfaceDefinition) {
            $analysis->addInterfaceDefinition($interfaceDefinition);
        }
        if ($traitDefinition) {
            $analysis->addTraitDefinition($traitDefinition);
        }
        if ($enumDefinition) {
            $analysis->addEnumDefinition($enumDefinition);
        }

        return $analysis;
    }

    /**
     * Parse comment and add annotations to analysis.
     */
    private function analyseComment(Analysis $analysis, DocBlockParser $docBlockParser, string $comment, Context $context): void
    {
        $analysis->addAnnotations($docBlockParser->fromComment($comment, $context), $context);
    }

    /**
     * The next non-whitespace, non-comment token.
     *
     *
     * @return array|string The next token (or false)
     */
    private function nextToken(array &$tokens, Context $context)
    {
        while (true) {
            $token = next($tokens);
            if (is_array($token)) {
                if ($token[0] === T_WHITESPACE) {
                    continue;
                }
                if ($token[0] === T_COMMENT) {
                    $pos = strpos($token[1], '@OA\\');
                    if ($pos) {
                        $line = $context->line ? $context->line + $token[2] : $token[2];
                        $commentContext = new Context(['line' => $line], $context);
                        $context->logger->warning('Annotations are only parsed inside `/**` DocBlocks, skipping ' . $commentContext);
                    }
                    continue;
                }
            }

            return $token;
        }
    }

    private function parseAttribute(array &$tokens, &$token, Context $parseContext): void
    {
        $nesting = 1;
        while ($token !== false) {
            $token = $this->nextToken($tokens, $parseContext);
            if (!is_array($token) && '[' === $token) {
                ++$nesting;
                continue;
            }

            if (!is_array($token) && ']' === $token) {
                --$nesting;
                if (!$nesting) {
                    break;
                }
            }
        }
    }

    /**
     * @return int[]
     */
    private function php8NamespaceToken(): array
    {
        return defined('T_NAME_QUALIFIED') ? [T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED] : [];
    }

    /**
     * Parse namespaced string.
     *
     * @param array|string $token
     */
    private function parseNamespace(array &$tokens, &$token, Context $parseContext): string
    {
        $namespace = '';
        $nsToken = array_merge([T_STRING, T_NS_SEPARATOR], $this->php8NamespaceToken());
        while ($token !== false) {
            $token = $this->nextToken($tokens, $parseContext);
            if (!in_array($token[0], $nsToken)) {
                break;
            }
            $namespace .= $token[1];
        }

        return $namespace;
    }

    /**
     * Parse comma separated list of namespaced strings.
     *
     * @param array|string $token
     */
    private function parseNamespaceList(array &$tokens, &$token, Context $parseContext): array
    {
        $namespaces = [];
        while ($namespace = $this->parseNamespace($tokens, $token, $parseContext)) {
            $namespaces[] = $namespace;
            if ($token != ',') {
                break;
            }
        }

        return $namespaces;
    }

    /**
     * Parse a use statement.
     */
    private function parseUseStatement(array &$tokens, &$token, Context $parseContext): array
    {
        $normalizeAlias = function ($alias): string {
            $alias = ltrim($alias, '\\');
            $elements = explode('\\', $alias);

            return array_pop($elements);
        };

        $class = '';
        $alias = '';
        $statements = [];
        $explicitAlias = false;
        $nsToken = array_merge([T_STRING, T_NS_SEPARATOR], $this->php8NamespaceToken());
        while ($token !== false) {
            $token = $this->nextToken($tokens, $parseContext);
            $isNameToken = in_array($token[0], $nsToken);
            if (!$explicitAlias && $isNameToken) {
                $class .= $token[1];
                $alias = $token[1];
            } elseif ($explicitAlias && $isNameToken) {
                $alias .= $token[1];
            } elseif ($token[0] === T_AS) {
                $explicitAlias = true;
                $alias = '';
            } elseif ($token === ',') {
                $statements[$normalizeAlias($alias)] = $class;
                $class = '';
                $alias = '';
                $explicitAlias = false;
            } elseif ($token === ';') {
                $statements[$normalizeAlias($alias)] = $class;
                break;
            } else {
                break;
            }
        }

        return $statements;
    }

    /**
     * Parse type of variable (if it exists).
     */
    private function parseTypeAndNextToken(array &$tokens, Context $parseContext): array
    {
        $type = Generator::UNDEFINED;
        $nullable = false;
        $token = $this->nextToken($tokens, $parseContext);

        if ($token[0] === T_STATIC) {
            $token = $this->nextToken($tokens, $parseContext);
        }

        if ($token === '?') { // nullable type
            $nullable = true;
            $token = $this->nextToken($tokens, $parseContext);
        }

        $qualifiedToken = array_merge([T_NS_SEPARATOR, T_STRING, T_ARRAY], $this->php8NamespaceToken());
        $typeToken = array_merge([T_STRING], $this->php8NamespaceToken());
        // drill down namespace segments to basename property type declaration
        while (in_array($token[0], $qualifiedToken)) {
            if (in_array($token[0], $typeToken)) {
                $type = $token[1];
            }
            $token = $this->nextToken($tokens, $parseContext);
        }

        return [$type, $nullable, $token];
    }
}
