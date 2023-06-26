<?php

if ( $requestData->context->block === "list" ) {

    $dateFrom = date( 'Y-m-d' ) . " 00:00:00";
    $dateTo   = date( 'Y-m-d' ) . " 23:59:59";

    if ( $requestData->starts_at ) $dateFrom = date( 'Y-m-d', strtotime( $requestData->starts_at ) ) . " 00:00:00";
    if ( $requestData->ends_at   ) $dateTo   = date( 'Y-m-d', strtotime( $requestData->ends_at ) )   . " 23:59:59";

    $requestSettings[ "filter" ][ "created_at > ?" ] = $dateFrom;
    $requestSettings[ "filter" ][ "created_at < ?" ] = $dateTo;

    if ( $requestData->store_id )   $requestSettings[ "filter" ][ "store_id = ?" ]   = $requestData->store_id;
    if ( $requestData->client_id )  $requestSettings[ "filter" ][ "client_id = ?" ]  = $requestData->client_id;
    if ( $requestData->pay_type )   $requestSettings[ "filter" ][ "pay_type = ?" ]   = $requestData->pay_type;
    if ( $requestData->pay_method ) $requestSettings[ "filter" ][ "pay_method = ?" ] = $requestData->pay_method;

}


