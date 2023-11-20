<?php


namespace Sales;

global $API, $requestData;

require_once ( 'calculate-promotions.php' );


/**
 * Если тип операции - "возврат", тогда собирается информация только о
 * прикреплённых к данной операции услугах
 */

if ( $isReturn ) {

    $saleServices = [];

    $soldSales = $API->DB->from( DB_SALES_PRODUCTS_LIST )
        ->where( [
            "sale_id" => $requestData->id,
            "type" => "service"
        ]);

    foreach ( $requestData->pay_object as $index => $saleID ) {

        $details = $this->getServiceDetails( $saleID );

        foreach ( $soldSales as $soldSale ) {

            if ( $saleID != $soldSale[ "service_id" ] ) continue;
            $details[ "price" ] = $soldSale[ "price" ];

        }

        $this->saleServices[] = $details;

    }

    foreach ( $this->allServices as $index => $sale ) {

        foreach ( $soldSales as $soldSale ) {

            if ( $sale[ "id" ] != $soldSale[ "service_id" ] ) continue;
            $sale[ "price" ] = $soldSale[ "price" ];
            $this->allServices[ $index ] = $sale;

        }

    }

} // if. $this->isReturn



/**
 * Получение итоговой суммы продажи
 */

foreach ( $this->visits as $visit ) {

    $visitPrice = $visit[ "price" ];

    if ( $requestData->discount_type === "fixed"   ) $visitPrice -= $requestData->discount_value;
    if ( $requestData->discount_type === "percent" ) $visitPrice -= ($visitPrice / 100) * $requestData->discount_value;

    $visitPrice = max( $visitPrice, 0 );
    $this->saleSummary += $visitPrice;

} // foreach. $saleVisits as $visit

if ( $this->isReturn ) {

    $saleDetails = $API->DB->from( DB_SALES_PRODUCTS_LIST )
        ->where( "id", $requestData->id )
        ->fetch();

    $this->saleSummary = $saleDetails[ "summary" ];

} // if ( $this->isReturn ) {