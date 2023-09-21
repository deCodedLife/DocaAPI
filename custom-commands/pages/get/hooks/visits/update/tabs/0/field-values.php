<?php

/**
 * Кастомизация выпадающего списка клиентов
 */

/**
 * Сформированный массив клиентов
 */
$clients = [];

/**
 * Обход списка
 */
foreach ( $generatedTab[ "settings" ][ "areas" ][ 0 ][ "blocks" ][ 1 ][ "fields" ][ 1 ][ "list" ] as $client ) {

    /**
     * Получение детальной информации о клиенте
     */
    $clientDetail = $API->DB->from( "clients" )
        ->where( "id", $client[ "value" ] )
        ->limit( 1 )
        ->fetch();

    /**
     * Форматирование телефона
     */
    $phoneFormat = "+" . sprintf("%s (%s) %s-%s-%s",
            substr($clientDetail [ "phone" ], 0, 1),
            substr($clientDetail [ "phone" ], 1, 3),
            substr($clientDetail [ "phone" ], 4, 3),
            substr($clientDetail [ "phone" ], 7, 2),
            substr($clientDetail [ "phone" ], 9)
        );

    /**
     * Форматирование телефона
     */
    $clients[] = [
        "title" => $clientDetail[ "last_name" ] . " " . $clientDetail[ "first_name" ] . " " . $clientDetail[ "patronymic" ] . ", " . $phoneFormat,
        "value" => $client[ "value" ]
    ];

}

/**
 * Переназначение значений списка
 */
$generatedTab[ "settings" ][ "areas" ][ 0 ][ "blocks" ][ 1 ][ "fields" ][ 1 ][ "list" ] = $clients;
