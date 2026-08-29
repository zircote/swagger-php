<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tools\Docs;

use OpenApi\Tools\Docs\Sections\ConfigSettingsSection;
use OpenApi\Tools\Docs\Sections\DescriptionSection;
use OpenApi\Tools\Docs\Sections\ReferencesSection;
use OpenApi\Tools\Docs\Sections\SectionInterface;
use OpenApi\Utils\Pipeline;

abstract class DocGenerator
{
    public const NO_DETAILS_AVAILABLE = 'No details available.';

    protected string $projectRoot;

    protected Renderer $renderer;

    /** @var list<SectionInterface> */
    protected array $sections;

    public function __construct(string $projectRoot, ?Renderer $renderer = null)
    {
        $this->projectRoot = realpath($projectRoot);
        $this->renderer = $renderer ?? new Renderer();
        $this->sections = $this->defaultSections();
    }

    public function docPath(string $relativeName): string
    {
        return $this->projectRoot . '/docs/' . $relativeName;
    }

    public function snippetContent(string $type): ?string
    {
        $path = $this->docPath('snippets' . DIRECTORY_SEPARATOR . 'preamble_' . strtolower($type) . '.md');

        return file_exists($path) ? file_get_contents($path) : null;
    }

    abstract public function generate(): array;

    /**
     * @param list<SectionInterface> $sections
     */
    public function setSections(array $sections): static
    {
        $this->sections = $sections;

        return $this;
    }

    /**
     * Names of the constructor parameters that count as public configuration.
     *
     * Constructor parameters are the configuration contract by convention. Object typed
     * ones (factories, resolvers, the generator) are collaborators rather than settings
     * and are excluded, matching {@see Pipeline::getConfig()} and therefore the `-D`
     * output.
     *
     * @return list<string>
     */
    public function configurableParameters(\ReflectionClass $rc): array
    {
        if (!$rc->hasMethod('__construct')) {
            return [];
        }

        $names = [];
        foreach ($rc->getMethod('__construct')->getParameters() as $parameter) {
            $type = $parameter->getType();
            if ($type instanceof \ReflectionNamedType && !$type->isBuiltin()) {
                continue;
            }

            $names[] = $parameter->getName();
        }

        return $names;
    }

    /**
     * @return array{content: string, see: list<string>, var: string, params: array<string, array{type: string, content: string|null}>}
     */
    public function parseDocblock(string|false|null $docblock): array
    {
        if (!$docblock) {
            return ['content' => '', 'see' => [], 'var' => '', 'params' => []];
        }

        $comment = preg_split('/(\n|\r\n)/', $docblock);

        $comment[0] = preg_replace('/[ \t]*\\/\*\*/', '', $comment[0]); // strip '/**'
        $lastIndex = count($comment) - 1;
        $comment[$lastIndex] = preg_replace('/\*\/[ \t]*$/', '', (string) $comment[$lastIndex]); // strip '*/'

        $see = [];
        $var = '';
        $params = [];
        $contentLines = [];
        $append = false;
        foreach ($comment as $line) {
            $line = preg_replace('/^\s+\* ?/', '', (string) $line);
            if (str_starts_with((string) $line, '@')) {
                if (str_starts_with((string) $line, '@see ')) {
                    $see[] = trim(substr((string) $line, 5));
                    continue;
                }
                if (str_starts_with((string) $line, '@var ')) {
                    $var = trim(substr((string) $line, 5));
                    continue;
                }
                if (str_starts_with((string) $line, '@param ')) {
                    preg_match('/^([^\$]+)\$([^\s]+)(.*)$/', trim(substr((string) $line, 7)), $match);
                    if (count($match) >= 3) {
                        $params[trim($match[2])] = [
                            'type' => trim($match[1]),
                            'content' => 4 === count($match) ? $match[3] : null,
                        ];
                        continue;
                    }
                }
                if (in_array(substr((string) $line, 0), ['@Annotation', '@inheritdoc'], true)) {
                    continue;
                }
            }

            if ($append) {
                $lastIndex = count($contentLines) - 1;
                $contentLines[$lastIndex] = substr((string) $contentLines[$lastIndex], 0, -1) . $line;
            } else {
                $contentLines[] = $line;
            }
            $append = (str_ends_with((string) $line, '\\'));
        }

        $content = trim(implode("\n", $contentLines));

        return ['content' => $content, 'see' => $see, 'var' => $var, 'params' => $params];
    }

    /**
     * @return list<SectionInterface>
     */
    protected function defaultSections(): array
    {
        return [
            new DescriptionSection(),
            new ConfigSettingsSection(),
            new ReferencesSection(),
        ];
    }

    /**
     * @param array<string,mixed> $data
     */
    protected function renderSections(array $data): string
    {
        $out = '';
        foreach ($this->sections as $section) {
            $rendered = $section->render($data);
            if ($rendered !== '') {
                $out .= "\n" . $rendered;
            }
        }

        return $out;
    }

    /**
     * The `Configuration` section, which differs between pipelines only in what the
     * configurable thing is called and how a build selects it.
     *
     * @param string       $noun     lowercase singular, e.g. `augmenter`
     * @param string       $modeFlag CLI flag selecting the pipeline, empty for the default
     * @param list<string> $examples CLI examples, without the leading `> `
     * @param string       $php      the "Programmatically with PHP" body
     */
    protected function renderConfiguration(string $noun, string $modeFlag, array $examples, string $php): string
    {
        $openapi = rtrim('./vendor/bin/openapi ' . $modeFlag);

        $out = "\n" . $this->renderer->sectionHeader(ucfirst($noun) . ' Configuration');

        $out .= "\n### Command line\n";
        $out .= "The `-c` option takes a name/value pair: the {$noun} name (starting lowercase)\n";
        $out .= "and the option name, separated by a dot (`.`).\n";
        $out .= "\n";
        $out .= "To list the available {$noun} names and options use `-D`. It still requires a\n";
        $out .= "source path, e.g. `{$openapi} -D src`. Unknown keys are\n";
        $out .= "reported as warnings.\n";
        $out .= "\n";
        $out .= "```shell\n";
        foreach ($examples as $example) {
            $out .= "> {$openapi} {$example}\n";
        }
        $out .= "```\n";

        $out .= "\n### Programmatically with PHP\n";

        return $out . $php;
    }

    /**
     * Type names for a reflected type, flattening unions and intersections.
     *
     * `getName()` only exists on `ReflectionNamedType`, so calling it on whatever
     * `getType()` returns fails on any union or intersection typed member.
     *
     * @return list<string>
     */
    protected function typeNames(?\ReflectionType $type): array
    {
        if ($type instanceof \ReflectionNamedType) {
            return [$type->getName()];
        }

        if ($type instanceof \ReflectionUnionType || $type instanceof \ReflectionIntersectionType) {
            $names = [];
            foreach ($type->getTypes() as $member) {
                $names = array_merge($names, $this->typeNames($member));
            }

            return $names;
        }

        return [];
    }

    /**
     * Configuration options, read from the setters that match a configurable constructor
     * parameter.
     *
     * @return list<array{name: string, type: string, default: string, description: string}>
     */
    protected function collectOptions(\ReflectionClass $rc): array
    {
        $options = [];
        $configurable = $this->configurableParameters($rc);

        foreach ($rc->getMethods() as $method) {
            if (!str_starts_with($method->getName(), 'set')) {
                continue;
            }

            $pname = lcfirst(substr($method->getName(), 3));
            if (!in_array($pname, $configurable, true)) {
                continue;
            }

            $type = 'n/a';
            if (1 === count($method->getParameters())) {
                if ($names = $this->typeNames($method->getParameters()[0]->getType())) {
                    $type = implode('|', $names);
                }
            }

            $phpdoc = $this->parseDocblock($method->getDocComment());
            $description = '';
            if ($phpdoc['content']) {
                $description = $phpdoc['content'];
            } elseif (array_key_exists($pname, $phpdoc['params']) && $phpdoc['params'][$pname]['content']) {
                $description = $phpdoc['params'][$pname]['content'];
            }

            $options[] = [
                'name' => $pname,
                'type' => $type,
                'default' => $this->resolveDefault($rc, $pname),
                'description' => $description,
            ];
        }

        return $options;
    }

    /**
     * The documented default for an option, taken from the matching constructor parameter.
     */
    protected function resolveDefault(\ReflectionClass $rc, string $pname): string
    {
        if (!$rc->hasMethod('__construct')) {
            return 'N/A';
        }

        foreach ($rc->getMethod('__construct')->getParameters() as $parameter) {
            if ($parameter->getName() !== $pname) {
                continue;
            }

            if (!$parameter->isDefaultValueAvailable()) {
                return 'N/A';
            }

            $dv = $parameter->getDefaultValue();

            return match (gettype($dv)) {
                'NULL' => 'null',
                'boolean' => $dv ? 'true' : 'false',
                'array' => '[' . implode(', ', $dv) . ']',
                'object' => $dv::class,
                default => (string) $dv,
            };
        }

        return 'N/A';
    }
}
