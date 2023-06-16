<?php
    
    /**
     * @file
     * Исключение кастомного параметра из группы
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
    if ( $Employees->validatePermission( "customParam-change", $userInfo->role_id ) !== true ) returnResponse( "Access denied", 403 );
    
    
    
    /**
     * Проверка. Переданны ли все обязательные параметры
     */
    if ( !$request->data->id || !$request->data->group_id ) returnResponse( "Bad request", 400 );
    
    
    
    /**
     * Исключение кастомного параметра из группы
     */
    if ( !$DB->customParam_groupLogOut(
        $request->data->id,
        $request->data->group_id
    ) ) returnResponse( "Something was wrong", 500 );
    
    
    
    return returnResponse( true, 200 );