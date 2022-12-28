<?php

/**
 * Отключение кнопок у завершенных Записей
 */

if ( $pageDetail[ "row_detail" ][ "status" ]->value === "ended" )
    $pageScheme[ "structure" ][ 1 ][ "settings" ][ 0 ][ "body" ][ 0 ][ "components" ][ "buttons" ] = [];