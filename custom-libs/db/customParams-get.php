<?php
    
    /**
     * @file
     * Вывод кастомных параметров
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
     * Проверка. Есть ли у пользователя права на совершение данного действия
     */
    if ( $Employees->validatePermission( "customParams-get", $userInfo->role_id ) !== true ) returnResponse( "Access denied", 403 );
    
    
    
    /**
     * Получение кастомных параметров
     */
    $customParams = $DB->getCustomParams(
        $request->data->id,
        $request->data->table,
        $request->data->row_id,
        $request->data->group_id,
        $request->data->order_by,
        $request->data->sort
    );
    
    if ( $customParams === false ) return returnResponse( "Something was wrong", 500 );
    
    
    
    return returnResponse( $customParams, 200 );