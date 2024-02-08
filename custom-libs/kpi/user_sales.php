<?php
//
//global $API, $requestData;
//
//
//$sales_summary = 0;
//$sales_count = 0;
//$sales_ids = [];
//
//
///**
// * Получаем информацию о сотруднике
// */
//$userDetail = $API->DB->from( "users" )
//    ->where( "id", $requestData->user_id )
//    ->fetch();
//
///**
// * Берём продажи непосредственно из таблицы
// */
//
//
//function getSales( $user_id, $start_at, $end_at, $type ): array {
//
//    $user_sales = mysqli_query(
//        $API->DB_connection, "
//        SELECT  salesList.*,
//                salesProductsList.id as _id,
//                salesProductsList.cost,
//                salesProductsList.amount,
//                salesProductsList.sale_id
//        FROM    salesList
//        INNER   JOIN  salesProductsList
//                ON salesProductsList.sale_id = salesList.id
//        WHERE   employee_id = $user_id
//                AND created_at > '$start_at'
//                AND created_at < '$end_at'
//                AND status = 'done'
//                AND action = $type"
//    );
//
//    foreach ( $user_sales as $sale ) $sales_ids[ $sale[ "id" ] ] = $sale;
//    return $sales_ids ?? [];
//
//}
//
//function getSummary( $sales_ids ): float {
//
//    $sales_ids = join( ",", array_keys( $sales_ids ?? [] ) );
//    return floatval( mysqli_fetch_array(
//        mysqli_query(
//            $API->DB_connection,
//            "SELECT Sum( (amount * cost) - discount )
//        FROM   salesProductsList
//        WHERE  sale_id IN ($sales_ids) AND type = 'service'"
//        )
//    )[0] ?? 0 );
//
//}
//
//getSummary( getSales( $requestData->user_id, $start_at, $end_at, "sell" ) );
//getSummary( getSales( $requestData->user_id, $start_at, $end_at, "sell" ) );
//
//
//$sales_ids = getSales( $requestData->user_id, $start_at, $end_at, "sellReturn" );
//
//
//
//
//
//
///**
// * Получаем количество и стоимость услуг
// */
//foreach ( $user_sales as $sale ) {
//
//    $API->returnResponse($sale);
//
//
//
//}
//
//
////
////    $count = mysqli_fetch_array(
////        mysqli_query(
////            $API->DB_connection,
////            "SELECT Sum(amount)
////            FROM   salesProductsList
////            WHERE  sale_id = {$sale[ "id" ]}
////                   AND type = 'service'; "
////        )
////    )[0];
////
////
////    if ( $sale[ "action" ] == "sellReturn" ) {
////
////        $summary = -$summary;
////        $count = -$count;
////
////    } // if ( $sale[ "action" ] == "sellReturn" )
////
////    $sales_summary += $summary;
////    $sales_count += $count;
////
////
////} // foreach ( $user_sales as $sale )