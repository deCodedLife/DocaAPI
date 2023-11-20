<?php

/**
 * @file
 * Проверка доступа сотрудника
 */



/**
 * Подключение модулей
 */

require_once( PATH_MODULES . "/db.php" );
require_once( PATH_MODULES . "/employees.php" );

require_once( PATH_MODULES . "/history.php" );



/**
 * Проверка. Авторизован ли пользователь
 */
$userInfo = validateJWT( $JWT, $request->jwt, $jwt[ "key" ] );
if ( !$userInfo ) returnResponse( "Authorization required", 401 );



/**
 * Проверка. Переданны ли все обязательные параметры
 */
if ( !$request->data->permission ) returnResponse( "Bad request", 400 );
if ( !$request->data->role_id ) returnResponse( "Bad request", 400 );



if ( !$Employees->validatePermission( $request->data->permission, $request->data->role_id ) ) returnResponse( "Something was wrong", 500 );

returnResponse( true, 200 );