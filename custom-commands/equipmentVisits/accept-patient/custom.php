<?php

/**
 * @file
 * Вызов клиента
 */


$API->DB->update( "equipmentVisits" )
    ->set( [
        "status" => "process"
    ] )
    ->where( [
        "id" => $requestData->id
    ] )
    ->execute();

$API->addEvent( "schedule" );