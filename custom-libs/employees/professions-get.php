<?php
    
    /**
     * @file
     * Вывод профессий сотрудников
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
     * Получение профессий сотрудников
     */
    $professions = $Employees->getProfessions( $request->data->id, $request->data->order_by, $request->data->sort );
    
    if ( $professions === false ) return returnResponse( "Something was wrong", 500 );
    
    
    
    return returnResponse( $professions, 200 );