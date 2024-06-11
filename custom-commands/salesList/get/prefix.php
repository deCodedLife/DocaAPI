<?php

if ( $requestData->context->block === "list" ) {

    if ( $requestData->start_at ) $dateFrom = date( 'Y-m-d', strtotime( $requestData->start_at ) ) . " 00:00:00";
    if ( $requestData->end_at   ) $dateTo   = date( 'Y-m-d', strtotime( $requestData->end_at ) )   . " 23:59:59";

    if ( $requestData->start_at ) $requestSettings[ "filter" ][ "created_at > ?" ] = $dateFrom ?? '';
    if ( $requestData->end_at   ) $requestSettings[ "filter" ][ "created_at < ?" ] = $dateTo ?? '';

    if ( $requestData->store_id )   $requestSettings[ "filter" ][ "store_id = ?" ]   = $requestData->store_id;

    if ( $requestData->pay_type )   $requestSettings[ "filter" ][ "pay_type = ?" ]   = $requestData->pay_type;
    if ( $requestData->pay_method ) $requestSettings[ "filter" ][ "pay_method = ?" ] = $requestData->pay_method;

    $requestSettings[ "filter" ][ "status = ?" ] = "done";

    if ( !$requestData->sort_by ) {

        $requestData->sort_by = "created_at";
        $requestData->sort_order = "desc";

    }

    if ( property_exists( $requestData, "action" ) && $requestData->action == "deposit" ) {

        $salesList = $API->DB->from( "salesList" )
            ->where( "(sum_deposit > :sum_deposit OR action = :action)", [
                ":sum_deposit" => 0,
                ":action" => "deposit",
            ] )
            ->where( "client_id = :client_id", [
                ":client_id" => $requestData->client_id,
            ] )
            ->fetchAll( "id" );

        unset( $requestData->action );
        $requestSettings[ "filter" ][ "id" ]  = array_keys( $salesList );

        if ( count( $requestSettings[ "filter" ][ "id" ] ) == 0 ) $requestSettings[ "filter" ][ "id" ] = [ -1 ];

    }

    if ( $requestData->client_id )  $requestSettings[ "filter" ][ "client_id = ?" ]  = $requestData->client_id;

}


