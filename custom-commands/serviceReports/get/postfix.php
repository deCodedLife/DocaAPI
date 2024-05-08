<?php

/**
 * @file
 * Список "продажа услуг
 */


function array_sort ( $array, $on, $order=SORT_ASC ) {

    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
                break;
            case SORT_DESC:
                arsort($sortable_array);
                break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;

} // function. array_sort


/**
 * Сформированный список
 */
$returnServices = [];


/**
 * Фильтр Услуг
 */
$servicesFilter = [];


/**
 * Формирование фильтров
 */

$salesFilter[ "salesList.status" ] = "done";
$salesFilter[ "salesProductsList.type" ] = "service";
$salesFilter[ "salesList.action" ] = "sell";

if ( $requestData->start_price ) $salesFilter[ "salesList.summary >= ?" ] = $requestData->start_price;
if ( $requestData->end_price ) $salesFilter[ "salesList.summary <= ?" ] = $requestData->end_price;
if ( $requestData->start_at ) $salesFilter[ "salesList.created_at >= ?" ] = $requestData->start_at . " 00:00:00";
if ( $requestData->end_at ) $salesFilter[ "salesList.created_at <= ?" ] = $requestData->end_at . " 23:59:59";
if ( $requestData->store_id ) $salesFilter[ "salesList.store_id" ] = $requestData->store_id;

if ( !$requestData->limit ) $requestData->limit = 20;


foreach ( $response[ "data" ] as $service ) {

    /**
     * Количество услуги в продажах
     */
    $count = 0;

    /**
     * Сумма услуги в продажах
     */
    $sum = 0;

    $salesFilter[ "salesProductsList.product_id" ] = $service[ "id" ];


    /**
     * Получение продаж
     */
    $salesList = $API->DB->from( "salesList" )
        ->leftJoin( "salesProductsList ON salesProductsList.sale_id = salesList.id" )
        ->select( null )->select( [
            "salesList.id",
            "salesProductsList.product_id",
            "salesProductsList.cost",
            "salesProductsList.amount"
        ] )
        ->where( $salesFilter )
        ->orderBy( "salesList.created_at desc" )
        ->limit( 0 );

    /**
     * Обход Продаж
     */
    foreach ( $salesList as $sale ) {

        /**
         * Проверка наличия услуги в продажах
         */
        $count += $sale[ "amount" ];
        $sum += $sale[ "amount" ] * $sale[ "cost" ];

    } // foreach .$salesList

    /**
     * Проверка на наличие услуги в продажах
     */
    if ( $count != 0 ) {

        $returnServices[] = [
            "id" => $service[ "id" ],
            "title" => $service[ "title" ],
            "count" => $count,
            "date" => $service[ "date" ],
            "sum" => $sum
        ];

    } // if ( $count != 0 )

} // foreach. $response[ "data" ]

$response[ "data" ] = $returnServices;


if ( $sort_by == "title" ) {

    if ( $sort_order == "desc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "title", SORT_DESC ) );
    if ( $sort_order == "asc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "title", SORT_ASC ) );

}

if ( $sort_by == "count" ) {

    if ( $sort_order == "desc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "count", SORT_DESC ) );
    if ( $sort_order == "asc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "count", SORT_ASC ) );

}

if ( $sort_by == "date" ) {

    if ( $sort_order == "desc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "date", SORT_DESC ) );
    if ( $sort_order == "asc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "date", SORT_ASC ) );

}

if ( $sort_by == "sum" ) {

    if ( $sort_order == "desc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "sum", SORT_DESC ) );
    if ( $sort_order == "asc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "sum", SORT_ASC ) );

}

if ( $sort_by ) {


    $response[ "detail" ] = [

        "pages_count" => ceil(count($response[ "data" ]) / $limit),
        "rows_count" => count($response[ "data" ])

    ];

    $response[ "data" ] = array_slice($response[ "data" ], $limit * $requestData->page - $limit, $limit);

}
