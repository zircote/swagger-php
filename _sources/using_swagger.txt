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

    php swagger.phar /projects/my_project  -o /var/html/swagger-docs

Check the help for additional options.

.. code-block:: bash

    php swagger.phar --help


Via PHP
*****************

The following example will generate and output the documentation of the "/pet" resource.

.. code-block:: php

    <?php
    use Swagger\Swagger;
    $swagger = new Swagger('/projects/my_project');
    header('Content-Type: application/json');
    echo $swagger->getResource('/pet', array('output' => 'json'));

