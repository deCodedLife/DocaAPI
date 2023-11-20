<?php
    
    /**
     * @file
     * Подсчет зарплаты
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
     * Проверка. Переданны ли все обязательные параметры
     */
    if ( !$request->data->employee_id ) returnResponse( "Bad request", 400 );
    
    
    
    /**
     * Подсчет зарплаты
     */
    $salary = $Employees->getSalary(
        $request->data->employee_id, $request->data->date_from, $request->data->date_to, $request->data->services, $request->data->group_id
    );
    
    if ( $salary === false ) return returnResponse( "Something was wrong", 500 );
    

    return returnResponse( $salary, 200 );