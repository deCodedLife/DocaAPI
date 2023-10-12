<?php

$requestSettings[ "filter" ][ "is_active" ] = "N";
$requestSettings[ "filter" ][ "cancelledDate <= ?" ] = $requestData->cancelledDate_end;
$requestSettings[ "filter" ][ "cancelledDate >= ?" ] = $requestData->cancelledDate_start;

if ( !$requestData->sort_by ) {

    $requestData->sort_by = "cancelledDate";
    $requestData->sort_order = "desc";

}