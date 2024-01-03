<?php



/**
 * Изменение статуса оплаты
 */

$API->DB->update( "salesList" )
    ->set( [
        "status" => $requestData->status ?? "error",
        "error" => $requestData->description ?? ""
    ] )
    ->where( "id", $requestData->sale_id )
    ->execute();