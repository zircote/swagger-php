<?php
use Swagger\Annotations as SWG;
/**
 * @SWG\Info(
 *   title="Swagger Sample App",
 *   description="This is a sample server Petstore server.  You can find out more about Swagger 
 *   at <a href=""http://swagger.wordnik.com"">http://swagger.wordnik.com</a> or on irc.freenode.net, #swagger.  For this sample,
 *   you can use the api key ""special-key"" to test the authorization filters",
 *   termsOfServiceUrl="http://helloreverb.com/terms/",
 *   contact="apiteam@wordnik.com",
 *   license="Apache 2.0",
 *   licenseUrl="http://www.apache.org/licenses/LICENSE-2.0.html"
 * )
 *
 * @SWG\Authorization(
 *   type="oauth2",
 *   @SWG\Scope(scope="write:pets", description="Modify pets in your account"),
 *   @SWG\Scope(scope="read:pets", description="Read your pets"),
 *   grantTypes={
 *     "implicit": {
 *       "loginEndpoint": { "url": "http://petstore.swagger.wordnik.com/api/oauth/dialog" },
 *       "tokenName": "access_token"
 *     },
 *     "authorization_code": {
 *       "tokenRequestEndpoint": {
 *         "url": "http://petstore.swagger.wordnik.com/api/oauth/requestToken",
 *         "clientIdName": "client_id",
 *         "clientSecretName": "client_secret"
 *       },
 *       "tokenEndpoint": {
 *         "url": "http://petstore.swagger.wordnik.com/api/oauth/token",
 *         "tokenName": "auth_code"
 *       }
 *     }
 *   } 
 * )
 */

