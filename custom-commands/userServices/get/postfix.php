<?php

/**
 * Проверка наличия обязательного филтьтра
 */
if (!$requestData->user_id) $API->returnResponse([]);


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

$visitsList = $API->DB->from( "services" )
    ->select( null )->select(
        [
            "salesProductsList.product_id",
            "salesProductsList.cost",
            "saleVisits.visit_id",
            "salesList.summary"
        ]
    )
    ->innerJoin( "salesProductsList on salesProductsList.product_id = services.id" )
    ->innerJoin( "salesList on salesList.id = salesProductsList.sale_id" )
    ->innerJoin( "saleVisits on saleVisits.sale_id = salesProductsList.sale_id" );


foreach ($visitsList as $visit) {

    $API->returnResponse($visit, 400);

}
