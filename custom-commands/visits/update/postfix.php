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


/**
 * Отмена посещения
 */
//if ( $requestData->id && $requestData->reason_id ) $API->DB->update( "visits" )
//    ->set( [
//        "is_active" => "N",
//        "reason_id" => $requestData->reason_id,
//        "cancelledDate" => date("Y-m-d H:i:s"),
//        "operator" => $API::$userDetail->id
//    ] )
//    ->where( [
//        "id" => $requestData->id,
//        "is_system" => "N"
//    ] )
//    ->execute();

/**
 * Отправка события об обновлении расписания
 */
$API->addEvent( "schedule" );
$API->addEvent( "day_planning" );
