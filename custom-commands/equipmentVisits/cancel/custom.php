<?php

/**
 * @file
 * Отмена записи к врачу
 */

$API->DB->update( "equipmentVisits" )
    ->set( [
        "is_active" => "N"
    ] )
    ->where( [
        "id" => $requestData->id
    ] )
    ->execute();

$API->addEvent( "schedule" );
