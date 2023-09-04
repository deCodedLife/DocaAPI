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
    if ( $requestData->start_price ) $salesFilter[ "summary >= ?" ] = $requestData->start_price;
    if ( $requestData->end_price ) $salesFilter[ "summary <= ?" ] = $requestData->end_price;
    if ( $requestData->start_at ) $salesFilter[ "created_at >= ?" ] = $requestData->start_at . " 00:00:00";
    if ( $requestData->end_at ) $salesFilter[ "created_at <= ?" ] = $requestData->end_at . " 23:59:59";
    if ( $requestData->store_id ) $salesFilter[ "store_id" ] = $requestData->store_id;

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
                    $visitUser = $API->DB->from( "visits" )
                        ->where( "id", $saleVisit[ "visit_id" ] )
                        ->limit( 1 )
                        ->fetch()[ "user_id" ];

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

    function array_sort ( $array, $on, $order=SORT_ASC )
    {
        $new_array = array();
        $sortable_array = array();

        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                    break;
                case SORT_DESC:
                    arsort($sortable_array);
                    break;
            }

            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }

        return $new_array;
    }

    if ( $sort_by == "title" ) {

        if ( $sort_order == "desc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "title", SORT_DESC ) );
        if ( $sort_order == "asc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "title", SORT_ASC ) );

    }

    if ( $sort_by == "count" ) {

        if ( $sort_order == "desc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "count", SORT_DESC ) );
        if ( $sort_order == "asc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "count", SORT_ASC ) );

    }

    if ( $sort_by == "price" ) {

        if ( $sort_order == "desc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "price", SORT_DESC ) );
        if ( $sort_order == "asc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "price", SORT_ASC ) );

    }

    if ( $sort_by == "discount_value" ) {

        if ( $sort_order == "desc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "discount_value", SORT_DESC ) );
        if ( $sort_order == "asc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "discount_value", SORT_ASC ) );

    }

    if ( $sort_by == "date" ) {

        if ( $sort_order == "desc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "date", SORT_DESC ) );
        if ( $sort_order == "asc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "date", SORT_ASC ) );

    }

    if ( $sort_by == "sum" ) {

        if ($sort_order == "desc") $response["data"] = array_values(array_sort($response["data"], "sum", SORT_DESC));
        if ($sort_order == "asc") $response["data"] = array_values(array_sort($response["data"], "sum", SORT_ASC));

    }

    $response[ "detail" ] = [

        "pages_count" => ceil(count($response[ "data" ]) / $requestData->limit),
        "rows_count" => count($response[ "data" ])

    ];

    $response[ "data" ] = array_slice($response[ "data" ], $requestData->limit * $requestData->page - $requestData->limit, $requestData->limit);

} else {

    $response["data"] = [];

}



