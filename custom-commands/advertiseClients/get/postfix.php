<?php

/**
 * @file
 * Список "Рекламные источники
 */


if ( !$requestData->id ) $API->returnResponse( [] );


$reportCache = $API->getHardReportCache( "advertise_fixed", $requestData );

if ( !$reportCache ) $API->returnResponse( [] );
else {

    $API->returnResponse( [

        "id" => $requestData->id,
        "title" => $reportCache[ "data" ][ "title" ],
        "clientsCount" => $reportCache[ "data" ][ "clientsCount" ],
        "recordedCount" => $reportCache[ "data" ][ "recordedCount" ],
        "extantCount" => $reportCache[ "data" ][ "extantCount" ],
        "underdoneCount" => $reportCache[ "data" ][ "underdoneCount" ],
        "visitsCount" => $reportCache[ "data" ][ "visitsCount" ],
        "price" => $reportCache[ "data" ][ "price" ]

    ] );

} // if. !$reportCache
