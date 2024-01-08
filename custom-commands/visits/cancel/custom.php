<?php

/**
 * @file
 * Отмена записи к врачу
 */

$API->DB->update( "visits" )
    ->set( [
        "is_active" => "N",
        "reason_id" => $requestData->reason_id,
        "cancelledDate" => date( "Y-m-d H:i:s" ),
        "operator" => $API::$userDetail->id
    ] )
    ->where( [
        "id" => $requestData->id
    ] )
    ->execute();

$userDetail = $API->DB->from( "users" )
    ->where( "id", $API::$userDetail->id )
    ->fetch();

$API->addLog( [
    "table_name" => "visits",
    "description" => "Посещение удалено пользователем " . $userDetail[ "last_name" ],
    "row_id" => $requestData->id
], (array) $requestData );


$API->addEvent( "schedule" );
$API->addEvent( "day_planning" );
