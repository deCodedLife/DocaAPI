<?php
$sort_by = $requestData->sort_by;
$sort_order = $requestData->sort_order;

if ( $sort_by ) {

    $limit = $requestData->sort_order;

}

unset($requestData->sort_by);
unset($requestData->sort_order);
