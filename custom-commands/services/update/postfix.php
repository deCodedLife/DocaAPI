<?php

if ( $requestData->price ) $API->DB->insertInto( "servicesCost" )
    ->values( [
        "service_id" => $requestData->id,
        "price" => $requestData->price
    ] )
    ->execute();
