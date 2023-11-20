<?php
    
    /**
     * @file
     * Вывод штрафов
     */



    /**
     * Подключение модулей
     */
    
    require_once( PATH_MODULES . "/db.php" );
    require_once( PATH_MODULES . "/employees.php" );
    require_once( PATH_MODULES . "/fines.php" );
    
    
    
    /**
     * Проверка. Авторизован ли пользователь
     */
    $userInfo = validateJWT( $JWT, $request->jwt, $jwt[ "key" ] );
    if ( !$userInfo ) returnResponse( "Authorization required", 401 );
    
    
    
    /**
     * Получение штрафов
     */
    $fines = $Fines->getFines( $request->data->id, $request->data->employee_id, $request->data->order_by, $request->data->sort );
    
    if ( $fines === false ) return returnResponse( "Something was wrong", 500 );
    
    
    
    return returnResponse( $fines, 200 );