<?php

if ( $requestData->begin_at ) {

    $requestSettings[ "filter" ][ "begin_at >= ?" ] = $requestData->begin_at;

    unset( $requestData->begin_at );

}
if ( $requestData->end_at ) {

    $requestSettings[ "filter" ][ "end_at <= ?" ] = $requestData->end_at;

    unset( $requestData->end_at );

}
