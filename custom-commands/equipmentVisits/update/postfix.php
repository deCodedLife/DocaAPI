<?php

/**
 * Обновление кеша пользователя для посещения
 */
if ( $requestData->clients_id && $requestData->id ) {

    $API->DB->update( "equipmentVisits" )
        ->set( "client_id", intval( $requestData->clients_id[ 0 ] ?? 1 ) )
        ->where( "id", $requestData->id )
        ->execute();

} // if ( $requestData->clients_id && $requestData->id )


/**
 * Отмена посещения
 */
if ( $requestData->id && $requestData->reason_id ) $API->DB->update( "equipmentVisits" )
    ->set( [
        "is_active" => "N",
        "reason_id" => $requestData->reason_id,
        "cancelledDate" => date("Y-m-d H:i:s"),
        "operator" => $API::$userDetail->id
    ] )
    ->where( [
        "id" => $requestData->id,
        "is_system" => "N"
    ] )
    ->execute();


if ( $requestData->status === "waited" ) {

    $visitDetails = $API->DB->from( "equipmentVisits" )
        ->where( "id", $requestData->id )
        ->fetch();

    $clientDetails = $API->DB->from( "clients" )
        ->innerJoin( "equipmentVisits on equipmentVisits.client_id = clients.id" )
        ->where( "equipmentVisits.id", $requestData->id )
        ->fetch();

    $clientName = $clientDetails[ "last_name" ];
    if ( $clientDetails[ "first_name" ] ) $clientName .= " " . mb_substr( $clientDetails[ "first_name" ], 0, 1 ) . ".";
    if ( $clientDetails[ "patronymic" ] ) $clientName .= " " . mb_substr( $clientDetails[ "patronymic" ], 0, 1 ) . ".";

    /**
     * Уведомление о добавлении Задачи
     */
    $API->addNotification(
        "system_alerts",
        "Приём пациента",
        "Пациент $clientName ожидает приёма",
        "info",
        $visitDetails[ "user_id" ],
        ""
    );

    /**
     * Отправка события о добавлении Задачи
     */
    $API->addEvent( "notifications" );


}


/**
 * Отправка события об обновлении расписания
 */
$API->addEvent( "schedule" );
$API->addEvent( "day_planning" );
