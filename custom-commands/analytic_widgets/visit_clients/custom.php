<?php

/**
 * @file
 * Отчет "Клиенты посещавшие специалистов
 */

/**
 * Детальная информация об отчете
 */
$reportStatistic = [

    /**
     * Сумма продаж
     */
    "visits_sum" => 0,

    /**
     * Сумма продаж
     */
    "services_user_percents" => 0,

    /**
     * Сумма продаж
     */
    "clients_count" => 0,

];

/**
 * Получение списка посещений
 */

//$requestData->limit = 0;
//$start = microtime( true );
//$visitsClients = $API->sendRequest( "visit_clients", "get", $requestData );
//$end = microtime( true );
//$API->returnResponse( $visitsClients );
//ini_set( "display_errors", 1 );
//if ( $requestData->user_id ) $filters[ "visits.user_id" ] = $requestData->user_id;
//if ( $requestData->start_price ) $filters[ "visits.price >= ?" ] = $requestData->start_price;
//if ( $requestData->end_price ) $filters[ "visits.price <= ?" ] = $requestData->end_price;
//if ( $requestData->start_at ) $filters[ "visits.start_at >= ?" ] = $requestData->start_at;
//if ( $requestData->end_at ) $filters[ "visits.end_at <= ?" ] = $requestData->end_at;
//$filters[ "visits.status <= ?" ] = "ended";
//$filters[ "visits.is_payed" ] = "Y";
//
//$start = microtime( true );
//$visitsList = $API->DB->from( "visits" )
//    ->innerJoin( "visits_services on visits_services.visit_id = visits.id" )
//    ->select( "service_id as service_id" )
//    ->where( $filters );
//
//foreach ( $visitsList as $visit ) {
//
//    $visitServices[ $visit[ "service_id" ] ]++;
//    $reportStatistic[ "visits_sum" ] += $visit[ "price" ];
//
//}
//
//$servicesUserPercents = $API->DB->from( "services_user_percents")
//    ->where( "row_id", $requestData->user_id );
//
//$services = [];
//
//foreach ( $servicesUserPercents as $servicesUserPercent)
//    $services[] = $servicesUserPercent[ "service_id" ];
//
//
//
//$reportStatistic[ "clients_count" ] += count( $visitServices );
//
///**
// * Обрабботка списка
// */
//foreach ( $visitsList as $visit ) {
//
//
//
//    foreach ( $visitsClient->services_id as $service ) {
//
//        if ( in_array( $service->value, $services ) ) {
//
//            $reportStatistic[ "services_user_percents" ] += $visitsClient->price;
//
//        }
//
//    }
//
//
//} // foreach .$userServices




$API->returnResponse(

    [
        [
            "value" => number_format( intval( $reportStatistic["visits_sum"] ), 0, '.', ' ' ),
            "description" => "Сумма продаж",
            "size" => "1",
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
            "value" => number_format( intval( $reportStatistic["services_user_percents"] ), 0, '.', ' ' ),
            "description" => "Сумма продаж с  %",
            "size" => "1",
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
            "value" => $reportStatistic[ "clients_count" ],
            "description" => "Количество клиентов",
            "icon" => "",
            "size" => "1",
            "prefix" => "",
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
