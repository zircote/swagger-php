#!/usr/bin/env php
<?php
/**
 * @license  http://www.apache.org/licenses/LICENSE-2.0
 *           Copyright [2012] [Robert Allen]
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @category Swagger
 * @package  Swagger
 */
require dirname(__DIR__) . '/vendor/autoload.php';

use Symfony\Component\Finder\Finder;

$compiler = new Compiler();
$compiler->compile();
/**
 * Shamelessly copied and modified from the Composer project,
 * because it works well
 *  - git://github.com/composer/composer.git
 *  - https://github.com/composer/composer
 *  - http://getcomposer.org
 */
class Compiler
{

    protected $version;

    /**
     * Compiles composer into a single phar file
     *
     * @throws \RuntimeException
     *
     * @param  string            $pharFile The full path to the file to create
     */
    public function compile($pharFile = 'swagger.phar')
    {
        if (file_exists($pharFile)) {
            unlink($pharFile);
        }

        $this->version = trim(file_get_contents(dirname(__DIR__ . '/VERSION')));
        $phar = new \Phar($pharFile, 0, 'swagger.phar');
        $phar->setSignatureAlgorithm(\Phar::SHA1);

        $phar->startBuffering();

        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->name('*.php')
            ->notName('Compiler.php')
            ->notName('ClassLoader.php')
            ->in(dirname(__DIR__) . '/');

        foreach ($finder as $file) {
            $this->addFile($phar, $file);
        }

        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->name('*.php')
            ->in(__DIR__ . '/../library');

        foreach ($finder as $file) {
            $this->addFile($phar, $file);
        }

        $this->addFile(
            $phar, new \SplFileInfo(dirname(__DIR__) . '/vendor/autoload.php')
        );
        $this->addFile(
            $phar, new \SplFileInfo(
                dirname(__DIR__) .'/vendor/composer/autoload_namespaces.php'
             )
        );
        $this->addFile(
            $phar, new \SplFileInfo(
                dirname(__DIR__) . '/vendor/composer/autoload_classmap.php'
            )
        );
        $this->addFile(
            $phar, new \SplFileInfo(
                dirname(__DIR__) . '/vendor/composer/ClassLoader.php'
             )
        );
        $this->addComposerBin($phar);

        $phar->setStub($this->getStub());

        $phar->stopBuffering();

        $this->addFile(
            $phar, new \SplFileInfo(dirname(__DIR__) . '/LICENSE-2.0.txt'),
            false
        );

        unset($phar);
    }

    protected function addFile($phar, \SplFileInfo $file, $strip = true)
    {
        $path = str_replace(
            dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR, '',
            $file->getRealPath()
        );

        $content = file_get_contents($file);
        if ($strip) {
            $content = $this->stripWhitespace($content);
        } elseif ('LICENSE-2.0.txt' === basename($file)) {
            $content = "\n" . $content . "\n";
        }
        $content = str_replace('@package_version@', $this->version, $content);

        $phar->addFromString($path, $content);
    }

    protected function addComposerBin(\Phar $phar)
    {
        $content = file_get_contents(dirname(__DIR__) . '/bin/swagger');
        $content = preg_replace('{^#!/usr/bin/env php\s*}', '', $content);
        $phar->addFromString('bin/swagger', $content);
    }

    /**
     * Removes whitespace from a PHP source string while preserving line numbers.
     *
     * @param  string $source A PHP string
     *
     * @return string The PHP string with the whitespace removed
     */
    protected function stripWhitespace($source)
    {
        if (!function_exists('token_get_all')) {
            return $source;
        }

        $output = '';
        foreach (token_get_all($source) as $token) {
            if (is_string($token)) {
                $output .= $token;
            } elseif (in_array(
                $token[ 0 ],
                array( T_COMMENT, T_DOC_COMMENT)
            )
            ) {
                $output .= str_repeat("\n", substr_count($token[ 1 ], "\n"));
            } elseif (T_WHITESPACE === $token[ 0 ]) {
                $whitespace = preg_replace('{[ \t]+}', ' ', $token[ 1 ]);
                $whitespace =
                    preg_replace('{(?:\r\n|\r|\n)}', "\n", $whitespace);
                $whitespace = preg_replace('{\n +}', "\n", $whitespace);
                $output .= $whitespace;
            } else {
                $output .= $token[ 1 ];
            }
        }

        return $output;
    }

    protected function getStub()
    {
        return $stub = <<<'EOF'
#!/usr/bin/env php
<?php
/**
 * @license  http://www.apache.org/licenses/LICENSE-2.0
 *           Copyright [2012] [Robert Allen]
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @category Swagger
 * @package  Swagger
 */
Phar::mapPhar('swagger.phar');

require 'phar://swagger.phar/bin/swagger';

__HALT_COMPILER();
EOF;
    }
}
