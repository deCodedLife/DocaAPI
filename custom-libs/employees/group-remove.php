<?php
    
    /**
     * @file
     * Удаление группы сотрудников
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
    if ( $Employees->validatePermission( "employees_group-remove", $userInfo->role_id ) !== true ) returnResponse( "Access denied", 403 );
    
    
    
    /**
     * Проверка. Переданны ли все обязательные параметры
     */
    if ( !$request->data->id ) returnResponse( "Bad request", 400 );
    
    
    
    /**
     * Удаление группы сотрудников
     */
    if ( !$Employees->removeGroup( $request->data->id ) ) returnResponse( "Something was wrong", 500 );
    
    
    
    /**
     * Добавление лога
     */
    
    $employeeDetail = $Employees->getEmployeeDetailById( $userInfo->id );
    
    $History->addLog(
        "employee_groups", "Сотрудник ( " . $employeeDetail[ "first_name" ] . " " . $employeeDetail[ "last_name" ] .
        " ) удалил группу сотрудников ( " . $request->data->id . " )", "info", 
        $userInfo->id, null, $request->data->id, $employeeDetail[ "hospital_id" ]
    );
    
    
    
    returnResponse( true, 200 );