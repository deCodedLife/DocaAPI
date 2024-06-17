<?php

$telegram_user = $requestData->context->user_id;

if ( empty( $telegram_user ) ) $API->returnResponse();

$client = $API->DB->from( "clients" )
    ->where( "telegram_id", $telegram_user )
    ->fetch();

if ( empty( $client ) || empty( $client[ "id" ] ) ) $API->returnResponse();

$userVisit = $API->DB->from( "visits" )
    ->where( [
        "client_id" => $client[ "id" ],
        "notify" => "Y",
        "is_called" => "N",
        "is_active" => "Y"
    ] )
    ->orderBy( "id DESC" )
    ->fetch();

if ( empty( $userVisit ) || empty( $userVisit[ "id" ] ) ) $API->returnResponse();

$request = [
    "context" => [
        "bot" => true
    ],
    "id" => $userVisit[ "id" ],
    "is_called" => true,
];


$data = $API->sendRequest( "visits", "update", $request );
$API->returnResponse( $data );