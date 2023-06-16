<?php
    
    /**
     * @file
     * Вывод сотрудников
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
     * Получение сотрудников
     */
    $employees = $Employees->get(
        $request->data->id,
        $request->data->role_id,
        $request->data->hospital_id,
        $request->data->group_id,
        $request->data->profession_id,
        $request->data->per_page,
        $request->data->page,
        $request->data->order_by,
        $request->data->sort,
        $request->data->ip_phone_login
    );
    
    if ( $employees === false ) return returnResponse( "Something was wrong", 500 );

    return returnResponse( $employees, 200 );