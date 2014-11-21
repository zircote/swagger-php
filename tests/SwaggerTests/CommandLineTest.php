<?php

namespace SwaggerTests;

/**
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 *             Copyright [2014] [Robert Allen]
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
 * @category   SwaggerTests
 * @package    Swagger
 * @subpackage UnitTests
 */
use Swagger\Swagger;

class CommandLineTest extends SwaggerTestCase
{

    public function testBasicUsage()
    {
        $swagger = new Swagger($this->examplesDir('Petstore'));
        $tmpDir = sys_get_temp_dir();
        $command = dirname(dirname(__DIR__)) . '/bin/swagger';
        // `swagger Examples/Petstore --output /tmp`
        shell_exec(escapeshellcmd($command) . ' ' . escapeshellarg($this->examplesDir('Petstore')) . ' --output ' . escapeshellarg($tmpDir));
        foreach (array('user', 'pet', 'store') as $record) {
            $json = $swagger->getResource('/' . $record, array('output' => 'json'));
            $filename = $tmpDir . DIRECTORY_SEPARATOR . $record . '.json';
            $this->assertOutputEqualsJson($filename, $json);
            unlink($filename);
        }
        unlink($tmpDir . DIRECTORY_SEPARATOR . 'api-docs.json');
    }

}
