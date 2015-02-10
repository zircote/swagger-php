******************
Partials
******************

To avoid duplication swagger-php introduces partials.

Defining a partial
******************

Any swagger annotation can become a partial by addding the property "partial".


.. code-block:: php

  /**
   * @SWG\Parameter(partial="path_id", name="id", paramType="path", type="integer")
   * @SWG\ResponseMessage(partial="error404", code=404, message="Entity not found")
   */

Using a partial
******************

.. code-block:: php

  /**
   * @SWG\Operation(
   *   method="GET",
   *   nickname="partialDemo"
   *   @SWG\Partial("path_id"),
   *   @SWG\Partial("error404"),
   * )
   */

Output:

.. code-block:: json

  operations: [
    {
      method: "GET",
      nickname: "partialDemo",
      parameters: [
        {
          paramType: "path",
          name: "id",
          type: "integer"
        }
      ],
      responseMessages: [
        {
          code: 404,
          message: "Entity not found"

        }
      ]
    }
  ]
