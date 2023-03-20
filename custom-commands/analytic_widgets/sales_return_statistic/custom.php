<?php

$currentDay = new DateTime();
$report = [];
$statistic = [];


$sales =  $API->DB->from( "sales" )
    ->where( [
        "created_at >= ?" => $currentDay->format("Y-m-d") . " 00:00:00",
        "created_at <= ?" => $currentDay->format("Y-m-d") . " 23:59:59",
        "pay_type = ?" => "sellReturn"
    ] );

if ( count( $sales ) == 0 ) {

    $report[ "Наличными" ] = 0;
    $report[ "Безналичными" ] = 0;

}



foreach ( $sales as $sale ) {

    $report[ "Наличными" ] += (float) $sale[ "cash_sum" ];
    $report[ "Безналичными" ] += (float) $sale[ "card_sum" ];

}



foreach ( $report as $key => $item ) {
    $statistic[] = [
        "value" => round($item, 2),
        "description" => $key,
        "icon" => "",
        "prefix" => "",
        "postfix" => [
            "icon" => "",
            "value" => "₽",
            "background" => "dark"
        ],
        "background" => "",
        "detail" => [
        ]
    ];
}



$API->returnResponse( $statistic );