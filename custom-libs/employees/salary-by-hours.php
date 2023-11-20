<?php

/**
* @file
* Подсчет зарплаты по часам
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
* Проверка. Переданы ли все обязательные параметры
*/
if ( !$request->data->employee_id ) returnResponse( "Bad request", 400 );
if ( !$request->data->month ) returnResponse( "Bad Request", 400 );

 // returnResponse( $salary );

/**
 * Подсчитываем зарплату
 */

try {
    $salary = $Employees->getSalaryByHour( $request->data->employee_id, $request->data->month );
    //$salary = bcdiv($salary);


    returnResponse( $salary );
} catch (Exception $e) {
    returnResponse( $e->getMessage(), 500 );
}