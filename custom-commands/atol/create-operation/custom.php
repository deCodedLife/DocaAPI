<?php

/**
 * Печать нулевого чека
 */

$operationTypes = [
    "openShift",
    "closeShift",
    "reportX"
];

$API->DB->insertInto( "atolOperations" )
    ->values( [
        "cashbox_id" => $requestData->cashbox_id,
        "type" => $operationTypes[ (int) $requestData->operation_type ]
    ] )
    ->execute();