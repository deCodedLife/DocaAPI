<?php

global $API;

/**
 * Подстановка ФИО
 */

$returnRows = [];

foreach ( $response[ "data" ] as $row ) {

    /**
     * Получение детальной информации о клиенте
     */

    $clientDetail = $API->DB->from( "clients" )
        ->where( "id", $row[ "id" ] ?? $row[ "value" ] )
        ->limit( 1 )
        ->fetch();

    $phoneFormat = "+" . sprintf("%s (%s) %s-%s-%s",
            substr( $clientDetail[ "phone" ], 0, 1 ),
            substr( $clientDetail[ "phone" ], 1, 3 ),
            substr( $clientDetail[ "phone" ], 4, 3 ),
            substr( $clientDetail[ "phone" ], 7, 2 ),
            substr( $clientDetail[ "phone" ], 9 )
        );

    /**
     * Формирование title записи
     */

    $client = "{$clientDetail[ "last_name" ]} {$clientDetail[ "first_name" ]} {$clientDetail[ "patronymic" ]}";
    $row[ "fio" ] = $client;
    $row[ "phone" ] = $phoneFormat;
    $row[ "menu_title" ] = "$client $phoneFormat";
    $row[ "title" ] = "$client";
    $returnRows[] = $row;

} // foreach. $response[ "data" ]

$response[ "data" ] = $returnRows;


$role = $API->DB->from( "roles" )
    ->where( "id", $API::$userDetail->role_id )
    ->limit(1)
    ->fetch()[ "article" ];

if ( $role == "support" ) {

    $siteClients = [];

    foreach ( $response[ "data" ] as $client ) {

        $siteClients[] = [

            "id" => $client[ "id" ],
            "fio" => $client[ "fio" ],
            "phone" => $client[ "phone" ],

        ];

    }

    $response[ "data" ] = $siteClients;

}