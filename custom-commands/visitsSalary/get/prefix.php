<?php

$start_at = date( "Y-m-d", strtotime( $requestData->start_at ) ) . " 00:00:00";
$end_at = date( "Y-m-d", strtotime( $requestData->end_at ) ) . " 23:59:59";

$requestData->id = array_merge(
    visits\GetVisitsIDsByUser(
        "visits",
        $start_at,
        $end_at,
        $requestData->user_id
    ),
    visits\GetVisitsIDsByAuthor(
        "visits",
        $start_at,
        $end_at,
        $requestData->user_id
    )
);


$requestData->context->user_id = $requestData->user_id;

unset( $requestData->start_at );
unset( $requestData->end_at );
unset( $requestData->user_id );
unset( $requestData->status );

if ( empty( $requestData->id ) ) $requestData->id = [ 0 ];
$requestSettings[ "filter" ][ "id" ] = $requestData->id;
