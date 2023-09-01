<?php

/**
 * @file
 * Список "продажа услуг
 */

/**
 * Сформированный список
 */
$returnServices = [];

/**
 * Фильтр Услуг
 */
$servicesFilter = [];

/**
 * Фильтр Продаж
 */
$salesFilter = [];

/**
 * Формирование фильтров
 */
$salesFilter[ "status" ] = "done";
$salesFilter[ "type" ] = "service";
$salesFilter[ "action" ] = "sell";
if ( $requestData->start_price ) $salesFilter[ "summary >= ?" ] = $requestData->start_price;
if ( $requestData->end_price ) $salesFilter[ "summary <= ?" ] = $requestData->end_price;
if ( $requestData->start_at ) $salesFilter[ "created_at >= ?" ] = $requestData->start_at . " 00:00:00";
if ( $requestData->end_at ) $salesFilter[ "created_at <= ?" ] = $requestData->end_at . " 23:59:59";
if ( $requestData->store_id ) $salesFilter[ "store_id" ] = $requestData->store_id;


if ( $requestData->id ) $servicesFilter[ "id" ] = $requestData->id;
if ( $requestData->category_id ) $servicesFilter[ "category_id" ] = $requestData->category_id;

/**
 * Получение услуг
 */

$services = $API->DB->from( "services" )
    ->where( $servicesFilter );

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

foreach ( $services as $service ) {

    /**
     * Колличество услуги в продажах
     */
    $count = 0;

    /**
     * Сумма услуги в продажах
     */
    $sum = 0;

    /**
     * Обход Продаж
     */
    foreach ( $salesList as $sale ) {


        /**
         * Проверка наличия услуги в продажах
         */
        if ( $sale[ "product_id" ] == $service[ "id" ] ) {

            $count += $sale[ "amount" ];
            $sum += $sale[ "amount" ] * $sale[ "cost" ];

        } //  if ( $sale[ "product_id" ] == $service[ "id" ])

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

} // foreach .$services

$response[ "data" ] = $returnServices;

function array_sort ( $array, $on, $order=SORT_ASC )
{
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
}

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
