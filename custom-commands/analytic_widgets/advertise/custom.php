<?php


/**
* Отчет "продажа услуг
*/

/**
 * Получение фильтров
 */

$filter = [];

if ( $requestData->start_at ) $filter[ "end_at >= ?" ] = $requestData->start_at . " 00:00:00";
if ( $requestData->end_at ) $filter[ "end_at <= ?" ] = $requestData->end_at . " 23:59:59";
if ( $requestData->store_id ) $filter[ "store_id" ] = $requestData->store_id;


/**
* Статистика клиента
*/
$companyStatistic = [

    /**
    * Количество посещений
    */
    "visits_count" => 0,

    /**
    * Сумма посещений
    */
    "visits_sum" => 0,

];


/**
* Получение посещений
*/
$companyVisits = $API->DB->from( "visits" )
    ->where( $filter );


/**
 * Формирование графика посещений
 */

if ( $requestData->advertise_id ) {

    foreach ($companyVisits as $visit) {

        $visitsClients = $API->DB->from( "visits_clients" )
            ->where( "visit_id", $visit[ "id" ] );

        foreach ( $visitsClients as $visitClient ) {

            $client = $API->DB->from( "clients" )
                ->where( "id", $visitClient [ "client_id" ] );

            if ( $client[ "advertise_id" ] == $requestData->advertise_id ) {

                $companyStatistic[ "visits_count" ]++;
                $companyStatistic[ "visits_sum" ] += (float) $visit[ "price" ];

            }

        }

    }

} else {

    foreach ( $companyVisits as $visit ) {

        $companyStatistic[ "visits_count" ]++;
        $companyStatistic[ "visits_sum" ] += (float) $visit[ "price" ];

    } // foreach. $userVisits


}


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
