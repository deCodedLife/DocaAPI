<?php
/**
* Отчет "продажа услуг
*/

$companyStatistic = [

    /**
     * Сумма посещений
     */
    "visits_sum" => 0,

];

/**
 * Фильтр для visits_services
 */
$filter = [];

/**
 * Фильтр для услуг
 */
$servicesFilter = [];

/**
 * Фильтр для пользователей
 */
$usersFilter = [];

/**
 * Формирование фильров
 */
if ( $requestData->start_at ) $filter[ "date >= ?" ] = $requestData->start_at . " 00:00:00";
if ( $requestData->end_at ) $filter[ "date <= ?" ] = $requestData->end_at . " 23:59:59";
if ( $requestData->store_id ) $filter[ "store_id" ] = $requestData->store_id;
if ( $requestData->category_id ) $servicesFilter[ "category_id" ] = $requestData->category_id;
if ( $requestData->id ) $servicesFilter[ "id" ] = $requestData->id;
if ( $requestData->user_id ) $usersFilter[ "id" ] = $requestData->user_id;

$servicesFilter[ "is_active" ] = "Y";

$companyVisitsServices = $API->DB->from( "visits_services" )
    ->where( $filter );

$services = $API->DB->from( "services" )
    ->where( $servicesFilter );

/**
 * Сформированный список
 */
$returnVisits = [];

foreach ( $services as $service ) {

    $count = 0;

    foreach ( $companyVisitsServices as $companyVisitsService ) {

        if ( $companyVisitsService[ "service_id" ] == $service[ "id" ]) {

            $count++;

        }

    }

    $returnVisits[] = [
        "id" => $service[ "id" ],
        "title" => $service[ "title" ],
        "count" => $count,
        "date" => $service[ "date" ],
        "store_id" => $service[ "store_id" ],
        "category_id" => $service[ "category_id" ],
        "sum" => $service[ "price" ] * $count
    ];

}

/**
 * Формирование графика посещений
 */

foreach ( $returnVisits as $serviceReport ) {

    $companyStatistic[ "visits_sum" ] += $serviceReport[ "sum" ];

} // foreach. $returnVisits


$API->returnResponse(

    [

        [
            "value" => $companyStatistic["visits_sum"],
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

);
