<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tools\CSFixer;

trait ScopedTrait
{
    /**
     * @var list<string>
     */
    protected array $scopes = [];

    public function supports(\SplFileInfo $file): bool
    {
        return parent::supports($file) && $this->isScoped($file);
    }

    /**
     * @param list<string> $scopes
     */
    public function scope(array $scopes): static
    {
        $this->scopes = $scopes;

        return $this;
    }

    public function isScoped(\SplFileInfo $file): bool
    {
        foreach ($this->scopes as $scope) {
            if (str_contains($file->getPath(), (string) $scope)) {
                return true;
            }
        }

        return false;
    }
}
