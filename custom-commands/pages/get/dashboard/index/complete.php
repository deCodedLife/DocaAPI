<?php

if ( isset( $response[ "data" ][ 1 ][ "settings" ][ "headers" ][ 1 ] ) ) {

    setlocale(LC_ALL, 'ru_RU.UTF-8');
    $previousMonth =  strftime("%B", strtotime("last month"));
    $previous2Month =  strftime("%B", strtotime("last month", strtotime("now - 1 month")));
    $previous3Month =  strftime("%B", strtotime("last month", strtotime("now - 2 month")));

    $response[ "data" ][ 1 ][ "settings" ][ "headers" ][ 1 ][ "title" ] = "C " . $previousMonth;
    $response[ "data" ][ 1 ][ "settings" ][ "headers" ][ 2 ][ "title" ] = "C " . $previous2Month;
    $response[ "data" ][ 1 ][ "settings" ][ "headers" ][ 3 ][ "title" ] = "C " . $previous3Month;

}

