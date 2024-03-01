<?php

/**
 * @file
 * Список "Рекламные источники
 */

$filter = [];
if ( $requestData->start_at ) $filter[ "created_at >= ?" ] = $requestData->start_at . " 00:00:00";
if ( $requestData->end_at ) $filter[ "created_at <= ?" ] = $requestData->end_at . " 23:59:59";


$clients = $API->DB->from( "clients" )
    ->select( null )
    ->select( [ "id", "advertise_id" ] )
    ->where( $filter );
