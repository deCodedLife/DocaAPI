<?php

/**
 * Фильтр по Сотруднику
 */
$eventFilter[ "user_id" ] = $requestData->user_id;

if ( $requestData->store_id ) $eventFilter[ "store_id" ] = $requestData->store_id;
