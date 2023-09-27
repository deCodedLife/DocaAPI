<?php

/**
 * @file
 * Список "Рекламные источники
 */

/**
 * Сформированный список
 */
$returnAdvertises = [];


/**
 * Фильтр источников
 */
$advertisesFilter = [];

/**
 * Фильтр посещений
 */
$visitsFilter = [];

if ( !$requestData->id ) $API->returnResponse( [] );


/**
 * Формирование фильтров
 */
if ( $requestData->start_price ) $visitsFilter[ "price >= ?" ] = $requestData->start_price;
if ( $requestData->end_price ) $visitsFilter[ "price <= ?" ] = $requestData->end_price;
if ( $requestData->start_at ) $visitsFilter[ "end_at >= ?" ] = $requestData->start_at . " 00:00:00";
if ( $requestData->end_at ) $visitsFilter[ "end_at <= ?" ] = $requestData->end_at;
if ( $requestData->store_id ) $visitsFilter[ "store_id" ] = $requestData->store_id;

$visitsFilter[ "is_payed" ] = "Y";


/**
 * Обход рекламных источников
 */
foreach ( $response[ "data" ] as $advertise ) {

    /**
     * Количество посещений
     */
    $visitsCount = 0;

    /**
     * Количество посещений
     */
    $visitsPrice = 0;

    /**
     * Количество записаных Клиентов
     */
    $recordedCount = 0;

    /**
     * Количество получивших услугу Клиентов
     */
    $extantCount = 0;

    /**
     * Количество недошедших Клиентов
     */
    $underdoneCount = 0;

    /**
     * Получение клиентов
     */

    $clients = $API->DB->from( "clients" )
        ->where( "advertise_id", $advertise[ "id" ] );


    /**
     * Получение посещений
     */

    $visitsFilter[ "advert_id" ] = $advertise[ "id" ];

    $visits = $API->DB->from( "visits" )
        ->where( $visitsFilter );

    foreach ( $visits as $visit ) {

        if ( $visit[ "status" ] == "ended" ) $extantCount++;
        $visitsCount++;
        $recordedCount++;
        $visitsPrice += $visit[ "price" ];

    } // foreach. $visits
    

    $returnAdvertises[] = [

        "id" => $advertise["id"],
        "title" => $advertise["title"],
        "clientsCount" => count( $clients ),
        "recordedCount" => $recordedCount,
        "extantCount" => $extantCount,
        "underdoneCount" => $underdoneCount,
        "visitsCount" => $visitsCount,
        "price" => $visitsPrice

    ];

}

$response[ "data" ] = $returnAdvertises;

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

if ( $sort_by == "clientsCount" ) {

    if ( $sort_order == "desc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "clientsCount", SORT_DESC ) );
    if ( $sort_order == "asc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "clientsCount", SORT_ASC ) );

}

if ( $sort_by == "recordedCount" ) {

    if ( $sort_order == "desc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "recordedCount", SORT_DESC ) );
    if ( $sort_order == "asc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "recordedCount", SORT_ASC ) );

}

if ( $sort_by == "extantCount" ) {

    if ( $sort_order == "desc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "extantCount", SORT_DESC ) );
    if ( $sort_order == "asc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "extantCount", SORT_ASC ) );

}

if ( $sort_by == "underdoneCount" ) {

    if ( $sort_order == "desc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "underdoneCount", SORT_DESC ) );
    if ( $sort_order == "asc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "underdoneCount", SORT_ASC ) );

}

if ( $sort_by == "visitsCount" ) {

    if ( $sort_order == "desc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "visitsCount", SORT_DESC ) );
    if ( $sort_order == "asc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "visitsCount", SORT_ASC ) );


}

if ( $sort_by == "price" ) {

    if ( $sort_order == "desc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "price", SORT_DESC ) );
    if ( $sort_order == "asc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "price", SORT_ASC ) );

}

if ( $sort_by == "title" ) {

    if ( $sort_order == "desc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "title", SORT_DESC ) );
    if ( $sort_order == "asc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "title", SORT_ASC ) );

}

$response[ "detail" ] = [

    "pages_count" => ceil(count($response[ "data" ]) / $requestData->limit),
    "rows_count" => count($response[ "data" ])

];

$response[ "data" ] = array_slice($response[ "data" ], $requestData->limit * $requestData->page - $requestData->limit, $requestData->limit);
