<?php
/**
 * Фильтр Записей по Клиентам
 */

if ( $requestData->clients_id ) {

    /**
     * Отфильтрованные Записи
     */
    $filteredEvents = [];


    /**
     * Фильтрация Записей
     */

    foreach ( $response[ "data" ] as $event ) {

        $isContinue = true;

        if ( !is_array( $event[ "client_id" ] ) ) {

            $arrayClients = [];
            $arrayClients[] = [ "value" => $event[ "client_id" ] ];

            $event[ "client_id" ] = $arrayClients;

        }

        foreach ( $event[ "client_id" ] as $eventClient )

            $eventClient[ "value" ] = [ $eventClient[ "value" ] ];

        if ( $eventClient[ "value" ] == $requestData->clients_id ) {

            $isContinue = false;

        }

        if ( !$isContinue ) $filteredEvents[] = $event;

    } // foreach. $response[ "data" ]


    /**
     * Обновление списка Записей
     */
    $response[ "data" ] = $filteredEvents;

} // if. $requestData->clients_id