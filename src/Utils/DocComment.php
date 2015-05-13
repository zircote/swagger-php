<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Utils;

/**
 * Data object for parsing the contents of a single DocBlock.
 */
class DocComment
{
    /** @var string[] */
    private $rows;

    /**
     * @param string $comment
     */
    public function __construct($comment)
    {
        $this->rows = $this->parseCommentRows($comment);
    }

    public function getSummary()
    {
        $firstRow = reset($this->rows);

        return strpos($firstRow, '@') !== 0 ? $firstRow : null;
    }

    public function getTag($name)
    {
        $pattern = sprintf('@%s ', $name);

        foreach ($this->rows as $row) {
            if (strpos($row, $pattern) === 0) {
                return substr($row, strlen($pattern));
            }
        }

        return null;
    }

    private function parseCommentRows($comment)
    {
        $rawRows = array_map('trim', explode("\n", $comment));

        return count($rawRows) > 1
            ? array_map(function ($row) { return trim(substr($row, 1)); }, array_slice($rawRows, 1, -1))
            : implode(' ', array_slice(explode(' ', $rawRows), 1, -1));
    }
}
