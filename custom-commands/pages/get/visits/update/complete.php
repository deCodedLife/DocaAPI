<?php

$userDetails = $API->DB->from( "users" )
    ->where( "id", $requestData->context->user_id )
    ->fetch();

if ( $userDetails[ "role_id" ] == 7 ) {

    $response[ "data" ][ 1 ][ "settings" ][ 0 ][ "body" ][ 0 ][ "settings" ][ "areas" ][ 0 ][ "blocks" ][ 1 ][ "fields" ][ 4 ][ "search" ] = "services";

}

$response[ "data" ][ 1 ][ "settings" ][ 2 ][ "body" ][ 0 ][ "settings" ][ "areas" ][ 0 ][ "blocks" ][ 1 ][ "fields" ][ 0 ][ "script" ] = [
    "object" => "visits",
    "command"=> "update",
    "properties" => [
        "id" => $pageDetail[ "row_detail" ][ "id" ],
        "is_called" => true
    ]
];

$response[ "data" ][ 1 ][ "settings" ][ 2 ][ "body" ][ 0 ][ "settings" ][ "areas" ][ 0 ][ "blocks" ][ 1 ][ "fields" ][ 1 ][ "script" ] = [
    "object" => "visits",
    "command"=> "update",
    "properties" => [
        "id" => $pageDetail[ "row_detail" ][ "id" ],
        "is_called" => true
    ]
];

//$pageScheme[ "data" ][ 1 ][ "settings" ][ 1 ][ "body" ][ 0 ][ "settings" ][ "areas" ][ 0 ][ "blocks" ][ 0 ][ "fields" ][ 4 ][ "is_visible" ] = true;
//$pageScheme[ "data" ][ 1 ][ "settings" ][ 1 ][ "body" ][ 0 ][ "settings" ][ "areas" ][ 0 ][ "blocks" ][ 0 ][ "fields" ][ 5 ][ "is_visible" ] = true;