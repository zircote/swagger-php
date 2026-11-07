<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Benchmarks\Fixtures;

use Composer\InstalledVersions;

/**
 * Writes the source tree of a generated API, in one flavour.
 *
 * The templates are classic. The spec flavour is the same tree migrated by the rector set a user
 * migrates with, so both describe the same document and spec is measured on what a migration
 * produces. A tree is written once per shape and flavour and reused after that. Trees live under a
 * hash of this file, the classic attributes, the rector rules and rector's version, so a change to
 * any of them writes new trees, and the trees of every other hash are removed.
 *
 * The default root is one per checkout, so two checkouts on different hashes never remove each
 * other's trees.
 */
class FixtureWriter
{
    protected const REPOSITORY = __DIR__ . '/../../..';

    public function __construct(protected ?string $root = null)
    {
        $this->root ??= sys_get_temp_dir() . '/swagger-php-bench/' . substr(md5((string) realpath(self::REPOSITORY)), 0, 8);
    }

    public function write(Shape $shape, Flavour $flavour): Fixture
    {
        $name = $flavour->value . $shape->key();
        $namespace = 'OpenApi\\Tests\\Benchmarks\\Generated\\' . $name;
        $base = $this->root . '/' . $this->hash();
        $dir = $base . '/' . $name;

        if (!is_dir($base)) {
            @mkdir($base, 0755, true);
            $this->prune($base);
        }

        if (!is_dir($dir)) {
            // written beside and renamed, so a concurrent run never reads half a tree
            $partial = $dir . '.' . uniqid((string) getmypid(), true) . '.partial';
            $this->writeTree($partial, $namespace, $shape);
            if ($flavour === Flavour::Spec) {
                $this->migrate($partial);
            }
            if (!@rename($partial, $dir)) {
                $this->remove($partial);
                // a concurrent run renamed its tree first; anything else leaves no tree
                if (!is_dir($dir)) {
                    throw new \RuntimeException("Could not move the generated tree to {$dir}");
                }
            }
        }

        return new Fixture($dir, $namespace, $shape, $flavour);
    }

    protected function writeTree(string $dir, string $namespace, Shape $shape): void
    {
        foreach (['Models', 'Controllers', 'App'] as $folder) {
            mkdir($dir . '/' . $folder, 0755, true);
        }

        $this->put($dir . '/Api.php', $this->file($namespace, "#[OA\\Info(title: 'Bench', version: '1.0.0')]\nclass Api\n{\n}"));

        $used = $shape->used();
        for ($i = 0; $i < $shape->schemas; $i++) {
            $related = $i < $used ? ($i + 1) % $used : null;
            $this->put($dir . "/Models/S{$i}.php", $this->file($namespace . '\\Models', $this->model($i, $shape->depth, $related)));
        }

        for ($c = 0; $c < $shape->controllers(); $c++) {
            $methods = [];
            for ($i = $c * $shape->perController; $i < min($used, ($c + 1) * $shape->perController); $i++) {
                $methods[] = $this->operation($i, "\\{$namespace}\\Models\\S{$i}");
            }
            $this->put($dir . "/Controllers/C{$c}.php", $this->file($namespace . '\\Controllers', "class C{$c}\n{\n" . implode("\n\n", $methods) . "\n}"));
        }

        for ($n = 0; $n < $shape->noise; $n++) {
            $this->put($dir . "/App/N{$n}.php", "<?php declare(strict_types=1);\n\nnamespace {$namespace}\\App;\n\nclass N{$n}\n{\n    private array \$state = [];\n\n    public function handle(string \$key, int \$count): string\n    {\n        \$this->state[\$key] = \$count;\n\n        return \$key . \$count;\n    }\n}\n");
        }
    }

    protected function model(int $index, int $depth, ?int $related): string
    {
        $members = [
            "    #[OA\\Property]\n    public ?int \$id = null;",
            "    #[OA\\Property]\n    public ?string \$name = null;",
        ];

        if ($depth > 0) {
            $members[] = "    #[OA\\Property(type: 'object', properties: [\n" . $this->nested($depth - 1, 2) . "\n    ])]\n    public \$detail;";
        }

        if ($related !== null) {
            $members[] = "    #[OA\\Property]\n    public ?S{$related} \$related = null;";
        }

        return "#[OA\\Schema]\nclass S{$index}\n{\n" . implode("\n\n", $members) . "\n}";
    }

    /**
     * One property of an inline object, holding `$levels` more inline objects, down to a string.
     */
    protected function nested(int $levels, int $indent): string
    {
        $pad = str_repeat('    ', $indent);

        if ($levels === 0) {
            return "{$pad}new OA\\Property(property: 'value', type: 'string'),";
        }

        $inner = $this->nested($levels - 1, $indent + 1);

        return "{$pad}new OA\\Property(property: 'level{$levels}', type: 'object', properties: [\n{$inner}\n{$pad}]),";
    }

    /**
     * An operation returning the schema, or for an odd index a list of it.
     */
    protected function operation(int $index, string $class): string
    {
        return sprintf(
            "    #[OA\\Get(path: '/s%d', operationId: 'getS%d', responses: [\n        new OA\\Response(response: 200, description: 'OK', content: new OA\\JsonContent(%s)),\n    ])]\n    public function getS%d(): void\n    {\n    }",
            $index,
            $index,
            $index % 2 === 1 ? "type: 'array', items: new OA\\Items(ref: {$class}::class)" : "ref: {$class}::class",
            $index,
        );
    }

    protected function file(string $namespace, string $body): string
    {
        return "<?php declare(strict_types=1);\n\nnamespace {$namespace};\n\nuse OpenApi\\Attributes as OA;\n\n{$body}\n";
    }

    /**
     * Migrates a classic tree in place, with the set the migration guide gives users.
     */
    protected function migrate(string $dir): void
    {
        $command = [PHP_BINARY, self::REPOSITORY . '/bin/rector', 'process', $dir, '--config', self::REPOSITORY . '/rector/set/classic-to-spec.php', '--no-progress-bar', '--no-diffs'];
        // stderr to a file, so a long error cannot block rector while stdout is read
        $stderr = tmpfile();
        $process = proc_open($command, [1 => ['pipe', 'w'], 2 => $stderr], $pipes, null, ['XDEBUG_MODE' => 'off'] + getenv());
        if (!is_resource($process)) {
            throw new \RuntimeException('Could not start rector');
        }

        $output = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $status = proc_close($process);
        rewind($stderr);
        $output .= stream_get_contents($stderr);
        fclose($stderr);
        if ($status !== 0) {
            $this->remove($dir);

            throw new \RuntimeException("Migrating {$dir} with rector failed:\n{$output}");
        }
    }

    /**
     * A hash of the templates and of what migrates them: the rector rules read the classic
     * attributes' constructors, so those count too.
     */
    protected function hash(): string
    {
        $files = [__FILE__];
        foreach (['src/Attributes', 'rector/set', 'rector/src'] as $folder) {
            array_push($files, ...glob(self::REPOSITORY . '/' . $folder . '/*.php') ?: []);
        }

        $hashes = array_map(md5_file(...), $files);
        $hashes[] = InstalledVersions::getVersion('rector/rector');

        return substr(md5(implode('', $hashes)), 0, 8);
    }

    /**
     * Removes the trees of every other hash, and anything else left in the root.
     */
    protected function prune(string $keep): void
    {
        foreach (glob($this->root . '/*') ?: [] as $path) {
            if ($path !== $keep && is_dir($path)) {
                $this->remove($path);
            }
        }
    }

    protected function put(string $path, string $content): void
    {
        file_put_contents($path, $content);
    }

    protected function remove(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($files as $file) {
            $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
        }
        rmdir($dir);
    }
}
