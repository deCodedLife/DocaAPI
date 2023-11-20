<?php
    
    /**
     * @file
     * Авторизовывает сотрудника в API.
     * Требуется для проведения остальных запросов.
     */



    /**
     * Подключение модулей
     */
    
    require_once( PATH_MODULES . "/db.php" );
    require_once( PATH_MODULES . "/employees.php" );
    
    require_once( PATH_MODULES . "/history.php" );
    
    
    
    /**
     * Проверка. Переданны ли все обязательные параметры
     */
    if ( !$request->data->email || !$request->data->password )
        returnResponse( "Email or password was empty", 400 );
    
    
    
    /**
     * Авторизация пользователя в системе
     */
    if ( !$Employees->signIn( $request->data->email, $request->data->password ) ) {
        
        returnResponse( "Bad email or password", 403 );
        
    } else {
        
        /**
         * Формирование токена для JWT авторизации
         */
        $token = [
            
            "id"      => $Employees->employeeInfo[ "id" ],
            "ip"      => $_SERVER[ "REMOTE_ADDR" ],
            "email"   => $Employees->employeeInfo[ "email" ],
            "role_id" => $Employees->employeeInfo[ "role_id" ]
            
        ]; // $token
        
        
        
        /**
         * JWT авторизация
         */
        $jwt_authorization = $JWT::encode( $token, $jwt[ "key" ] );
        
        
        
        /**
         * Добавление лога
         */
        
        $employeeDetail = $Employees->getEmployeeDetailById( $Employees->employeeInfo[ "id" ] );
        
        $History->addLog(
            "employees", "Сотрудник " . $employeeDetail[ "first_name" ] . " " . $employeeDetail[ "last_name" ] .
            " авторизовался в системе", "info", $userInfo->id, null, null, $employeeDetail[ "hospital_id" ]
        );
        
        
        
        returnResponse( $jwt_authorization, 200 );
        
    } // if. signIn