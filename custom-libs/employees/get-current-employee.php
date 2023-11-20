<?php
	
	/**
	 * @file
	 * Вывод информации о текущем сотруднике
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
	 * Получение сотрудника
	 */
	$employees = $Employees->get(
	    $userInfo->id,
        null,
        null,
        null,
        null,
        null,
        null,
        null,
        null,
        null,
        true
    )[ 0 ];
	
	
	
	if ( $employees === false ) return returnResponse( "Something was wrong", 500 );
	
	return returnResponse( $employees, 200 );