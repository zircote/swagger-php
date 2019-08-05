<?php

namespace OpenApi\Annotations;

interface HasCustomDeserialization
{
    /**
     * @param mixed $value
     *
     * @return bool
     */
    public static function shouldApplyCustomDeserialization($value): bool;

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public static function applyCustomDeserialization($value);
}
