<?php

/**
 * Игнорирование запросов, не относящихся к событиям
 */
if ( $requestData->cmd === "event" ) {

    /**
     * Получение сотрудника
     */

    $query = "SELECT * FROM users WHERE domru_login LIKE '%$requestData->user%' LIMIT 1";
    $result = mysqli_query( $API->DB_connection, $query );
    $result = mysqli_fetch_array( $result );

    $employeeDetail = $API->DB->from( "users" )
        ->where( "id", $result[ "id" ] )
        ->fetch();

    if ( !$employeeDetail ) $API->returnResponse( false );


    /**
     * Получение клиента
     */

    $clientDetail = $API->DB->from( "clients" )
        ->where( "phone", $requestData->phone )
        ->limit( 1 )
        ->fetch();

    if ( !$clientDetail ) $clientDetail[ "id" ] = null;


    /**
     * Удаление прошлых событий сотрудника
     */
    $API->DB->deleteFrom( "callHistory" )
        ->where( [
            "api_id" => 0,
            "user_id" => $employeeDetail[ "id" ]
        ] )
        ->execute();

    /**
     * Сохранение события
     */
    $API->DB->insertInto( "callHistory" )
        ->values( [
            "api_id" => 0,
            "status" => $requestData->type,
            "user_id" => $employeeDetail[ "id" ],
            "client_id" => $clientDetail[ "id" ],
            "client_phone" => $requestData->phone
        ] )
        ->execute();


    /**
     * Уведомления о пропущенных звонках
     */
    if ( $requestData->type === "missed" ) {

        $API->addNotification(
            "system_alerts",
            "Пропущен звонок",
            $requestData->phone,
            "info",
            $employeeDetail[ "id" ]
        );

        $API->addEvent( "notifications" );

    } // if. $requestData->type === "missed"


    if ( $clientDetail[ "id" ] ) $API->addLog( [
        "table_name" => "clients",
        "description" => "Звонок сотрудника: $requestData->user",
        "row_id" => $clientDetail[ "id" ]
    ], $requestData );
    
    $API->returnResponse( true );

} // if. $requestData->cmd === "event"