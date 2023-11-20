<?php

/**
 * @file
 * Создание штрафа
 */



/**
 * Подключение модулей
 */

require_once( PATH_MODULES . "/db.php" );
require_once( PATH_MODULES . "/alerts.php" );
require_once( PATH_MODULES . "/employees.php" );
require_once( PATH_MODULES . "/fines.php" );

require_once( PATH_MODULES . "/history.php" );



/**
 * Проверка. Авторизован ли пользователь
 */
$userInfo = validateJWT( $JWT, $request->jwt, $jwt[ "key" ] );
if ( !$userInfo ) returnResponse( "Authorization required", 401 );

/**
 * Проверка. Есть ли у пользователя права на совершение данного действия
 */
if ( $Employees->validatePermission( "fine-add", $userInfo->role_id ) !== true ) returnResponse( "Access denied", 403 );



/**
 * Проверка. Переданны ли все обязательные параметры
 */
if ( !$request->data->employee_id ) returnResponse( "Bad request", 400 );
if ( !$request->data->amount ) returnResponse( "Bad request", 400 );



/**
 * Создание штрафа
 */


$fineId = $Fines->addFine(
    $userInfo->id,
    $request->data->employee_id,
    $request->data->amount,
    $request->data->comment
);

if ( $fineId === false) returnResponse( "Something was wrong", 500 );



/**
 * Добавление лога
 */

$employeeDetail = $Employees->getEmployeeDetailById( $userInfo->id );

$History->addLog(
    "fine", "Сотрудник ( " . $employeeDetail[ "first_name" ] . " " . $employeeDetail[ "last_name" ] .
    " ) добавил штраф сотруднику ( " . $request->data->employee_id . " )", "info",
    $userInfo->id, null, $fineId, $employeeDetail[ "hospital_id" ]
);

/**
 * Добавление уведомлений
 */
$Alerts->add( "Штраф " . $request->data->amount . "руб: " . $request->data->comment, "info", "", [ $request->data->employee_id ] );
    
returnResponse( true, 200 );