<?php
/**
 * @file
 * Формирование списка клиентов посещавшие специалистов
 */

if ( $requestData->user_id ) {

    /**
     * Сформированный список
     */
    $returnVisits = [];

    /**
     * Фильтр посещений
     */

    $visitsFilter = [];

    if ( $requestData->start_price ) $visitsFilter[ "price >= ?" ] = $requestData->start_price;
    if ( $requestData->end_price ) $visitsFilter[ "price <= ?" ] = $requestData->end_price;
    if ( $requestData->start_at ) $visitsFilter[ "start_at >= ?" ] = $requestData->start_at . " 00:00:00";
    if ( $requestData->end_at ) $visitsFilter[ "start_at <= ?" ] = $requestData->end_at . " 23:59:59";
    if ( $requestData->store_id ) $visitsFilter[ "store_id" ] = $requestData->store_id;
    if ( $requestData->user_id ) $visitsFilter[ "user_id" ] = $requestData->user_id;
    $visitsFilter[ "is_payed" ] = "Y" ;


    /**
     * Получение посещений Сотрудника
     */
    $visits = $API->DB->from( "visits" )
        ->where( $visitsFilter );



    foreach (  $visits as $visit ) {

        /**
         * Масив клиентов
         */
        $clients = [];

        /**
         * Масив услуг
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

} else {

    $response[ "data" ] = [];

}

