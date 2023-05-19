<?php

/**
 * Отмена посещения
 */
if ( $requestData->id && $requestData->reason_id ) $API->DB->update( "visits" )
    ->set( "is_active", "N" )
    ->where( [
        "id" => $requestData->id,
        "is_system" => "N"
    ] )
    ->execute();

/**
 * Отправка события об обновлении расписания
 */
$API->addEvent( "schedule" );
