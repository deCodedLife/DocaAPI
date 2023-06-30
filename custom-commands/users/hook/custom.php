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

    if ( mb_substr( $requestData->patronymic, -2 ) === "ич" )
        $formFieldsUpdate[ "gender" ] = "M";
    elseif ( mb_substr( $requestData->patronymic, -2 ) === "на" )
        $formFieldsUpdate[ "gender" ] = "W";

} // if. $requestData->patronymic


/**
 * Блок "Процент от продаж услуг"
 */

if ( $requestData->is_percent == "Y" ) {

    $formFieldsUpdate[ "services_user_percents" ][ "is_visible" ] = true;

} else {

    $formFieldsUpdate[ "services_user_percents" ][ "is_visible" ] = false;

} // if. $requestData->is_percent == "Y"


$API->returnResponse( $formFieldsUpdate );