<?php

$requestData->client_id = intval( $requestData->clients_id[ 0 ] ?? 1 );

$API->DB->update( "visits" )
    ->set( "client_id", $requestData->client_id )
    ->where( "id", $insertId )
    ->execute();


if ( $requestData->phone || $requestData->last_name || $requestData->first_name || $requestData->patronymic ) {

    $clientDetail = $API->DB->from( "clients" )
        ->where( "phone", $requestData->phone )
        ->limit( 1 )
        ->fetch();

   $requestData->clients_id = [$clientDetail[ "id" ]];

}


/**
 * Отправка события об обновлении расписания
 */
$API->addEvent( "schedule" );
$API->addEvent( "day_planning" );

/**
 * Отправка уведомления
 */
$API->addNotification(
    "system_alerts",
    "Создана запись ",
    "на " . date( "H:i:s d.m.Y", strtotime( $requestData->start_at ) ),
    "info",
    $requestData->user_id
);
