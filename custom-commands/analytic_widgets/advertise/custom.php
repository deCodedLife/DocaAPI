<?php
/**
 * @file
 * Отчет "ректамные источники
 */

/**
 * Статистика клиента
 */
$advertiseStatistic = [

    /**
     * Прибыль
     */
    "cacheFlow" => 0,

];

/**
 * Получение списка статистики рекламных источников
 */
$advertises = $API->sendRequest( "advertiseClients", "get", $requestData );

/**
 * Обод спискка
 */
foreach ( $advertises as $advertise ) {

    $advertiseStatistic[ "cacheFlow" ] += $advertise->price;

}


$API->returnResponse(

    [
        [
            "value" => $advertiseStatistic[ "cacheFlow" ],
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
