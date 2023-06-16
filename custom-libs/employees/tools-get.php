<?php

/**
 * @file
 * Вывод носимого оборудования
 */



/**
 * Подключение модулей
 */

require_once( PATH_MODULES . "/db.php" );
require_once( PATH_MODULES . "/employees.php" );



/**
 * Проверка. Авторизован ли пользователь
 */
$userInfo = validateJWT( $JWT, $request->jwt, $jwt[ "key" ] );
if ( !$userInfo ) returnResponse( "Authorization required", 401 );



/**
 * Получение носимого оборудования
 */
$tools = $Employees->getTools( $request->data->id );

if ( $tools === false ) return returnResponse( "Something was wrong", 500 );



return returnResponse( $tools, 200 );