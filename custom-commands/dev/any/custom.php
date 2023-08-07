<?php

mysqli_query(
    $API->DB_connection,
    "drop table visits_users"
);

$API->returnResponse( "NYAAAAAA" );

header('X-Accel-Buffering: no');
header( "Content-Type: text/event-stream" );
header('Cache-Control: no-store');

session_start();
ob_end_flush();
ob_start();

$testData = [
    "cashboxID" => 200,
    "task" => [
        "test" => 100
    ]
];

$i = 0;

while ( true ) {

    if ( connection_aborted() ) break;
    echo json_encode( $testData );
    echo "\n\n";

    ob_flush();
    flush();

    sleep( random_int(10, 100) / 100 );
    break;

}

exit();



//$publicAppPath = $API::$configs[ "paths" ][ "public_app" ];

//require_once ( $publicAppPath . '/custom-libs/sales/business_logic.php' );
//require_once ( $publicAppPath . "/custom-libs/sales/install.php" );

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

$API->DB->update( "sales" )
    ->set( [
        "status" => "done"
    ] )
    ->where( "id", 93 )
    ->execute();

$API->DB->update( "visits" )
    ->set( "is_payed", 'Y' )
    ->where( "id", 246 )
    ->execute();

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