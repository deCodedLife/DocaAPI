<?php

//ini_set( "display_errors", true );

global $API, $requestData;


require_once ( "classes/Doca.php" );
require_once ( "index.php" );
require_once ( LIB_PATH . "/promotions/index.php" );

use Sales\Sales;
use Sales\Product;
use Sales\Discount;
use Sales\Subject;
use Sales\Modifier;


$allVisits = $requestData->visits_ids ?? [];
$saleProducts = $requestData->doca_products ?? [];
$allProducts = [];
$saleVisits = [];
$allServices = [];
$saleServices = [];
$saleSummary  = 0;
$productsPrice = 0;
$isReturn = false;
$sum_card = $requestData->sum_card ?? 0;
$sum_cash = $requestData->sum_cash ?? 0;
$isReturn = ($requestData->action ?? 'sell') === "sellReturn";
$store_id = $requestData->store_id;
$employee = $requestData->user_id;
$object = $requestData->object ?? "visits";


/**
 * Получение списка посещений
 */
//$API->returnResponse( $object, 500 );
$Doca->Table = $object;
$Doca->getVisits( $allVisits, $saleVisits, $isReturn );
$Doca->getServices( $allVisits, $allServices, $saleServices );
$Doca->getProducts( $allProducts, $saleProducts );

require_once ( 'index.php' );

/**
 * Если тип операции - "возврат", тогда собирается информация только о
 * прикреплённых к данной операции услугах
 */

if ( $isReturn ) {

    $saleServices = [];

    $soldSales = $API->DB->from( "salesProductsList" )
        ->where( [
            "sale_id" => $requestData->id,
            "type" => "service"
        ] );

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

    $productsPrice += $product[ "price" ] * $product[ "amount" ];

}

$saleSummary += $productsPrice;

if ( ( $requestData->discount_type ?? "" ) === "fixed"   ) $saleSummary -= ( $requestData->discount_value ?? 0 );
if ( ( $requestData->discount_type ?? "" ) === "percent" ) $saleSummary -= ( $saleSummary / 100 ) * ( $requestData->discount_value ?? 0 );

$saleSummary = max( $saleSummary, 0 );



if ( $isReturn ) {

    $saleDetails = $API->DB->from( DB_SALES_LIST )
        ->innerJoin( "saleVisits on saleVisits.sale_id = {" . DB_SALES_LIST . "}.id" )
        ->where( "saleVisits.visit_id", $requestData->id )
        ->fetch();

    $saleSummary = $saleDetails[ "summary" ];

} // if ( $isReturn )



/**
 * Получение скидок
 */
foreach ( Discount::GetActiveDiscounts( DB_PROMOTIONS ) as $discount ) {

    // При возврате не считаем скидки
    if ( $isReturn ) continue;

    /**
     * Получение филиалов в которых действует акция
     */
    $stores = $API->DB->from( "promotionStores" )
        ->where( "promotion_id", $discount[ "id" ] );

    $promotionStores = [];

    foreach ( $stores as $store ) {

        $promotionStores[] = $store[ "store_id" ];

    } // foreach ( $stores as $store ) {

    if ( !in_array( $store_id, $promotionStores ) ) continue;


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
    foreach ( $API->DB->from( "clientsGroupsAssociation" )->where( "client_id", $requestData->client_id ) as $group )
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
//    $API->returnResponse( $newSubjects, 500 );

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

$discountPerProduct = 0;

if ( $allServicesPrice + $productsPrice != 0 ) {

    $discountPerProduct = $amountOfPhysicalPayments / ( $allServicesPrice + $productsPrice );

}


/**
 * Нахождение суммы для налички и карты с учётом скидок
 */

$amountOfPhysicalPayments = ($saleServicesPrice + $productsPrice) * $discountPerProduct;
$amountOfPhysicalPayments = round( $amountOfPhysicalPayments, 2 );


//$saleSummary = $amountOfPhysicalPayments;


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
    $formFieldsUpdate[ "products_display" ][ "value" ][] = $service[ "title" ];

if ( $allServices ) {

    foreach ( $allServices as $product ) {

        $formFieldsUpdate[ "products" ][ "value" ][] = [
            "title" => $product[ "title" ],
            "type" => "service",
            "cost" => $product[ "price" ],
            "amount" => 1,
            "product_id" => $product[ "id" ]
        ];

    }

}

if ( $allProducts ) {

    foreach ( $allProducts as $product ) {

        $formFieldsUpdate[ "products" ][ "value" ][] = [
            "title" => $product[ "title" ],
            "type" => "product",
            "cost" => $product[ "price" ],
            "amount" => $product[ "amount" ],
            "product_id" => $product[ "id" ]
        ];

    }

}