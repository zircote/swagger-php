<?php
namespace Swagger;

/**
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 *             Copyright [2013] [Robert Allen]
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
use Swagger\Contexts\ClassContext;
use Swagger\Contexts\Context;
use Swagger\Contexts\MethodContext;
use Swagger\Contexts\PropertyContext;
use Swagger\Processors\ProcessorInterface;

/**
 * @category   Swagger
 * @package    Swagger
 */
class Parser
{

    /**
     * All detected resources
     * @var Resource[]
     */
    protected $resources = array();

    /**
     * Current resource
     * @var Resource
     */
    protected $currentResource = false;

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
     * Current model
     * @var Model
     */
    protected $currentModel = false;

    /**
     * @var DocParser
     */
    private $docParser;

    /**
     * @var string
     */
    private $filename;

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
        AbstractAnnotation::$context = $this->filename;
        $resources = array();
        foreach ($this->resources as $resource) {
            if ($resource->validate()) {
                $resources[] = $resource;
            }
        }
        $this->resources = $resources;
        AbstractAnnotation::$context = 'unknown';
        return $resources;
    }

    /**
     * @param Resource $resource resource
     */
    public function appendResource(Resource $resource)
    {
        $this->resources[] = $resource;
        $this->currentResource = $resource;
    }

    /**
     * @return Annotation\Resource
     */
    public function getCurrentResource()
    {
        return $this->currentResource;
    }

    /**
     * Get all valid models.
     * @return Model[]
     */
    public function getModels()
    {
        AbstractAnnotation::$context = $this->filename;
        $models = array();
        foreach ($this->models as $model) {
            if ($model->validate()) {
                $models[] = $model;
            }
        }
        $this->models = $models;
        AbstractAnnotation::$context = 'unknown';
        return $models;
    }

    /**
     * @param Model $model model
     */
    public function appendModel(Model $model)
    {
        $this->models[] = $model;
        $this->currentModel = $model;
    }

    /**
     * @return Annotation\Model
     */
    public function getCurrentModel()
    {
        return $this->currentModel;
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
     * Extract and process all doc-comments from a file.
     * @param string $filename Path to a php file.
     */
    public function parseFile($filename)
    {
        $this->filename = $filename;
        $tokenParser = new TokenParser(file_get_contents($this->filename));
        return $this->parseTokens($tokenParser);
    }

    /**
     * Extract and process all doc-comments from the contents.
     * @param string $contents PHP code.
     * @param string $context The original location of the contents.
     */
    public function parseContents($contents, $context = 'unknown')
    {
        $this->filename = $context;
        $tokenParser = new TokenParser($contents);
        return $this->parseTokens($tokenParser);
    }

    /**
     * Shared implementation for parseFile() & parseContents().
     * @param TokenParser $tokenParser
     */
    protected function parseTokens(TokenParser $tokenParser)
    {
        $this->docParser = new DocParser();
        $this->docParser->setIgnoreNotImportedAnnotations(true);

        $token = $tokenParser->next(false);
        $namespace = '';
        $class = false;

        $imports = array(
            'swg' => 'Swagger\Annotations' // Use @SWG\* for swagger annotations (unless overwrittemn by a use statement)
        );
        $this->docParser->setImports($imports);
        $uses = array();
        $docComment = false;
        while ($token != null) {
            $previousToken = $token;
            $token = $tokenParser->next(false);
            if (is_array($token) === false) { // Ignore tokens like "{", "}", etc
                continue;
            }
            if ($token[0] === T_DOC_COMMENT) {
                $location = $this->filename . ' on line ' . $token[2];
                AbstractAnnotation::$context = $location;
                if ($docComment) { // 2 Doc-comments in succession?
                    $this->parseContext(new Context($docComment));
                }
                $docComment = $token[1];
                continue;
            }
            if ($token[0] === T_ABSTRACT) {
                $token = $tokenParser->next(false); // Skip "abstract" keyword
            }
            if ($token[0] === T_CLASS) { // Doc-comment before a class?
                if (is_array($previousToken) && $previousToken[0] === T_DOUBLE_COLON) {
                    //php 5.5 class name resolution (i.e. ClassName::class)
                    continue;
                }
                $token = $tokenParser->next();
                $class = $namespace ? $namespace . '\\' . $token[1] : $token[1];
                $this->currentModel = false;
                // @todo detect end-of-class and reset $class
                if ($docComment) {
                    $extends = null;
                    $token = $tokenParser->next(false);
                    if ($token[0] === T_EXTENDS) {
                        $extends = $this->prefixNamespace($namespace, $tokenParser->parseClass(), $uses);
                    }
                    AbstractAnnotation::$context = $class . ' in ' . $location;
                    $this->parseContext(new ClassContext($class, $extends, $docComment));
                    $docComment = false;
                    continue;
                }
            }
            if ($docComment) {
                if ($token[0] == T_STATIC) {
                    $token = $tokenParser->next(false);
                    if ($token[0] === T_VARIABLE) { // static property
                        AbstractAnnotation::$context = $class . '::' . $token[1] . ' in ' . $location;

                        $this->parseContext(new PropertyContext(substr($token[1], 1), $docComment));
                        $docComment = false;
                        continue;
                    }
                }
                if (in_array($token[0], array(T_PRIVATE, T_PROTECTED, T_PUBLIC, T_VAR))) { // Scope
                    $token = $tokenParser->next(false);
                    if ($token[0] == T_STATIC) {
                        $token = $tokenParser->next(false);
                    }
                    if ($token[0] === T_VARIABLE) { // instance property
                        AbstractAnnotation::$context = $class . '->' . substr($token[1], 1) . ' in ' . $location;
                        $this->parseContext(new PropertyContext(substr($token[1], 1), $docComment));
                        $docComment = false;
                    } elseif ($token[0] === T_FUNCTION) {
                        $token = $tokenParser->next(false);
                        if ($token[0] === T_STRING) {
                            AbstractAnnotation::$context = $class . '->' . $token[1] . '(...)' . ' in ' . $location;
                            $this->parseContext(new MethodContext($token[1], $docComment));
                            $docComment = false;
                        }
                    }
                    continue;
                } elseif ($token[0] === T_FUNCTION) {
                    $token = $tokenParser->next(false);
                    if ($token[0] === T_STRING) {
                        AbstractAnnotation::$context = $class . '->' . $token[1] . '(...)' . ' in ' . $location;
                        $this->parseContext(new MethodContext($token[1], $docComment));
                        $docComment = false;
                    }
                }
                if (in_array($token[0], array(T_NAMESPACE, T_USE)) === false) { // Skip "use" & "namespace" to prevent "never imported" warnings)
                    // Not a doc-comment for a class, property or method?
                    $this->parseContext(new Context($docComment));
                    $docComment = false;
                }
            }
            if ($token[0] === T_NAMESPACE) {
                $namespace = $tokenParser->parseNamespace();
                continue;
            }
            if ($token[0] === T_USE) {
                $nsLength = strlen(__NAMESPACE__);
                foreach ($tokenParser->parseUseStatement() as $alias => $target) {
                    if ($target[0] === '\\') {
                        $target = substr($target, 1);
                    }

                    $imports[$alias] = $target;
                }
                $this->docParser->setImports($imports);
                continue;
            }
        }
        if ($docComment) { // File ends with a T_DOC_COMMENT
            $this->parseContext(new Context($docComment));
        }
        AbstractAnnotation::$context = 'unknown';
    }

    /**
     *
     * @param Context $context Content containing the docComment
     * @return AbstractAnnotation[]
     */
    protected function parseContext($context)
    {
        try {
            $annotations = $this->docParser->parse($context->getDocComment(), AbstractAnnotation::$context);
        } catch (\Exception $e) {
            Logger::warning($e);
            return array();
        }

        foreach ($annotations as $annotation) {
            foreach ($this->processors as $processor) {
                if ($processor->supports($annotation, $context)) {
                    $processor->process($this, $annotation, $context);
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
