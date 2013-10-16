=========================
Using Swagger-PHP
=========================

Generating the *Swagger Documentation* can be performed in multiple ways.Depending on your workflow the method you
choose may vary. Large projects will want to compile the documentation at deployment, while conversely in an development
environment you may choose to generate on demand. Within these constraints there is again more than one means by which
to produce the documents in your project, you may choose to create your own tooling that utilizes the \Swagger\Swagger
class and control your filtering options on demand. While finally the alternative to this is to utilize the CLI swagger.phar
to produce your *swagger documentation*,

Swagger\\Swagger
*****************

The following example will render the documentation to a web request.

.. code-block:: php

    <?php
    use Swagger\Swagger;
    $swagger = new Swagger('/project/root/top_level');
    header("Content-Type: application/json")
    echo $swagger->getResource('/pet', array('output' => 'json'));


While the CLI example will create individual json documents for each resource discovered in your project, these file are
then mappable via the `swagger-ui` of any other swagger friendly tool you wish to utilize against them.

.. code-block:: bash

    php swagger.phar /project/root/top_level -o /var/html/swagger-docs

Check the help for additional options.

.. code-block:: bash

    php swagger.phar --help