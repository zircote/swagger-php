<?php
namespace Swagger;

/**
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 *             Copyright [2014] [Robert Allen]
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * @category   Swagger
 * @package    Swagger
 */
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\DocParser;
use Doctrine\Common\Annotations\TokenParser;

use Swagger\Annotations\AbstractAnnotation;
use Swagger\Annotations\Model;
use Swagger\Annotations\Resource;
use Swagger\Processors\ProcessorInterface;

/**
 * @category   Swagger
 * @package    Swagger
 */
class Parser
{
    /**
     * Allows Annotation classes to know the context of the annotation that is being processed.
     * @var Context
     */
    public static $context;

    /**
     * The metadata about the API
     * @var Annotations\Info
     */
    protected $info = null;

    /**
     * The authentication config
     * @var Annotations\Authorizations
     */
    protected $authorizations = null;

    /**
     * All detected resources
     * @var Resource[]
     */
    protected $resources = array();

    /**
     * All detected models
     * @var Model[]
     */
    protected $models = array();

    /**
     * All detected annotation partials;
     * @var AbstractAnnotation[]
     */
    protected $partials = array();

    /**
     * @var DocParser
     */
    private $docParser;

    /**
     * @var ProcessorInterface[]
     */
    private $processors;

    /**
     *
     * @param ProcessorInterface[] $processors
     * @param string $filename
     */
    public function __construct($processors, $filename = null)
    {
        $this->processors = $processors;

        AnnotationRegistry::registerAutoloadNamespace(__NAMESPACE__, dirname(__DIR__));
        if ($filename !== null) {
            $this->parseFile($filename);
        }
    }

    /**
     * Get all valid resources.
     * @return Resource[]
     */
    public function getResources()
    {
        $resources = array();
        foreach ($this->resources as $resource) {
            if ($resource->validate()) {
                $resources[] = $resource;
            }
        }
        $this->resources = $resources;
        return $resources;
    }

    /**
     * Get all valid models.
     * @return Model[]
     */
    public function getModels()
    {
        $models = array();
        foreach ($this->models as $model) {
            if ($model->validate()) {
                $models[] = $model;
            }
        }
        $this->models = $models;
        return $models;
    }

    /**
     * Get all annotation partials.
     * @return AbstractAnnotation[]
     */
    public function getPartials()
    {
        return $this->partials;
    }

    /**
     * @param string $key key
     *
     * @return boolean
     */
    public function hasPartial($key)
    {
        return isset($this->partials[$key]);
    }

    /**
     * @param string $key        key
     * @param object $annotation annotation
     */
    public function setPartial($key, $annotation)
    {
        $this->partials[$key] = $annotation;
    }

    /**
     * @return Annotations\Info
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @return Annotations\Authorizations
     */
    public function getAuthorizations()
    {
        return $this->authorizations;
    }

    /**
     * Extract and process all doc-comments from a file.
     * @param string $filename Path to a php file.
     */
    public function parseFile($filename)
    {
        $tokens = token_get_all(file_get_contents($filename));
        return $this->parseTokens($tokens, new Context(array('filename' => $filename)));
    }

    /**
     * Extract and process all doc-comments from the contents.
     * @param string $contents PHP code.
     * @param Context $context The original location of the contents.
     */
    public function parseContents($contents, $context)
    {
        $tokens = token_get_all($contents);
        return $this->parseTokens($tokens, $context);
    }

    /**
     * Shared implementation for parseFile() & parseContents().
     * @param array $tokens The result of a token_get_all()
     */
    protected function parseTokens($tokens, $parseContext)
    {
        $this->docParser = new DocParser();
        $this->docParser->setIgnoreNotImportedAnnotations(true);

        reset($tokens);
        $token = '';
        $namespace = '';
        $imports = array(
            'swg' => 'Swagger\Annotations' // Use @SWG\* for swagger annotations (unless overwrittemn by a use statement)
        );
        $this->docParser->setImports($imports);
        $uses = array();
        $classContext = $parseContext; // Use the parseContext until a classContext is created.
        $comment = false;
        $line = 0;
        while ($token !== false) {
            $previousToken = $token;
            $token = $this->nextToken($tokens, $parseContext);
            if (is_array($token) === false) { // Ignore tokens like "{", "}", etc
                continue;
            }
            if ($token[0] === T_DOC_COMMENT) {
                if ($comment) { // 2 Doc-comments in succession?
                    $this->parseContext(new Context(array('comment' => $comment, 'line' => $line), $classContext));
                }
                $comment = $token[1];
                $line = $token[2];
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
                $classContext = new Context(array(
                    'class' => $namespace ? $namespace . '\\' . $token[1] : $token[1],
                    'line' => $token[2],
                ), $parseContext);
                // @todo detect end-of-class and reset $class
                $extends = null;
                $token = $this->nextToken($tokens, $parseContext);
                if ($token[0] === T_EXTENDS) {
                    $classContext->extends = $this->prefixNamespace($namespace, $this->parseNamespace($tokens, $token, $parseContext), $uses);
                }
                if ($comment) {
                    $classContext->comment = $comment;
                    $classContext->line = $line;
                    $this->parseContext($classContext);
                    $comment = false;
                    continue;
                }
            }
            if ($comment) {
                if ($token[0] == T_STATIC) {
                    $token = $this->nextToken($tokens, $parseContext);
                    if ($token[0] === T_VARIABLE) { // static property
                        $this->parseContext(new Context(array(
                            'property' => substr($token[1], 1),
                            'static' => true,
                            'comment' => $comment,
                            'line' => $line
                        ), $classContext));
                        $comment = false;
                        continue;
                    }
                }
                if (in_array($token[0], array(T_PRIVATE, T_PROTECTED, T_PUBLIC, T_VAR))) { // Scope
                    $token = $this->nextToken($tokens, $parseContext);
                    if ($token[0] == T_STATIC) {
                        $token = $this->nextToken($tokens, $parseContext);
                    }
                    if ($token[0] === T_VARIABLE) { // instance property
                        $this->parseContext(new Context(array(
                            'property' => substr($token[1], 1),
                            'comment' => $comment,
                            'line' => $line
                        ), $classContext));
                        $comment = false;
                    } elseif ($token[0] === T_FUNCTION) {
                        $token = $this->nextToken($tokens, $parseContext);
                        if ($token[0] === T_STRING) {
                            $this->parseContext(new Context(array(
                                'method' => $token[1],
                                'comment' => $comment,
                                'line' => $line
                            ), $classContext));
                            $comment = false;
                        }
                    }
                    continue;
                } elseif ($token[0] === T_FUNCTION) {
                    $token = $this->nextToken($tokens, $parseContext);
                    if ($token[0] === T_STRING) {
                        $this->parseContext(new Context(array(
                            'method' => $token[1],
                            'comment' => $comment,
                            'line' => $line
                        ), $classContext));
                        $comment = false;
                    }
                }
                if (in_array($token[0], array(T_NAMESPACE, T_USE)) === false) { // Skip "use" & "namespace" to prevent "never imported" warnings)
                    // Not a doc-comment for a class, property or method?
                    $this->parseContext(new Context(array('comment' => $comment, 'line' => $line), $classContext));
                    $comment = false;
                }
            }
            if ($token[0] === T_NAMESPACE) {
                $namespace = $this->parseNamespace($tokens, $token, $parseContext);
                continue;
            }
            if ($token[0] === T_USE) {
                $statements = $this->parseUseStatement($tokens, $token, $parseContext);
                foreach ($statements as $alias => $target) {
                    if ($target[0] === '\\') {
                        $target = substr($target, 1);
                    }
                    $imports[$alias] = $target;
                }
                $this->docParser->setImports($imports);
                continue;
            }
        }
        if ($comment) { // File ends with a T_DOC_COMMENT
            $this->parseContext(new Context(array('comment' => $comment, 'line' => $line), $classContext));
        }
        $rootContext = $parseContext->getRootContext();
        if ($rootContext->info) {
            $this->info = $rootContext->info;
        }
        if ($rootContext->authorizations) {
            $this->authorizations = $rootContext->authorizations;
        }
    }
    
    /**
     * The next non-whitespace, non-comment token.
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
                $commentContext = new Context(array('line' => $token[2]), $context);
                Logger::notice('Annotations are only parsed inside `/**` DocBlocks, skipping '.$commentContext);
            }
            return $this->nextToken($tokens, $context);
        }
        return $token;
    }
    
    private function parseNamespace(&$tokens, &$token, $parseContext) {
        $namespace = '';
        while($token !== false) {
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
        $statements = array();
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
                $statements[strtolower($alias)] = $class;
                $class = '';
                $alias = '';
                $explicitAlias = false;
            } else if ($token === ';') {
                $statements[strtolower($alias)] = $class;
                break;
            } else {
                break;
            }
        }
        return $statements;
    }

    /**
     *
     * @param Context $context
     * @return AbstractAnnotation[]
     */
    protected function parseContext($context)
    {
        try {
            self::$context = $context;
            $annotations = $this->docParser->parse($context->comment, $context);
            self::$context = null;
        } catch (\Exception $e) {
            self::$context = null;
            if (preg_match('/^(.+) at position ([0-9]+) in '.preg_quote($context, '/').'\.$/', $e->getMessage(), $matches)) {
                $errorMessage = $matches[1];
                $errorPos = $matches[2];
                $atPos = strpos($context->comment, '@');
                $context->line += substr_count($context->comment, "\n", 0, $atPos + $errorPos);
                $lines = explode("\n", substr($context->comment, $atPos, $errorPos));
                $context->character = strlen(array_pop($lines)) + 1; // position starts at 0 character starts at 1
                Logger::warning(new \Exception($errorMessage.' in '.$context, $e->getCode(), $e));
            } else {
                Logger::warning($e);
            }
            return array();
        }

        foreach ($annotations as $annotation) {
            foreach ($this->processors as $processor) {
                $processor->process($annotation, $context);
            }
            if ($annotation instanceof AbstractAnnotation) {
                if ($annotation->hasPartialId()) {
                    if ($this->hasPartial($annotation->_partialId)) {
                        Logger::notice('partial="' . $annotation->_partialId . '" is not unique. another was found in ' . $annotation->_context);
                    }
                    $this->setPartial($annotation->_partialId, $annotation);
                } elseif ($annotation instanceof Resource) {
                    $this->resources[] = $annotation;
                } elseif ($annotation instanceof Model) {
                    $this->models[] = $annotation;
                }
            }
        }
        return $annotations;
    }

    /**
     * Resolve the full classname.
     *
     * @param string $namespace  Active namespace
     * @param string $class  The class name
     * @param array $uses  Active USE statements.
     * @return string
     */
    private function prefixNamespace($namespace, $class, $uses = array())
    {
        $pos = strpos($class, '\\');
        if ($pos !== false) {
            if ($pos === 0) {
                // Fully qualified name (\Foo\Bar)
                return substr($class, 1);
            }
            // Qualified name (Foo\Bar)
            foreach ($uses as $alias => $aliasedNamespace) {
                $alias .= '\\';
                if (strtolower(substr($class, 0, strlen($alias))) === $alias) {
                    // Aliased namespace (use \Long\Namespace as Foo)
                    return $aliasedNamespace . substr($class, strlen($alias) - 1);
                }
            }
        } else {
            // Unqualified name (Foo)
            $alias = strtolower($class);
            if (isset($uses[$alias])) { // Is an alias?
                return $uses[$alias];
            }
        }
        if ($namespace == '') {
            return $class;
        }
        return $namespace . '\\' . $class;
    }
}
