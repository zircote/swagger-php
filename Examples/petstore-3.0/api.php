<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

/**
 * @OAS\Info(
 *     description="This is a sample Petstore server.  You can find
out more about Swagger at
[http://swagger.io](http://swagger.io) or on
[irc.freenode.net, #swagger](http://swagger.io/irc/).",
 *     version="1.0.0",
 *     title="Swagger Petstore",
 *     termsOfService="http://swagger.io/terms/",
 *     @OAS\Contact(
 *         email="apiteam@swagger.io"
 *     ),
 *     @OAS\License(
 *         name="Apache 2.0",
 *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 */
/**
 * @OAS\Tag(
 *     name="pet",
 *     description="Everything about your Pets",
 *     @OAS\ExternalDocumentation(
 *         description="Find out more",
 *         url="http://swagger.io"
 *     )
 * )
 * @OAS\Tag(
 *     name="store",
 *     description="Access to Petstore orders",
 * )
 * @OAS\Tag(
 *     name="user",
 *     description="Operations about user",
 *     @OAS\ExternalDocumentation(
 *         description="Find out more about store",
 *         url="http://swagger.io"
 *     )
 * )
 * @OAS\Server(
 *     server="swaggerHUBApi",
 *     description="SwaggerHUB API Mocking",
 *     url="https://virtserver.swaggerhub.com/swagger/Petstore/1.0.0"
 * )
 */

/**
 * @OAS\Schemes(
 *     schemes={"http", "https"}
 * )
 */
