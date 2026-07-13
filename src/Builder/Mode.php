<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Builder;

enum Mode: string
{
    case CLASSIC = 'classic';
    case HYBRID = 'hybrid';
    case SPEC = 'spec';

    public function isClassic(): bool
    {
        return $this === Mode::CLASSIC;
    }

    public function isHybrid(): bool
    {
        return $this === Mode::HYBRID;
    }

    public function isSpec(): bool
    {
        return $this === Mode::SPEC;
    }
}
