<?php

/**
 * @file
 * Вывод графика работы сотрудника
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
 * Получение ролей сотрудников
 */
$workSchedule = $Employees->getWorkSchedule( $request->data->employee_id, $request->data->month );

if ( $workSchedule === false ) return returnResponse( "Something was wrong", 500 );

// return returnResponse( $workSchedule, 500 );

return returnResponse( $workSchedule, 200 );