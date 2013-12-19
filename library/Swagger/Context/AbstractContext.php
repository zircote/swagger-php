<?php

namespace Swagger\Context;

/**
 * AbstractContext
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
abstract class AbstractContext
{
    /**
     * @return string
     */
    public function extractDescription()
    {
        $lines = explode("\n", $this->getDocComment());
        unset($lines[0]);
        $description = '';
        foreach ($lines as $line) {
            $line = ltrim($line, "\t *");
            if (substr($line, 0, 1) === '@') {
                break;
            }
            $description .= $line.' ';
        }
        $description = trim($description);
        if ($description === '') {
            return null;
        }
        if (stripos($description, 'license')) {
            return null; // Don't use the GPL/MIT, etc license text as description.
        }
        return $description;
    }

    abstract public function getDocComment();
}
