swagger-php
============

 - [![Master Build Status](https://secure.travis-ci.org/zircote/swagger-php.png?branch=master)](http://travis-ci.org/zircote/swagger-php) `master`
 - [![0.7.x Development Build Status](https://api.travis-ci.org/zircote/swagger-php.png?branch=0.7)](http://travis-ci.org/zircote/swagger-php) `0.7.*@dev`

Swagger-PHP is a PHP library that serves as an annotations toolkit to produce [Swagger Doc](http://swagger.wordnik.com)
it makes extensive use of the [Doctrine Common library](http://www.doctrine-project.org/projects/common.html) for
annotations support and caching.

To report issues or ask questions please feel free to submit to [Github Issues](https://github.com/zircote/swagger-php/issues)

Download / Installation
------------------------
 - pear: http://zircote.com/pear
 - [composer](http://getcomposer.org/): [zircote/swagger-php](https://packagist.org/packages/zircote/swagger-php)
 - tarball: https://github.com/zircote/swagger-php/downloads
 - Clone via git: https://github.com/zircote/swagger-php.git

Documentation
--------------
Documentation is available at http://zircote.com/swagger-php

 To submit changes, additions or updates to the documentation or swagger-php itself please fork the project and submit a pull request. Documentation resides within the `gh-pages` branch.

Features
-------------------
 - Fully compatible with the full swagger documented proposal.
 - Caching layer using the `\Doctrine\Common\Cache` library
 - Full project discovery
 - Standalone CLI phar implementation
 - Free

 More on Swagger:
  * http://swagger.wordnik.com/
  * https://github.com/wordnik/swagger-core/wiki
  * https://github.com/outeredge/SwaggerModule a ZF2 Module implementing swagger-php
