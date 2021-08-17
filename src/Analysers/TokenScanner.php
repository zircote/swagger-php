<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Analysers;

/**
 * High level, PHP token based, scanner.
 */
class TokenScanner
{
    /**
     * Scan file for all classes, interfaces and traits.
     *
     * @return string[][] File details
     */
    public function scanFile(string $filename): array
    {
        return $this->scanTokens(token_get_all(file_get_contents($filename)));
    }

    /**
     * Scan file for all classes, interfaces and traits.
     *
     * @return string[][] File details
     */
    protected function scanTokens(array $tokens): array
    {
        $units = [];
        $namespace = '';
        $lastToken = null;
        $curlyNested = 0;
        $stack = [];

        while (false !== ($token = $this->nextToken($tokens))) {
            if (!is_array($token)) {
                switch ($token) {
                    case '{':
                        ++$curlyNested;
                        break;
                    case '}':
                        --$curlyNested;
                        break;
                }
                if ($stack) {
                    $last = array_pop($stack);
                    if ($last[1] <= $curlyNested) {
                        $stack[] = $last;
                    }
                }
                continue;
            }
            switch ($token[0]) {
                case T_NAMESPACE:
                    $namespace = $this->nextWord($tokens);
                    break;
                case T_CLASS:
                    if ($lastToken && is_array($lastToken) && $lastToken[0] === T_DOUBLE_COLON) {
                        // ::class
                        break;
                    }

                    // class name
                    $token = $this->nextToken($tokens);

                    // unless ...
                    if (is_string($token) && ($token === '(' || $token === '{')) {
                        // new class[()] { ... }
                        break;
                    } elseif (is_array($token) && in_array($token[1], ['extends', 'implements'])) {
                        // new class[()] extends { ... }
                        break;
                    }

                    $name = $namespace . '\\' . $token[1];
                    $this->skipTo($tokens, '{');
                    $stack[] = [$name, ++$curlyNested];
                    $units[$name] = ['methods' =>[], 'properties' => []];
                    break;
                case T_INTERFACE:
                case T_TRAIT:
                    $token = $this->nextToken($tokens);
                    $name = $namespace . '\\' . $token[1];
                    $this->skipTo($tokens, '{');
                    $stack[] = [$name, ++$curlyNested];
                    $units[$name] = ['methods' =>[], 'properties' => []];
                    break;
                case T_FUNCTION:
                    $token = $this->nextToken($tokens);

                    if (1 == count($stack)) {
                        $name = $stack[0][0];
                        $this->skipTo($tokens, '{');
                        $stack[] = [$token[1], ++$curlyNested];

                        $units[$name]['methods'][] = $token[1];
                    }
                    break;
                case T_VARIABLE:
                    if (1 == count($stack)) {
                        $name = $stack[0][0];
                        $units[$name]['properties'][] = substr($token[1], 1);
                    }
                    break;
            }
            $lastToken = $token;
        }

        return $units;
    }

    /**
     * Get the next token that is not whitespace or comment.
     */
    protected function nextToken(array &$tokens)
    {
        $token = true;
        while ($token) {
            $token = next($tokens);
            if (is_array($token)) {
                if (in_array($token[0], [T_WHITESPACE, T_COMMENT])) {
                    continue;
                }
            }

            return $token;
        }

        return $token;
    }

    protected function skipTo(array &$tokens, $char): void
    {
        while (false !== ($token = next($tokens))) {
            if (is_string($token) && $token == $char) {
                break;
            }
        }
    }

    /**
     * Read next word.
     *
     * Skips leading whitespace.
     */
    protected function nextWord(array &$tokens): string
    {
        $word = '';
        while (false !== ($token = next($tokens))) {
            if (is_array($token)) {
                if ($token[0] === T_WHITESPACE) {
                    if ($word) {
                        break;
                    }
                    continue;
                }
                $word .= $token[1];
            }
        }

        return $word;
    }
}
