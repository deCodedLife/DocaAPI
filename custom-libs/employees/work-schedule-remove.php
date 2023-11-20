<?php

/**
 * @file
 * Удаление графика работы сотрудника
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
 * Проверка. Есть ли у пользователя права на совершение данного действия
 */
if ( $Employees->validatePermission( "employees-get", $userInfo->role_id ) !== true ) returnResponse( "Access denied", 403 );



$workSchedule = $Employees->removeWorkSchedules( $request->data->id );

if ( $workSchedule === false ) return returnResponse( "Something was wrong", 500 );



return returnResponse( $workSchedule, 200 );