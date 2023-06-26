<?php


/**
* Клиенты
*/

$companyStatistic = [

    /**
     * Сумма посещений
     */
    "visits_sum" => 0,

    /**
     * Колличество посещений
     */
    "visits_count" => 0,

    /**
     * Колличество новых клиентов
     */
    "clients_count" => 0,

];

$clientsFilter = [];
$visitsFilter = [];

$clientsFilter[ "is_active" ] = "Y";
$visitsFilter [ "is_active" ] = "Y";

if ( $requestData->start_at ) {

    $clientsFilter[ "created_at >= ?" ] = $requestData->start_at . " 00:00:00";
    $visitsFilter[ "end_at >= ?" ] = $requestData->start_at . " 00:00:00";

}
if ( $requestData->end_at ) {

    $clientsFilter[ "created_at <= ?" ] = $requestData->end_at . " 23:59:59";
    $visitsFilter[ "end_at <= ?" ] = $requestData->end_at . " 23:59:59";

}
if ( $requestData->start_ear ) {

    $clientsFilter[ "birthday >= ?" ] = $requestData->start_ear;

}
if ( $requestData->end_ear ) {

    $clientsFilter[ "birthday <= ?" ] = $requestData->end_ear;

}
if ( $requestData->store_id ) {

    $visitsFilter[ "store_id" ] = $requestData->store_id;

}


$clients = $API->DB->from( "clients" )
    ->where( $clientsFilter );

$visits = $API->DB->from( "visits" )
    ->where( $visitsFilter );


foreach ( $visits as $visit ) {

    $companyStatistic[ "visits_count" ]++;
    $companyStatistic[ "visits_sum" ] += (float) $visit[ "price" ];

} // foreach. $companyVisits

$companyStatistic[ "clients_count" ] = count( $clients );

$API->returnResponse(

    [
        [
            "value" => $companyStatistic[ "clients_count" ],
            "description" => "Новые клиенты",
            "icon" => "",
            "prefix" => "",
            "postfix" => [
                "icon" => "",
                "value" => "",
                "background" => ""
            ],
            "type" => "char",
            "background" => "",
            "detail" => []
        ],
        [
            "value" => $companyStatistic[ "visits_count" ],
            "description" => "Посещения",
            "icon" => "",
            "prefix" => "",
            "postfix" => [
                "icon" => "",
                "value" => "",
                "background" => ""
            ],
            "type" => "char",
            "background" => "",
            "detail" => []
        ],
        [
            "value" => $companyStatistic[ "visits_sum" ],
            "description" => "Приход",
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
        ],
        [
            "value" => $companyStatistic[ "visits_sum" ] / $companyStatistic[ "visits_count" ],
            "description" => "Средний чек",
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

);
