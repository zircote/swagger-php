<?php
//
// Allow indentation with tab(s).
// 
// http://www.doctrine-project.org/jira/browse/DCOM-255
// https://github.com/zircote/swagger-php/issues/168
// https://github.com/zircote/swagger-php/issues/203
 
   /**
    *
    *	@SWG\Put(
    * 		path="/users/{id}",
    * 		tags={"users"},
    * 		operationId="updateUser",
    * 		summary="Update user entry",
    * 		@SWG\Parameter(
    * 			name="id",
    * 			in="path",
    * 			required=true,
    * 			type="string",
    * 			description="UUID",
    * 		),
    * 		@SWG\Parameter(
    * 			name="user",
    * 			in="body",
    * 			required=true,
    * 			@SWG\Schema(ref="#/definitions/User"),
    *		),
    * 		@SWG\Response(
    * 			status=200,
    * 			description="success",
    * 		),
    * 		@SWG\Response(
    * 			status="default",
    * 			description="error",
    * 			@SWG\Schema(ref="#/definitions/Error"),
    * 		),
    * 	)
    *
    */
   Route::put('/users/{user_id}', 'UserController@update');

	/**
	 *
	 * 	@SWG\Delete(
	 * 		path="/users/{id}",
	 * 		tags={"users"},
	 * 		operationId="deleteUser",
	 * 		summary="Remove user entry",
	 * 		@SWG\Parameter(
	 * 			name="id",
	 * 			in="path",
	 * 			required=true,
	 * 			type="string",
	 * 			description="UUID",
	 * 		),
	 * 		@SWG\Response(
	 * 			status=200,
	 * 			description="success",
	 * 		),
	 * 		@SWG\Response(
	 * 			status="default",
	 * 			description="error",
	 * 			@SWG\Schema(ref="#/definitions/Error"),
	 * 		),
	 * 	)
	 *
	 */
	Route::delete('/users/{user_id}', 'UserController@destroy');