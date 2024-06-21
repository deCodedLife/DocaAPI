<?php
if ( $clientContract[ "client_id" ] ) {

    $client = $API->DB->from( "clients" )
        ->where( "id", $clientContract[ "client_id" ] )
        ->limit( 1 )
        ->fetch();

    $clientFio = $client[ "last_name" ] . " " . substr($client[ "first_name" ], 0, 2) . ". " . substr($client[ "patronymic" ], 0, 2) . ".";

}

$logDescription = "Удалена запись " . $clientContract[ "title" ] . " в договорах клиента " . $clientFio;
$requestData->client_id = $clientContract[ "client_id" ];
