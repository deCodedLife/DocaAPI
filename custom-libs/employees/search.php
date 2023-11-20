<?php

/**
 * @file
 * Поиск сотрудников
 */



/**
 * Подключение библиотек
 */

require_once( PATH_LIBS . "/sphinx/api/sphinxapi.php" );



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
if ( !$request->data->search ) returnResponse( "Bad request", 400 );



/**
 * Получение услуг
 */
$employees = $Employees->search( $request->data->search, $request->data->is_visible_in_dashboard );

if ( $employees === false ) return returnResponse( "Something was wrong", 500 );


return returnResponse( $employees, 200 );