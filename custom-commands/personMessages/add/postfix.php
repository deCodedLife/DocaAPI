<?php

/**
 * Получение детальной информации о Задаче
 */

$userDetail = $API->DB->from( "users" )
    ->where( "id", $API::$userDetail->id )
    ->limit( 1 )
    ->fetch();


/**
 * Уведомление о добавлении Задачи
 */
$API->addNotification( "system_alerts", "Сообщение от : " . $userDetail[ "last_name" ] . " " . $userDetail[ "first_name" ], $requestData->message, "info", $requestData->chat_key );

/**
 * Отправка события о добавлении Задачи
 */
$API->addEvent( "notifications" );

/**
 * Отправка события об обновлении чата
 */
$API->addEvent( "personMessages" );