<?php

$reportX = $API->DB->from( "atolOperations" )
    ->where( "cashbox_id", $requestData->cashbox_id )
    ->limit(1)
    ->fetch();

if ( !$reportX ) $API->returnResponse();

$request = [
    "request" => [
        "uuid" => "doca-" . $reportX[ "type" ] . "-id-" . $reportX[ "id" ],
        "type" => $reportX[ "type" ],
        "operator" => [
            "name" => "Миннахматовна Э. Ц.",
            "vatin" => "123654789507"
        ]
    ]
];

$API->DB->deleteFrom( "atolOperations" )
    ->where( "id", $reportX[ "id" ] )
    ->execute();

$API->returnResponse( $request );