<?php

/**
 * @file
 * Хуки на Запись к врачу
 */
$formFieldsUpdate = [];

/**
 * Расчет стоимости и времени выполнения Записи
 */
if ( $requestData->services_id && $requestData->users_id ) {

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
                    "user_id" => $requestData->users_id[0]
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

        $phoneFormat = "+" . sprintf("%s (%s) %s-%s-%s",
                substr($clientDetail [ "phone" ], 0, 1),
                substr($clientDetail [ "phone" ], 1, 3),
                substr($clientDetail [ "phone" ], 4, 3),
                substr($clientDetail [ "phone" ], 7, 2),
                substr($clientDetail [ "phone" ], 9)
            );

        $clientsInfo[] = $clientDetail[ "last_name" ] . " " . $clientDetail[ "first_name" ] . " " . $clientDetail[ "patronymic" ] . ", " . $phoneFormat;

    }

    $formFieldsUpdate[ "clients_info" ] = [ "is_visible" => true, "value" => $clientsInfo ];

} else {

    $formFieldsUpdate[ "clients_info" ] = [ "is_visible" => false ];

}

if ( $requestData->users_id ) {

    /**
     *  Указание филиала
     */
    if ( !$requestData->store_id ) {

        $userDetails = $API->DB->from( "users_stores" )
            ->innerJoin( "users on users.id = users_stores.user_id" )
            ->where( "users.id", ( $requestData->users_id[ 0 ] ?? 1 ) )
            ->limit( 1 )
            ->fetch();

        $formFieldsUpdate[ "store_id" ][ "value" ] = $userDetails[ "store_id" ];

    }

//    /**
//     * Указание кабинета сотрудника
//     */
//    if ( $performerWorkSchedule[ "cabinet_id" ] ) {
//
//        $cabinetDetail = $API->DB->from( "cabinets" )
//            ->where( "id", $performerWorkSchedule[ "cabinet_id" ] )
//            ->limit( 1 )
//            ->fetch();
//
//
//        $resultSchedule[ $scheduleDateKey ][ $schedulePerformerKey ][ "performer_title" ] .= " [Каб. " . $cabinetDetail[ "title" ] . "]";
//
//    } // if. $performerWorkSchedule[ "cabinet_id" ]

}




$API->returnResponse( $formFieldsUpdate );
