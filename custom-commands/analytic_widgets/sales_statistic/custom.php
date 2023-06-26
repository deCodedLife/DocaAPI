<?php

/**
 * Переменные для выдачи
 */
$currentDay = new DateTime();
$report = [];
$statistic = [];



/**
 * Обработка фильтра по дате
 */
$dateFrom = $currentDay->format("Y-m-d") . " 00:00:00";
$dateTo   = $currentDay->format("Y-m-d") . " 23:59:59";

if ( $requestData->start_at ) $dateFrom = $requestData->start_at;
if ( $requestData->end_at )   $dateTo   = $requestData->end_at;



/**
 * Создание фильтров для запроса
 */
$filter = [
    "created_at >= ?" => $dateFrom,
    "created_at <= ?" => $dateTo,
    "not pay_type = ?" => "sellReturn",
    "status" => "done"
];


/**
 * Обработка оставшихся параметров из фильтров
 */
if ( $requestData->payment_type ) $filter[ "pay_method = ?" ] = $requestData->payment_type;
if ( $requestData->client_id ) $filter[ "client_id = ?" ] = $requestData->client_id;
if ( $requestData->user_id ) $filter[ "employee = ?" ] = $requestData->user_id;
if ( $requestData->store_id ) $filter[ "store_id = ?" ] = $requestData->store_id;


/**
 * Получение продаж
 */
$sales =  $API->DB->from( "sales" )
    ->where( $filter );



/**
 * Формирование списка графиков
 */
$report[ "Наличными" ] = 0;
$report[ "Безналичными" ] = 0;
$report[ "Аванс" ] = 0;
$report[ "Итого" ] = 0;
$report[ "Возврат наличными" ] = 0;
$report[ "Возврат безналичными" ] = 0;



/**
 * Подсчёт значений графиков
 */
foreach ( $sales as $sale ) {

    $report[ "Аванс" ] += (float) $sale[ "deposit_sum" ];
    $report[ "Наличными" ] += (float) $sale[ "cash_sum" ];
    $report[ "Безналичными" ] += (float) $sale[ "card_sum" ];
    $report[ "Итого" ] += (float) $sale[ "deposit_sum" ] + (float) $sale[ "cash_sum" ] + (float) $sale[ "card_sum" ];

}

/**
 * Получение списка возвратов
 */
unset( $filter[ "not pay_type = ?" ] );
$filter[ "pay_type = ?" ] = "sellReturn";

$sales =  $API->DB->from( "sales" )
    ->where( $filter );


/**
 * Подсчёт значений возвратов
 */
foreach ( $sales as $sale ) {

    $report[ "Возврат наличными" ] += (float) $sale[ "cash_sum" ];
    $report[ "Возврат безналичными" ] += (float) $sale[ "card_sum" ];

}


/**
 * Формирование и выдача графиков
 */
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