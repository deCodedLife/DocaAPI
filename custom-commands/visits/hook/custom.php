<?php

/**
 * @file
 * Хуки на Запись к врачу
 */
$formFieldsUpdate = [];
$hasAssist = false;


/**
 * Проход
 */
foreach ( $requestData->services_id as $service ) {

    $serviceDetail = $API->DB->from( "services" )
        ->where( "id", $service )
        ->fetch();

    if ( $serviceDetail[ "preparation" ] ) $formFieldsUpdate[ "modal_info" ][] = $serviceDetail[ "preparation" ];

}

/**
 * Расчет стоимости и времени выполнения Записи
 */
if ( $requestData->services_id && $requestData->user_id ) {

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
     * Обход услуг
     */
    foreach ( $requestData->services_id as $serviceId ) {

        $second_users = $API->DB->from( "services_second_users" )
            ->where( "service_id", $serviceId );

        if ( count( $second_users ) != 0 ) $hasAssist = true;


        /**
         * Получение детальной информации об услуге
         */
        $serviceDetail = $API->DB->from( "services" )
            ->where( "id", $serviceId )
            ->limit( 1 )
            ->fetch();

        /**
         * Получение информации о сотруднике
         */
        $serviceUser = $API->DB->from( "workingTime" )
            ->where(
                [
                    "row_id" => $serviceId,
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

    } // foreach. $requestData->services_id

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


if ( $requestData->clients_id ) {

    $clientsInfo = [];

    foreach ($requestData->clients_id as $clientId) {

        $clientDetail = $API->DB->from( "clients" )
            ->where("id", $clientId)
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

    }

    $formFieldsUpdate[ "clients_info" ] = [ "is_visible" => true, "value" => $clientsInfo ];

} else {

    $formFieldsUpdate[ "clients_info" ] = [ "is_visible" => false ];

}


if ( $requestData->start_at ) {

    if ( !$visitTakeMinutes ) {

        $visits = $API->DB->from( "visits" )
            ->where( "id", $requestData->id )
            ->limit( 1 )
            ->fetch();

        $visits[ "start_at" ] = strtotime($visits[ "start_at" ]);
        $visits[ "end_at" ] = strtotime($visits[ "end_at" ]);

        $diff = abs($visits[ "start_at" ] - $visits[ "end_at" ]);
        $minutes = $diff / 60;
        
        $formFieldsUpdate[ "end_at" ] = [
            "value" => date(
                "Y-m-d H:i:s", strtotime(
                    "+$minutes minutes", strtotime( $requestData->start_at )
                )
            )
        ];

    }

}


if ( $hasAssist ) $formFieldsUpdate[ "assist_id" ][ "is_visible" ] = true;
else $formFieldsUpdate[ "assist_id" ][ "is_visible" ] = false;


$API->returnResponse( $formFieldsUpdate );
