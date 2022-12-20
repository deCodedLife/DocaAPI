<?php

/**
 * Фильтр по Специальности
 */

if ( $requestData->profession_id ) {

    /**
     * Отфильтрованный список сотрудников
     */
    $filteredUsers = [];


    /**
     * Получение списка сотрудников с указанной специальностью
     */

    $usersWithCurrentProfession = [];

    $usersProfessions = $API->DB->from( "users_professions" )
        ->select( [ "user_id", "profession_id" ] )
        ->where( "profession_id", $requestData->profession_id );

    foreach ( $usersProfessions as $userProfession ) $usersWithCurrentProfession[] = $userProfession[ "user_id" ];


    /**
     * Добавление сотрудников с указанной специальностью
     * в отфильтрованный список
     */
    foreach ( $performersRows as $performersRowKey => $performersRow )
        if ( in_array( $performersRow[ "id" ], $usersWithCurrentProfession ) ) $filteredUsers[] = $performersRow;


    /**
     * Обновление списка сотрудников
     */
    $performersRows = $filteredUsers;

} // if. $requestData->profession_id

