<?php

/**
 * @file
 * Завершение записи к врачу
 */

$API->DB->update( "equipmentVisits" )
    ->set( "status", "ended" )
    ->where( [
        "id" => $requestData->id
    ] )
    ->execute();

$API->addEvent( "schedule" );
