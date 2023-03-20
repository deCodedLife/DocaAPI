<?php



/**
 * Изменение статуса оплаты
 */

$API->DB->update( "sales" )
    ->set( [
        "status" => $requestData->status,
        "error" => $requestData->description
    ] )
    ->where( [
        "id" => $requestData->sale_id
    ] )
    ->execute();