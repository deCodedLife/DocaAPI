<?php

/**
 * @file
 * Формирование списка клиентов посещавшие специалистов
 */


if ( !$requestData->user_id ) $API->returnResponse( [] );


/**
 * Сформированный список
 */
$returnVisits = [];


foreach ( $response[ "data" ] as $visit ) {

    /**
     * Массив клиентов
     */
    $clients = [];

    /**
     * Массив услуг
     */
    $services = [];


    /**
     * Получение услуг из заявки
     */
    $visits_services = $API->DB->from( "visits_services" )
        ->where( "visit_id",  $visit[ "id" ]);

    foreach ( $visits_services as $visit_services ) {

        $service = $API->DB->from( "services" )
            ->where( "id",  $visit_services[ "service_id"] )
            ->limit( 1 )
            ->fetch();

        $services[$visit[ "id" ]] = [

            "title" => $service[ "title" ],
            "value" => (int)$service[ "id" ]

        ];

    }

    /**
     * Получение клиентов из заявки
     */
    $visits_clients = $API->DB->from( "visits_clients" )
        ->where( "visit_id",  $visit[ "id" ]);

    foreach ( $visits_clients as $visit_clients ) {

        $client = $API->DB->from( "clients" )
            ->where( "id",  $visit_clients[ "client_id"] )
            ->limit( 1 )
            ->fetch();

        $clients[] = [

            "fio" => $client[ "last_name" ] . " " . $client[ "first_name" ] . " " . $client[ "patronymic" ],
            "id" => (int)$visit_clients[ "client_id" ]

        ];

    }

    $returnVisits[] = [

        "id" => $visit[ "id" ],
        "client_id" => $clients[ 0 ][ "id" ],
        "fio" => $clients[ 0 ][ "fio" ],
        "services_id" => $services[$visit[ "id" ]],
        "price" => $visit[ "price" ],
        "period" => date( 'Y-m-d H:i', strtotime( $visit[ "start_at" ] ) ) . " - " . date( "H:i", strtotime( $visit[ "end_at" ] ) )

    ];

}

$response[ "data" ] = $returnVisits;

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

}

if ( $sort_by == "id" ) {

    if ( $sort_order == "desc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "id", SORT_DESC ) );
    if ( $sort_order == "asc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "id", SORT_ASC ) );

}

if ( $sort_by == "fio" ) {

    if ( $sort_order == "desc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "fio", SORT_DESC ) );
    if ( $sort_order == "asc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "fio", SORT_ASC ) );

}

if ( $sort_by == "price" ) {

    if ( $sort_order == "desc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "price", SORT_DESC ) );
    if ( $sort_order == "asc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "price", SORT_ASC ) );

}

if ( $sort_by == "period" ) {

    if ( $sort_order == "desc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "period", SORT_DESC ) );
    if ( $sort_order == "asc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "period", SORT_ASC ) );

}



$response[ "detail" ] = [

    "pages_count" => ceil(count($response[ "data" ]) / $requestData->limit),
    "rows_count" => count($response[ "data" ])

];

$response[ "data" ] = array_slice($response[ "data" ], $requestData->limit * $requestData->page - $requestData->limit, $requestData->limit);
