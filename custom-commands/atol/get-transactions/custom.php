<?php

require_once $API::$configs[ "paths" ][ "public_app" ] . "/custom-libs/atol/index.php";
ini_set( 'serialize_precision', -1 );



$AtolReciept = new Сashbox\Atol;
$AtolReciept->operator = new Сashbox\IOperator;
$AtolReciept->operator->name = "Миннахматовна Э. Ц.";
$AtolReciept->operator->vatin = "123654789507";




$cashboxStore = $API->DB->from( "atolCashboxes" )
    ->where( "cashbox_id", $requestData->cashbox_id )
    ->limit( 1 )
    ->fetch()[ "store_id" ];



$processedSale = $API->DB->from( "sales" )
    ->where( [
        "store_id", $cashboxStore,
        "status" => "waiting",
        "is_active" => "Y",
        "created_at > ?" => date('Y-m-d') . " 00:00:00"
    ] )
    ->orderBy( "id DESC" )
    ->limit( 1 )
    ->fetch();
if ( !$processedSale ) $API->returnResponse( [] );



if ( $processedSale[ "online_receipt" ] === "Y" ) {

    $clientDetails = $API->DB->from( "clients" )
        ->where( "id", $processedSale[ "client_id" ] )
        ->fetch();

    $AtolReciept->clientInfo = [
        "name" => $clientDetails[ "last_name" ] . " " . $clientDetails[ "first_name" ] . " " . $clientDetails[ "patronymic" ],
        "emailOrPhone" => $clientDetails[ "email" ],
    ];

    $AtolReciept->electronically = true;

}

$paymentSales = [];
$paymentSales[] = $processedSale;

$saleVisits = $API->DB->from( "salesVisits" )
    ->where( "sale_id", $processedSale[ "id" ] );

$servicesPrice = 0;
$difference = 0;
$services = [];

foreach ( $API->DB->from( "salesServices" )->where( "sale_id", $processedSale[ "id" ] ) as $service ) {

    $details = $API->DB->from( "services" )
        ->where( "id", $service[ "service_id" ] )
        ->fetch();

    $details[ "price" ] = $service[ "price" ];
    $difference += $service[ "price" ];

    $services[] = $details;

}

$summary = $processedSale[ "summary" ] - $processedSale[ "bonus_sum" ];
$discountPerProduct = $summary / $difference;


foreach ( $services as $service ) {

    $paymentObject = new Сashbox\IProduct;
    $paymentObject->name = $service[ "title" ];
    $paymentObject->paymentObject = PAYMENT_OBJECTS[ 4 ];
    $paymentObject->quantity = 1;
    $paymentObject->price = round( $service[ "price" ] * $discountPerProduct, 2 );
    $paymentObject->piece = true;
    $paymentObject->tax = [ "type" => "none" ];
    $paymentObject->type = "position";
    $paymentObject->amount = round( $paymentObject->price * $paymentObject->quantity, 2 );

    $AtolReciept->items[] = $paymentObject;

}



if ( $processedSale[ "deposit_sum" ] ) $AtolReciept->payments[] = new Сashbox\IPayment( "2", $processedSale[ "deposit_sum" ] );
if ( $processedSale[ "card_sum" ] ) $AtolReciept->payments[] = new Сashbox\IPayment( "1", $processedSale[ "card_sum" ] );
if ( $processedSale[ "cash_sum" ] ) $AtolReciept->payments[] = new Сashbox\IPayment( "cash", $processedSale[ "cash_sum" ] );



$AtolReciept->taxationType = "usnIncomeOutcome";
$AtolReciept->uuid = $processedSale[ "id" ];

$AtolReciept->sales = [ (int) $processedSale[ "id" ] ];
$AtolReciept->sale_type = $processedSale[ "pay_type" ];

$API->returnResponse( $AtolReciept->GetReciept() );