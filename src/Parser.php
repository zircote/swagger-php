<?php

/**
 * @license Apache 2.0
 */

namespace Swagger;

use Annotations\AbstractAnnotation;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\DocParser;
use Exception;

// Load all whitelisted annotations
AnnotationRegistry::registerLoader(function ($class) {
    foreach (Parser::$whitelist as $namespace) {
        if (strtolower(substr($class, 0, strlen($namespace))) === strtolower($namespace)) {
            $loaded = class_exists($class);
            if (!$loaded && $namespace === 'Swagger\\Annotations\\') {
                if (in_array(strtolower(substr($class, 20)), ['model', 'resource', 'api'])) { // Detected an 1.x annotation?
                    throw new Exception('The annotation @SWG\\'.substr($class, 20).'() is deprecated. Found in '.Parser::$context."\nFor more information read the migration guide: https://github.com/zircote/swagger-php/blob/2.x/docs/Migrating-to-v2.md");
                }
            }
            return $loaded;
        }
    }
    return false;
});

/**
 * Swagger\Parser extracts swagger-php annotations from php code.
 */
class Parser {

    /**
     * List of namespaces that should be detected by the doctrine annotation parser.
     * @var array
     */
    public static $whitelist = ['Swagger\\Annotations\\'];

    /**
     * Allows Annotation classes to know the context of the annotation that is being processed.
     * @var Context
     */
    public static $context;

    /**
     * @var DocParser
     */
    private $docParser;

    /**
     * @param string $filename
     */
    public function __construct($filename = null) {
        if ($filename !== null) {
            $this->parseFile($filename);
        }
    }

    /**
     * Extract and process all doc-comments from a file.
     *
     * @param string $filename Path to a php file.
     * @return AbstractAnnotation[]
     */
    public function parseFile($filename) {
        $tokens = token_get_all(file_get_contents($filename));
        return $this->parseTokens($tokens, new Context(['filename' => $filename]));
    }

    /**
     * Extract and process all doc-comments from the contents.
     *
     * @param string $contents PHP code.
     * @param Context $context The original location of the contents.
     * @return AbstractAnnotation[]
     */
    public function parseContents($contents, $context) {
        $tokens = token_get_all($contents);
        return $this->parseTokens($tokens, $context);
    }

    /**
     * Shared implementation for parseFile() & parseContents().
     *
     * @param array $tokens The result of a token_get_all()
     * @return AbstractAnnotation[]
     */
    protected function parseTokens($tokens, $parseContext) {
        $this->docParser = new DocParser();
        $this->docParser->setIgnoreNotImportedAnnotations(true);

        $annotations = [];
        reset($tokens);
        $token = '';
        $imports = ['swg' => 'Swagger\Annotations']; // Use @SWG\* for swagger annotations (unless overwrittemn by a use statement)

        $this->docParser->setImports($imports);
        $parseContext->uses = [];
        $classContext = $parseContext; // Use the parseContext until a classContext is created.
        $comment = false;
        $line = 0;
        $lineOffset = $parseContext->line ? : 0;
        while ($token !== false) {
            $previousToken = $token;
            $token = $this->nextToken($tokens, $parseContext);
            if (is_array($token) === false) { // Ignore tokens like "{", "}", etc
                continue;
            }
            if ($token[0] === T_DOC_COMMENT) {
                if ($comment) { // 2 Doc-comments in succession?
                    $this->parseContext(new Context(['comment' => $comment, 'line' => $line], $classContext), $annotations);
                }
                $comment = $token[1];
                $line = $token[2] + $lineOffset;
                continue;
            }
            if ($token[0] === T_ABSTRACT) {
                $token = $this->nextToken($tokens, $parseContext); // Skip "abstract" keyword
            }
            if ($token[0] === T_CLASS) { // Doc-comment before a class?
                if (is_array($previousToken) && $previousToken[0] === T_DOUBLE_COLON) {
                    //php 5.5 class name resolution (i.e. ClassName::class)
                    continue;
                }
                $token = $this->nextToken($tokens, $parseContext);
                $classContext = new Context(['class' => $token[1], 'line' => $token[2]], $parseContext);
                // @todo detect end-of-class and reset $classContext
                $extends = null;
                $token = $this->nextToken($tokens, $parseContext);
                if ($token[0] === T_EXTENDS) {
                    $classContext->extends = $this->parseNamespace($tokens, $token, $parseContext);
                }
                if ($comment) {
                    $classContext->comment = $comment;
                    $classContext->line = $line;
                    $this->parseContext($classContext, $annotations);
                    $comment = false;
                    continue;
                }
            }
            if ($comment) {
                if ($token[0] == T_STATIC) {
                    $token = $this->nextToken($tokens, $parseContext);
                    if ($token[0] === T_VARIABLE) { // static property
                        $this->parseContext(new Context([
                            'property' => substr($token[1], 1),
                            'static' => true,
                            'comment' => $comment,
                            'line' => $line
                                ], $classContext), $annotations);
                        $comment = false;
                        continue;
                    }
                }
                if (in_array($token[0], [T_PRIVATE, T_PROTECTED, T_PUBLIC, T_VAR])) { // Scope
                    $token = $this->nextToken($tokens, $parseContext);
                    if ($token[0] == T_STATIC) {
                        $token = $this->nextToken($tokens, $parseContext);
                    }
                    if ($token[0] === T_VARIABLE) { // instance property
                        $this->parseContext(new Context([
                            'property' => substr($token[1], 1),
                            'comment' => $comment,
                            'line' => $line
                                ], $classContext), $annotations);
                        $comment = false;
                    } elseif ($token[0] === T_FUNCTION) {
                        $token = $this->nextToken($tokens, $parseContext);
                        if ($token[0] === T_STRING) {
                            $this->parseContext(new Context([
                                'method' => $token[1],
                                'comment' => $comment,
                                'line' => $line
                                    ], $classContext), $annotations);
                            $comment = false;
                        }
                    }
                    continue;
                } elseif ($token[0] === T_FUNCTION) {
                    $token = $this->nextToken($tokens, $parseContext);
                    if ($token[0] === T_STRING) {
                        $this->parseContext(new Context([
                            'method' => $token[1],
                            'comment' => $comment,
                            'line' => $line
                                ], $classContext), $annotations);
                        $comment = false;
                    }
                }
                if (in_array($token[0], [T_NAMESPACE, T_USE]) === false) { // Skip "use" & "namespace" to prevent "never imported" warnings)
                    // Not a doc-comment for a class, property or method?
                    $this->parseContext(new Context(['comment' => $comment, 'line' => $line], $classContext), $annotations);
                    $comment = false;
                }
            }
            if ($token[0] === T_NAMESPACE) {
                $parseContext->namespace = $this->parseNamespace($tokens, $token, $parseContext);
                continue;
            }
            if ($token[0] === T_USE) {
                $statements = $this->parseUseStatement($tokens, $token, $parseContext);
                foreach ($statements as $alias => $target) {
                    if ($target[0] === '\\') {
                        $target = substr($target, 1);
                    }
                    
                    $parseContext->uses[$alias] = $target;
                    foreach (Parser::$whitelist as $namespace) {
                        if (strcasecmp(substr($target, 0, strlen($namespace)), $namespace) === 0) {
                            $imports[strtolower($alias)] = $target;
                            break;
                        }
                    }
                }
                $this->docParser->setImports($imports);
                continue;
            }
        }
        if ($comment) { // File ends with a T_DOC_COMMENT
            $this->parseContext(new Context(['comment' => $comment, 'line' => $line], $classContext), $annotations);
        }
        return $annotations;
    }

    /**
     * The next non-whitespace, non-comment token.
     *
     * @param array $tokens
     * @param Context $context
     * @return string|array The next token (or false)
     */
    private function nextToken(&$tokens, $context) {
        $token = next($tokens);
        if ($token[0] === T_WHITESPACE) {
            return $this->nextToken($tokens, $context);
        }
        if ($token[0] === T_COMMENT) {

            $pos = strpos($token[1], '@SWG\\');
            if ($pos) {
                $line = $context->line ? $context->line + $token[2] : $token[2];
                $commentContext = new Context(['line' => $line], $context);
                Logger::notice('Annotations are only parsed inside `/**` DocBlocks, skipping ' . $commentContext);
            }
            return $this->nextToken($tokens, $context);
        }
        return $token;
    }

    private function parseNamespace(&$tokens, &$token, $parseContext) {
        $namespace = '';
        while ($token !== false) {
            $token = $this->nextToken($tokens, $parseContext);
            if ($token[0] !== T_STRING && $token[0] !== T_NS_SEPARATOR) {
                break;
            }
            $namespace .= $token[1];
        }
        return $namespace;
    }

    private function parseUseStatement(&$tokens, &$token, $parseContext) {
        $class = '';
        $alias = '';
        $statements = [];
        $explicitAlias = false;
        while ($token !== false) {
            $token = $this->nextToken($tokens, $parseContext);
            $isNameToken = $token[0] === T_STRING || $token[0] === T_NS_SEPARATOR;
            if (!$explicitAlias && $isNameToken) {
                $class .= $token[1];
                $alias = $token[1];
            } else if ($explicitAlias && $isNameToken) {
                $alias .= $token[1];
            } else if ($token[0] === T_AS) {
                $explicitAlias = true;
                $alias = '';
            } else if ($token === ',') {
                $statements[$alias] = $class;
                $class = '';
                $alias = '';
                $explicitAlias = false;
            } else if ($token === ';') {
                $statements[$alias] = $class;
                break;
            } else {
                break;
            }
        }
        return $statements;
    }

    /**
     * Use doctrine to parse the comment block and add detected annotations to the $annotations array.
     *
     * @param Context $context
     * @param  AbstractAnnotation[] $annotations
     */
    protected function parseContext($context, &$annotations) {
        try {
            self::$context = $context;
            if ($context->is('annotations') === false) {
                $context->annotations = [];
            }
            $comment = preg_replace_callback('/^[\t ]*\*[\t ]+/m', function ($match) {
                // Replace leading tabs with spaces.
                // Workaround for http://www.doctrine-project.org/jira/browse/DCOM-255
                return str_replace("\t", ' ', $match[0]); 
            }, $context->comment);
            $result = $this->docParser->parse($comment, $context);
            self::$context = null;
        } catch (Exception $e) {
            self::$context = null;
            if (preg_match('/^(.+) at position ([0-9]+) in ' . preg_quote($context, '/') . '\.$/', $e->getMessage(), $matches)) {
                $errorMessage = $matches[1];
                $errorPos = $matches[2];
                $atPos = strpos($context->comment, '@');
                $context->line += substr_count($context->comment, "\n", 0, $atPos + $errorPos);
                $lines = explode("\n", substr($context->comment, $atPos, $errorPos));
                $context->character = strlen(array_pop($lines)) + 1; // position starts at 0 character starts at 1
                Logger::warning(new Exception($errorMessage . ' in ' . $context, $e->getCode(), $e));
            } else {
                Logger::warning($e);
            }
            return [];
        }
        foreach ($result as $annotation) {
            $annotations[] = $annotation;
        }
        return $annotations;
    }

}
