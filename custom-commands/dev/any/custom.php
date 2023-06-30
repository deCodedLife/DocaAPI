<?php

$publicAppPath = $API::$configs[ "paths" ][ "public_app" ];
//require_once ( $publicAppPath . '/custom-libs/sales/business_logic.php' );

//$newTables = [
//  "salesTypes",
//  "salesMethods",
//  "salesList",
//  "salesProductTypes",
//  "salesProductList"
//];
//
//foreach ( $newTables as $table ) {
//    mysqli_query(
//        $API->DB_connection,
//        "DROP TABLE $table"
//    );
//}

require_once ( $publicAppPath . "/custom-libs/sales/init.php" );

$API->returnResponse();

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