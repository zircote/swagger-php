{
    "swagger": "2.0",
    "info": {
        "title": "Example of using references in swagger-php",
        "version": "1.0.0"
    },
    "paths": {
        "/products/{product_id}": {
            "get": {
                "tags": [
                    "Products"
                ],
                "responses": {
                    "default": {
                        "$ref": "#/responses/product"
                    }
                }
            },
            "patch": {
                "tags": [
                    "Products"
                ],
                "parameters": [
                    {
                        "$ref": "#/parameters/product_in_body"
                    }
                ],
                "responses": {
                    "default": {
                        "$ref": "#/responses/product"
                    }
                }
            },
            "parameters": [
                {
                    "$ref": "#/parameters/product_id_in_path_required"
                }
            ]
        },
        "/products": {
            "post": {
                "tags": [
                    "Products"
                ],
                "parameters": [
                    {
                        "$ref": "#/parameters/product_in_body"
                    }
                ],
                "responses": {
                    "default": {
                        "$ref": "#/responses/product"
                    }
                }
            }
        }
    },
    "definitions": {
        "Product": {
            "properties": {
                "id": {
                    "description": "The unique identifier of a product in our catalog.",
                    "type": "integer",
                    "format": "int64"
                },
                "status": {
                    "$ref": "#/definitions/product_status"
                }
            }
        },
        "product_status": {
            "description": "The status of a product",
            "type": "string",
            "default": "available",
            "enum": [
                "available",
                "discontinued"
            ]
        }
    },
    "parameters": {
        "product_id_in_path_required": {
            "name": "product_id",
            "in": "path",
            "description": "The ID of the product",
            "required": true,
            "type": "integer",
            "format": "int64"
        },
        "product_in_body": {
            "name": "product",
            "in": "body",
            "schema": {
                "$ref": "#/definitions/Product"
            }
        }
    },
    "responses": {
        "product": {
            "description": "All information about a product",
            "schema": {
                "$ref": "#/definitions/Product"
            }
        },
        "todo": {
            "description": "This API call has no documentated response (yet)"
        }
    }
}