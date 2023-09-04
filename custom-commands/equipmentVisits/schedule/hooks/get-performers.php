<?php

/**
 * Фильтр по Сотруднику
 */

if ( $requestData->equipment_id ) {

    /**
     * Отфильтрованный список сотрудников
     */
    $filteredUsers = [];


    /**
     * Добавление сотрудников с указанной специальностью
     * в отфильтрованный список
     */
    foreach ( $performersRows as $performersRowKey => $performersRow )
        if ( $performersRow[ "id" ] == $requestData->equipment_id ) $filteredUsers[] = $performersRow;


    /**
     * Обновление списка сотрудников
     */
    $performersRows = $filteredUsers;

} // if. $requestData->users_id


/**
 * Фильтр по филиалу
 */

if ( $requestData->store_id ) {

    /**
     * Отфильтрованный список сотрудников
     */
    $filteredUsers = [];


    /**
     * Добавление сотрудников с указанной специальностью
     * в отфильтрованный список
     */
    foreach ( $performersRows as $performersRowKey => $performersRow ) {

        $userStores = $API->DB->from( "equipment_stores" )
            ->where( [
                "store_id" => $requestData->store_id,
                "equipment_id" => $performersRow[ "id" ]
            ] )
            ->limit( 1 )
            ->fetch();

        if ( !empty( $userStores ) ) $filteredUsers[] = $performersRow;

    }


    /**
     * Обновление списка сотрудников
     */
    $performersRows = $filteredUsers;

} // if. $requestData->users_id
