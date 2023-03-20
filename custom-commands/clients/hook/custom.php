<?php

/**
 * @file
 * Хуки Клиентов
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
        $formFieldsUpdate[ "gender" ][ "value" ] = "M";
    elseif ( mb_substr( $requestData->patronymic, -2 ) === "на" )
        $formFieldsUpdate[ "gender" ][ "value" ] = "W";

} // if. $requestData->patronymic


if ( $requestData->is_representative == "Y" ) {

    $formFieldsUpdate[ "present_last_name" ] = [
        "is_visible" => true
    ];
    $formFieldsUpdate[ "present_first_name" ] = [
        "is_visible" => true
    ];
    $formFieldsUpdate[ "present_patronymic" ] = [
        "is_visible" => true
    ];
    $formFieldsUpdate[ "present_passport_series" ] = [
        "is_visible" => true
    ];
    $formFieldsUpdate[ "present_passport_number" ] = [
        "is_visible" => true
    ];
    $formFieldsUpdate[ "present_passport_issued" ] = [
        "is_visible" => true
    ];

} else {

    $formFieldsUpdate[ "present_last_name" ] = [
        "is_visible" => false
    ];
    $formFieldsUpdate[ "present_first_name" ] = [
        "is_visible" => false
    ];
    $formFieldsUpdate[ "present_patronymic" ] = [
        "is_visible" => false
    ];
    $formFieldsUpdate[ "present_passport_series" ] = [
        "is_visible" => false
    ];
    $formFieldsUpdate[ "present_passport_number" ] = [
        "is_visible" => false
    ];
    $formFieldsUpdate[ "present_passport_issued" ] = [
        "is_visible" => false
    ];

}


$API->returnResponse( $formFieldsUpdate );