<?php

if ( $requestData->context === "list" ) {

    $requestData->created_at = date( "Y-m-d" );

    $requestSettings[ "filter" ][ "created_at > ?" ] = date( 'Y-m-d' ) . " 00:00:00";
    $requestSettings[ "filter" ][ "created_at < ?" ] = date( 'Y-m-d' ) . " 23:59:59";

}