<?php
    
    /**
     * @file
     * Создание сотрудника
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
     * Проверка. Есть ли у пользователя права на совершение данного действия
     */
    if ( $Employees->validatePermission( "employees-add", $userInfo->role_id ) !== true ) returnResponse( "Access denied", 403 );
    
    
    
    /**
     * Проверка. Переданны ли все обязательные параметры
     */
    if (
        !$request->data->first_name || 
        !$request->data->last_name || 
        !$request->data->email || 
        !$request->data->password ||
        !$request->data->role_id || 
        !$request->data->work_schedule
    ) returnResponse( "Bad request", 400 );

    /**
     * Проверка. Переданы ли в графике работ 14 чисел
     */
    if ( $request->data->is_tool ) $request->data->professions_id = [ 53 ];
    
    
    
    /**
     * Создание сотрудника
     */

    $employeeId = $Employees->signUp(
        $request->data->first_name,
        $request->data->last_name,
        $request->data->patronymic,
        $request->data->email,
        $request->data->password,
        $request->data->role_id,
        $request->data->work_schedule,
        $request->data->passport_series,
        $request->data->passport_number,
        $request->data->passport_issued,
        $request->data->snils,
        $request->data->inn,
        $request->data->address,
        $request->data->phone,
        $request->data->phone_second,
        $request->data->salary_type,
        $request->data->salary_value,
        $request->data->salary_day,
        $request->data->salary_kpi,
        $request->data->comment,
        $request->data->groups_id,
        $request->data->professions_id,
        $request->data->date_birth,
        $request->data->hospital_id,
        $request->data->is_tool,
        $request->data->ip_phone_login
    );

    if ( !$employeeId ) returnResponse( "Something was wrong", 500 );

    
    
    /**
     * Добавление лога
     */
    
    $employeeDetail = $Employees->getEmployeeDetailById( $userInfo->id );
    
    $History->addLog(
        "employees", "Сотрудник " . $employeeDetail[ "first_name" ] . " " . $employeeDetail[ "last_name" ] .
        " добавил сотрудника " . $request->data->first_name . " " . $request->data->last_name, "info",
        $userInfo->id, null, $employeeId, $employeeDetail[ "hospital_id" ]
    );
    
    
    
    returnResponse( true, 200 );