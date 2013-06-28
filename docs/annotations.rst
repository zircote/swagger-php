******************
Annotations
******************

.. warning:: Any annotation you intend to implement in your resources and or models must be declared with a ``use`` statement. The ``use`` statement does not require a namespace be declared.

.. code-block:: php

    <?php

    use Swagger\Annotations as SWG;

    /**
     *
     * @SWG\Model(id="Pet")
     */
    class Pet
    {
        /**
         * @var array<Tags>
         *
         * @SWG\Property(name="tags",type="Array", items="$ref:Tag")
         */
        protected $tags = array();
    }



Annotation Hierarchy
*********************

.. code-block:: text

 - ``@SWG\Resource``
    -``@SWG\Api``
      - ``@SWG\Operations``
        - ``@SWG\Operation``
          - ``@SWG\ErrorResponses``
            - ``@SWG\ErrorResponse``
          - ``@SWG\Parameters``
            - ``@SWG\Parameter``
              - ``@SWG\AllowableValues``
 - ``@SWG\Model``
   - ``@SWG\Property``
     - ``@SWG\AllowableValues``
     - ``@SWG\Items``


AllowableValues
******************

.. note:: The `AllowableValues` annotation provides support for Enumerations as well as Range limits. This annotation may exist in either `Parameter`_ definitions within an `Operation`_ definition, or as an augmentation to the `Property`_ annotation within models.

**Attributes**
Types Supported: ``LIST`` || ``RANGE``
``LIST`` attributes:

- ``valueType``
- ``values=[json encoded enumerations]``

  - non-indexed values as a json array: ``['one','two','three']``
  - indexed values as a json object: ``{'1': 'one', '2': 'two', '3': 'three'}``

``RANGE`` attributes:

- ``valueType=RANGE``
- ``min``
- ``max``

**Example Annotations**

.. code-block:: php

    use Swagger\Annotations as SWG;

    /**
     * @SWG\AllowableValues(valueType="LIST",values="['available', 'pending', 'sold']")
     *
     * @SWG\AllowableValues(valueType="RANGE",min="0", max="5")
     */

**Derived JSON**

.. code-block:: javascript

    "allowableValues":{
        "valueType":"LIST",
        "values":["available", "pending", "sold"]
    }
    ...
    "allowableValues":{
        "valueType":"RANGE",
        "min": 0,
        "max": 5
    },

**Allowable Use:**
    - Enclosed within `Parameter`_
    - Enclosed within `Property`_

Api
******************

**Attributes**

- ``path``
- ``description``
- `Operations`_

**Example Annotations**

.. code-block:: php

    use Swagger\Annotations as SWG;

    /**
     *
     * @SWG\Api(
     *   path="/pet.{format}/{petId}",
     *   description="Operations about pets",
     *   @SWG\Operations(@SWG\Operation(@SWG\Parameters(@SWG\Parameter(...)),
     *       @SWG\ErrorResponses(
     *          @SWG\ErrorResponse(@SWG\errorResponse(...)
     *       )
     *     )
     *   )
     * )
     */

**Derived JSON**

.. code-block:: javascript

        {
            "path":"/pet.{format}/{petId}",
            "description":"Operations about pets",
            "operations":[
                ...
            ]
        }

**Allowable Use:**
    - Method Annotation

ErrorResponse
******************

**Attributes**

- ``code``
- ``reason``

**Example Annotations**

.. code-block:: php

    use Swagger\Annotations as SWG;

    /**
     * @SWG\ErrorResponse(code="404", reason="Pet not found")
     */

**Derived JSON**

.. code-block:: javascript


    "errorResponses":[
        {
            "code":400,
            "reason":"Invalid ID supplied"
        },
        {
            "code":404,
            "reason":"Pet not found"
        }
    ]

**Allowable Use:**
    - Enclosed within `ErrorResponses`_

ErrorResponses
******************

**Attributes**

- `ErrorResponse`_

**Example Annotations**

.. code-block:: php

    use Swagger\Annotations as SWG;

    /**
     * @SWG\ErrorResponses(@SWG\ErrorResponse(...)[ @SWG\ErrorResponse(...), ])
     */

**Derived JSON**

.. code-block:: javascript

    {
        "code":400,
        "reason":"Invalid ID supplied"
    },

**Allowable Use:**
    - Enclosed within: `Operation`_

Items
******************

.. note:: The ``Items`` annotation defines an array type i.e. an array of integers, strings or ``$ref`` to another model type. References are defined with a **$ref:** preamble followed by the model ID name as defined within a `Model`_ annotation. The ``@SWG\Items`` annotation resides within a `Property`_ declaration.

**Attributes**

- ``Type``

**Example Annotations**

.. code-block:: php

    use Swagger\Annotations as SWG;

    class Pet
    {
        /**
         * @SWG\Property(name="tags",type="Array", items="$ref:Tag")
         */
        protected $tags = array();

        /**
         * @SWG\Property(name="photoUrls",type="Array", @SWG\Items(type="string"))
         */
        protected $photoUrls = array();
    }


**Derived JSON**

.. code-block:: javascript

    "properties":{
        "tags":{
            "items":{
                "$ref":"Tag"
            },
            "type":"Array"
        },
        "id":{
            "type":"long"
        },
        "category":{
            "type":"Category"
        },
        "status":{
            "allowableValues":{
                "valueType":"LIST",
                "values":["available", "pending", "sold"]
            },
            "description":"pet status in the store",
            "type":"string"
        },
        "name":{
            "type":"string"
        },
        "photoUrls":{
            "items":{
                "type":"string"
            },
            "type":"Array"
        }
    }

**Allowable Use:**
    - Enclosed within: `Property`_

Model
******************

.. note:: The annotations parser will follow any `extend` statements of the current model class and include annotations from the base class as well, as long as the ``Model`` annotation is placed into the comment block directly above the class declaration. Be sure also to activate the parser in the base class with the `use` statement and appropriate annotations.

**Attributes**

- ``id`` the formal name of the Model being described.

**Example Annotations**

.. code-block:: php

    use Swagger\Annotations as SWG;

    /**
     * @SWG\Model(id="Pet")
     */
     class Pet
     {
        ...
     }

**Derived JSON**

.. code-block:: javascript

    "Pet":{
        "id":"Pet",
        "properties":{
            ...
        }

**Allowable Use:**
    - Class Annotation

Operation
******************

**Attributes**

- ``httpMethod`` GET|POST|DELETE|PUT|PATCH etc
- ``summary`` string
- ``notes`` string
- ``responseClass`` the `Model`_ ID returned
- ``nickname`` string
- ``deprecated`` boolean
- `ErrorResponses`_
- `Parameters`_

**Example Annotations**

.. code-block:: php

    use Swagger\Annotations as SWG;

    /**
     * @SWG\Operation(
     *     httpMethod="GET", summary="Find pet by ID", notes="Returns a pet based on ID",
     *     responseClass="Pet", nickname="getPetById"
     * )
     */

**Derived JSON**

.. code-block:: javascript

    {
        "httpMethod":"GET",
        "summary":"Find pet by ID",
        "notes":"Returns a pet based on ID",
        "responseClass":"Pet",
        "nickname":"getPetById",
        "parameters":[...],
        "errorResponses":[...]
    }

**Allowable Use:**

    - Enclosed within: `Operations`_

Operations
******************

A container of one or more `Operation`_ s

**Attributes**

- `Operation`_

**Example Annotations**

.. code-block:: php

    use Swagger\Annotations as SWG;

    /**
     * @SWG\Operations(@SWG\Operation()[, @SWG\Operation()])
     */

**Derived JSON**

.. code-block:: javascript

    "operations":[
        { ... }, {...}
    ]

**Allowable Use:**
    - Enclosed within: `Api`_

Parameter
******************

**Attributes**

- ``name``
- ``description``
- ``paramType`` body|query|path
- ``required`` bool
- ``allowMultiple`` bool
- ``dataType`` scalar or Model|object
- ``defaultValue``

**Example Annotations**

.. code-block:: php

    use Swagger\Annotations as SWG;

    /**
     * @SWG\Parameter(
     *           name="petId",
     *           description="ID of pet that needs to be fetched",
     *           paramType="path",
     *           required="true",
     *           allowMultiple="false",
     *           dataType="string"
     *         )
     */

**Derived JSON**

.. code-block:: javascript

    {
        "name":"petId",
        "description":"ID of pet that needs to be fetched",
        "paramType":"path",
        "required":true,
        "allowMultiple":false,
        "dataType":"string"
    }

**Allowable Use:**

    - `Parameters`_

Parameters
******************

A collection of one or more `Parameter`_ s

**Attributes**

- `Parameter`_

**Example Annotations**

.. code-block:: php

    use Swagger\Annotations as SWG;

    /**
     * @SWG\Parameters(@SWG\Parameter()[, @SWG\Parameter()])
     */

**Derived JSON**

.. code-block:: javascript

    "parameters":[...]

**Allowable Use:**

    - `Operation`_

Property
******************

**Attributes**

- ``name``
- ``type``
- ``description``
- `Items`_

**Example Annotations**

.. code-block:: php

    use Swagger\Annotations as SWG;

    /**
     * @SWG\Property(name="category",type="Category")
     */
     public $category;
     * @SWG\Property(
     *      name="status",type="string",
     *      @SWG\AllowableValues(
     *          valueType="LIST",
     *          values="['available', 'pending', 'sold']"
     *      ),
     *      description="pet status in the store")
     */
     public $status;

**Derived JSON**

.. code-block:: javascript

    "category":{
        "type":"Category"
    },
    "status":{
        "allowableValues":{
            "valueType":"LIST",
            "values":["available", "pending", "sold"]
        },
        "description":"pet status in the store",
        "type":"string"
    },

**Allowable Use:**
    - Property Annotation

Resource
******************

**Attributes**

- ``apiVersion`` the version this api is being rendered as
- ``swaggerVersion`` the swagger-docs version being rendered ``2.0``
- ``resourcePath`` the HTTP URI path for the resource
- ``basePath`` the service root HTTP URI path

**Example Annotations**

.. code-block:: php

    use Swagger\Annotations as SWG;

    /**
     * @SWG\Resource(
     *     apiVersion="0.2",
     *     swaggerVersion="1.1",
     *     resourcePath="/pet",
     *     basePath="http://petstore.swagger.wordnik.com/api"
     * )
     */

**Derived JSON**

.. code-block:: javascript

    {
        "apiVersion":"0.2",
        "swaggerVersion":"1.1",
        "basePath":"http://petstore.swagger.wordnik.com/api",
        "resourcePath":"/pet",
        "apis":[...],
        "models": [...]
    }

**Allowable Use:**
    - Class Annotation
