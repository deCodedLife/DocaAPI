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


$API->returnResponse( $formFieldsUpdate );