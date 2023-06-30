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


$API->returnResponse( $formFieldsUpdate );
