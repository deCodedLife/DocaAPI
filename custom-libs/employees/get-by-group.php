<?php
    
    /**
     * @file
     * Вывод сотрудников по группе
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
    if ( !$request->data->id ) returnResponse( "Bad request", 400 );
    
    
    
    /**
     * Получение сотрудников
     * @todo Сделать модуль
     */
    $employees = $Employees->getByGroup( $request->data->id, $request->data->order_by, $request->data->sort );
    
    if ( $employees === false ) return returnResponse( "Something was wrong", 500 );
    
    
    
    return returnResponse( $employees, 200 );