<?php
//
// Allow indentation with tab(s).
//
// http://www.doctrine-project.org/jira/browse/DCOM-255
// https://github.com/zircote/swagger-php/issues/168
// https://github.com/zircote/swagger-php/issues/203
//
// @codingStandardsIgnoreStart
//

   /**
    *
    *	@OAS\Put(
    * 		path="/users/{id}",
    * 		tags={"users"},
    * 		operationId="updateUser",
    * 		summary="Update user entry",
    * 		@OAS\Parameter(
    * 			name="id",
    * 			in="path",
    * 			required=true,
    * 			description="UUID",
    * 		),
    * 		@OAS\Parameter(
    * 			name="user",
    * 			in="cookie",
    * 			required=true,
    * 			@OAS\Schema(ref="#/definitions/User"),
    *		),
    * 		@OAS\Response(
    * 			response=200,
    * 			description="success",
    * 		),
    * 		@OAS\Response(
    * 			response="default",
    * 			description="error",
    * 			@OAS\Schema(ref="#/definitions/Error"),
    * 		),
    * 	)
    * @OAS\Options(
    * path="/users/{id}",
    * @OAS\Response(response=200,description="Some CORS stuff")
    * )
    */
   Route::put('/users/{user_id}', 'UserController@update');

	/**
	 *
	 * 	@OAS\Delete(
	 * 		path="/users/{id}",
	 * 		tags={"users"},
	 * 		operationId="deleteUser",
	 * 		summary="Remove user entry",
	 * 		@OAS\Parameter(
	 * 			name="id",
	 * 			in="path",
	 * 			required=true,
	 * 			description="UUID",
	 * 		),
	 * 		@OAS\Response(
	 * 			response=200,
	 * 			description="success",
	 * 		),
	 * 		@OAS\Response(
	 * 			response="default",
	 * 			description="error",
	 * 			@OAS\Schema(ref="#/definitions/Error"),
	 * 		),
	 * 	)
	 *
	 */
	Route::delete('/users/{user_id}', 'UserController@destroy');

      /**
      *@OAS\Head(path="/users/{id}",@OAS\Response(response=200,description="Only checking if it exists"))
      */
     Route::get('/users/{user_id}', 'UserController@show');

/**
 * @OAS\Schema(schema="Error")
 * @OAS\Schema(schema="User")
 */
//
// @codingStandardsIgnoreEnd
//
