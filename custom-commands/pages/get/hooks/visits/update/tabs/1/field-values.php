<?php

/**
 * Получение информации об услугах
 */

$servicesInfo = [];

foreach ( $pageDetail[ "row_detail" ][ "services_id" ] as $service )
    $servicesInfo[] = $service->title;

$generatedTab[ "settings" ][ "areas" ][ 0 ][ "blocks" ][ 0 ][ "fields" ][ 0 ][ "value" ] = $servicesInfo;

