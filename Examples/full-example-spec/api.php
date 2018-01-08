<?php
/**
 * @OAS\Info(
 *     title="OpenAPI full spec example",
 *     version="1.0.0",
 *     description="Full example openapi",
 *     termsOfService="http://openapi-example.com/terms-of-service",
 *     @OAS\Contact(
 *         name="Test Dev 1",
 *         url="https://openapi-example.com/test-dev-1",
 *         email="test-dev-1@openapi-example.com",
 *     ),
 *     @OAS\License(
 *         name="Apache 2.0",
 *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 * @see https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#infoObject
 * @see https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#contactObject
 * @see https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#licenseObject
 */

/**
 * @OAS\Server(
 *     server="Test server",
 *     url="http://openapi-example.com",
 *     description="Example server",
 *     @OAS\ServerVariable(
 *         serverVariable="test-variable",
 *         default="test",
 *         description="Test server variable",
 *     ),
 *     @OAS\ServerVariable(
 *         serverVariable="enum-test-variable",
 *         default="test",
 *         enum={"test", "example", "openapi"},
 *         description="Test server variable",
 *     )
 * )
 */

/**
 * @OAS\Tag(
 *     description="Test first tag",
 *     name="first-tag",
 *     @OAS\ExternalDocumentation(
 *         description="First Test external documentation",
 *         url="http://openapi-example.com/docs/first"
 *     )
 * )
 * @OAS\Tag(
 *     description="Test second tag",
 *     name="second-tag",
 *     @OAS\ExternalDocumentation(
 *         description="Second Test external documentation",
 *         url="http://openapi-example.com/docs/second"
 *     )
 * )
 */

/**
 * @OAS\SecurityScheme(
 *     name="basicAuth",
 *     securityScheme="basicAuth",
 *     description="Basic auth annotation",
 *     type="http",
 *     scheme="basic"
 * )
 */

/**
 * @OAS\SecurityScheme(
 *     name="apiKeyAuth",
 *     securityScheme="apiKeyAuth",
 *     description="Api key auth in query annotation",
 *     type="apiKey",
 *     scheme="apiKey",
 *     in="query"
 * )
 */

/**
 * @OAS\SecurityScheme(
 *     name="apiKeyHeaderAuth",
 *     securityScheme="apiKeyHeaderAuth",
 *     description="Api key auth in header annotation",
 *     type="apiKey",
 *     scheme="apiKey",
 *     in="header"
 * )
 */

/**
 * @OAS\SecurityScheme(
 *     name="apiKeyCookieAuth",
 *     securityScheme="apiKeyCookieAuth",
 *     description="Api key auth in cookie annotation",
 *     type="apiKey",
 *     scheme="apiKey",
 *     in="cookie"
 * )
 */

/**
 * @OAS\SecurityScheme(
 *     name="bearerAuth",
 *     securityScheme="bearerAuth",
 *     description="Bearer auth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */

/**
 * @OAS\SecurityScheme(
 *     name="oauth2Flows",
 *     scheme="oauth2",
 *     securityScheme="oauth2Flows",
 *     description="OAUTH2 flows test",
 *     type="oauth2",
 *     @OAS\Flow(
 *         flow="implicit",
 *         name="implicitGrantOauth2",
 *         authorizationUrl="http://openapi-example.com/oauth2/authorize",
 *         refreshUrl="http://openapi-example.com/oauth2/refresh-token",
 *         scopes={
 *              {"default": "Default scope"},
 *              {"userEdit": "Edit user profile"},
 *              {"meOnly": "Permission for current user account info only"}
 *         }
 *     ),
 *     @OAS\Flow(
 *         flow="authorizationCode",
 *         name="authorizationCodeGrantOauth2",
 *         authorizationUrl="http://openapi-example.com/oauth2/authorize",
 *         tokenUrl="http://openapi-example.com/oauth2/access_token",
 *         refreshUrl="http://openapi-example.com/oauth2/refresh-token",
 *         scopes={
 *              {"default": "Default scope"},
 *              {"userEdit": "Edit user profile"},
 *              {"meOnly": "Permission for current user account info only"}
 *         }
 *     ),
 *     @OAS\Flow(
 *         flow="password",
 *         name="passwordGrantOauth2",
 *         tokenUrl="http://openapi-example.com/oauth2/access_token",
 *         refreshUrl="http://openapi-example.com/oauth2/refresh-token",
 *         scopes={
 *              {"default": "Default scope"},
 *              {"userEdit": "Edit user profile"},
 *              {"meOnly": "Permission for current user account info only"}
 *         }
 *     ),
 *     @OAS\Flow(
 *         flow="clientCredentials",
 *         name="clientCredentialsGrantOauth2",
 *         tokenUrl="http://openapi-example.com/oauth2/access_token",
 *         refreshUrl="http://openapi-example.com/oauth2/refresh-token",
 *         scopes={
 *              {"default": "Default scope"},
 *              {"userEdit": "Edit user profile"},
 *              {"meOnly": "Permission for current user account info only"}
 *         }
 *     ),
 * )
 */
