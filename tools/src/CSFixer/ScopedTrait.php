<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tools\CSFixer;

trait ScopedTrait
{
    protected array $scopes = [];

    public function supports(\SplFileInfo $file): bool
    {
        return parent::supports($file) && $this->isScoped($file);
    }

    public function scope(array $scopes)
    {
        $this->scopes = $scopes;

        return $this;
    }

    public function isScoped(\SplFileInfo $file): bool
    {
        foreach ($this->scopes as $scope) {
            if (false !== strpos($file->getPath(), $scope)) {
                return true;
            }
        }

        return false;
    }
}
