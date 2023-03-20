<?php

  
/**
* Отчет "Статистика клиента
*/


/**
* Статистика клиента
*/
$clientStatistic = [

    /**
    * Количество посещений
    */
    "visits_count" => 0,

    /**
    * Сумма посещений
    */
    "visits_sum" => 0,

    /**
    * Средний чек
    */
    "medium_visit_price" => 0,

    /**
    * Минимальная цена
    */
    "min_visit_price" => 0,

    /**
    * Максимальная цена
    */
    "max_visit_price" => 0,

    /**
    * Дата последнего посещения
    */
    "last_visit_date" => ""

];


/**
* Получение посещений Сотрудника
*/
$clientVisits = $API->DB->from( "visits" )
    ->leftJoin( "visits_clients ON visits_clients.visit_id = visits.id" )
    ->select( null )->select( [  "visits.id", "visits.start_at", "visits.is_active", "visits.status", "visits.price"  ] )
    ->where( [
        "visits_clients.client_id" => $requestData->client_id
    ] )
    ->orderBy( "visits.start_at desc" )
    ->limit( 0 );


/**
* Формирование графика посещений
*/

foreach ( $clientVisits as $userVisit ) {

$clientStatistic[ "visits_count" ]++;
$clientStatistic[ "visits_sum" ] += (float) $userVisit[ "price" ];

} // foreach. $userVisits


$API->returnResponse(

    [
        [
            "value" => $clientStatistic[ "visits_count" ] . " посещений",
            "description" => "всего",
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
            "value" => $clientStatistic[ "visits_sum" ],
            "description" => "Сумма посещений",
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