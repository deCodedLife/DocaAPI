<?php
$visitReport = $API->DB->from( "visitReports" )
    ->where( "id", $requestData->id )
    ->limit( 1 )
    ->fetch();

if ( $visitReport[ "client_id" ] ) {

    $client = $API->DB->from( "clients" )
        ->where( "id", $visitReport[ "client_id" ] )
        ->limit( 1 )
        ->fetch();

    $clientFio = $client[ "last_name" ] . " " . substr($client[ "first_name" ], 0, 2) . ". " . substr($client[ "patronymic" ], 0, 2) . ".";

}

$logDescription = "Изменена запись " . $visitReport[ "title" ] . " в мед. карте клиента " . $clientFio;
$requestData->client_id = $visitReport[ "client_id" ];
