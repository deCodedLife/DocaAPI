<?php

/**
 * @file
 * Вызов клиента
 */


$API->DB->update( "visits" )
    ->set( [
        "status" => "process"
    ] )
    ->where( [
        "id" => $requestData->id
    ] )
    ->execute();

$API->addEvent( "schedule" );
$API->addEvent( "day_planning" );