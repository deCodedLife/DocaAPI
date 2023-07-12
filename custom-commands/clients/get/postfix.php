<?php

/**
 * Подстановка ФИО
 */

$returnRows = [];

foreach ( $response[ "data" ] as $row ) {

    $row[ "fio" ] = $row[ "last_name" ] . " " . $row[ "first_name" ] . " " . $row[ "patronymic" ];
    $returnRows[] = $row;

} // foreach. $response[ "data" ]

$response[ "data" ] = $returnRows;