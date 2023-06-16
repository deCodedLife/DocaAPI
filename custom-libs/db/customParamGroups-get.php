<?php
    
    /**
     * @file
     * Вывод групп кастомных параметров
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
    if ( $Employees->validatePermission( "customParamGroups-get", $userInfo->role_id ) !== true ) returnResponse( "Access denied", 403 );
    
    
    
    /**
     * Получение групп кастомных параметров
     */
    $customParamGroups = $DB->getCustomParamGroups(
        $request->data->id, $request->data->order_by, $request->data->sort
    );
    
    if ( $customParamGroups === false ) return returnResponse( "Something was wrong", 200 );
    
    
    
    return returnResponse( $customParamGroups, 200 );