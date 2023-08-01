<?php

/**
 * Подстановка ФИО
 */

$returnRows = [];

foreach ( $response[ "data" ] as $row ) {

    /**
     * Получение детальной информации о клиенте
     */

    $handbookDetail = $API->DB->from( "handbook" )
        ->where( "id", $row[ "value" ] )
        ->limit( 1 )
        ->fetch();

    $returnRows[] = $row;

} // foreach. $response[ "data" ]

$response[ "data" ] = $returnRows;