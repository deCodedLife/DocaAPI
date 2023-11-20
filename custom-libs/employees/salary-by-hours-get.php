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
if ( !$request->data->start ) returnResponse( "Bad request", 400 );
if ( !$request->data->end ) returnResponse( "Bad request", 400 );

$startDate =  date( 'Y-m-d', strtotime( $request->data->start ) );
$endDate = date( 'Y-m-d', strtotime( $request->data->end ) );

if ( !$startDate || !$endDate ) returnResponse( "Unavailable date format. Use Y-m-d", 400 );
if ( $startDate > $endDate ) returnResponse( "Incorrect date", 400 );

$firstMonthDay = new DateTime( "first day of " . $request->data->start );
$lastMonthDay = new DateTime( "last day of " . $request->data->start );

if ( $firstMonthDay->format( 'Y-m-d' ) != $request->data->start ) returnResponse( 0 );

$checkData = date( 'Y-m-d', strtotime( $lastMonthDay->format( 'Y-m-d' ) ) );
if ( $endDate > $checkData ) returnResponse(0);



/**
 * Подсчитываем зарплату
 */
try {

    $salary = $Employees->getSalaryByPeriod(
        $request->data->employee_id,
        $request->data->start,
        $request->data->end
    );

    $salary = round( $salary, 2);
    returnResponse( $salary );

} catch (Exception $e) {

    returnResponse( $e->getMessage(), 500 );

}