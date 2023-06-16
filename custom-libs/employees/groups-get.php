<?php

/**
 * @file
 * Вывод групп сотрудников
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
 * Получение групп сотрудников
 */
$groups = $Employees->getGroups( $request->data->id, $request->data->order_by, $request->data->sort );

if ( $groups === false ) return returnResponse( "Something was wrong", 500 );



return returnResponse( $groups, 200 );