<?php

if ( !$requestData->sort_by ) {

    $requestData->sort_by = "title";
    $requestData->sort_order = "asc";

}

if ( $requestData->sort_by ) {

    $sort_by = $requestData->sort_by;
    $sort_order = $requestData->sort_order;
    $limit = $requestData->limit;
    $requestData->limit = 0;

    unset($requestData->sort_by);

}
