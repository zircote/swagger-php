=========================
Using Swagger-PHP
=========================

Generating the *Swagger Documentation* can be performed in multiple ways.Depending on your workflow the method you
choose may vary. Large projects will want to compile the documentation at deployment, while conversely in an development
environment you may choose to generate on demand. Within these constraints there is again more than one means by which
to produce the documents in your project, you may choose to create your own tooling that utilizes the \Swagger\Swagger
class and control your filtering options on demand. While finally the alternative to this is to utilize the CLI swagger.phar
to produce your *swagger documentation*,


CLI
***************



\Swagger\Swagger
*******************

The following example will render the documentation to a web request.

.. code-block:: php

    <?php
    use Swagger\Swagger;
    $path = '/project/root/top_level';
    $swagger = Swagger::discover($path);
    header("Content-Type: application/json")
    echo $swagger->jsonEncode($swagger->registry['/pet']);


While the CLI example will create individual json documents for each resource discovered in your project, these file are
then mappable via the `swagger-ui` of any other swagger friendly tool you wish to utilize against them.

.. code-block:: bash

    php swagger.phar -p /project/root/top_level \
      -o /var/html/swagger-docs -f \
      --include-path Zend:/usr/local/shar/pear,Rediska:/usr/local/share/pear
