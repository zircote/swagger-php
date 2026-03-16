<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Console;

use OpenApi\Annotations as OA;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class GenerateInput
{
    #[Argument('Source path(s) to scan')]
    public array $paths;

    #[Option('Generator config (e.g. -c operationId.hash=false)', shortcut: 'c')]
    public array $config = [];

    #[Option('Show default config', shortcut: 'D')]
    public bool $defaults = false;

    #[Option('Path to store the generated documentation (e.g. -o openapi.yaml)', shortcut: 'o')]
    public ?string $output = null;

    #[Option('Force yaml or json', shortcut: 'f')]
    public GenerateFormat $format = GenerateFormat::AUTO;

    #[Option('Exclude path(s) (e.g. -e vendor -e library/Zend)', shortcut: 'e')]
    public array $exclude = [];

    #[Option('Pattern of files to scan (e.g. -n "/\.(phps|php)$/")', shortcut: 'n')]
    public string $pattern = '*.php';

    #[Option('Bootstrap php file(s) for defining constants, etc. (e.g. -b config/constants.php)', shortcut: 'b')]
    public array $bootstrap = [];

    #[Option('Register an additional processor', shortcut: 'a')]
    public array $addProcessor = [];

    #[Option('Remove an existing processor', shortcut: 'r')]
    public array $removeProcessor = [];

    #[Option('The OpenAPI version')]
    public string $version = OA\OpenApi::DEFAULT_VERSION;

    #[Option('Show additional error information', shortcut: 'd')]
    public bool $debug = false;

    /**
     * @return iterable<string>
     */
    public function getBootstrapFilenames(): iterable
    {
        foreach ($this->bootstrap as $bootstrapPattern) {
            $filenames = glob($bootstrapPattern);

            if (!$filenames) {
                throw new InvalidArgumentException('Invalid `--bootstrap` value: "' . $bootstrapPattern . '"');
            }

            yield from $filenames;
        }
    }
}
