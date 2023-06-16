<?php
    
    /**
     * @file
     * Редактирование профессии сотрудника
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
    if ( $Employees->validatePermission( "profession-change", $userInfo->role_id ) !== true ) returnResponse( "Access denied", 403 );
    
    
    
    /**
     * Проверка. Переданны ли все обязательные параметры
     */
    if ( !$request->data->id || !$request->data->title ) returnResponse( "Bad request", 400 );
    
    
    
    /**
     * Редактирование роли сотрудника
     */
    if ( !$Employees->changeProfession( 
        $request->data->id, $request->data->title )
    ) returnResponse( "Something was wrong", 500 );
    
    
    
    /**
     * Добавление лога
     */
    
    $employeeDetail = $Employees->getEmployeeDetailById( $userInfo->id );
    
    $History->addLog(
        "professions", "Сотрудник ( " . $employeeDetail[ "first_name" ] . " " . $employeeDetail[ "last_name" ] .
        " ) редактировал профессию ( " . $request->data->title . " )", "info", 
        $userInfo->id, null, $request->data->id, $employeeDetail[ "hospital_id" ]
    );
    
    
    
    returnResponse( true, 200 );