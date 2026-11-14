<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Type\Generics;

use OpenApi\Contracts\SourceInspectorInterface;
use OpenApi\Utils\SourceInspector;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;
use PHPStan\PhpDocParser\ParserConfig;
use Symfony\Component\TypeInfo\Type;
use Symfony\Component\TypeInfo\TypeContext\TypeContextFactory;
use Symfony\Component\TypeInfo\TypeResolver\StringTypeResolver;

/**
 * Reads the templates a class declares, and what each stands for in one of its members where the
 * class is used with type arguments, such as `T` as `Customer` in `Envelope<Customer>`.
 *
 * Templates are matched to arguments by position, as PHPStan and Psalm do. A template a method
 * declares itself hides the class's of that name. Only a member the class declares itself is
 * bound, since the arguments of a parent or a trait are not read yet.
 */
class TemplateReader
{
    protected SourceInspectorInterface $sourceInspector;

    protected TypeContextFactory $contexts;

    /** @var array<class-string, list<string>> the templates each class declares, by class */
    protected array $templateNames = [];

    /**
     * @param SourceInspectorInterface|null $sourceInspector Tells the members a class declares itself
     */
    public function __construct(?SourceInspectorInterface $sourceInspector = null)
    {
        $this->sourceInspector = $sourceInspector ?? new SourceInspector();
        $this->contexts = new TypeContextFactory(new StringTypeResolver());
    }

    /**
     * The templates a class declares, in declaration order.
     *
     * None for a class that does not exist.
     *
     * @return list<string>
     */
    public function templateNames(string $class): array
    {
        if (!class_exists($class) && !interface_exists($class)) {
            return [];
        }

        return $this->templateNames[$class] ??= array_keys($this->contexts->createFromClassName($class)->templates);
    }

    /**
     * The type each template stands for in a member of the class used with the type arguments,
     * by template name.
     *
     * None where there is not one argument for each template. Null where the member is not the
     * class's own, but inherited or taken from a trait.
     *
     * @return array<string, Type>|null
     */
    public function bindings(\Reflector $member, string $class, TypeArguments $typeArguments): ?array
    {
        $names = $this->templateNames($class);
        if ($names === [] || count($names) !== count($typeArguments->types)) {
            return [];
        }

        if (!$this->declares($class, $member)) {
            return null;
        }

        // a method's own templates hide the class's of the same name
        return array_diff_key(array_combine($names, $typeArguments->types), array_flip($this->ownTemplateNames($member)));
    }

    /**
     * The templates the method whose docblock types the member declares itself: a method's, a
     * parameter's method's, or a promoted property's constructor's.
     *
     * @return list<string>
     */
    protected function ownTemplateNames(\Reflector $member): array
    {
        $function = match (true) {
            $member instanceof \ReflectionFunctionAbstract => $member,
            $member instanceof \ReflectionParameter => $member->getDeclaringFunction(),
            $member instanceof \ReflectionProperty && $member->isPromoted() => $member->getDeclaringClass()->getConstructor(),
            default => null,
        };
        $docComment = $function?->getDocComment();
        if (!is_string($docComment)) {
            return [];
        }

        $config = new ParserConfig([]);
        $constExprParser = new ConstExprParser($config);
        $docNode = (new PhpDocParser($config, new TypeParser($config, $constExprParser), $constExprParser))
            ->parse(new TokenIterator((new Lexer($config))->tokenize($docComment)));

        $names = [];
        foreach (['@template', '@phpstan-template', '@psalm-template'] as $tagName) {
            foreach ($docNode->getTemplateTagValues($tagName) as $template) {
                $names[] = $template->name;
            }
        }

        return $names;
    }

    /**
     * Whether the member is written in the class's own body, not inherited or taken from a trait.
     */
    protected function declares(string $class, \Reflector $member): bool
    {
        $member = $member instanceof \ReflectionParameter ? $member->getDeclaringFunction() : $member;
        if (!$member instanceof \ReflectionMethod && !$member instanceof \ReflectionProperty && !$member instanceof \ReflectionClassConstant) {
            return false;
        }

        $declaringClass = $member->getDeclaringClass();

        return $declaringClass->getName() === $class && $this->sourceInspector->declares($declaringClass, $member);
    }
}
