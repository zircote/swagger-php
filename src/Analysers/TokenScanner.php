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
        $uses = [];
        $isInterface = false;
        $namespace = '';
        $currentName = null;
        $lastToken = null;
        $stack = [];

        while (false !== ($token = $this->nextToken($tokens))) {
            if (!is_array($token)) {
                switch ($token) {
                    case '{':
                        $stack[] = $token;
                        break;
                    case '}':
                        array_pop($stack);
                        break;
                }
                continue;
            }

            switch ($token[0]) {
                case T_CURLY_OPEN:
                case T_DOLLAR_OPEN_CURLY_BRACES:
                    $stack[] = $token[1];
                    break;

                case T_NAMESPACE:
                    $namespace = $this->nextWord($tokens);
                    break;

                case T_USE:
                    if (!$stack) {
                        $uses = array_merge($uses, $this->parseFQNStatement($tokens, $token));
                    } elseif ($currentName) {
                        $traits = $this->resolveFQN($this->parseFQNStatement($tokens, $token), $namespace, $uses);
                        $units[$currentName]['traits'] = array_merge($units[$currentName]['traits'], $traits);
                    }
                    break;

                case T_CLASS:
                    if ($stack) {
                        break;
                    }

                    if ($lastToken && is_array($lastToken) && $lastToken[0] === T_DOUBLE_COLON) {
                        // ::class
                        break;
                    }

                    // class name
                    $token = $this->nextToken($tokens);

                    // unless ...
                    if (is_string($token) && ($token === '(' || $token === '{')) {
                        // new class[()] { ... }
                        if ('{' == $token) {
                            prev($tokens);
                        }
                        break;
                    } elseif (is_array($token) && in_array($token[1], ['extends', 'implements'])) {
                        // new class[()] extends { ... }
                        break;
                    }

                    $isInterface = false;
                    $currentName = $namespace . '\\' . $token[1];
                    $units[$currentName] = ['uses' => $uses, 'interfaces' => [], 'traits' => [], 'methods' => [], 'properties' => []];
                    break;

                case T_INTERFACE:
                    if ($stack) {
                        break;
                    }

                    $isInterface = true;
                    $token = $this->nextToken($tokens);
                    $currentName = $namespace . '\\' . $token[1];
                    $units[$currentName] = ['uses' => $uses, 'interfaces' => [], 'traits' => [], 'methods' => [], 'properties' => []];
                    break;

                case T_TRAIT:
                    if ($stack) {
                        break;
                    }

                    $isInterface = false;
                    $token = $this->nextToken($tokens);
                    $currentName = $namespace . '\\' . $token[1];
                    $this->skipTo($tokens, '{', true);
                    $units[$currentName] = ['uses' => $uses, 'interfaces' => [], 'traits' => [], 'methods' => [], 'properties' => []];
                    break;

                case T_EXTENDS:
                    $fqns = $this->parseFQNStatement($tokens, $token);
                    if ($isInterface && $currentName) {
                        $units[$currentName]['interfaces'] = $this->resolveFQN($fqns, $namespace, $uses);
                    }
                    if (!is_array($token) || T_IMPLEMENTS !== $token[0]) {
                        break;
                    }
                    // no break
                case T_IMPLEMENTS:
                    $fqns = $this->parseFQNStatement($tokens, $token);
                    if ($currentName) {
                        $units[$currentName]['interfaces'] = $this->resolveFQN($fqns, $namespace, $uses);
                    }
                    break;

                case T_FUNCTION:
                    $token = $this->nextToken($tokens);

                    if (1 == count($stack) && $currentName) {
                        if (!$isInterface) {
                            // more nesting
                            $this->skipTo($tokens, '{', true);
                        } else {
                            // no function body
                            $this->skipTo($tokens, ';');
                        }
                        $units[$currentName]['methods'][] = $token[1];
                    }
                    break;

                case T_VARIABLE:
                    if (1 == count($stack) && $currentName) {
                        $units[$currentName]['properties'][] = substr($token[1], 1);
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

    protected function resolveFQN(array $names, string $namespace, array $uses): array
    {
        $resolve = function ($name) use ($namespace, $uses) {
            if ('\\' == $name[0]) {
                return substr($name, 1);
            }

            if (array_key_exists($name, $uses)) {
                return $uses[$name];
            }

            return $namespace . '\\' . $name;
        };

        return array_values(array_map($resolve, $names));
    }

    protected function skipTo(array &$tokens, $char, bool $prev = false): void
    {
        while (false !== ($token = next($tokens))) {
            if (is_string($token) && $token == $char) {
                if ($prev) {
                    prev($tokens);
                }

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

    /**
     * Parse a use statement.
     */
    protected function parseFQNStatement(array &$tokens, &$token): array
    {
        $normalizeAlias = function ($alias) {
            $alias = ltrim($alias, '\\');
            $elements = explode('\\', $alias);

            return array_pop($elements);
        };

        $class = '';
        $alias = '';
        $statements = [];
        $explicitAlias = false;
        $php8NSToken = defined('T_NAME_QUALIFIED') ? [T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED] : [];
        $nsToken = array_merge([T_STRING, T_NS_SEPARATOR], $php8NSToken);
        while ($token !== false) {
            $token = $this->nextToken($tokens);
            $isNameToken = in_array($token[0], $nsToken);
            if (!$explicitAlias && $isNameToken) {
                $class .= $token[1];
                $alias = $token[1];
            } elseif ($explicitAlias && $isNameToken) {
                $alias .= $token[1];
            } elseif ($token[0] === T_AS) {
                $explicitAlias = true;
                $alias = '';
            } elseif ($token[0] === T_IMPLEMENTS) {
                $statements[$normalizeAlias($alias)] = $class;
                break;
            } elseif ($token === ',') {
                $statements[$normalizeAlias($alias)] = $class;
                $class = '';
                $alias = '';
                $explicitAlias = false;
            } elseif ($token === ';') {
                $statements[$normalizeAlias($alias)] = $class;
                break;
            } elseif ($token === '{') {
                $statements[$normalizeAlias($alias)] = $class;
                prev($tokens);
                break;
            } else {
                break;
            }
        }

        return $statements;
    }
}
