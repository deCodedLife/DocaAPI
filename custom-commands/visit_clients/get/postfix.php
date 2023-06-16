<?php

/**
 * Формирование списка пользователей
 */


if ( $requestData->context->block === "list" ) {

    /**
     * Сформированный список
     */
    $filter = [];

    if ( $requestData->start_at ) $filter[ "start_at >= ?" ] = $requestData->start_at . " 00:00:00";
    if ( $requestData->end_at ) $filter[ "end_at <= ?" ] = $requestData->end_at . " 23:59:59";
    if ( $requestData->store_id ) $filter[ "store_id" ] = $requestData->store_id;

    $returnVisits = [ ];

    $visitsСlients = $API->DB->from( "visits_clients" );

    foreach ( $response[ "data" ] as $visit ) {

        $user_id = 0;

        foreach ( $visitsСlients as $visitСlient ) {

            if ( $visitСlient[ "visit_id" ] == $visit[ "id" ]) {

                $user_id = $visitСlient[ "client_id" ];

            }

        }

        $user = $API->DB->from( "clients" )
            ->where( "id", $user_id )
            ->limit( 1 )
            ->fetch( );

        $visitsServices = $API->DB->from( "visits_services" )
            ->where( "visit_id", $visit[ "id" ] );

        $services = [ ];

        foreach ($visitsServices as $visitsService) {

            $service = $API->DB->from( "services" )
                ->where( "id", $visitsService["service_id"] )
                ->limit( 1 )
                ->fetch( );
            $services = [ "title" => $service[ "title" ] ];

        }

        $returnVisits[] = [
            "client_id" => $user_id,
            "fio" => $user[ "last_name" ] . " " .$user[ "first_name" ] . " " . $user[ "patronymic" ],
            "services" => $services,
            "price" => $visit[ "price" ],
            "start_at" => $visit[ "start_at" ],
            "end_at" => $visit[ "end_at"],
            "store_id" => $visit[ "store_id"]
        ];

    }

    $response[ "data" ] = $returnVisits;


} // if. $requestData->context->block === "list"
