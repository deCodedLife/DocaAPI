<?php

require_once( $API::$configs[ "paths" ][ "public_modules" ] . "/dom_ru.php" );


/**
 * Получение истории
 */

$IPCallsHistory = $IPCallsDomRu->getHistory();
if ( $IPCallsHistory === false ) $API->returnResponse( "Something was wrong", 500 );


/**
 * Формирование истории
 */

$IPCallsHistory = str_getcsv( $IPCallsHistory, "\n" );
foreach( $IPCallsHistory as &$row ) $row = str_getcsv( $row, "," );

foreach( $IPCallsHistory as $IPCallsHistoryRow ) {

    if ( !$IPCallsHistoryRow[ 0 ] ) continue;

    $api_id = $IPCallsHistoryRow[ 0 ];
    $status = $IPCallsHistoryRow[ 1 ];
    $client_phone = $IPCallsHistoryRow[ 2 ];
    $employee_login = $IPCallsHistoryRow[ 3 ];
    $employee_phone = $IPCallsHistoryRow[ 4 ];
    $created_at = $IPCallsHistoryRow[ 5 ];
    $wait_duration = $IPCallsHistoryRow[ 6 ];
    $call_duration = $IPCallsHistoryRow[ 7 ];
    $record_href = $IPCallsHistoryRow[ 8 ];

    if ( strpos( $employee_login, "@" ) )
        $employee_login = substr( $employee_login, 0, strpos( $employee_login, "@" ) );


    /**
     * Игнорирование дубликатов
     */

    $callFromId = $API->DB->from( "callHistory" )
        ->where( "api_id", $api_id )
        ->limit( 1 )
        ->fetch();

    if ( $callFromId ) continue;

    /**
     * Получение сотрудника
     */

    $employeeDetail = $API->DB->from( "users" )
        ->where( "domru_login", $employee_login )
        ->limit( 1 )
        ->fetch();

    if ( !$employeeDetail ) continue;


    /**
     * Получение клиента
     */

    $clientDetail = $API->DB->from( "clients" )
        ->where( "phone", $client_phone )
        ->limit( 1 )
        ->fetch();

    if ( !$clientDetail ) $clientDetail[ "id" ] = null;


    $API->DB->insertInto( "callHistory" )
        ->values( [
            "api_id" => $api_id,
            "status" => $status,
            "user_id" => $employeeDetail[ "id" ],
            "client_id" => $clientDetail[ "id" ],
            "client_phone" => $client_phone,
            "wait_duration" => $wait_duration,
            "call_duration" => $call_duration,
            "record" => $record_href,
            "created_at" => $created_at
        ] )
        ->execute();

} // foreach. $IPCallsHistory as $IPCallsHistoryRow