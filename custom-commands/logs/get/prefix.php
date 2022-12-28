<?php

/**
 * Фильтр логов за день
 */

if ( !$requestData->created_at ) $requestData->created_at = date( "Y-m-d" );

$requestSettings[ "filter" ][ "created_at <= ?" ] = $requestData->created_at . " 23:59:59";