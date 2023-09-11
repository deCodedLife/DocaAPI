<?php


/**
 * Фильтр по Сотруднику
 */

if ( $requestData->user_id ) {

    /**
     * Отфильтрованный список сотрудников
     */
    $filteredUsers = [];


    /**
     * Добавление сотрудников с указанной специальностью
     * в отфильтрованный список
     */
    foreach ( $performersRows as $performersRowKey => $performersRow )
        if ( $performersRow[ "id" ] == $requestData->user_id ) $filteredUsers[] = $performersRow;


    /**
     * Обновление списка сотрудников
     */
    $performersRows = $filteredUsers;

} // if. $requestData->users_id

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

    $usersProfessions = mysqli_query(
      $API->DB_connection,
      "SELECT user_id, profession_id FROM users_professions where profession_id = $requestData->profession_id"
    );

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

        $userStores = mysqli_fetch_array(
            mysqli_query(
                $API->DB_connection,
                "SELECT * FROM users_stores WHERE store_id = $requestData->store_id AND user_id = {$performersRow[ "id" ]}"
            )
        );

        if ( !empty( $userStores ) ) $filteredUsers[] = $performersRow;

    }


    /**
     * Обновление списка сотрудников
     */
    $performersRows = $filteredUsers;

} // if. $requestData->users_id