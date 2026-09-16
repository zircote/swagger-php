<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Builder\Result;
use OpenApi\Compiler\OpenApi30Compiler;
use OpenApi\Compiler\OpenApi31Compiler;
use OpenApi\Compiler\OpenApi32Compiler;
use OpenApi\Contracts\AttributeInterface;
use OpenApi\Contracts\CompilerInterface;
use OpenApi\Spec\AbstractAttribute;
use OpenApi\Spec\Operation;
use PHPUnit\Framework\TestCase;

final class DocsAccuracyTest extends TestCase
{
    private const DOCS = __DIR__ . '/../docs';

    public function testCliHelpOutputMatchesDocs(): void
    {
        $page = $this->readFile(self::DOCS . '/guide/generating-openapi-documents.md');

        $documented = rtrim($this->captureGroup('/^> \.\/vendor\/bin\/openapi -h\n\n(.+?)```$/ms', $page, 'Could not find the help block in the docs'));

        exec('php ' . escapeshellarg(__DIR__ . '/../bin/openapi') . ' -h 2>/dev/null', $lines, $ret);
        $this->assertSame(0, $ret);
        $actual = implode("\n", $lines);

        $this->assertSame($documented, $actual, 'openapi -h output has drifted from docs/guide/generating-openapi-documents.md');
    }

    public function testIsRootClassificationMatchesDocs(): void
    {
        $page = $this->readFile(self::DOCS . '/dev/pipeline.md');

        $always = $this->captureGroup('/\*\*Always root\*\*: (.+?)(?=\n- \*\*)/s', $page, 'Could not find "Always root" list');
        $cond = $this->captureGroup('/\*\*Conditionally root\*\*[^:]*:(.+?)(?=\n- \*\*)/s', $page, 'Could not find "Conditionally root" list');
        $never = $this->captureGroup('/\*\*Never root\*\*: (.+?)(?= —)/s', $page, 'Could not find "Never root" list');

        $parseNames = static function (string $text): array {
            preg_match_all('/`([A-Z][A-Za-z\\\\]+)`/', $text, $m);

            return $m[1];
        };

        $docAlways = $parseNames($always);
        $docConditional = $parseNames($cond);
        $docNever = $parseNames($never);

        $specClasses = $this->concreteAttributeClasses();

        $codeAlways = [];
        $codeConditional = [];
        $codeNever = [];

        foreach ($specClasses as $class) {
            $rc = new \ReflectionClass($class);
            $method = $rc->getMethod('isRoot');
            $declaringClass = $method->getDeclaringClass()->getName();

            if ($declaringClass !== $class) {
                continue;
            }

            if ($declaringClass === AbstractAttribute::class) {
                continue;
            }

            $source = $this->readFile((string) $method->getFileName());
            $lines = array_slice(
                explode("\n", $source),
                $method->getStartLine() - 1,
                $method->getEndLine() - $method->getStartLine() + 1
            );
            $body = implode("\n", $lines);

            if (preg_match('/return\s+true\s*;/', $body)) {
                $codeAlways[] = $this->shortSpecName($class);
            } elseif (preg_match('/return\s+false\s*;/', $body)) {
                $codeNever[] = $this->shortSpecName($class);
            } else {
                $codeConditional[] = $this->shortSpecName($class);
            }
        }

        sort($docAlways);
        sort($codeAlways);
        $this->assertSame($codeAlways, $docAlways, '"Always root" list in docs/dev/pipeline.md has drifted');

        sort($docConditional);
        sort($codeConditional);
        $this->assertSame($codeConditional, $docConditional, '"Conditionally root" list in docs/dev/pipeline.md has drifted');

        foreach ($docNever as $name) {
            $fqcn = 'OpenApi\\Spec\\' . str_replace('\\\\', '\\', $name);
            $this->assertTrue(class_exists($fqcn), "Documented never-root class {$name} does not exist");
            $rc = new \ReflectionClass($fqcn);
            $declaringClass = $rc->getMethod('isRoot')->getDeclaringClass()->getName();
            $this->assertSame(
                AbstractAttribute::class,
                $declaringClass,
                "{$name} overrides isRoot() but is listed as never-root in docs/dev/pipeline.md"
            );
        }
    }

    public function testCompilerTableMatchesDocs(): void
    {
        $page = $this->readFile(self::DOCS . '/reference/architecture.md');

        preg_match_all('/`(OpenApi\d+Compiler)`\s*\|\s*([\d.x]+)/', $page, $m);
        $this->assertNotEmpty($m[0], 'Could not find the compiler table in docs/reference/architecture.md');

        $documentedCompilers = array_combine($m[1], $m[2]);

        $compilerClasses = [
            OpenApi30Compiler::class,
            OpenApi31Compiler::class,
            OpenApi32Compiler::class,
        ];

        foreach ($compilerClasses as $class) {
            $short = (new \ReflectionClass($class))->getShortName();
            $this->assertArrayHasKey($short, $documentedCompilers, "Compiler {$short} missing from docs/reference/architecture.md table");
        }

        foreach ($documentedCompilers as $short => $versionPattern) {
            $fqcn = 'OpenApi\\Compiler\\' . $short;
            $this->assertTrue(class_exists($fqcn), "Documented compiler {$short} does not exist");
            $this->assertTrue(
                (new \ReflectionClass($fqcn))->implementsInterface(CompilerInterface::class),
                "{$short} does not implement CompilerInterface"
            );

            /** @var CompilerInterface $compiler implementsInterface() asserted above */
            $compiler = new $fqcn();
            $majorMinor = substr($versionPattern, 0, 3);
            $this->assertStringStartsWith($majorMinor, $compiler->getVersion(), "{$short}::getVersion() does not match documented version {$versionPattern}");
        }
    }

    public function testConcernsTableMatchesDocs(): void
    {
        $page = $this->readFile(self::DOCS . '/dev/testing.md');

        $section = $this->captureGroup('/^## Shared helpers$(.*?)^## /ms', $page, 'Could not find the "Shared helpers" section in docs/dev/testing.md');

        preg_match_all('/^\| `(\w+)` \|/m', $section, $m);
        $documented = $m[1];
        sort($documented);

        $traits = array_map(
            static fn (string $file): string => basename($file, '.php'),
            glob(__DIR__ . '/Concerns/*.php') ?: []
        );
        $traits = array_values(array_filter($traits, static fn (string $name): bool => !str_ends_with($name, 'Test')));
        sort($traits);

        $this->assertSame($traits, $documented, 'The tests/Concerns table in docs/dev/testing.md has drifted');
    }

    public function testResultMethodListingMatchesDocs(): void
    {
        $page = $this->readFile(self::DOCS . '/reference/builder.md');

        preg_match_all('/\$result->(\w+)\(/', $page, $m);
        $documentedMethods = array_unique($m[1]);
        sort($documentedMethods);

        $rc = new \ReflectionClass(Result::class);
        $publicMethods = array_filter(
            $rc->getMethods(\ReflectionMethod::IS_PUBLIC),
            fn (\ReflectionMethod $m): bool => !$m->isStatic() && !$m->isConstructor()
        );
        $codeMethods = array_map(fn (\ReflectionMethod $m): string => $m->getName(), $publicMethods);
        sort($codeMethods);

        $this->assertSame($codeMethods, $documentedMethods, 'Result method listing in docs/reference/builder.md has drifted');
    }

    /**
     * @see docs/guide/spec-attributes.md § "No requestBody on Get, Head, Options, Trace"
     */
    public function testNoRequestBodyOnReadOnlyOperations(): void
    {
        $readOnly = [
            Operation\Get::class,
            Operation\Head::class,
            Operation\Options::class,
            Operation\Trace::class,
        ];

        $parentHasIt = (new \ReflectionClass(Operation::class))
            ->getConstructor()
            ->getParameters();
        $parentParamNames = array_map(fn (\ReflectionParameter $p): string => $p->getName(), $parentHasIt);
        $this->assertContains('requestBody', $parentParamNames, 'Operation::__construct() should have $requestBody');

        foreach ($readOnly as $class) {
            $params = (new \ReflectionClass($class))
                ->getConstructor()
                ->getParameters();
            $paramNames = array_map(fn (\ReflectionParameter $p): string => $p->getName(), $params);

            $short = (new \ReflectionClass($class))->getShortName();
            $this->assertNotContains(
                'requestBody',
                $paramNames,
                "{$short}::__construct() should not have \$requestBody (docs/guide/spec-attributes.md)"
            );
        }
    }

    /**
     * @return list<class-string<AttributeInterface>>
     */
    private function concreteAttributeClasses(): array
    {
        $classes = [];
        $srcDir = (string) realpath(__DIR__ . '/../src');
        $dir = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($srcDir . '/Spec', \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($dir as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $relative = str_replace('/', '\\', substr((string) $file->getRealPath(), strlen($srcDir) + 1));
            $fqcn = 'OpenApi\\' . substr($relative, 0, -4);

            if (!class_exists($fqcn)) {
                continue;
            }

            $rc = new \ReflectionClass($fqcn);
            if ($rc->isAbstract() || $rc->isInterface() || $rc->isEnum()) {
                continue;
            }
            if (!$rc->implementsInterface(AttributeInterface::class)) {
                continue;
            }

            /** @var class-string<AttributeInterface> $attributeClass implementsInterface() above is the guarantee */
            $attributeClass = $fqcn;
            $classes[] = $attributeClass;
        }

        sort($classes);

        return $classes;
    }

    /**
     * Capture group 1 of a pattern the test requires to match, failing with a useful message
     * rather than indexing into an empty match array. PHPUnit's assertions do not narrow
     * types for static analysis; fail() returns never, so this does.
     */
    private function captureGroup(string $pattern, string $subject, string $message): string
    {
        if (1 !== preg_match($pattern, $subject, $m) || !isset($m[1])) {
            $this->fail($message);
        }

        return $m[1];
    }

    /**
     * Read a file that the test requires to exist, failing with its path rather than letting
     * a `false` flow on into preg_match()/explode().
     */
    private function readFile(string $path): string
    {
        $content = file_get_contents($path);
        $this->assertIsString($content, "Could not read {$path}");

        return $content;
    }

    private function shortSpecName(string $fqcn): string
    {
        return str_replace('OpenApi\\Spec\\', '', $fqcn);
    }
}
