<?php

$eventStart = explode( " ", $response[ "data" ][ 0 ][ "settings" ][ "areas" ][ 0 ][ "blocks" ][ 1 ][ "fields" ][ 0 ][ "value" ] );
$eventEnd = explode( " ", $response[ "data" ][ 0 ][ "settings" ][ "areas" ][ 0 ][ "blocks" ][ 1 ][ "fields" ][ 1 ][ "value" ] );


/**
 * Обновление состава полей
 */
$response[ "data" ][ 0 ][ "settings" ][ "areas" ][ 0 ][ "blocks" ][ 0 ][ "fields" ][ 0 ][ "value" ] = $eventStart[ 0 ];
$response[ "data" ][ 0 ][ "settings" ][ "areas" ][ 0 ][ "blocks" ][ 0 ][ "fields" ][ 1 ][ "value" ] = $eventEnd[ 0 ];
$response[ "data" ][ 0 ][ "settings" ][ "areas" ][ 0 ][ "blocks" ][ 1 ][ "fields" ][ 0 ][ "value" ] = $eventStart[ 1 ];
$response[ "data" ][ 0 ][ "settings" ][ "areas" ][ 0 ][ "blocks" ][ 1 ][ "fields" ][ 1 ][ "value" ] = $eventEnd[ 1 ];
