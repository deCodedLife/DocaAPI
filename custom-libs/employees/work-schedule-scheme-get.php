<?php

/**
 * @file
 * Вывод схемы графика работы сотрудника
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
 * Проверка. Переданны ли все обязательные параметры
 */
if ( !$request->data->id ) returnResponse( "Bad request", 400 );



/**
 * Получение ролей сотрудников
 */
$workSchedule = $Employees->getWorkScheduleScheme( $request->data->id );

if ( $workSchedule === false ) return returnResponse( "Something was wrong", 500 );



return returnResponse( $workSchedule, 200 );