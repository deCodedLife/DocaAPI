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

function array_sort ( $array, $on, $order=SORT_ASC ) {

    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
                break;
            case SORT_DESC:
                arsort($sortable_array);
                break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;

}

if ( $sort_by == "fio" ) {

    if ( $sort_order == "desc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "fio", SORT_DESC ) );
    if ( $sort_order == "asc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "fio", SORT_ASC ) );

}
