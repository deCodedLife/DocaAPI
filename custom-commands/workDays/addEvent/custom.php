<?php


/**
 * Вызываем метод создания ячеек и их валидации
 */
require_once $API::$configs[ "paths" ][ "public_app" ] . "/custom-libs/workdays/validate.php";


/**
 * Создание записи в расписании
 */
$API->DB->insertInto( "workDays" )
    ->values( $newEvent )
    ->execute();