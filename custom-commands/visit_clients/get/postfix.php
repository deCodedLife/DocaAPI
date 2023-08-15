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
    if ( $requestData->start_at ) $visitsFilter[ "start_at >= ?" ] = $requestData->start_at . " 00:00:00";
    if ( $requestData->end_at ) $visitsFilter[ "start_at <= ?" ] = $requestData->end_at . " 23:59:59";
    if ( $requestData->store_id ) $visitsFilter[ "store_id" ] = $requestData->store_id;
    if ( $requestData->user_id ) $visitsFilter[ "visits_users.user_id" ] = $requestData->user_id;
    $visitsFilter[ "is_payed" ] = "Y" ;


    /**
     * Получение посещений Сотрудника
     */
    $visits = $API->DB->from( "visits" )
        ->leftJoin( "visits_users ON visits_users.visit_id = visits.id" )
        ->select( null )->select( [ "visits.id", "visits_users.user_id", "visits.start_at",  "visits.end_at", "visits.is_active", "visits.price" , "visits.status" ] )
        ->where( $visitsFilter )
        ->orderBy( "visits.start_at desc" )
        ->limit( 0 );


    /**
     * Масив клиентов
     */
    $clients = [];

    /**
     * Масив услуг
     */
    $services = [];

    foreach (  $visits as $visit ) {

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

            $services[] = [

                "title" => $service[ "title" ],
                "value" => (int)$visit_services[ "service_id" ]

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
            "user_id" => $visit[ "user_id" ],
            "id" => $visit[ "id" ],
            "client_id" => $clients[ 0 ][ "id" ],
            "fio" => $clients[ 0 ][ "fio" ],
            "services_id" => $services,
            "price" => $visit[ "price" ],
            "end_at" => $visit[ "end_at" ],
            "start_at" => $visit[ "start_at" ],
            "period" => date( 'Y-m-d H:i', strtotime( $visit[ "start_at" ] ) ) . " - " . date( "H:i", strtotime( $visit[ "end_at" ] ) )

        ];

    }

    $response[ "data" ] = $returnVisits;

} else {

    $response[ "data" ] = [];

}

