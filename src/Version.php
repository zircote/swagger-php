<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

/**
 * Version utilities for OpenAPI PHP library.
 */
class Version
{
    private ?string $version;
    private ?string $commitHash;

    public function __construct(?string $version = null, ?string $commitHash = null)
    {
        $this->version = $version;
        $this->commitHash = $commitHash;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function getCommitHash(): ?string
    {
        return $this->commitHash;
    }

    public function __toString(): string
    {
        $data = [];

        if ($this->version) {
            $data['version'] = $this->version;
        }

        if ($this->commitHash) {
            $data['commit'] = substr($this->commitHash, 0, 8);
        }

        return json_encode($data, JSON_THROW_ON_ERROR);
    }

    /**
     * Get current version information.
     */
    public static function getCurrent(): self
    {
        try {
            $repo = (new \CzProject\GitPhp\Git())->open(__DIR__ . '/..');
            $commitHash = (string) $repo->getLastCommitId();
            $version = self::resolveVersion($repo);

            return new self($version, $commitHash);
        } catch (\Exception $exception) {
            return new self('unknown', null);
        }
    }

    /**
     * Resolve version from git repository.
     */
    private static function resolveVersion(\CzProject\GitPhp\GitRepository $repo): string
    {
        $latest = self::getLatestSemverTag($repo) ?: '0.0.1';

        // Check if current commit is tagged with the latest version
        $baseVersion = self::isCurrentCommitTagged($repo, $latest) ? $latest : self::createDevVersion($latest);

        // Check if working directory is dirty
        if (self::isWorkingDirectoryDirty($repo)) {
            $baseVersion .= '-dirty';
        }

        return $baseVersion;
    }

    /**
     * Get latest semantic version tag.
     */
    private static function getLatestSemverTag(\CzProject\GitPhp\GitRepository $repo): ?string
    {
        $latest = null;

        foreach ($repo->getTags() as $tag) {
            try {
                (new \Composer\Semver\VersionParser())->normalize($tag);
                if (!$latest || \Composer\Semver\Comparator::greaterThan($tag, $latest)) {
                    $latest = $tag;
                }
            } catch (\Exception $exception) {
                continue;
            }
        }

        return $latest;
    }

    /**
     * Check if current commit corresponds to a specific tag.
     */
    private static function isCurrentCommitTagged(\CzProject\GitPhp\GitRepository $repo, string $tag): bool
    {
        try {
            // Get commit hash for the tag
            $tagCommit = $repo->execute('rev-list', '-n', '1', $tag);
            $tagCommitHash = trim(implode('', $tagCommit));

            // Get current commit hash
            $currentCommitHash = (string) $repo->getLastCommitId();

            return $tagCommitHash === $currentCommitHash;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * Create development version by bumping patch version.
     */
    private static function createDevVersion(string $latest): string
    {
        return self::bumpPatch($latest) . '-dev';
    }

    /**
     * Check if working directory has uncommitted changes.
     */
    private static function isWorkingDirectoryDirty(\CzProject\GitPhp\GitRepository $repo): bool
    {
        try {
            // Check for uncommitted changes using git status --porcelain
            $status = $repo->execute('status', '--porcelain');

            return !in_array(trim(implode('', $status)), ['', '0'], true);
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * Bump patch version.
     */
    private static function bumpPatch(string $version): string
    {
        $normalized = (new \Composer\Semver\VersionParser())->normalize($version);
        $parts = explode('.', $normalized);

        if (count($parts) >= 3) {
            $major = (int) $parts[0];
            $minor = (int) $parts[1];
            $patch = (int) explode('-', $parts[2])[0];

            return sprintf('%d.%d.%d', $major, $minor, $patch + 1);
        }

        return $version;
    }
}
