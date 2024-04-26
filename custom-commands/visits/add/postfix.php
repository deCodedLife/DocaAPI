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

if ( !empty( $requestData->clients_id ) ) {

    $clientDetail = $API->DB->from( "clients" )
        ->where( "id", $requestData->clients_id[ 0 ] )
        ->fetch();

    $employeeDetail = $API->DB->from( "users" )
        ->where( "id", $requestData->user_id )
        ->fetch();

    $date = date( "d.m.Y в H:i", strtotime( $requestData->start_at ) );

    $clientFio = $clientDetail[ "first_name" ];
    if ( empty( $clientDetail[ "patronymic" ] ) ) $clientFio .= " " . trim( $clientDetail[ "last_name" ] );
    else $clientFio .= " " . trim( $clientDetail[ "patronymic" ] );

    $employeeFio = $employeeDetail[ "last_name" ];
    if ( !empty( $employeeDetail[ "first_name" ] ) ) $employeeFio .= " {$employeeDetail[ "first_name" ]}";
    if ( !empty( $employeeDetail[ "patronymic" ] ) ) $employeeFio .= " {$employeeDetail[ "patronymic" ]}";

    $app_name = $API->DB->from( "stores" )->where( "id", $requestData->store_id )->fetch()[ "name" ];
    $app_address = $API->DB->from( "stores" )->where( "id", $requestData->store_id )->fetch()[ "address" ];
    $app_map = $API->DB->from( "stores" )->where( "id", $requestData->store_id )->fetch()->fetch()[ "map" ];
    $app_phone = $API->DB->from( "stores" )->where( "id", $requestData->store_id )->fetch()[ "phone" ];

    telegram\sendMessage(
        "Здравствуйте!\n\n$clientFio, Вы записаны на приём $date\n\nВрач: $employeeFio.\n\nПознакомиться с доктором предстоящего визита вы можете по ссылке: {$employeeDetail[ "site_url" ]}\n\nТел: $app_phone Адрес: $app_address\n\n$app_map\n\nДо встречи в $app_name!",
        telegram\getClient( $requestData->clients_id[ 0 ] )
    );

}