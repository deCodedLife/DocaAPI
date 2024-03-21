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
 * Фильтр по Филиалу
 */

if ( $requestData->stores_id ) {

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

        $userStores = $API->DB->from( "users_stores" )
            ->where( "user_id", $row[ "id" ] );

        foreach ( $userStores as $userStore )
            if ( $userStore[ "store_id" ] == $requestData->stores_id ) $isContinue = false;

        if ( $isContinue ) continue;


        $filteredRows[] = $row;

    } // foreach. $response[ "data" ]


    /**
     * Обновление списка Записей
     */
    $response[ "data" ] = $filteredRows;

} // if. $requestData->store_id

/**
 * Подстановка ФИО
 */

$returnRows = [];

foreach ( $response[ "data" ] as $row ) {

    $row[ "fio" ] = $row[ "last_name" ] . " " . $row[ "first_name" ] . " " . $row[ "patronymic" ];
    $returnRows[] = $row;

} // foreach. $response[ "data" ]

$response[ "data" ] = $returnRows;


if ( $API->isPublicAccount() ) {

    $siteUsers = [];

    foreach ( $response[ "data" ] as $key => $user ) {

        /**
         * Фильтр по филиалу
         */
        if ( property_exists( $API->request->data, "store_id" ) ) {
            $userStores = array_map( fn( $store ) => $store[ "value" ], $user[ "stores_id" ] ?? [] );
            if ( !in_array( $API->request->data->store_id, $userStores ) ) continue;
        }


        $phoneFormat = "+" . sprintf("%s (%s) %s-%s-%s",
                substr( $user[ "phone" ], 0, 1 ),
                substr( $user[ "phone" ], 1, 3 ),
                substr( $user[ "phone" ], 4, 3 ),
                substr( $user[ "phone" ], 7, 2 ),
                substr( $user[ "phone" ], 9 )
            );

        $siteUsers[] = [

            "id" => $user[ "id" ],
            "fio" => $user[ "fio" ]

        ];

    }

    $response[ "data" ] = $siteUsers;

}


/**
 * Обработка чата
 */
if ( $requestData->context->block === "chat" ) {

    $chatContext = [];

    foreach ( $response[ "data" ] as $user ) {

        $chatContext[] = [
            "id" => $user[ "id" ],
            "title" => "{$user[ "last_name" ]} {$user[ "first_name" ]} {$user[ "patronymic" ]}"
        ];

    }

    $response[ "data" ] = $chatContext;

}