<?php
    
    /**
     * @file
     * Вывод истории
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
     * Получение истории
     */

    if ( !$request->data->order_by ) $request->data->order_by = "id";
    if ( !$request->data->sort ) $request->data->sort = "desc";

    $history = $History->history_get(
        $request->data->table,
        $request->data->row_id,
        $request->data->employee_id,
        $request->data->client_id,
        $request->data->date_from,
        $request->data->date_to,
        $request->data->hospital_id,
        $request->data->per_page,
        $request->data->page,
        $request->data->order_by,
        $request->data->sort
    );
    
    if ( $history === false ) return returnResponse( "Something was wrong", 200 );
    
    
    
    return returnResponse( $history, 200 );