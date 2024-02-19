<?php

/**
 * Подстановка ФИО
 */

foreach ( $response[ "data" ] as $key => $row ) {

    $row[ "fio" ] = $row[ "last_name" ] . " " . $row[ "first_name" ] . " " . $row[ "patronymic" ];
    $response[ "data" ][ $key ] = $row;

} // foreach. $response[ "data" ]