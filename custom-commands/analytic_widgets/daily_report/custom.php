<?php

/**
 * @file
 * Отчет "Суточный отчет
 */


/**
 * Детальная информация об отчете
 */

$reportStatistic = [

    /**
     * Количество посещений
     */
    "visits_count" => 0,

    /**
     * Сумма поступлений
     */
    "visits_sum" => 0,


    /**
     * Сумма расходов
     */
    "expenses_sum" => 0,
];


/**
 * Фильтр продаж
 */
$salesFilter = [];

/**
 * Фильтр расходников
 */
$expensesFilter = [];

/**
 * Формирование фильтров
 */

$salesFilter[ "status" ] = "done";
$salesFilter[ "action" ] = "sell";
if ( $requestData->start_price ) $salesFilter[ "summary >= ?" ] = $requestData->start_price;
if ( $requestData->end_price ) $salesFilter[ "summary <= ?" ] = $requestData->end_price;
if ( $requestData->start_at ) $salesFilter[ "created_at >= ?" ] = $requestData->start_at . " 00:00:00";
if ( $requestData->end_at ) $salesFilter[ "created_at <= ?" ] = $requestData->end_at . " 23:59:59";
if ( $requestData->store_id ) $salesFilter[ "store_id" ] = $requestData->store_id;

if ( $requestData->store_id ) $expensesFilter[ "store_id" ] = $requestData->store_id;
if ( $requestData->start_at ) $expensesFilter[ "created_at >= ?" ] = $requestData->start_at . " 00:00:00";
if ( $requestData->end_at ) $expensesFilter[ "created_at <= ?" ] = $requestData->end_at . " 23:59:59";


/**
 * Получение продаж
 */
$salesList = $API->DB->from( "salesList" )
    ->leftJoin( "saleVisits ON saleVisits.sale_id = salesList.id" )
    ->select( null )->select( [ "saleVisits.visit_id", "salesList.summary"  ] )
    ->where( $salesFilter )
    ->orderBy( "salesList.created_at desc" )
    ->limit( 0 );

/**
 * Получение расходов
 */
$expenses = $API->DB->from( "expenses" )
    ->where( $expensesFilter );


/**
 * Обработка продаж
 */

foreach ( $salesList as $sale ) {

   if ( $sale[ "visit_id"] != null ) $reportStatistic[ "visits_count" ]++;
   $reportStatistic[ "visits_sum" ] += $sale[ "summary" ];

} // foreach. $salesList

/**
 * Обработка расходов
 */
foreach ( $expenses as $expense )
    $reportStatistic[ "expenses_sum" ] += (float) $expense[ "price" ];

$API->returnResponse(

    [
        [
            "size" => 1,
            "value" => round( $reportStatistic[ "visits_count"] ),
            "description" => "Посещений",
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
            "size" => 1,
            "value" => number_format( round( $reportStatistic[ "visits_sum"] ), 0, '.', ' '),
            "description" => "Поступления",
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
            "size" => 1,
            "value" => number_format( round( $reportStatistic[ "expenses_sum"] ), 0, '.', ' '),
            "description" => "Расход",
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
            "size" => 1,
            "value" => number_format( round( $reportStatistic[ "visits_sum" ] - $reportStatistic[ "expenses_sum" ] ), 0, '.', ' '),
            "description" => "Итог",
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
