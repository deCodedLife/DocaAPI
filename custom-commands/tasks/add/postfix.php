<?php

/**
 * Уведомление о добавлении Задачи
 */
$API->addNotification( "system_alerts", "Новая задача", "Перейдите в раздел задач", "info", $requestData->performer_id );
