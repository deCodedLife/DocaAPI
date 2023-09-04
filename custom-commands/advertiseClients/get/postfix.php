<?php
/**
 * @file
 * Список "ректамные источники
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

/**
 * Формирование фильтров
 */
if ( $requestData->id ) $advertisesFilter[ "id" ] = $requestData->id;

if ( $requestData->start_price ) $visitsFilter[ "price >= ?" ] = $requestData->start_price;
if ( $requestData->end_price ) $visitsFilter[ "price <= ?" ] = $requestData->end_price;
if ( $requestData->store_id ) $visitsFilter[ "visits.store_id" ] = $requestData->store_id;
if ( $requestData->start_at ) $visitsFilter[ "end_at >= ?" ] = $requestData->start_at . " 00:00:00";
if ( $requestData->end_at ) $visitsFilter[ "end_at <= ?" ] = $requestData->end_at . " 23:59:59";
$visitsFilter[ "visits.is_payed" ] = "Y";

/**
 * Получение рекламных источников
 */
$advertises = $API->DB->from( "advertise" )
    ->where( $advertisesFilter );

/**
 * Обход рекламных источников
 */
foreach ( $advertises as $advertise ) {

    /**
     * Колличество посещений
     */
    $visitsCount = 0;

    /**
     * Колличество посещений
     */
    $visitsPrice = 0;

    /**
     * Колличество записаных Клиентов
     */
    $recordedCount = 0;

    /**
     * Колличество получивших услугу Клиентов
     */
    $extantCount = 0;

    /**
     * Колличество недошедших Клиентов
     */
    $underdoneCount = 0;

    /**
     * Получение клиентов
     */
    $clients = $API->DB->from( "clients" )
        ->where( "advertise_id", $advertise [ "id" ] );

    foreach ( $clients as $client ) {

        /**
         * Фильтрация посещений по клиенту
         */
        $visitsFilter[ "visits_clients.client_id" ] = $client[ "id" ];

        /**
         * Получение посещений Клиента
         */
        $clientVisits = $API->DB->from( "visits" )
            ->leftJoin( "visits_clients ON visits_clients.visit_id = visits.id" )
            ->select( null )->select( [ "visits.id", "visits.status", "visits.start_at", "visits.store_id", "visits.price", "visits.status", "visits.is_payed" ] )
            ->where( $visitsFilter )
            ->orderBy( "visits.start_at desc" )
            ->limit( 0 );

        if ( $clientVisits ) {

            $recordedCount++;

        } else {

            $underdoneCount++;

        }
        /**
         * Обход посещений клиента
         */
        foreach ( $clientVisits as $clientVisit ) {

            if ( $clientVisit[ "status" ] == "ended" ) {

                $extantCount++;

            }
            $visitsCount++;
            $visitsPrice += $clientVisit[ "price" ];


        } // foreach. $userVisits

    } // foreach. $clients

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
