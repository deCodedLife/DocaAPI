<?php

/**
 * @file
 * Хуки на Запись к врачу
 */
$formFieldsUpdate = [];
$hasAssist = false;


if ( $requestData->context->trigger == "store_id" ) $formFieldsUpdate[ "cabinet_id" ][ "value" ] = null;


/**
 * Проход
 */
foreach ( $requestData->services_id as $service ) {

    if ( $requestData->context->trigger == "services_id" ) {

        $serviceDetail = $API->DB->from( "services" )
            ->where( "id", $service )
            ->fetch();

        $modalExists = false;

        foreach ( $formFieldsUpdate[ "modal_info" ] as $info ) {

            if ( $info == $serviceDetail[ "preparation" ] ) $modalExists = true;

        }

        if ( !$modalExists && $serviceDetail[ "preparation" ] ) $formFieldsUpdate[ "modal_info" ][] = $serviceDetail[ "preparation" ];

    }

}


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


    $second_users = $API->DB->from( "services_second_users" )
        ->where( "service_id", $requestData->service_id );

    if ( count( $second_users ) != 0 ) $hasAssist = true;


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

if ( $hasAssist ) $formFieldsUpdate[ "assist_id" ][ "is_visible" ] = true;
else $formFieldsUpdate[ "assist_id" ][ "is_visible" ] = false;

/**
 * Зачищать ассистента при изменении услуги
 */
if (
    $requestData->context->trigger == "services_id" &&
    !$hasAssist
) $formFieldsUpdate[ "assist_id" ][ "value" ] = false;

$API->returnResponse( $formFieldsUpdate );
