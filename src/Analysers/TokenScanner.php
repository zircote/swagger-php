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
     * @return array<string, array<string, mixed>> File details
     */
    protected function scanTokens(array $tokens): array
    {
        $units = [];
        $uses = [];
        $isInterface = false;
        $isAbstractFunction = false;
        $namespace = '';
        $currentName = null;
        $unitLevel = 0;
        $lastToken = null;
        $stack = [];

        $initUnit = function ($uses): array {
            return [
                'uses' => $uses,
                'interfaces' => [],
                'traits' => [],
                'enums' => [],
                'methods' => [],
                'properties' => [],
            ];
        };

        while (false !== ($token = $this->nextToken($tokens))) {
            // named arguments
            $nextToken = $this->nextToken($tokens);
            if (($token !== '}' && $nextToken === ':') || $nextToken === false) {
                continue;
            }
            do {
                $prevToken = prev($tokens);
            } while ($token !== $prevToken);

            if (!is_array($token)) {
                switch ($token) {
                    case '{':
                        $stack[] = $token;
                        break;
                    case '}':
                        array_pop($stack);
                        if (count($stack) == $unitLevel) {
                            $currentName = null;
                        }
                        break;
                }
                continue;
            }

            switch ($token[0]) {
                case T_ABSTRACT:
                    if (count($stack)) {
                        $isAbstractFunction = true;
                    }
                    break;

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
                    if ($currentName) {
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
                    $unitLevel = count($stack);
                    $units[$currentName] = $initUnit($uses);
                    break;

                case T_INTERFACE:
                    if ($currentName) {
                        break;
                    }

                    $isInterface = true;
                    $token = $this->nextToken($tokens);
                    $currentName = $namespace . '\\' . $token[1];
                    $unitLevel = count($stack);
                    $units[$currentName] = $initUnit($uses);
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
                    if ((!is_array($token) && '&' == $token)
                        || (defined('T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG') && T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG == $token[0])) {
                        $token = $this->nextToken($tokens);
                    }

                    if (($unitLevel + 1) == count($stack) && $currentName) {
                        $units[$currentName]['methods'][] = $token[1];
                        if (!$isInterface && !$isAbstractFunction) {
                            // more nesting
                            $units[$currentName]['properties'] = array_merge(
                                $units[$currentName]['properties'],
                                $this->parsePromotedProperties($tokens)
                            );
                            $this->skipTo($tokens, '{', true);
                        } else {
                            // no function body
                            $this->skipTo($tokens, ';');
                            $isAbstractFunction = false;
                        }
                    }
                    break;

                case T_VARIABLE:
                    if (($unitLevel + 1) == count($stack) && $currentName) {
                        $units[$currentName]['properties'][] = substr($token[1], 1);
                    }
                    break;
                default:
                    // handle trait here too to avoid duplication
                    if (T_TRAIT === $token[0] || (defined('T_ENUM') && T_ENUM === $token[0])) {
                        if ($currentName) {
                            break;
                        }

                        $isInterface = false;
                        $token = $this->nextToken($tokens);
                        $currentName = $namespace . '\\' . $token[1];
                        $unitLevel = count($stack);
                        $this->skipTo($tokens, '{', true);
                        $units[$currentName] = $initUnit($uses);
                    }
                    break;
            }
            $lastToken = $token;
        }

        return $units;
    }

    /**
     * Get the next token that is not whitespace or comment.
     *
     * @return string|array|false
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

    /**
     * @return array<string>
     */
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

    protected function skipTo(array &$tokens, string $char, bool $prev = false): void
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
    protected function parseFQNStatement(array &$tokens, array &$token): array
    {
        $normalizeAlias = function ($alias): string {
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

    protected function parsePromotedProperties(array &$tokens): array
    {
        $properties = [];

        $this->skipTo($tokens, '(');
        $round = 1;
        $promoted = false;
        while (false !== ($token = $this->nextToken($tokens))) {
            if (is_string($token)) {
                switch ($token) {
                    case '(':
                        ++$round;
                        break;
                    case ')':
                        --$round;
                        if (0 == $round) {
                            return $properties;
                        }
                }
            }
            if (is_array($token)) {
                switch ($token[0]) {
                    case T_PUBLIC:
                    case T_PROTECTED:
                    case T_PRIVATE:
                        $promoted = true;
                        break;
                    case T_VARIABLE:
                        if ($promoted) {
                            $properties[] = ltrim($token[1], '$');
                            $promoted = false;
                        }
                        break;
                }
            }
        }

        return $properties;
    }
}
