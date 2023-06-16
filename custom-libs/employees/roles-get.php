<?php
    
    /**
     * @file
     * Вывод ролей сотрудников
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
    $roles = $Employees->getRoles( $request->data->id, $request->data->order_by, $request->data->sort );
    
    if ( $roles === false ) return returnResponse( "Something was wrong", 500 );
    
    
    
    return returnResponse( $roles, 200 );