<?php

/**
 * Отмена посещения
 */
if ( $requestData->id && $requestData->reason_id ) $API->DB->update( "visits" )
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

/**
 * Отправка события об обновлении расписания
 */
$API->addEvent( "schedule" );
$API->addEvent( "day_planning" );
