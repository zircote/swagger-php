=========================
Using Swagger-PHP
=========================

Generating the *Swagger Documentation* can be performed in multiple ways.

Depending on your workflow the method you choose may vary. 
Generally you'll want to generate the documentation on-the-fly in development and 
generate static json files in production.

Via Command Line
*****************

The CLI will create json files for each resource discovered in your project.
To make them accessable to `swagger-ui` they must be placed onto an webserver.

.. code-block:: bash

    php vendor/zircote/swagger-php/bin/swagger /projects/my_project  -o /var/html/swagger-docs

Check the help for additional options.

.. code-block:: bash

    php vendor/zircote/swagger-php/bin/swagger --help


Via PHP
*****************

The following example will generate and output the documentation for all documentation within the `/path/to/project` path

.. code-block:: php

    <?php
    require("vendor/autoload.php");
    $swagger = \Swagger\scan('/path/to/project');
    header('Content-Type: application/json');
    echo $swagger;

