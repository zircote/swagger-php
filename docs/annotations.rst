******************
Annotations
******************


AllowableValues
******************
The `AllowableValues` annotation provides support for Enumerations as well as Range limits. This annotation may exist
in either `Parameter`_ definitions the exists within an `Operation`_ definition or as an augmentation to the
`Property`_ annotation with in models.

**Example Annotations**

.. code-block:: php

    /**
     * @allowableValues(valueType="LIST",values="['available', 'pending', 'sold']")
     *
     * @allowableValues(valueType="RANGE",min="0", max="5")
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

**Example Annotations**

.. code-block:: php

    /**
     *
     * @Api(
     *   path="/pet.{format}/{petId}",
     *   description="Operations about pets",
     *   @operations(@operation(@parameters(@parameter(...)),
     *       @errorResponses(
     *          @errorResponse(@errorResponse(...)
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

**Example Annotations**

.. code-block:: php

    /**
     * @errorResponse(code="404", reason="Pet not found")
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

**Example Annotations**

.. code-block:: php

    /**
     * @errorResponses(@errorResponse(...)[ @errorResponse(...), ])
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

**Example Annotations**

.. code-block:: php

    class Pet
    {
        /**
         * @Property(name="tags",type="array", items="$ref:Tag")
         */
        protected $tags = array();

        /**
         * @Property(name="id",type="long")
         */
        protected $id;

        /**
         * @Property(name="category",type="Category")
         */
        protected $category;

        /**
         * @Property(
         *      name="status",type="string",
         *      @allowableValues(
         *          valueType="LIST",
         *          values="['available', 'pending', 'sold']"
         *      ),
         *      description="pet status in the store")
         */
        protected $status;

        /**
         * @Property(name="name",type="string")
         */
        protected $name;

        /**
         * @Property(name="photoUrls",type="array", @items(type="string"))
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

**Example Annotations**

.. code-block:: php

    /**
     * @Model(id="Pet")
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

**Example Annotations**

.. code-block:: php

    /**
     * @operation(
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

**Example Annotations**

.. code-block:: php

    /**
     * @operations(@operation()[, @operation()])
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

**Example Annotations**

.. code-block:: php

    /**
     * @parameter(
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

**Example Annotations**

.. code-block:: php

    /**
     * @parameters(@parameter()[, @parameter()])
     */

**Derived JSON**

.. code-block:: javascript

    "parameters":[...]

**Allowable Use:**
    - `Operation`_

Property
******************

**Example Annotations**

.. code-block:: php

    /**
     * @Property(name="category",type="Category")
     */
     public $category;
     * @Property(
     *      name="status",type="string",
     *      @allowableValues(
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

**Example Annotations**

.. code-block:: php

    /**
     * @Resource(
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
