<?php

$data = (array) $API->sendRequest(
    "visits",
    "get",
    [ "id" => 502172 ],
    $_SERVER[ "SERVER_NAME" ]
)[ 0 ];

$API->returnResponse( $data );

//$testQuery = $API->DB->from( "users" )
//    ->where( "is_active", 'Y' );
//$ids = [ 1, 2, 120, 121, 123 ];
//
//
//foreach ( $ids as $id ) {
//    $testQuery->where( "id", $id );
//}
//
//foreach ( $testQuery as $user ) {
//    $users[] = $user;
//}

//$API->returnResponse( $users );



//$API->returnResponse( $API::$userDetail->id );

//$API->DB->insertInto( "atolCashboxes" )
//    ->values( [
//        "store_id" => 62,
//        "cashbox_id" => "1_yashlek"
//    ] )
//    ->execute();


//set_time_limit( 0 );


//$visits = $API->DB->from( "visits" );
//
//foreach ( $visits as $visit ) {
//
//    $API->returnResponse( $visit );
//
//    $visitClient = $API->DB->from( "visits_clients" )
//        ->where( "visit_id", $visit[ "id" ] )
//        ->limit( 1 )
//        ->fetch();
//
//    $clientDetail = $API->DB->from( "clients" )
//        ->where( "id", $visitClient[ "client_id" ] )
//        ->limit( 1 )
//        ->fetch();
//
//
//    $API->DB->update( "visits" )
//        ->set( [
//            "advert_id" => $clientDetail[ "advertise_id" ]
//        ] )
//        ->where( [
//            "id" => $visit[ "id" ]
//        ] )
//        ->execute();
//
//
//    sleep( 0.01 );
//
//}


//$oldPassword = "81dc9bdb52d04dc20036dbd8313ed055";
//$newPassword = md5( "840QwertY320" );
//
//$users = $API->DB->update( "users" )
//    ->set( "password", $newPassword )
//    ->where( "not id = ?", 2 )
//    ->execute();


//mysqli_query(
//    $API->DB_connection,
//    "drop table visits_users"
//);
//
//header('X-Accel-Buffering: no');
//header( "Content-Type: text/event-stream" );
//header('Cache-Control: no-store');
//
//session_start();
//ob_end_flush();
//ob_start();
//
//$testData = [
//    "cashboxID" => 200,
//    "task" => [
//        "test" => 100
//    ]
//];
//
//$i = 0;
//
//while ( true ) {
//
//    if ( connection_aborted() ) break;
//    echo json_encode( $testData );
//    echo "\n\n";
//
//    ob_flush();
//    flush();
//
//    sleep( random_int(10, 100) / 100 );
//    break;
//
//}
//
//exit();


//$API->returnResponse( $rules );

// $API->DB->delete( "promotionObjects" )
	// ->where( "id", 121 )
	// ->execute();/

//$activePromitions = $Discounts->GetActiveDiscounts();
//
//$Modifier = new Сashbox\Modifier;
//$Modifier->Items = [ 2 ];
//$Modifier->Type = MODIFIER_TYPES[ 0 ];
//
////$Modifier->Type = MODIFIER_TYPES[ 3 ];

//$API->returnResponse();







//$API->returnResponse( "Nya" );

//mysqli_query(
//    $API->DB_connection,
//    "DROP TABLE servicesGroups"
//);
//exit(200);
//mysqli_query($API->DB_connection, "UPDATE clients set bonuses = 100, deposit = 100");

//mysqli_query($API->DB_connection, "DELETE FROM visits");
//mysqli_query($API->DB_connection, "DELETE FROM sales;");
//mysqli_query($API->DB_connection, "DELETE FROM salesServices;");
//mysqli_query($API->DB_connection, "DELETE FROM salesVisits");

//$API->DB->update( "sales" )
//    ->set( [
//        "status" => "done"
//    ] )
//    ->where( "id", 93 )
//    ->execute();
//
//$API->DB->update( "visits" )
//    ->set( "is_payed", 'Y' )
//    ->where( "id", 246 )
//    ->execute();

//
//$API->DB->delete( "salesServices" )
//    ->where( "id", 22)
//    ->execute();

//$API->DB->update( "salesServices" )
//    ->set( [
//        "status" => "waiting",
//        "pay_type" => "sellReturn"
//    ] )
//    ->where( "id", 40 )
//    ->execute();




//$API->DB->update("sales")
//    ->set