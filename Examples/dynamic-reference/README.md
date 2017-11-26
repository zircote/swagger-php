# Dynamic References

*Dynamic References* import all attributes from the reference object to the specified object. 

It works similar to the `#ref` however allows for customization of properties and attributes.

The dynamic reference uses a `$` instead of `#` for the `ref` attribute.
```
    ref="$/responses/ExampleResponse"
```

### Examples


Define a default object which be our base structure.

In this case it is a response, which will contain a variable `data` property.
```php
<?php
/**
 * @SWG\Response(
 *      response="Json",
 *      description="the basic response",
 *      @SWG\Schema(
 *          @SWG\Property(
 *              type="boolean",
 *              property="success"
 *          ),
 *          @SWG\Property(
 *              property="data"
 *          ),
 *          @SWG\Property(
 *              property="errors",
 *              type="object"
 *          ),
 *          @SWG\Property(
 *              property="token",
 *              type="string"
 *          )
 *      )
 * )
 *
*/
```


Then you can extend the response in this example *POST* request by using the `$` ref.


```php
<?php
/**
 * @SWG\Post(
 *     path="/api/path",
 *     summary="Post to URL",
 *     @SWG\Parameter(
 *          name="body",
 *          in="body",
 *          required=true,
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="name",
 *                  type="string",
 *                  maximum=64
 *              ),
 *              @SWG\Property(
 *                  property="description",
 *                  type="string"
 *              )
 *          )
 *     ),
 *     @SWG\Response(
 *          response=200,
 *          description="Example extended response",
 *          ref="$/responses/Json",
 *          @SWG\Schema(
 *              @SWG\Property(
 *                  property="data",
 *                  ref="#/definitions/Product"
 *              )
 *          )
 *     ),
 *     security={{"Bearer":{}}}
 * )
 */
```

As you can see `ref="$/responses/Json` is telling it to extend the base `Json` response.

We follow the reference with a `Schema` layout which specifies that the `data` property will actually be a `Product`.

### Example Output

```json
{
  "paths": {
    "/api/path": {
      "post": {
        "parameters": [
          {
            "in": "body",
            "name": "body",
            "required": true,
            "schema": {
              "properties": {
                "description": {
                  "type": "string"
                },
                "name": {
                  "maximum": 64,
                  "type": "string"
                }
              }
            }
          }
        ],
        "responses": {
          "200": {
            "description": "Example extended response",
            "schema": {
              "properties": {
                "data": {
                  "$ref": "#/definitions/Product"
                },
                "errors": {
                  "type": "object"
                },
                "success": {
                  "type": "boolean"
                },
                "token": {
                  "type": "string"
                }
              }
            }
          }
        },
        "security": [
          {
            "Bearer": []
          }
        ],
        "summary": "Post to URL"
      }
    }
  },
  "responses": {
    "Json": {
      "description": "the basic response",
      "schema": {
        "properties": {
          "data": {},
          "errors": {
            "type": "object"
          },
          "success": {
            "type": "boolean"
          },
          "token": {
            "type": "string"
          }
        }
      }
    }
  }
}
```