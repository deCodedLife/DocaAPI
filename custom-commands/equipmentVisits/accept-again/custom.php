<?php

/**
 * @file
 *  Повторный вызов клиента
 */


$API->DB->update( "equipmentVisits" )
    ->set([
        "is_alert" => "N"
    ])
    ->where([
        "id" => $requestData->id
    ])
    ->execute();