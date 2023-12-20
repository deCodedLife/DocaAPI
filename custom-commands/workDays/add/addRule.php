<?php

$insertRule = (array) $requestData;
unset( $insertRule[ "start_from" ] );
unset( $insertRule[ "start_to" ] );

$insertRule[ "event_from" ] = $begin->format( "Y-m-d H:i:s" );
$insertRule[ "event_to" ] = $end->format( "Y-m-d H:i:s" );

$API->DB->insertInto("workDays")
    ->values( $insertRule )
    ->execute();