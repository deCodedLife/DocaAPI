<?php

/**
 * @file
 * Вывод списка доступов
 */



/**
 * Подключение модулей
 */

require_once( PATH_MODULES . "/db.php" );



$permissions_return = [];



foreach ( $DB->makeQuery( "SELECT * FROM permissions" ) as $permission ) {

    $isCheck = false;



    /**
     * Проверка доступа у роли
     */
    if ( $request->data->role_id ) {

        /**
         * Получение id доступа
         */
        $permissionId = mysqli_fetch_array(
            $DB->makeQuery ( "SELECT * FROM `permissions` WHERE articul = '" . $permission[ "articul" ] . "' LIMIT 1" )
        );
        if ( !$permissionId ) continue;
        $permissionId = $permissionId[ "id" ];



        if ( mysqli_fetch_array( $DB->makeQuery ( "SELECT * FROM `roles-permissions` WHERE role_id = " . $request->data->role_id . " 
          AND permission_id = $permissionId LIMIT 1" ) ) ) $isCheck = true;

    } // if. $request->data->role_id


    if ( $permission[ "parent_group" ] ) {

        $permissions_return[ $permission[ "parent_group" ] ][ $permission[ "group" ] ][] = [
            "title" => $permission[ "title" ],
            "articul" => $permission[ "articul" ],
            "is_check" => $isCheck
        ];

    } else {

        $permissions_return[ $permission[ "group" ] ][] = [
            "title" => $permission[ "title" ],
            "articul" => $permission[ "articul" ],
            "is_check" => $isCheck
        ];

    } // if. $permission[ "parent_group" ]

} // foreach. $DB->makeQuery( "SELECT * FROM permissions" ) as $permission




returnResponse( $permissions_return, 200 );