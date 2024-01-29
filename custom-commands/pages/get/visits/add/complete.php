<?php

$userDetails = $API->DB->from( "users" )
    ->where( "id", $requestData->context->user_id )
    ->fetch();

if ( $userDetails[ "role_id" ] == 7 ) {

    $response[ "data" ][ 1 ][ "settings" ][ "areas" ][ 0 ][ "blocks" ][ 1 ][ "fields" ][ 5 ][ "search" ] = "services";

}