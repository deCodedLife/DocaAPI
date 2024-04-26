<?php

/**
 * Обновление кеша пользователя для посещения
 */
if ( $requestData->clients_id && $requestData->id ) {

    $API->DB->update( "visits" )
        ->set( "client_id", intval( $requestData->clients_id[ 0 ] ?? 1 ) )
        ->where( "id", $requestData->id )
        ->execute();

} // if ( $requestData->clients_id && $requestData->id )


if ( $requestData->status === "waited" ) {

    $visitDetails = $API->DB->from( "visits" )
        ->where( "id", $requestData->id )
        ->fetch();

    $clientDetails = $API->DB->from( "clients" )
        ->innerJoin( "visits on visits.client_id = clients.id" )
        ->where( "visits.id", $requestData->id )
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


if ( property_exists( $API->request->data, "context" ) && property_exists( $API->request->data->context, "bot" ) ) {

    $clientDetails = $API->DB->from( "clients" )
        ->innerJoin( "visits on visits.client_id = clients.id" )
        ->where( "visits.id", $requestData->id )
        ->fetch();

    $app_name = $API->DB->from( "stores" )->where( "id", $visitDetails[ "store_id" ] ?? 0 )->fetch()[ "name" ];
    $app_map = $API->DB->from( "stores" )->where( "id", $visitDetails[ "store_id" ] ?? 0 )->fetch()[ "map" ];

    if ( property_exists( $requestData, "is_active" ) ) {

        telegram\sendMessage(
            "Ваша запись отменена.",
            telegram\getClient( $clientDetails[ "id" ] )
        );

    } else {

        telegram\sendMessage(
            "Ваша запись подтверждена.\n\nДо встречи в $app_name.\n\n$app_map",
            telegram\getClient( $clientDetails[ "id" ] )
        );

    }

}
