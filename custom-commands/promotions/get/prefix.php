<?php


/**
 * Фильтр по дате
 */

if ($requestData->created_at_from) {

    $requestSettings["filter"]["begin_at >= ?"] = $requestData->begin_at_from;
    unset($requestData->created_at_from);

} // if. $requestData->created_at

if ($requestData->created_at_to) {

    $requestSettings["filter"]["begin_at <= ?"] = $requestData->begin_at_to;
    unset($requestData->created_at_to);

} // if. $requestData->created_at
