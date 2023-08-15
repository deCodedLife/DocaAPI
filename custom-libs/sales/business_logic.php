<?php

ini_set( 'display_errors', 1 );
global $API, $requestData;


require_once ( "classes/Doca.php" );
require_once ( "variables.php" );
require_once ( "index.php" );
require_once ( "promotions/index.php" );

use Sales\Sales;
use Sales\Product;
use Sales\Discount;
use Sales\Subject;
use Sales\Modifier;
use Sales\SALES_VARIABLES;


$allVisits = $requestData->visits_ids ?? [];
$allProducts = $requestData->doca_products ?? [];
$saleVisits = [];
$allServices = [];
$saleServices = [];
$saleSummary  = 0;
$productsPrice = 0;
$isReturn = false;
$sum_card = $requestData->sum_card ?? 0;
$sum_cash = $requestData->sum_cash ?? 0;
$isReturn = ($requestData->action ?? 'sell') === "sellReturn";


/**
 * Получение списка посещений
 */
$Doca->getVisits( $allVisits, $saleVisits, $isReturn );
$Doca->getServices( $allVisits, $allServices, $saleServices );

require_once ( 'index.php' );

/**
 * Если тип операции - "возврат", тогда собирается информация только о
 * прикреплённых к данной операции услугах
 */

if ( $isReturn ) {

    $saleServices = [];

    $soldSales = $API->DB->from( $SALES_VARIABLES::$DB_SALES_PRODUCTS_LIST )
        ->where( [
            "sale_id" => $requestData->id,
            "type" => "service"
        ]);

    foreach ( $requestData->return_services as $saleID ) {

        $details = $Doca->getServiceDetails( $saleID );

        foreach ( $soldSales as $soldSale ) {

            if ( $saleID != $soldSale[ "service_id" ] ) continue;
            $details[ "price" ] = $soldSale[ "price" ];

        }

        $saleServices[] = $details;

    }

    foreach ( $allServices as $index => $sale ) {

        foreach ( $soldSales as $soldSale ) {

            if ( $sale[ "id" ] != $soldSale[ "service_id" ] ) continue;
            $sale[ "price" ] = $soldSale[ "price" ];
            $allServices[ $index ] = $sale;

        }

    }

} // if isReturn



/**
 * Получение итоговой суммы продажи
 */

foreach ( $allVisits as $visit )
    $saleSummary += $visit[ "price" ];

foreach ( $allProducts as $product ) {

    $productDetails = $API->DB->from( "products" )
        ->where( "id", $product->id )
        ->fetch();

    $productsPrice += $productDetails[ "price" ] * $product->amount;

}

$saleSummary += $productsPrice;

if ( $requestData->discount_type === "fixed"   ) $saleSummary -= ( $requestData->discount_value ?? 0 );
if ( $requestData->discount_type === "percent" ) $saleSummary -= ( $saleSummary / 100 ) * ( $requestData->discount_value ?? 0 );

$saleSummary = max( $saleSummary, 0 );



if ( $isReturn ) {

    $saleDetails = $API->DB->from( $SALES_VARIABLES::$DB_SALES_LIST )
        ->innerJoin( "saleVisits on saleVisits.sale_id = {$SALES_VARIABLES::$DB_SALES_LIST}.id" )
        ->where( "saleVisits.visit_id", $requestData->id )
        ->fetch();

    $saleSummary = $saleDetails[ "summary" ];

} // if ( $isReturn )



/**
 * Получение скидок
 */
foreach ( Discount::GetActiveDiscounts( $SALES_VARIABLES::$DB_PROMOTIONS ) as $discount ) {

    // При возврате не считаем скидки
    if ( $isReturn ) continue;



    $servicesGroups = [];
    $Discount = new Discount();
    $Discount->GetModifiers( "promotion_id", $discount[ "id" ] );



    /**
     * Добавляем услуги как участников акции
     */
    foreach ( $allServices as $service ) {

        $Discount->Subjects[] = new Subject(
            "services",
            $service[ "id" ],
            $service[ "price" ],
            Discount::getGroups( $service[ "category_id" ], "serviceGroups" )
        );

    } // foreach $allServices as $service



    /**
     * Не забываем про клиентов
     */
    foreach ( $API->DB->from( "clientsGroupsAssaciation" )->where( "client_id", $requestData->client_id ) as $group )
        $clientGroups[] = $group[ "clientGroup_id" ];

    $Discount->Subjects[] = new Subject(
        "clients",
        $requestData->client_id,
        0,
        $clientGroups ?? []
    );



    /**
     * Смотрим, подходит акция под наши условия
     */
    if ( !$Discount->IsValid() ) continue;
    $newSubjects = $Discount->Apply( $discount[ "id" ] );
    $discountSum = 0;

    foreach ( $newSubjects as $subject ) {

        foreach ( $allServices as $index => $service ) {

            if (
                $subject->Type == "services" &&
                $service[ "id" ] == $subject->ID &&
                $service[ "price" ] != $subject->Price
            ) {

                $discountSum -= $subject->Price - $service[ "price" ];
                $service[ "price" ] = $service[ "price" ] - $discountSum;
                $allServices[ $index ] = $service;

                // May cause error in sale return case
                $saleServices[ $index ] = $service;

            }

        } //  foreach ( $allServices as $index => $service ) {

    } // foreach ( $newSubjects as $subject )


    $saleSummary -= $discountSum;

} // foreach. Discount::GetActiveDiscounts( "promotions" ) as $discount

$saleSummary = max( $saleSummary, 0 );

/**
 * Вычет депозита и бонусов для расчёта сумм налички и карты
 */

$amountOfPhysicalPayments = 0;
$amountOfPhysicalPayments = $saleSummary - ( ($requestData->sum_bonus ?? 0) + ($requestData->sum_deposit ?? 0) );

$saleServicesPrice = 0;
$allServicesPrice = 0;

/**
 * Подсчёт стоимости посещения без скидок
 */

foreach ( $allServices as $service )
    $allServicesPrice += $service[ "price" ];

foreach ( $saleServices as $service )
    $saleServicesPrice += $service[ "price" ];



/**
 * Нахождение скидки для товаров по формуле (стоимость со скидками / стоимость без скидок)
 */

$discountPerProduct = $amountOfPhysicalPayments / ( $allServicesPrice + $productsPrice );



/**
 * Нахождение суммы для налички и карты с учётом скидок
 */

$amountOfPhysicalPayments = ($saleServicesPrice + $productsPrice) * $discountPerProduct;
$amountOfPhysicalPayments = round( $amountOfPhysicalPayments, 2 );

$saleSummary = $amountOfPhysicalPayments;


if ( $isReturn ) {

    $saleServicesPrice = 0;
    $allServicesPrice = 0;

    /**
     * Подсчёт стоимости посещения без скидок
     */

    foreach ( $allServices as $service )
        $allServicesPrice += $service[ "price" ];

    foreach ( $saleServices as $service )
        $saleServicesPrice += $service[ "price" ];



    /**
     * Нахождение скидки для товаров по формуле (стоимость со скидками / стоимость без скидок)
     */

    $discountPerProduct = $amountOfPhysicalPayments / $allServicesPrice;



    /**
     * Нахождение суммы для налички и карты с учётом скидок
     */

    $amountOfPhysicalPayments = $saleServicesPrice * $discountPerProduct;
    $amountOfPhysicalPayments = round( $amountOfPhysicalPayments, 2 );

    $saleSummary = $amountOfPhysicalPayments;

    if ( $requestData->return_bonuses ?? 'N' == "Y" ) $saleSummary += $requestData->sum_bonus;
    if ( $requestData->return_deposit ?? 'N' == "Y" ) $saleSummary += $requestData->sum_deposit;

} // if. isReturn



if ( $sum_cash > $amountOfPhysicalPayments ) $sum_cash = $amountOfPhysicalPayments;
if ( $sum_card > $amountOfPhysicalPayments ) $sum_card = $amountOfPhysicalPayments;

foreach ( $allVisits as $visit )
    $formFieldsUpdate[ "visits_ids" ][ "value" ][] = $visit[ "id" ];

foreach ( $allServices as $service )
    $formFieldsUpdate[ "doca_services" ][ "value" ][] = $service[ "title" ];

//foreach ( $allProducts as $product )
//    $formFieldsUpdate[ "doca_products" ][ "value" ][] = $product[ "title" ];