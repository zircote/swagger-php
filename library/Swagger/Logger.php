<?php
namespace Swagger;

/**
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 *             Copyright [2013] [Robert Allen]
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
 * @category   Swagger
 * @package    Swagger
 */
/**
 * @category   Swagger
 * @package    Swagger
 */
class Logger
{
    /**
     * Singleton
     * @var Logger
     */
    public static $instance;

    /**
     * @var Closure
     */
    public $log;

    protected function __construct()
    {
        /**
         * @param \Exception|string $entry
         * @param int Error type
         */
        $this->log = function ($entry, $type) {
            if ($entry instanceof \Exception) {
                $entry = $entry->getMessage();
            }
            trigger_error($entry, $type);
        };
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Logger();
        }
        return self::$instance;
    }

    /**
     * Log a Swagger warning.
     * @param \Exception|string $entry
     */
    public static function warning($entry)
    {
        call_user_func(self::getInstance()->log, $entry, E_USER_WARNING);
    }

    /**
     * Log a Swagger notice.
     * @param \Exception|string $entry
     */
    public static function notice($entry)
    {
        call_user_func(self::getInstance()->log, $entry, E_USER_NOTICE);
    }
}
