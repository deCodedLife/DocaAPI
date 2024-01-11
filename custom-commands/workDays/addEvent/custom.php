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
$API->DB->insertInto( "workDays" )
    ->values( (array) $requestData )
    ->execute();


if ( $requestData->is_weekend ) {

    $API->DB->deleteFrom( "scheduleEvents" )
        ->where( [
            "user_id" => $requestData->user_id,
            "event_from > ?" => $begin->format( "Y-m-d 00:00:00" ),
            "event_to < ?" => $begin->format( "Y-m-d 23:59:59" ),
            "store_id" => $requestData->store_id
        ] )
        ->execute();

}


$API->DB->insertInto( "scheduleEvents" )
    ->values( (array) $requestData )
    ->execute();