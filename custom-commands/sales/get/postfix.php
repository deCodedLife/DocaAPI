<?php

if ( $requestData->context === "list" ) {

    $returnSales = [];

    /**
     * Формирование списка пользователей
     */
    foreach ( $response[ "data" ] as $sale ) {

        if ( $sale[ "pay_type" ][ "value" ] == "sellReturn" )
            continue;

        if ( $sale[ "status" ][ "value" ] != "done" )
            continue;

        $sale[ "clients" ] = [];

        foreach ( $sale[ "visits_ids" ] as $visit ) {

            $visitClients = $API->DB->from( "clients" )
                ->innerJoin( "visits_clients ON visits_clients.client_id = clients.id" )
                ->where( "visit_id", $visit[ "value" ] );

            foreach ( $visitClients as $visitClient )
                $sale[ "clients" ][] = [
                  "title" => $visitClient[ "last_name" ],
                  "value" => $visitClient[ "id" ]
                ];

        }

        $returnSales[] = $sale;

    }


    /**
     * Обновление списка пользователей
     */
    $response[ "data" ] = $returnSales;


}