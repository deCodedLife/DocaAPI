<?php

/**
 * Проверка наличия обязательного филтьтра
 */
if (!$requestData->user_id) $API->returnResponse([]);


/**
 * Сформированный список
 */
$returnData= [];

/**
 * Фильтр Услуг
 */
$servicesFilter = [];

/**
 * Фильтр Продаж
 */
$salesFilter = [];

$visitsList = $API->DB->from( "salesList" )
    ->select( null )->select(
        [
            "salesList.id",
            "salesProductsList.product_id",
            "saleVisits.visit_id",
            "services.title",
            "salesProductsList.cost"
        ]
    )
    ->innerJoin( "salesProductsList on salesProductsList.sale_id = salesList.id" )
    ->innerJoin( "services on services.id = salesProductsList.product_id" )
    ->innerJoin( "saleVisits on saleVisits.sale_id = salesList.id" )
    ->limit(10);


foreach ( $visitsList as $visit ) {

    $API->returnResponse( $visit, 400);

}
