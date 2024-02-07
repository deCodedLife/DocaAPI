<?php
//
//global $API, $requestData;
//
//
//$sales_summary = 0;
//$sales_count = 0;
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
//$user_sales = mysqli_query(
//    $API->DB_connection,
//    "SELECT id
//        FROM   salesList
//        WHERE  employee_id = $requestData->user_id
//               AND created_at > '$start_at'
//               AND created_at < '$end_at'
//               AND status = 'done'
//               AND ( action = 'sell' OR action = 'sellReturn' ) "
//);
//
//
///**
// * Получаем количество и стоимость услуг
// */
//foreach ( $user_sales as $sale ) {
//
//    $sale = $API->DB->from( "salesList" )
//        ->where( "id", $sale[ "id" ] )
//        ->fetch();
//
//    if ( !$sale || !$sale[ "action" ] ) continue;
//
//    $summary = mysqli_fetch_array(
//        mysqli_query(
//            $API->DB_connection,
//            "SELECT Sum(( amount * cost ))
//            FROM   salesProductsList
//            WHERE  sale_id = {$sale[ "id" ]}
//                   AND type = 'service'"
//        )
//    )[0];
//
//    $count = mysqli_fetch_array(
//        mysqli_query(
//            $API->DB_connection,
//            "SELECT Sum(amount)
//            FROM   salesProductsList
//            WHERE  sale_id = {$sale[ "id" ]}
//                   AND type = 'service'; "
//        )
//    )[0];
//
//
//    if ( $sale[ "action" ] == "sellReturn" ) {
//
//        $summary = -$summary;
//        $count = -$count;
//
//    } // if ( $sale[ "action" ] == "sellReturn" )
//
//    $sales_summary += $summary;
//    $sales_count += $count;
//
//
//} // foreach ( $user_sales as $sale )