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

        foreach ( $event[ "clients_id" ] as $eventClient )
            if ( $eventClient[ "value" ] == $requestData->clients_id ) $isContinue = false;

        if ( !$isContinue ) $filteredEvents[] = $event;

    } // foreach. $response[ "data" ]


    /**
     * Обновление списка Записей
     */
    $response[ "data" ] = $filteredEvents;

} // if. $requestData->clients_id