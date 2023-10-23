<?php

/**
 * @file
 * Отчет "Рекламные источники"
 */


$reportCache = $API->getHardReportCache( "advertise_fixed", $requestData );


if ( !$reportCache ) {

    $API->returnResponse( [
        "status" => "no_cache",
        "updated_at" => null,
        "report" => []
    ] );

} else {

    $API->returnResponse(

        [
            "status" => $reportCache[ "status" ],
            "updated_at" => $reportCache[ "updated_at" ],
            "report" => [
                [
                    "value" => number_format( intval( $reportCache[ "data" ][ "price" ] ), 0, '.', ' ' ),
                    "description" => "Прибыль",
                    "icon" => "",
                    "prefix" => "₽",
                    "postfix" => [
                        "icon" => "",
                        "value" => "",
                        "background" => ""
                    ],
                    "type" => "char",
                    "background" => "",
                    "detail" => []
                ]
            ]
        ]

    );

} // if. !$reportCache
