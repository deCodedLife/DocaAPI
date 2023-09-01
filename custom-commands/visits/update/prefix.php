<?php

/**
 * Расчет свободности Исполнителей, Клиентов и Кабинетов
 */

/**
 * Валидация посещения
 */
$publicAppPath = $API::$configs[ "paths" ][ "public_app" ];
require_once ( $publicAppPath . '/custom-libs/visits/validate.php' );

/**
 * Получение Записей за указанный период
 */

$sqlTimeCondition = "(
    ( start_at >= '$requestData->start_at' and start_at < '$requestData->end_at' ) OR
    ( end_at > '$requestData->start_at' and end_at < '$requestData->end_at' ) OR
    ( start_at < '$requestData->start_at' and end_at > '$requestData->end_at' )
)";

$currentVisit = $API->DB->from( "visits" )
    ->where( "id", $requestData->id )
    ->fetch();

$start_at = $requestData->start_at ?? $currentVisit[ "start_at" ];
$end_at   = $requestData->end_at ?? $currentVisit[ "end_at" ];


$existingVisits = mysqli_query(
    $API->DB_connection,
    "SELECT id, cabinet_id FROM visits WHERE $sqlTimeCondition AND is_active = 'Y'"
);

if ( !$requestData->reason_id ) {
    /**
     * Пустое поле усуг
     */
    if ( $requestData->services_id === [] ) {

        $API->returnResponse("Укажите услугу", 400);

        /**
         * Услуги не изменялись
         */
    } elseif ( !$requestData->services_id ) {

        /**
         * Получение услуг посещения
         */
        $visits_services = $API->DB->from( "visits_services" )
            ->where( "visit_id", $requestData->id );

        /**
         * Обход услуг
         */
        foreach ( $visits_services as $visit_service ) {

            /**
             * Получение расходников для услуги
             */
            $services_consumables = $API->DB->from( "services_consumables" )
                ->where( "row_id", $visit_service[ "service_id" ] );

            foreach ( $services_consumables as $service_consumable ) {

                $allConsumables[$service_consumable[ "consumable_id" ]][ "count" ] += $service_consumable["count"];

            }

            /**
             * Получение детальной информации об услуге
             */
            $serviceDetail = $API->DB->from("services")
                ->where("id", $visit_service["service_id"])
                ->limit(1)
                ->fetch();

            /**
             * Пустое поле сотрудники
             */
            if ($requestData->users_id === []) {

                $API->returnResponse("Укажите сотрудника", 400);

                /**
                 * Сотрудники изменялись
                 */
            } elseif ( $requestData->users_id && $requestData->users_id !== [] ) {

                /**
                 * Получение вторых исполнителей услуги
                 */
                $services_second_users = $API->DB->from("services_second_users")
                    ->where("service_id", $visit_service["service_id"]);

//                foreach ( $requestData->users_id as $visitUser ) {
//
//                    $API->DB->from( "visits_users" )
//                        ->innerJoin( "visits" )
//                        ->where( [
//                            "not visits.id = ?", $requestData->id,
//                            "start_at >= ?" => $start_at,
//                            "end_at <=" =>
//                        ] )
//
//                }

                /**
                 * Нет необходимости указывать второго исполнителя?
                 */
                $specifySecondEmployee = true;

                /**
                 * Обход вторых исполнителей
                 */
                foreach ($services_second_users as $secondUser) {

                    if ($secondUser) {

                        $specifySecondEmployee = false;

                        foreach ($requestData->users_id as $service_user) {

                            if ($service_user == $secondUser["user_id"]) {

                                $specifySecondEmployee = true;

                            }

                        }

                    } else {

                        $specifySecondEmployee = true;

                    }

                }

                if ($specifySecondEmployee == false) {

                    $API->returnResponse("Укажите второго сотрудника для услуги " . $serviceDetail["title"], 400);

                }

            }

        }

        /**
         * Услуги изменялись
         */
    } else {

        /**
         * Обход услуг
         */
        foreach ($requestData->services_id as $serviceId) {

            /**
             * Получение расходников для услуги
             */
            $services_consumables = $API->DB->from("services_consumables")
                ->where("row_id", $serviceId);

            foreach ($services_consumables as $service_consumable) {

                $allConsumables[$service_consumable["consumable_id"]]["count"] += $service_consumable["count"];

            }

            /**
             * Получение детальной информации об услуге
             */
            $serviceDetail = $API->DB->from("services")
                ->where("id", $serviceId)
                ->limit(1)
                ->fetch();

            /**
             * Пустое поле сотрудники
             */
            if ($requestData->users_id === []) {

                $API->returnResponse("Укажите сотрудника", 400);

                /**
                 * Сотрудники не изменялись
                 */
            } elseif (!$requestData->users_id) {

                /**
                 * Получение сотрудников
                 */
                $services_users = $API->DB->from("services_users")
                    ->where("service_id", $serviceId);

                /**
                 * Получение вторых исполнителей услуги
                 */
                $services_second_users = $API->DB->from("services_second_users")
                    ->where("service_id", $serviceId);

                /**
                 * Нет необходимости указывать второго исполнителя?
                 */
                $specifySecondEmployee = true;


                /**
                 * Обход вторых исполнителей
                 */
                foreach ($services_second_users as $secondUser) {

                    if ($secondUser) {

                        foreach ($services_users as $service_user) {

                            if ($service_user["user_id"] == $secondUser["user_id"]) {

                                $specifySecondEmployee = true;

                            }

                        }

                    } else {

                        $specifySecondEmployee = true;

                    }

                }

                if ($specifySecondEmployee == false) {

                    $API->returnResponse("Укажите второго сотрудника для услуги ${$serviceDetail[ "title" ]}", 400);

                }

                /**
                 * Сотрудники  изменялись
                 */
            } else {

                /**
                 * Получение вторых исполнителей услуги
                 */
                $services_second_users = $API->DB->from("services_second_users")
                    ->where("service_id", $serviceId);

                /**
                 * Нет необходимости указывать второго исполнителя?
                 */
                $specifySecondEmployee = true;

                /**
                 * Обход вторых исполнителей
                 */
                foreach ($services_second_users as $secondUser) {

                    if ($secondUser) {

                        $specifySecondEmployee = false;

                        foreach ($requestData->users_id as $service_user) {

                            if ($service_user == $secondUser["user_id"]) {

                                $specifySecondEmployee = true;

                            }

                        }

                    } else {

                        $specifySecondEmployee = true;

                    }

                }

                if ($specifySecondEmployee == false) {

                    $API->returnResponse("Укажите второго сотрудника для услуги ${$serviceDetail[ "title" ]}", 400);

                }

            }

        }

    }

    foreach ($allConsumables as $consumable_id => $consumable) {

        $warehouse = $API->DB->from("warehouses")
            ->where([
                "store_id" => $requestData->store_id,
                "consumable_id" => $consumable_id
            ])
            ->limit(1)
            ->fetch();

        $consumable = $API->DB->from("consumables")
            ->where( "id", $consumable_id )
            ->limit(1)
            ->fetch();

        if ($warehouse && $consumable["count"] > $warehouse["count"]) {

            $API->returnResponse("Недостаточно расходника - ${$consumable[ "title" ]}", 400);

        }

    }
}

/**
 * Обработка полученных Записей
 */

foreach ( $existingVisits as $existingVisit ) {

    /**
     * Игнорирование текущей Записи
     */
    if ( $existingVisit[ "id" ] == $requestData->id ) continue;


    /**
     * Проверка свободности Кабинета
     */
    if ( $existingVisit[ "cabinet_id" ] == $requestData->cabinet_id )
        $API->returnResponse( "Кабинет занят", 400 );


    /**
     * Проверка свободности Сотрудника
     */

    $visitUsers = $API->DB->from( "visits_users" )
        ->where( "visit_id", $existingVisit[ "id" ] );

    foreach ( $visitUsers as $visitUser )
        if ( in_array( $visitUser[ "user_id" ], $requestData->users_id ) ) {

            /**
             * Получение детальной информации о Сотруднике
             */
            $userDetail = $API->DB->from( "users" )
                ->where( "id", $visitUser[ "user_id" ] )
                ->limit( 1 )
                ->fetch();

            $API->returnResponse( "Сотрудник ${userDetail[ "last_name" ]} занят, посещение - № ${$visitUser[ "visit_id" ]}", 400 );

        } // if. in_array( $visitUser[ "user_id" ], $requestData->users_id


    /**
     * Проверка свободности Клиента
     */

    $visitClients = $API->DB->from( "visits_clients" )
        ->where( "visit_id", $existingVisit[ "id" ] );

    foreach ( $visitClients as $visitClient )

        if ( in_array( $visitClient[ "client_id" ], $requestData->client_id ) ) {

            /**
             * Получение детальной информации о Клиенте
             */
            $clientDetail = $API->DB->from( "clients" )
                ->where( "id", $visitClient[ "client_id" ] )
                ->limit( 1 )
                ->fetch();

            $API->returnResponse( "Клиент ${clientDetail[ "last_name" ]}  занят, посещение - № ${$visitClient[ "visit_id" ]}", 400 );

        } // if. in_array( $visitClient[ "client_id" ], $requestData->client_id

} // foreach. $existingVisits
