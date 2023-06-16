<?php
	
	/**
	 * @file
	 * Создание кастомных параметров
	 */



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
	if (
		!$request->data->title ||
		!$request->data->type ||
		!$request->data->table
	) returnResponse( "Bad request", 400 );
	
	/**
	 * Проверка. Есть ли у пользователя права на совершение данного действия
	 */
	if ( $Employees->validatePermission( "customParam-add", $userInfo->role_id ) !== true ) returnResponse( "Access denied", 403 );

	/**
	 * Проверка. Передан ли доступный тип параметра
	 */
	switch ( $request->data->type ) {
		
		case "checkbox":
		case "datetime":
		case "file":
		case "float":
		case "int":
		case "select":
		case "text":
		case "varchar":
			break;
		default:
			returnResponse( "Bad param type", 400 );
		
	} // switch. $request->data->type
	
	
	
	/**
	 * Cоздание кастомного параметра
	 */


    $paramId = $DB->makeCustomParam(
        $request->data->title,
        $request->data->type,
        $request->data->table,
        $request->data->groups
    );


	if ( $paramId === false ) returnResponse( "Something was wrong", 500 );
	
	
	
	/**
	 * Добавление лога
	 */
	
	$employeeDetail = $Employees->getEmployeeDetailById( $userInfo->id );
	
	$History->addLog(
		"settings", "Сотрудник ( " . $employeeDetail[ "first_name" ] . " " . $employeeDetail[ "last_name" ] . 
		" ) добавил кастомный параметр ( " . $request->data->title . " )", "info",
        $userInfo->id, null, $paramId, $employeeDetail[ "hospital_id" ]
	);
	
	
	
	returnResponse( true, 200 );
	