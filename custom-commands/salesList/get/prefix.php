<?php

if ( $requestData->context->block === "list" ) {

    if ( $requestData->start_at ) $dateFrom = date( 'Y-m-d', strtotime( $requestData->start_at ) ) . " 00:00:00";
    if ( $requestData->end_at   ) $dateTo   = date( 'Y-m-d', strtotime( $requestData->end_at ) )   . " 23:59:59";

//    $API->returnResponse( json_encode( $requestData ), 500 );

    if ( $requestData->start_at ) $requestSettings[ "filter" ][ "created_at > ?" ] = $dateFrom ?? '';
    if ( $requestData->end_at   ) $requestSettings[ "filter" ][ "created_at < ?" ] = $dateTo ?? '';

    if ( $requestData->store_id )   $requestSettings[ "filter" ][ "store_id = ?" ]   = $requestData->store_id;
    if ( $requestData->client_id )  $requestSettings[ "filter" ][ "client_id = ?" ]  = $requestData->client_id;
    if ( $requestData->pay_type )   $requestSettings[ "filter" ][ "pay_type = ?" ]   = $requestData->pay_type;
    if ( $requestData->pay_method ) $requestSettings[ "filter" ][ "pay_method = ?" ] = $requestData->pay_method;

    $requestSettings[ "filter" ][ "status = ?" ] = "done";

}
