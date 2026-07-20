<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tools\Tests\CSFixer;

use OpenApi\Tools\CSFixer\SpecNamespaceAliasFixer;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SpecNamespaceAliasFixerTest extends TestCase
{
    protected SpecNamespaceAliasFixer $fixer;

    protected function setUp(): void
    {
        $this->fixer = new SpecNamespaceAliasFixer();
    }

    #[DataProvider('provideFixCases')]
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield 'already correct - no change' => [
            '<?php
namespace App;

use OpenApi\Spec as OA;

class Foo
{
    public function bar(): OA\Schema
    {
    }
}
',
        ];

        yield 'bare namespace import without alias' => [
            '<?php
namespace App;

use OpenApi\Spec as OA;

class Foo
{
    public function bar(): OA\Schema
    {
    }
}
',
            '<?php
namespace App;

use OpenApi\Spec;

class Foo
{
    public function bar(): Spec\Schema
    {
    }
}
',
        ];

        yield 'individual class import' => [
            '<?php
namespace App;

use OpenApi\Spec as OA;

class Foo
{
    public function bar(): OA\Schema
    {
    }
}
',
            '<?php
namespace App;

use OpenApi\Spec\Schema;

class Foo
{
    public function bar(): Schema
    {
    }
}
',
        ];

        yield 'multiple individual imports' => [
            '<?php
namespace App;

use OpenApi\Spec as OA;

class Foo
{
    public function bar(OA\Parameter $param): OA\Response
    {
    }
}
',
            '<?php
namespace App;

use OpenApi\Spec\Parameter;
use OpenApi\Spec\Response;

class Foo
{
    public function bar(Parameter $param): Response
    {
    }
}
',
        ];

        yield 'nested class import' => [
            '<?php
namespace App;

use OpenApi\Spec as OA;

class Foo
{
    public function bar(): OA\Operation\Get
    {
    }
}
',
            '<?php
namespace App;

use OpenApi\Spec\Operation\Get;

class Foo
{
    public function bar(): Get
    {
    }
}
',
        ];

        yield 'skips files in OpenApi\Spec namespace' => [
            '<?php
namespace OpenApi\Spec;

class Schema
{
}
',
        ];

        yield 'alias already present but individual imports remain' => [
            '<?php
namespace App;

use OpenApi\Spec as OA;

class Foo
{
    public function bar(): OA\Schema
    {
    }
}
',
            '<?php
namespace App;

use OpenApi\Spec as OA;
use OpenApi\Spec\Schema;

class Foo
{
    public function bar(): Schema
    {
    }
}
',
        ];

        yield 'static method call on imported class' => [
            '<?php
namespace App;

use OpenApi\Spec as OA;

class Foo
{
    public function bar()
    {
        return OA\Schema::create();
    }
}
',
            '<?php
namespace App;

use OpenApi\Spec\Schema;

class Foo
{
    public function bar()
    {
        return Schema::create();
    }
}
',
        ];

        yield 'new instance of imported class' => [
            '<?php
namespace App;

use OpenApi\Spec as OA;

class Foo
{
    public function bar()
    {
        return new OA\Info();
    }
}
',
            '<?php
namespace App;

use OpenApi\Spec\Info;

class Foo
{
    public function bar()
    {
        return new Info();
    }
}
',
        ];

        yield 'no spec imports - no change' => [
            '<?php
namespace App;

use Some\Other\Class_;

class Foo
{
}
',
        ];
    }

    protected function doTest(string $expected, ?string $input = null): void
    {
        if ($input === null) {
            $tokens = Tokens::fromCode($expected);
            $this->fixer->fix(new \SplFileInfo(__FILE__), $tokens);
            self::assertSame($expected, $tokens->generateCode());

            return;
        }

        $tokens = Tokens::fromCode($input);
        self::assertTrue($this->fixer->isCandidate($tokens));
        $this->fixer->fix(new \SplFileInfo(__FILE__), $tokens);
        $tokens->clearEmptyTokens();
        self::assertSame($expected, $tokens->generateCode());
    }
}
