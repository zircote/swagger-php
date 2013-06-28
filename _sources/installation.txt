******************
Installation
******************

Composer
**************

First acquire Composer at `<http://getcomposer.org>`_ following the installation instructions. Finally add the composer
details for swagger-php to your projects *composer.json*

.. code-block:: javascript

    "require": {
        "zircote/swagger-php": "master-dev"
    }

Finally run *composer install* to update the composer dependencies and implement the *vendor/autoload.php* in your
  projects bootstrap.

git
*************

You may also install *swagger-php* with git; this installation method will however require that you manually install
the `Doctrine Common <http://www.doctrine-project.org/projects/common.html>`_ library following the procedure they have
documented on the project page.

Add *swagger-php/library* to your include path.

.. code-block:: bash

    % git clone git@github.com:zircote/swagger-php.git swagger-php

Notes on use:
****************
For classes you with to implement *swagger-php* annotations, you must declare the annotation class in your *use* statements
such as:

.. code-block:: php

    <?php
    namespace Tardis\Models;

    use Swagger\Annotations as SWG;

    class Sidekick
    {
    ...
    }

Unless *use* statements are declared the annotations will go ignored and unparsed.

