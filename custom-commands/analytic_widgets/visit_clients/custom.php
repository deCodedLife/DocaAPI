<?php
/**
* Суточный отчет
*/

/**
 * Получение фильтров
 */

$filter = [];
if ( $requestData->start_at ) $filter[ "start_at >= ?" ] = $requestData->start_at . " 00:00:00";
if ( $requestData->end_at ) $filter[ "start_at <= ?" ] = $requestData->end_at . " 23:59:59";
if ( $requestData->store_id ) $filter[ "store_id" ] = $requestData->store_id;

$companyStatistic = [

    /**
     * Количество клиентов
     */
    "clients_count" => 0,

    /**
     * Сумма посещений
     */
    "visits_sum" => 0,

    /**
     * Сумма слуг с процентами
     */
    "services_user_percents" => 0,

];

/**
 * Получение посещений
 */
$companyVisits = $API->DB->from( "visits" )
    ->where( $filter );


/**
* Получение слуг с процентами
*/
$servicesUserPercents = $API->DB->from( "services_user_percents" );


/**
 * Формирование графика посещений
 */

foreach ( $companyVisits as $userVisit ) {
    $visit_client = $API->DB->from( "visits_clients" )
        ->where( "visit_id", $userVisit["id"] )
        ->limit( 1 )
        ->fetch( );
    $companyStatistic[ "clients_ids" ][] = $visit_client[ "client_id" ];
    $companyStatistic[ "visits_sum" ] += (float) $userVisit[ "price" ];

} // foreach. $userVisits

foreach ( $servicesUserPercents as $serviceUserPercents ) {

    /**
     * Получение услуг
     */
    $service = $API->DB->from( "services" )
        ->where( "id", $serviceUserPercents["service_id"] )
        ->limit( 1 )
        ->fetch( );
    $companyStatistic[ "services_user_percents" ]  += (float) $service[ "price" ];

} // foreach. $userVisits


$companyStatistic[ "clients_count" ] = count(array_unique($companyStatistic[ "clients_ids" ]));

$API->returnResponse(

    [
        [
            "value" => $companyStatistic["visits_sum"],
            "description" => "Сумма продаж",
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
            "value" => $companyStatistic["services_user_percents"],
            "description" => "Сумма продаж с  %",
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
            "value" => $companyStatistic[ "clients_count" ],
            "description" => "Количество клиентов",
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
        ]
    ]

);
