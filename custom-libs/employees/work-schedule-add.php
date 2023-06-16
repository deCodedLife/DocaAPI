<?php

/**
 * @file
 * Добавление графика работы сотрудника
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



$workSchedule = $Employees->addWorkSchedules( $request->data->employee_id, $request->data->from, $request->data->to,
    $request->data->start, $request->data->end, $request->data->is_monday, $request->data->is_tuesday,
    $request->data->is_wednesday, $request->data->is_thursday, $request->data->is_friday, $request->data->is_saturday,
    $request->data->is_sunday, $request->data->weeks_odd, $request->data->days_odd, $request->data->exception );

if ( $workSchedule === false ) return returnResponse( "Something was wrong", 500 );



return returnResponse( $workSchedule, 200 );