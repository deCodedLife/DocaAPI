<?php
///**
// * Сформированный список
// */
//$returnVisits = [];
//
//foreach ( $response[ "data" ] as $visit ) {
//
//    $user = $API->DB->from( "users" )
//        ->where( "id", $visit[ "user_id" ][ "value" ])
//        ->limit( 1 )
//        ->fetch();
//
//
//    $saleVisits = $API->DB->from( "saleVisits" )
//        ->where( "id", $visit[ "user_id" ][ "value" ] );
//
//    if ( $saleVisits ) {
//
//        foreach ( $saleVisits as $saleVisit ) {
//
//            $saleList = $API->DB->from( "salesList" )
//                ->where( "id", $saleVisit[ "sale_id" ] );
//
//            foreach ( $saleList as $sale ) {
//
//                if ( $sale[ "sum_entity" ] > 0 ) {
//
//                    $salePayMethod = $API->DB->from( "salePayMethods" )
//                        ->where( "article", "legalEntity" )
//                        ->limit( 1 )
//                        ->fetch();
//
//                    $visit[ "paymentMethod" ] = [
//
//                        "title" => $salePayMethod[ "title" ],
//                        "value" => $salePayMethod[ "id" ]
//
//                    ];
//
//                } else {
//
//                    $salePayMethod = $API->DB->from( "salePayMethods" )
//                        ->where( "article", $sale[ "pay_method" ] )
//                        ->limit( 1 )
//                        ->fetch();
//
//                    $visit[ "paymentMethod" ] = [
//
//                        "title" => $salePayMethod[ "title" ],
//                        "value" => $salePayMethod[ "id" ]
//
//                    ];
//
//                }
//
//            }
//
//        }
//
//    }
//
//    if ( $user[ "first_name" ] ){
//
//        $user[ "first_name" ] =  " " . mb_substr($user[ "first_name" ], 0, 1) . ". ";
//
//    }
//
//    if ( $user[ "patronymic" ] ){
//
//        $user[ "patronymic" ] =  mb_substr($user[ "patronymic" ], 0, 1) . ". ";
//
//    }
//
//    $visit[ "user_id" ] = [
//
//        "title" => $user[ "last_name" ] . $user[ "first_name" ] . $user[ "patronymic" ],
//        "value" => $visit[ "user_id" ][ "value" ]
//
//    ];
//
//    if ( $visit[ "is_payed" ] == "Y" ) {
//
////        $paymentMethod = $API->DB->from( "paymentMethod" )
////            ->where( "id", $visit[ "paymentMethod" ] )
////            ->fetch();
//
//        $visit[ "payed" ] = "Оплачено, ";
//
//    } else $visit[ "payed" ] = "Не оплачено";
//
//
//    $visits_services = $API->DB->from( "visits_services" )
//        ->where( "visit_id", $visit[ "id" ] );
//
//    $visit[ "period" ] = date( 'Y-m-d H:i', strtotime( $visit[ "start_at" ] ) ) . " - " . date( "H:i", strtotime( $visit[ "end_at" ] ) );
//
//    foreach ( $visits_services as $visit_service) {
//
//        $service = $API->DB->from( "services" )
//            ->where( "id", $visit_service[ "service_id" ] )
//            ->limit( 1 )
//            ->fetch();
//
////        $visit[ "services_id" ][] = [
////            "title" => $service[ "title" ],
////            "value" => $service[ "id" ]
////        ];
//
//        $visit[ "category_id" ][] = [
//            "title" => $service[ "category_id" ],
//            "value" => (int)$service[ "category_id" ]
//        ];
//
//    }
//
//    $returnVisits[] = $visit;
//
//}
//
//if ( $requestData->category ) {
//
//    foreach ( $returnVisits as $index => $returnVisit ) {
//
//        $visits_services = $API->DB->from( "visits_services" )
//            ->where( "visit_id", $returnVisit[ "id" ] );
//
//        $service_exists = false;
//
//        foreach ( $visits_services as $visit_service) {
//
//            $service = $API->DB->from( "services" )
//                ->where( "id", $visit_service[ "service_id" ] )
//                ->limit( 1 )
//                ->fetch();
//
//            if ( $service[ "category_id" ] == $requestData->category )
//                $service_exists = true;
//
//        }
//
//        if ( $service_exists == false ) unset( $returnVisits[ $index ] );
//
//    }
//
//}
//
//if ( $requestData->service && !empty( $requestData->service ) ) {
//
//    foreach ( $returnVisits as $index => $returnVisit ) {
//
//        $visits_services = $API->DB->from( "visits_services" )
//            ->where( "visit_id", $returnVisit[ "id" ] );
//
//        $service_exists = false;
//
//        foreach ( $visits_services as $visit_service) {
//
//            if ( in_array( (int)$visit_service[ "service_id" ], $requestData->service  ) )
//                $service_exists = true;
//
//        }
//        if ( $service_exists == false ) unset( $returnVisits[ $index ] );
//
//    }
//
//}
//
//
//$response[ "data" ] = array_values($returnVisits);
//
