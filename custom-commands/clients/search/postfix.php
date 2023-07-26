<?php

/**
 * Подстановка ФИО
 */

$returnRows = [];

foreach ( $response[ "data" ] as $row ) {

    /**
     * Получение детальной информации о клиенте
     */

    $clientDetail = $API->DB->from( "clients" )
        ->where( "id", $row[ "value" ] )
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

    $fio = explode( " ", $row[ "title" ] );

    $row[ "title" ] = $fio[ 0 ];

    if ( $fio[ 1 ] ) $row[ "title" ] .= " " . mb_substr( $fio[ 1 ], 0, 1 ) . ".";
    if ( $fio[ 2 ] ) $row[ "title" ] .= " " . mb_substr( $fio[ 2 ], 0, 1 ) . ".";

    $row[ "title" ] .= " $phoneFormat";


    $returnRows[] = $row;

} // foreach. $response[ "data" ]

$response[ "data" ] = $returnRows;