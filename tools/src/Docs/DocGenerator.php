<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tools\Docs;

abstract class DocGenerator
{
    public const NO_DETAILS_AVAILABLE = 'No details available.';

    protected $projectRoot;

    public function __construct($projectRoot)
    {
        $this->projectRoot = realpath($projectRoot);
    }

    public function docPath(string $relativeName): string
    {
        return $this->projectRoot . '/docs/' . $relativeName;
    }

    public function formatClassHeader(string $name, string $namespace): string
    {
        return <<< EOT
### [$name](https://github.com/zircote/swagger-php/tree/master/src/$namespace/$name.php)


EOT;
    }

    public function preamble(string $type): string
    {
        $title = rtrim($type, 's');

        $preamble = <<< EOT
# $title Reference

This page is generated automatically from the `swagger-php` sources.

For improvements head over to [GitHub](https://github.com/zircote/swagger-php) and create a PR ;)


EOT;

        $preambleSnippet = $this->docPath('snippets' . DIRECTORY_SEPARATOR . 'preamble_' . strtolower($type) . '.md');

        if (file_exists($preambleSnippet)) {
            $preamble .= file_get_contents($preambleSnippet);
        }

        $preamble .= <<< EOT

EOT;

        return $preamble;
    }

    protected function linkFromMarkup(string $see): ?string
    {
        preg_match('/\[([^]]+)]\((.*)\)/', $see, $matches);

        return 3 == count($matches) ? '<a href="' . $matches[2] . '">' . $matches[1] . '</a>' : null;
    }

    protected function extractDocumentation($docblock): array
    {
        if (!$docblock) {
            return ['content' => '', 'see' => [], 'var' => '', 'params' => []];
        }

        $comment = preg_split('/(\n|\r\n)/', (string) $docblock);

        $comment[0] = preg_replace('/[ \t]*\\/\*\*/', '', $comment[0]); // strip '/**'
        $i = count($comment) - 1;
        $comment[$i] = preg_replace('/\*\/[ \t]*$/', '', $comment[$i]); // strip '*/'

        $see = [];
        $var = '';
        $params = [];
        $contentLines = [];
        $append = false;
        foreach ($comment as $line) {
            $line = preg_replace('/^\s+\* ?/', '', $line);
            if (substr($line, 0, 1) === '@') {
                if (substr($line, 0, 5) === '@see ') {
                    $see[] = trim(substr($line, 5));
                    continue;
                }
                if (substr($line, 0, 5) === '@var ') {
                    $var = trim(substr($line, 5));
                    continue;
                }
                if (substr($line, 0, 7) === '@param ') {
                    preg_match('/^([^\$]+)\$([^\s]+)(.*)$/', trim(substr($line, 7)), $match);
                    if (count($match) >= 3) {
                        $params[trim($match[2])] = [
                            'type' => trim($match[1]),
                            'content' => 4 == count($match) ? $match[3] : null,
                        ];
                        continue;
                    }
                }
                if (in_array(substr($line, 0), ['@Annotation', '@inheritdoc', '@since 3.1.0'])) {
                    continue;
                }
            }

            if ($append) {
                $i = count($contentLines) - 1;
                $contentLines[$i] = substr($contentLines[$i], 0, -1) . $line;
            } else {
                $contentLines[] = $line;
            }
            $append = (substr($line, -1) === '\\');
        }

        $content = trim(implode("\n", $contentLines));

        return ['content' => $content, 'see' => $see, 'var' => $var, 'params' => $params];
    }
}
