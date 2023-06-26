<?php

if ( $requestData->context->block === "list" ) {

    $returnSales = [];
    $filter = [];

    /**
     * Формирование списка пользователей
     */
    foreach ( $response[ "data" ] as $sale ) {

        /**
         * Учёт части фильтров, которая не работает в prefix
         */
        if ( $requestData->store_id && $sale[ "store_id" ][ "value" ] != $requestData->store_id ) continue;
        if ( $requestData->pay_type && $sale[ "pay_type" ][ "value" ] != $requestData->pay_type ) continue;
        if ( $requestData->client_id && $sale[ "client_id" ][ "value" ] != $requestData->client_id ) continue;
        if ( $requestData->pay_method && $sale[ "pay_method" ][ "value" ] != $requestData->pay_method ) continue;

        /**
         * Вывод только операций оплаты
         */
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