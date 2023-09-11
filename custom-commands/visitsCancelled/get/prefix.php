<?php

$requestSettings[ "filter" ][ "is_active" ] = "N";
$requestSettings[ "filter" ][ "cancelledDate <= ?" ] = $requestData->cancelledDate_end;
$requestSettings[ "filter" ][ "cancelledDate >= ?" ] = $requestData->cancelledDate_start;
