<?php

/**
 * Отчет рекламные источники
 */

/**
 * Статистика клиента
 */
$companyStatistic = [

    /**
     * Сумма посещений
     */
    "visits_sum" => 0,

];

$advertises = $API->sendRequest( "advertiseClients", "get", $requestData );


if ( $requestData->advertise_id ) {

    foreach ( $advertises as $advertise ) {

        if ( $advertise->id == $requestData->advertise_id ) {

            $companyStatistic["visits_sum"] += $advertise->price;

        }

    }

} else {

    foreach ( $advertises as $advertise ) {

        $companyStatistic["visits_sum"] += $advertise->price;

    }

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
