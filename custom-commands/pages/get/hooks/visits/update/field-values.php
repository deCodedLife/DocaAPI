<?php

/**
 * Получение детальной информации о посещении
 */

//$visitDetails = $API->DB->from( "visits" )
//    ->where( "id", $pageDetail[ "row_id" ] )
//    ->limit( 1 )
//    ->fetch();
//
//$saleDetails =  $API->DB->from( "salesVisits" )
//    ->where( "visit_id", $pageDetail[ "row_id" ] )
//    ->fetch();
//
//
///**
// * Получение детальной информации о пользователе
// */
//
//$userDetails = $API->DB->from( "users" )
//    ->where( "id", $API::$userDetail->id )
//    ->fetch();
//
//if ( !$userDetails[ "store_id" ] ) $userDetails[ "store_id" ] = $API->DB->from( "stores" )->limit( 1 )->fetch()["id"];
//
//
///**
// * Подсчёт итоговой стоимости посещения
// */
//
//$paymentSummary = $visitDetails[ "price" ];
//
//if ( $visitDetails[ "discount_type" ] == "fixed" ) $paymentSummary -= $visitDetails[ "discount_value" ];
//if ( $visitDetails[ "discount_type" ] == "percent" ) $paymentSummary -= ($paymentSummary / 100) * $visitDetails[ "discount_value" ];
//
//if ( $paymentSummary < 0 ) $paymentSummary = 0;
//
//$clients = $API->DB->from( "visits_clients" )
//    ->where( "visit_id", $pageDetail[ "row_id" ] )
//    ->limit( 1 )
//    ->fetch();


/**
 * Заполнение полей
 */



//if ( $visitDetails[ "is_payed" ] == "Y" || ( $saleDetails && $saleDetails[ "status" ] != "error" ) ) {
//
//    $formFieldValues =
//        $API->DB->from( "salesList" )
//            ->innerJoin( "salesVisits ON salesVisits.sale_id = sales.id" )
//            ->where( "salesVisits.visit_id", $pageDetail[ "row_id" ] )
//            ->fetch();
//
//    $formFieldValues[ "summary" ] = (float) $formFieldValues[ "summary" ];
//    $formFieldValues[ "sun_cash" ] = (float) $formFieldValues[ "sun_cash" ];
//    $formFieldValues[ "sum_card" ] = (float) $formFieldValues[ "sum_card" ];
//    $formFieldValues[ "sum_bonus" ] = (float) $formFieldValues[ "sum_bonus" ];
//    $formFieldValues[ "sum_deposit" ] = (float) $formFieldValues[ "sum_deposit" ];
//    $formFieldValues[ "is_combined" ] = $formFieldValues[ "is_combined" ] == "Y";
//    $formFieldValues[ "online_receipt" ] = $formFieldValues[ "online_receipt" ] == "Y";
//
////    foreach ( $API->DB->from( "salesVisits" )
////                  ->where( "sale_id", $saleDetails[ "sale_id" ] ) as $saleVisit )
////        $formFieldValues[ "visits_ids" ][] = $saleVisit[ "visit_id" ];
////
////    foreach ( $API->DB->from( "salesServices" )
////                  ->where( "sale_id", $saleDetails[ "sale_id" ] ) as $saleService )
////        $formFieldValues[ "pay_object" ][] = $saleService[ "service_id" ];
//
//} else {
//
//    $formFieldValues = [
//        "sun_cash" => $paymentSummary,
//        "pay_method" => "cash",
//        "store_id" => (int) $userDetails[ "store_id" ],
//        "client_id" => $clients[ "client_id" ],
//        "online_receipt" => true
//    ];
//
//    $formFieldValues[ "summary" ] = $paymentSummary;
//
////    foreach ( $API->DB->from( "visits_services" )
////                  ->where( "visit_id", $pageDetail[ "row_id" ] ) as $visitService )
////        $formFieldValues[ "pay_object" ][] = $visitService[ "service_id" ];
//
//} // if. $visitDetails[ "is_payed" ] == "Y" || ( $saleDetails && $saleDetails[ "status" ] != "error" )
//
//$formFieldValues[ "pay_type" ] = "sell";
