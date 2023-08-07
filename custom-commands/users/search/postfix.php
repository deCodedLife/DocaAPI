<?php

/**
 * Подстановка ФИО
 */

$returnRows = [];

foreach ( $response[ "data" ] as $row ) {

    /**
     * Получение детальной информации о клиенте
     */
    $clientDetail = $API->DB->from( "users" )
        ->where( "id", $row[ "id" ] ?? $row[ "value" ] )
        ->limit( 1 )
        ->fetch();

    $user = "{$clientDetail[ "last_name" ]} {$clientDetail[ "first_name" ]} {$clientDetail[ "patronymic" ]}";
    $row[ "fio" ] = $user;

    $returnRows[] = $row;

} // foreach. $response[ "data" ]

$response[ "data" ] = $returnRows;