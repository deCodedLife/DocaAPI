<?php
    
    /**
     * @file
     * Удаление роли сотрудника
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
    if ( $Employees->validatePermission( "role-remove", $userInfo->role_id ) !== true ) returnResponse( "Access denied", 403 );
    
    
    
    /**
     * Проверка. Переданы ли все обязательные параметры
     */
    if ( !$request->data->id ) returnResponse( "Bad request", 400 );
    
    
    
    /**
     * Создание роли сотрудника
     */
returnResponse( $Employees->removeRole( $request->data->id ), 500 );
    if ( !$Employees->removeRole( $request->data->id ) ) returnResponse( "Something was wrong", 500 );
    
    
    /**
     * Добавление лога
     */
    
    $employeeDetail = $Employees->getEmployeeDetailById( $userInfo->id );
    
    $History->addLog(
        "roles", "Сотрудник " . $employeeDetail[ "first_name" ] . " " . $employeeDetail[ "last_name" ] .
        " удалил роль №" . $request->data->id, "info",
        $userInfo->id, null, $request->data->id, $employeeDetail[ "hospital_id" ]
    );
    
    
    
    returnResponse( true, 200 );