<?php


//mysqli_query( $API->DB_connection,  "DROP TABLE promotionsServices" );
//mysqli_query( $API->DB_connection,  "DROP TABLE promotionsServicesGroups" );
//mysqli_query( $API->DB_connection,  "DROP TABLE promotionsRequiredServicesGroups" );
//mysqli_query( $API->DB_connection,  "DROP TABLE promotionsRequiredServices" );
//mysqli_query( $API->DB_connection,  "DROP TABLE promotionsExcludedServices" );
//mysqli_query( $API->DB_connection,  "DROP TABLE promotionsClientsGroups" );
//mysqli_query( $API->DB_connection,  "DROP TABLE promotionsObjectsCategories" );

//mysqli_query($API->DB_connection, "DELETE FROM promotions");
//mysqli_query($API->DB_connection, "DELETE FROM promotionObjects");

$API->returnResponse();

require_once $API::$configs[ "paths" ][ "public_app" ] . "/custom-libs/discounts/index.php";

$Discounts = new Сashbox\Discounts;
$activePromitions = $Discounts->GetActiveDiscounts();

$Modifier = new Сashbox\Modifier;
$Modifier->Items = [ 2 ];
$Modifier->Type = MODIFIER_TYPES[ 0 ];

//$Modifier->Type = MODIFIER_TYPES[ 3 ];

$API->returnResponse( $activePromitions );














//$API->returnResponse( "Nya" );

//mysqli_query(
//    $API->DB_connection,
//    "DROP TABLE sales_visits"
//);
//mysqli_query($API->DB_connection, "UPDATE clients set bonuses = 100, deposit = 100");

//mysqli_query($API->DB_connection, "DELETE FROM visits");
//mysqli_query($API->DB_connection, "DELETE FROM salesVisits");
//mysqli_query($API->DB_connection, "DELETE FROM sales;");
//mysqli_query($API->DB_connection, "DELETE FROM salesServices;");

//$API->DB->update( "sales" )
//    ->set( [
//        "status" => "waiting",
//        "pay_type" => "sellReturn"
//    ] )
//    ->where( "id", 41 )
//    ->execute();

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


//$API->DB->update( "visits" )
//    ->set( "is_payed", 'Y' )
//    ->where( "id", 207 )
//    ->execute();

//$API->DB->update("sales")
//    ->set