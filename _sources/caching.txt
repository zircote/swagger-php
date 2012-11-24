******************
Caching
******************

The Cache
**************

By default Swagger-PHP utilizes the ``Doctrine\Common\Cache\ArrayCache`` to minimize system impact and waste of resources.
This results in a single discovery of the class hierarchy once per request. This however is less than optimal in the even you
intend to make such discoveries dynamic and in a production environment. With this in mind you may utilize the various
other caching classes Doctrine provides or extend the ``Doctrine\Common\Cache\CacheProvider`` to implement your own. In
any event this document will serve as an overview of the basic yet effective caching services that are packaged with
swagger-php.


Cache Related Methods
*********************

    - ``Swagger::flushCache()`` this method will flush the cache and trigger a new discovery of the current path parameter swagger-php was instantiated with.
    - ``Swagger::getCache()`` returns the current cache object.
    - ``Swagger::setCache()`` set/replace the current cache object with a new one.
    - ``Swagger::$cacheKey`` this protected key is comprised of a ``sha1`` of the project directory and exclude path concatenated.


Explicit Cache Declaration example:
******************************************


.. code-block:: php

    <?php
    use \Doctrine\Common\Cache\PhpFileCache;

    $swagger = new \Swagger\Swagger($projectDirectory, null, new PhpFileCache($cacheDir, '.cache'));

    $registry = $swagger->getRegistry();


Implicit Cache example:
******************************************


.. code-block:: php

    <?php
    $swagger = new \Swagger\Swagger($projectDirectory, null);
    $registry = $swagger->getRegistry();


Replacing the Cache
******************************************


.. code-block:: php

    <?php
    use \Doctrine\Common\Cache\PhpFileCache;
    use \Doctrine\Common\Cache\RedisCache;
    $swagger = new \Swagger\Swagger($projectDirectory, null, new PhpFileCache($cacheDir, '.cache'));

    $registry = $swagger->getRegistry();

    $swagger->setCache(new RedisCache());
    $registry = $swagger->getRegistry();


Accessing the Cache directly
******************************************


.. code-block:: php

    <?php
    $swagger = new \Swagger\Swagger('/projectDirectory', null);
    var_dump(swagger->getCache()->has(sha1($projectDirectory))); // bool(True)
