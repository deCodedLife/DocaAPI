<?php

/**
 * @file
 * Список "продажа услуг по сотрудникам
 */

/**
 * Проверка наличия обязательного филтьтра
 */
if ( $requestData->user_id ) {

    /**
     * Сформированный список
     */
    $returnVisits = [];

    /**
     * Фильтр Услуг
     */
    $servicesFilter = [];

    /**
     * Фильтр Продаж
     */
    $salesFilter = [];

    /**
     * Формирование фильтров
     */
    $salesFilter[ "status" ] = "done";
    $salesFilter[ "type" ] = "service";
    $salesFilter[ "action" ] = "sell";
    if ( $requestData->start_at ) $salesFilter[ "created_at >= ?" ] = $requestData->start_at . " 00:00:00";
    if ( $requestData->end_at ) $salesFilter[ "created_at <= ?" ] = $requestData->end_at . " 23:59:59";
    if ( $requestData->store_id ) $salesFilter[ "store_id" ] = $requestData->store_id;


    $servicesFilter[ "is_active" ] = "Y";
    if ( $requestData->id ) $servicesFilter[ "id" ] = $requestData->id;
    if ( $requestData->category_id ) $servicesFilter[ "category_id" ] = $requestData->category_id;

    /**
     * Список услуг
     */
    $services = $API->DB->from( "services" )
        ->where($servicesFilter);


    /**
     * Получение продаж
     */
    $salesList = $API->DB->from( "salesList" )
        ->leftJoin( "salesProductsList ON salesProductsList.sale_id = salesList.id" )
        ->select(null)->select([
            "salesList.id",
            "salesProductsList.product_id",
            "salesProductsList.cost",
            "salesProductsList.amount"
        ])
        ->where( $salesFilter )
        ->orderBy( "salesList.created_at desc" )
        ->limit ( 0 );

    /**
     * Обработка услуг
     */
    foreach ( $services as $service ) {

        /**
         * Колличество услуг в посещениях
         */
        $count = 0;

        /**
         * Сумма услуг в посещениях
         */
        $sum = 0;

        /**
         * Обход продаж
         */
        foreach ( $salesList as $sale ) {

            /**
             * Проверка наличия услуги в продаже
             */
            if ( $sale[ "product_id" ] == $service[ "id" ] ) {

                /**
                 * Получение посещений продажи
                 */
                $saleVisits = $API->DB->from( "saleVisits" )
                    ->where( "sale_id", $sale[ "id" ] );

                /**
                 * Считать ли результаты
                 */
                $isContinue = false;

                /**
                 * Обход посещений продажи
                 */
                foreach ( $saleVisits as $saleVisit ) {

                    /**
                     * Получение сотрудника посещения
                     */
                    $visitUser = $API->DB->from( "visits_users" )
                        ->where( "visit_id", $saleVisit[ "visit_id" ] )
                        ->limit( 1 )
                        ->fetch();

                    /**
                     * Приверка на наличие сотрудника в посещении
                     */
                    if ( $visitUser[ "user_id" ] == $requestData->user_id ) {

                        $isContinue = true;

                    }


                }

                /**
                 * Подсчет результатов если сотрудник есть
                 */
                if ( $isContinue == true ) {

                    $count += $sale[ "amount" ];
                    $sum += $sale[ "amount" ] * $sale[ "cost" ];

                } // if ( $isContinue == true )

            } // if ( $sale[ "product_id" ] == $service[ "id" ] )

        } // foreach .$salesList

        if ( $count != 0 ) {

            /**
             * Запись жлемента в список
             */
            $returnVisits[] = [
                "id" => $service[ "id" ],
                "title" => $service[ "title" ],
                "price" => $sum / $count,
                "discount_value" => $sum / $count * $count - $sum,
                "count" => $count,
                "date" => $service[ "date" ],
                "sum" => $sum
            ];

        } // if ( $count != 0 )


    }

    /**
     * Обновление списка
     */
    $response["data"] = $returnVisits;

} else {

    $response["data"] = [];

}


