<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tools\CSFixer;

use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;

class ScopedDeclareStrictTypesFixer extends AbstractFixer
{
    protected DeclareStrictTypesFixer $declareStrictTypesFixer;

    public function __construct()
    {
        $this->declareStrictTypesFixer = new DeclareStrictTypesFixer();
    }

    public function fix(\SplFileInfo $file, Tokens $tokens): void
    {
        $this->declareStrictTypesFixer->fix($file, $tokens);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return $this->declareStrictTypesFixer->getDefinition();
    }

    public function getName(): string
    {
        return 'OpenApi/declare_strict_types';
    }

    public function getPriority(): int
    {
        return 5;
    }

    public function supports(\SplFileInfo $file): bool
    {
        return parent::supports($file) && false !== strpos($file->getPath(), '/src/');
    }
}
