<?php

/**
 * @file
 * Хуки на Запись к врачу
 */
$formFieldsUpdate = [];
$hasAssist = false;



$serviceDetail = $API->DB->from( "services" )
    ->where( "id", $requestData->service_id )
    ->fetch();

if ( $serviceDetail[ "preparation" ] ) $formFieldsUpdate[ "modal_info" ][] = $serviceDetail[ "preparation" ];


/**
 * Расчет стоимости и времени выполнения Записи
 */
if ( $requestData->service_id && $requestData->user_id ) {

    /**
     * Стоимость Записи
     */
    $visitPrice = 0;

    /**
     * Время выполнения Записи (мин)
     */
    $visitTakeMinutes = 0;


    /**
     * Проверка наличия обязательных св-в
     */
    if ( !$requestData->start_at ) $API->returnResponse( [] );


    /**
     * Получение детальной информации об услуге
     */
    $serviceDetail = $API->DB->from( "services" )
        ->where( "id", $requestData->service_id )
        ->limit( 1 )
        ->fetch();

    /**
     * Получение информации о сотруднике
     */
    $serviceUser = $API->DB->from( "workingTime" )
        ->where(
            [
                "row_id" => $requestData->service_id,
                "user" => $requestData->user_id
            ]
        )
        ->limit( 1 )
        ->fetch();


    /**
     * Обновление стоимости Записи
     */

    if ( $serviceUser ) {

        $visitPrice += (float) $serviceUser[ "price" ];


    } else {

        $visitPrice += (float) $serviceDetail[ "price" ];

    }


    /**
     * Обновление времени выполнения Записи
     */

    if ( $serviceUser ) {

        $visitTakeMinutes += (int) $serviceUser[ "time" ];

    } else {

        $visitTakeMinutes += (int) $serviceDetail[ "take_minutes" ];

    }



    /**
     * Обновление полей формы
     */

    $formFieldsUpdate[ "price" ] = [
        "value" => $visitPrice
    ];

    $formFieldsUpdate[ "end_at" ] = [
        "value" => date(
            "Y-m-d H:i:s", strtotime(
                "+$visitTakeMinutes minutes", strtotime( $requestData->start_at )
            )
        )
    ];

} // if. $requestData->services_id && $requestData->users_id


if ( $requestData->client_id ) {

    $clientsInfo = [];


    $clientDetail = $API->DB->from( "clients" )
        ->where("id", $requestData->client_id)
        ->limit(1)
        ->fetch();


    if ( $clientDetail[ "phone" ] ) {
//            $API->returnResponse($clientDetail[ "phone" ] );

        $phoneFormat = ", +" . sprintf("%s (%s) %s-%s-%s",
                substr($clientDetail["phone"], 0, 1),
                substr($clientDetail["phone"], 1, 3),
                substr($clientDetail["phone"], 4, 3),
                substr($clientDetail["phone"], 7, 2),
                substr($clientDetail["phone"], 9)
            );

    } else {

        $phoneFormat = "";

    }

    $clientsInfo[] = "№{$clientDetail[ "id" ]} {$clientDetail[ "last_name" ]} {$clientDetail[ "first_name" ]} {$clientDetail[ "patronymic" ]} $phoneFormat";


    $formFieldsUpdate[ "clients_info" ] = [ "is_visible" => true, "value" => $clientsInfo ];

} else {

    $formFieldsUpdate[ "clients_info" ] = [ "is_visible" => false ];

}


$API->returnResponse( $formFieldsUpdate );
