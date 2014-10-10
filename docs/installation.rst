******************
Installation
******************


Via Composer (Recommended)
**************

First download and install `Composer <http://getcomposer.org>`_ add zircote/swagger-php to your *composer.json*

.. code-block:: javascript

    "require": {
        "zircote/swagger-php": "*"
    }

Finally run *composer install* to update the composer dependencies and implement the *vendor/autoload.php* in your
  projects bootstrap.


Via Phar
**************

Download the latest `swagger.phar <https://github.com/zircote/swagger-php/raw/master/swagger.phar>`_

.. code-block:: bash

    % php swagger.phar --help


Manual (Unsupported)
**************
Download/clone the source from `GitHub <https://github.com/zircote/swagger-php/>`_, install `Doctrine Annotations <http://www.doctrine-project.org/projects/common.html>`_ and configure a PSR compatible class loader.
