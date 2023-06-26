<?php

/**
 * @file
 *  Повторный вызов клиента
 */


$API->DB->update( "visits" )
    ->set([
        "is_alert" => "N"
    ])
    ->where([
        "id" => $requestData->id
    ])
    ->execute();