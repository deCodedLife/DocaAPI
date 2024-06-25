<?php

if ( !empty( $API->request->data->end_at ) ) $requestData->end_at = $API->request->data->end_at;
if ( !empty( $API->request->data->start_at ) ) $requestData->start_at = $API->request->data->start_at;

$requestData->end_at =  date( "Y-m-d H:i:s", strtotime( "$requestData->start_at + 31 day" ) );
//$API->request->data->end_at = date( 'Y-m-d H:i:s', strtotime( "+ 1 month" ) );

$API->request->data->user_id = [ 0 ];
$requestData->user_id = [ 0 ];