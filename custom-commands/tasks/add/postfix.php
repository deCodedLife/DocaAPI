<?php

/**
 * Уведомление о добавлении Задачи
 */
$API->addNotification( "system_alerts", "Новая задача", $requestData->description, "info", $requestData->performer_id );

/**
 * Отправка события о добавлении Задачи
 */
$API->addEvent( "notifications" );