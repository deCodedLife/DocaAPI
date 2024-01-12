<?php


/**
 * Вызываем метод создания ячеек и их валидации
 */

require_once $API::$configs[ "paths" ][ "public_app" ] . "/custom-libs/workdays/createEvents.php";
require_once $API::$configs[ "paths" ][ "public_app" ] . "/custom-libs/workdays/validate.php";


unset( $requestData->work_days );
unset( $requestData->id );

/**
 * Создание записи в расписании
 */
if ( !$requestData->is_weekend ) $requestData->is_weekend = 'N';

$API->DB->insertInto( "workDays" )
    ->values( (array) $requestData )
    ->execute();

removeEvents(
    $begin->format( "Y-m-d H:i:s" ),
    $end->format( "Y-m-d H:i:s" ),
    $requestData->store_id,
    $requestData->user_id,
    $requestData->is_rule,
    $requestData->is_weekend
);

$API->DB->insertInto( "scheduleEvents" )
    ->values( (array) $requestData )
    ->execute();