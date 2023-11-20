<?php

/**
 * Время действия статуса "Повторное" у Записей
 */
$repeatStatusFrom = date(
    "Y-m-d", strtotime( "-30 days", strtotime( date( "Y-m-d" ) ) )
);

/**
 * Отправка события об обновлении расписания
 */
$API->addEvent( "schedule" );

/**
 * Отправка уведомления
 */
$API->addNotification(
    "system_alerts",
    "Создана запись ",
    "на " . date( "H:i:s d.m.Y", strtotime( $requestData->start_at ) ),
    "info",
    $requestData->user_id
);
