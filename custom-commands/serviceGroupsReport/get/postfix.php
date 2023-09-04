<?php

/**
 * @file
 * Отчет "группы услуг
 */


$returnServices = [];


/**
 * Фильтр для продаж
 */

$salesFilter[ "status" ] = "done";
$salesFilter[ "type" ] = "service";
$salesFilter[ "action" ] = "sell";
$salesFilter[ "created_at >= ?" ] = date( "Y-m-" ) . "01 00:00:00";


/**
 * Получение списка продаж
 */
$salesList = $API->DB->from( "salesList" )
    ->leftJoin( "salesProductsList ON salesProductsList.sale_id = salesList.id" )
    ->select( null )->select( [
        "salesList.id",
        "salesList.created_at",
        "salesProductsList.product_id",
        "salesProductsList.cost",
        "salesProductsList.amount"
    ] )
    ->where( $salesFilter )
    ->orderBy( "salesList.created_at desc" )
    ->limit( 0 );

/**
 * Получение групп услуг
 */
$serviceGroups = $API->DB->from( "serviceGroups" )
    ->where( "is_active", "Y" );

/**
 * Получение текущего времени
 */
$currentDateTime = new DateTime();

/**
 * Обход групп услуг
 */
foreach ( $serviceGroups as $serviceGroup ) {

    /**
     * Обход продаж
     */
    foreach ( $salesList as $sale ) {

        /**
         * Получение детальной информации об услуги
         */
        $serviceDetail = $API->DB->from( "services" )
            ->where( "id",  $sale[ "product_id" ] )
            ->limit( 1 )
            ->fetch();

        /**
         * Заполнение списка
         */
        if ( $serviceDetail[ "category_id" ] == $serviceGroup[ "id" ] ) {

            $returnServices[$serviceGroup[ "id" ]][ "title" ] = $serviceGroup[ "title" ];

            if ( $sale[ "created_at" ] >= $currentDateTime->format( "Y-m-01 00:00:00" ) ){

                $returnServices[$serviceGroup[ "id" ]][ "sum_one" ] = $returnServices[$serviceGroup[ "id" ]][ "sum_one" ] + $sale[ "amount" ] * $sale[ "cost" ];

            }

            if ( $sale[ "created_at" ] >= $currentDateTime->modify( "-1 month" )->format( "Y-m-01 00:00:00" ) ) {

                $returnServices[$serviceGroup[ "id" ]][ "sum_two" ] = $returnServices[$serviceGroup[ "id" ]][ "sum_two" ] + $sale[ "amount" ] * $sale[ "cost" ];

            }

            if ( $sale[ "created_at" ] >= $currentDateTime->modify( "-2 month" )->format( "Y-m-01 00:00:00" ) ) {

                $returnServices[$serviceGroup[ "id" ]][ "sum_three" ] = $returnServices[$serviceGroup[ "id" ]][ "sum_three" ] + $sale[ "amount" ] * $sale[ "cost" ];

            }

        }
    }


} // foreach. $services

$response[ "data" ] = array_values($returnServices);

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

if ( $sort_by == "sum_one" ) {

    if ( $sort_order == "desc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "sum_one", SORT_DESC ) );
    if ( $sort_order == "asc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "sum_one", SORT_ASC ) );

}

if ( $sort_by == "sum_two" ) {

    if ( $sort_order == "desc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "sum_two", SORT_DESC ) );
    if ( $sort_order == "asc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "sum_two", SORT_ASC ) );

}
if ( $sort_by == "sum_three" ) {

    if ( $sort_order == "desc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "sum_three", SORT_DESC ) );
    if ( $sort_order == "asc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "sum_three", SORT_ASC ) );

}

$response[ "detail" ] = [

    "pages_count" => ceil(count($response[ "data" ]) / $requestData->limit),
    "rows_count" => count($response[ "data" ])

];

$response[ "data" ] = array_slice($response[ "data" ], $requestData->limit * $requestData->page - $requestData->limit, $requestData->limit);
