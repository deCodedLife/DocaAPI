<?php


/**
 * Игнорирование исполнителя в логе
 */


/**
 * Получение детальной информации о клиенте
 */

$cabinetTitle = $API->DB->from( "cabinets" )
    ->where( "id", $requestData->cabinet_id )
    ->limit( 1 )
    ->fetch()[ "title" ];

$logDescription = "Кабинет изменен на \"$cabinetTitle\"";