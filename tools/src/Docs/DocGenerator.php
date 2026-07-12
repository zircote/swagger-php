<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tools\Docs;

abstract class DocGenerator
{
    public const NO_DETAILS_AVAILABLE = 'No details available.';

    protected string $projectRoot;

    protected Renderer $renderer;

    public function __construct(string $projectRoot, ?Renderer $renderer = null)
    {
        $this->projectRoot = realpath($projectRoot);
        $this->renderer = $renderer ?? new Renderer();
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
}
