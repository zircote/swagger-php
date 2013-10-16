******************
Annotations
******************

.. warning:: Using annotation in swagger-php before 0.7.3 require a ``use`` statement. Later versions register the ``@SWG\`` for annotations without a use statement.

Tip! Mistype an attribute on purpose and a warning will be shown with the available attributes for that particular annotation.

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
         * @SWG\Property(name="tags",type="array", items="$ref:Tag")
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
          - ``@SWG\Parameters``
            - ``@SWG\Parameter``
          - ``@SWG\ResponseMessages``
            - ``@SWG\ResponseMessage``
          
 - ``@SWG\Model``
   - ``@SWG\Property``
     - ``@SWG\Items``

Container annotations like ``@SWG\Operations``, ``@SWG\Parameters`` and ``@SWG\ResponseMessages`` are optional. 

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
     *   @SWG\Operation(..., 
     *      @SWG\Parameter(...),
     *      @SWG\ResponseMessage(...),
     *      @SWG\ResponseMessage(...)
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

ResponseMessage
******************

**Attributes**

- ``code``
- ``message``

**Example Annotations**

.. code-block:: php

    use Swagger\Annotations as SWG;

    /**
     * @SWG\ResponseMessage(code=404, message="Pet not found")
     */

**Derived JSON**

.. code-block:: javascript


    "responseMessages":[
        {
            "code":404,
            "message":"Pet not found"
        }
    ]

**Allowable Use:**
    - Enclosed within `ResponseMessages`_ or `Operation`_

ResponseMessages
******************

**Attributes**

- `ResponseMessage`_

**Example Annotations**

.. code-block:: php

    use Swagger\Annotations as SWG;

    /**
     * @SWG\ResponseMessages(@SWG\ResponseMessage(...)[ @SWG\ResponseMessage(...), ])
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
         * @SWG\Property(name="photoUrls",type="array",@SWG\Items("string"))
         */
        public $photos;

        /**
         * @SWG\Property(name="tags",type="array",@SWG\Items("Tag"))
         */
        public $tags;

        }


**Derived JSON**

.. code-block:: javascript

    "properties":{
        "tags":{
            "items":{
                "$ref":"Tag"
            },
            "type":"array"
        },
        "id":{
            "type":"integer",
            "format: "int64"
        },
        "category":{
            "type":"Category"
        },
        "status":{
            "enum":["available", "pending", "sold"]
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
            "type":"array"
        }
    }

**Allowable Use:**
    - Enclosed within: `Property`_ or `Model`_

Model
******************

.. note:: The annotations parser will follow any `extend` statements of the current model class and include annotations from the base class as well, as long as the ``Model`` annotation is placed into the comment block directly above the class declaration. Be sure also to activate the parser in the base class with the appropriate annotations.

**Attributes**

- ``id`` the formal name of the Model being described. Defaults to the name of the class (excl. namespace).
- ``required`` the required properties. Example: required="['id','name']"

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

Operation
******************

**Attributes**

- ``method`` GET|POST|DELETE|PUT|PATCH etc
- ``summary`` string
- ``notes`` string
- ``type`` the `Model`_ ID returned
- ``nickname`` string
- `ResponseMessages`_
- `Parameters`_

**Example Annotations**

.. code-block:: php

    use Swagger\Annotations as SWG;

    /**
     * @SWG\Operation(
     *     method="GET", summary="Find pet by ID", notes="Returns a pet based on ID",
     *     type="Pet", nickname="getPetById", ...
     * )
     */

**Derived JSON**

.. code-block:: javascript

    {
        "method":"GET",
        "summary":"Find pet by ID",
        "notes":"Returns a pet based on ID",
        "type":"Pet",
        "nickname":"getPetById",
        "parameters":[...],
        "responseMessages":[...]
    }

**Allowable Use:**

    - Enclosed within: `Operations`_ or `Api`_

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
- ``type`` scalar or Model|object
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
     *           type="string"
     *         )
     */

**Derived JSON**

.. code-block:: javascript

    {
        "name":"petId",
        "description":"ID of pet that needs to be fetched",
        "paramType":"path",
        "allowMultiple":false,
        "type":"string"
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
     *      enum="['available', 'pending', 'sold']",
     *      description="pet status in the store")
     */
     public $status;

**Derived JSON**

.. code-block:: javascript

    "category":{
        "type":"Category"
    },
    "status":{
        "enum":["available", "pending", "sold"],
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
