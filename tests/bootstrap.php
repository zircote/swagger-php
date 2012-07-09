<?php
/**
 *
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * Copyright [2012] [Robert Allen]
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
 */
// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../'));
// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'testing'));

//require_once dirname(__DIR__) . '/vendor/autoload.php';

set_include_path(get_include_path() . PATH_SEPARATOR . APPLICATION_PATH . '/library');

require_once 'Swagger/Swagger.php';
require_once 'Swagger/AbstractEntity.php';
require_once 'Swagger/Resource.php';
require_once 'Swagger/Api.php';
require_once 'Swagger/Models.php';
require_once 'Swagger/Model.php';
require_once 'Swagger/Operation.php';
require_once 'Swagger/Param.php';

require_once 'fixtures/controllers/LeadResponder/RoutesController.php';
require_once 'fixtures/controllers/LeadResponder/RoutesIdController.php';
require_once 'fixtures/models/LeadResponder/Route.php';
require_once 'fixtures/models/LeadResponder/RouteCollection.php';
