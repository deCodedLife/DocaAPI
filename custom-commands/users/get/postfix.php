<?php

/**
 * Фильтр по Специальностям
 */

if ( $requestData->professions_id ) {

    /**
     * Отфильтрованные Записи
     */
    $filteredRows = [];


    /**
     * Фильтрация Записей
     */

    foreach ( $response[ "data" ] as $row ) {

        $isContinue = true;


        /**
         * Запрос специальностей
         */

        $userProfessions = $API->DB->from( "users_professions" )
            ->where( "user_id", $row[ "id" ] );

        foreach ( $userProfessions as $userProfession )
            if ( $userProfession[ "profession_id" ] == $requestData->professions_id ) $isContinue = false;

        if ( $isContinue ) continue;


        $filteredRows[] = $row;

    } // foreach. $response[ "data" ]


    /**
     * Обновление списка Записей
     */
    $response[ "data" ] = $filteredRows;

} // if. $requestData->professions_id


/**
 * Подстановка ФИО
 */

$returnRows = [];

foreach ( $response[ "data" ] as $row ) {

    $row[ "fio" ] = $row[ "last_name" ] . " " . $row[ "first_name" ] . " " . $row[ "patronymic" ];
    $returnRows[] = $row;

} // foreach. $response[ "data" ]

$response[ "data" ] = $returnRows;