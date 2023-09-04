<?php

/**
 * @file
 * Отмена записи к врачу
 */

$API->DB->update( "visits" )
    ->set( [
        "is_active" => "N"
    ] )
    ->where( [
        "id" => $requestData->id
    ] )
    ->execute();

$API->addEvent( "schedule" );
$API->addEvent( "day_planning" );
