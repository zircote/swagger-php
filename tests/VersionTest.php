<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Version;

class VersionTest extends OpenApiTestCase
{
    public function testConstructor(): void
    {
        $version = new Version('1.0.0', 'abc123');

        $this->assertSame('1.0.0', $version->getVersion());
        $this->assertSame('abc123', $version->getCommitHash());
    }

    public function testConstructorWithNullValues(): void
    {
        $version = new Version();

        $this->assertNull($version->getVersion());
        $this->assertNull($version->getCommitHash());
    }

    public function testGetVersion(): void
    {
        $version = new Version('2.1.0', 'def456');
        $this->assertSame('2.1.0', $version->getVersion());
    }

    public function testGetCommitHash(): void
    {
        $version = new Version('1.5.3', 'xyz789');
        $this->assertSame('xyz789', $version->getCommitHash());
    }

    public function testToStringWithAllFields(): void
    {
        $version = new Version('3.2.1', 'abcdef123456');
        $result = (string) $version;

        $this->assertJson($result);
        $data = json_decode($result, true);
        $this->assertSame('3.2.1', $data['version']);
        $this->assertSame('abcdef12', $data['commit']);
    }

    public function testToStringWithVersionOnly(): void
    {
        $version = new Version('1.0.0', null);
        $result = (string) $version;

        $this->assertJson($result);
        $data = json_decode($result, true);
        $this->assertSame('1.0.0', $data['version']);
        $this->assertArrayNotHasKey('commit', $data);
    }

    public function testToStringWithCommitOnly(): void
    {
        $version = new Version(null, 'commit123');
        $result = (string) $version;

        $this->assertJson($result);
        $data = json_decode($result, true);
        $this->assertArrayNotHasKey('version', $data);
        $this->assertSame('commit12', $data['commit']);
    }

    public function testToStringWithNoFields(): void
    {
        $version = new Version();
        $result = (string) $version;

        $this->assertJson($result);
        $this->assertSame('[]', $result);
    }

    public function testToStringCommitTruncation(): void
    {
        $version = new Version('2.0.0', 'verylongcommithash1234567890');
        $result = (string) $version;

        $data = json_decode($result, true);
        $this->assertSame('verylong', $data['commit']);
        $this->assertSame(8, strlen($data['commit']));
    }

    public function testToStringShortCommit(): void
    {
        $version = new Version('1.0.0', 'abc');
        $result = (string) $version;

        $data = json_decode($result, true);
        $this->assertSame('abc', $data['commit']);
    }

    public function testGetCurrentReturnsVersionInstance(): void
    {
        $version = Version::getCurrent();
        $this->assertInstanceOf(Version::class, $version);
    }

    public function testDevVersionFormat(): void
    {
        $version = new Version('5.1.5-dev', 'abc12345');
        $result = (string) $version;

        $data = json_decode($result, true);
        $this->assertSame('5.1.5-dev', $data['version']);
        $this->assertStringEndsWith('-dev', $data['version']);
    }

    public function testReleaseVersionFormat(): void
    {
        $version = new Version('5.1.4', 'def54321');
        $result = (string) $version;

        $data = json_decode($result, true);
        $this->assertSame('5.1.4', $data['version']);
        $this->assertStringNotContainsString('-dev', $data['version']);
        $this->assertStringNotContainsString('dev-', $data['version']);
    }

    public function testVersionStringImmutability(): void
    {
        $version = new Version('1.0.0', 'commit123');
        $string1 = (string) $version;
        $string2 = (string) $version;

        $this->assertSame($string1, $string2);
    }

    public function testDirtyVersionFormat(): void
    {
        $version = new Version('5.1.4-dirty', 'abc12345');
        $result = (string) $version;

        $data = json_decode($result, true);
        $this->assertSame('5.1.4-dirty', $data['version']);
        $this->assertStringEndsWith('-dirty', $data['version']);
    }

    public function testDevDirtyVersionFormat(): void
    {
        $version = new Version('5.1.5-dev-dirty', 'def67890');
        $result = (string) $version;

        $data = json_decode($result, true);
        $this->assertSame('5.1.5-dev-dirty', $data['version']);
        $this->assertStringContainsString('-dev', $data['version']);
        $this->assertStringEndsWith('-dirty', $data['version']);
    }

    public function testCleanVersionWithoutDirty(): void
    {
        $version = new Version('1.2.3', 'clean123');
        $result = (string) $version;

        $data = json_decode($result, true);
        $this->assertSame('1.2.3', $data['version']);
        $this->assertStringNotContainsString('-dirty', $data['version']);
    }

    public function testDefaultVersionWhenNoTags(): void
    {
        // Test the 0.0.1 fallback logic (simulated)
        $version = new Version('0.0.2-dev', 'notags123');
        $result = (string) $version;

        $data = json_decode($result, true);
        $this->assertSame('0.0.2-dev', $data['version']);
        $this->assertStringEndsWith('-dev', $data['version']);
    }

    public function testDefaultVersionWithDirty(): void
    {
        // Test the 0.0.1 fallback with dirty state (simulated)
        $version = new Version('0.0.2-dev-dirty', 'notagsdirty456');
        $result = (string) $version;

        $data = json_decode($result, true);
        $this->assertSame('0.0.2-dev-dirty', $data['version']);
        $this->assertStringEndsWith('-dirty', $data['version']);
    }
}
