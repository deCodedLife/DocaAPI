<?php

/**
 * @file
 * Хуки Сотрудников
 */


/**
 * Обновление полей формы
 */
$formFieldsUpdate = [];


/**
 * Определение пола по Отчеству
 */
if ( $requestData->patronymic ) {

    $formFieldsUpdate[ "gender" ] = "W";

} // if. $requestData->patronymic


$API->returnResponse( $formFieldsUpdate );