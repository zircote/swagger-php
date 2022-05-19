<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tools\CSFixer;

use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

class LicenseFixer extends AbstractFixer
{
    public function fix(\SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if ($token->isComment()) {
                if (false !== strpos($token->getContent(), '@license')) {
                    return;
                }
            }
        }

        $license = <<< EOC
/**
 * @license Apache 2.0
 */
EOC;

        if ($sequence = $tokens->findSequence([[T_NAMESPACE]])) {
            $index = array_keys($sequence)[0];
            $tokens->insertAt($index, new Token([
                T_COMMENT,
                $license,
            ]));
        }
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'All .php files MUST have a @license docblock annotation before namespace / use statement(s)',
            []
        );
    }

    public function getName(): string
    {
        return 'OpenApi/license';
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
