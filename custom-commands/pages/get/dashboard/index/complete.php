<?php

setlocale(LC_ALL, 'ru_RU.UTF-8');
$previousMonth =  strftime("%B", strtotime("last month"));
$previous2Month =  strftime("%B", strtotime("last month", strtotime("now - 1 month")));
$previous3Month =  strftime("%B", strtotime("last month", strtotime("now - 2 month")));

if ( isset( $response[ "data" ][ 1 ][ "settings" ][ "headers" ][ 1 ] ) ) {


    $response[ "data" ][ 1 ][ "settings" ][ "headers" ][ 1 ][ "title" ] = "C " . $previousMonth;
    $response[ "data" ][ 1 ][ "settings" ][ "headers" ][ 2 ][ "title" ] = "C " . $previous2Month;
    $response[ "data" ][ 1 ][ "settings" ][ "headers" ][ 3 ][ "title" ] = "C " . $previous3Month;

}

if ( isset( $response[ "data" ][ 1 ][ "settings" ][ "headers" ][ 2 ] ) ) {

    $response[ "data" ][ 2 ][ "settings" ][ "headers" ][ 1 ][ "title" ] = "Смены " . $previousMonth;
    $response[ "data" ][ 2 ][ "settings" ][ "headers" ][ 3 ][ "title" ] = "Смены " . $previous2Month;
    $response[ "data" ][ 2 ][ "settings" ][ "headers" ][ 5 ][ "title" ] = "Смены " . $previous3Month;

}
