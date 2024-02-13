<?php

//ini_set( 'display_errors', 1 );
$publicAppPath = $API::$configs[ "paths" ][ "public_app" ];


/**
 * Проверка корректности введённых значений
 */
if ( !isset( $requestData->employee_id ) ) $API->returnResponse( "Ошибка. Не указан сотрудник", 500 );
if ( !isset( $requestData->products ) ) $API->returnResponse( "Ошибка. Продукты не подгрузились", 500 );

if ( $requestData->sum_cash != 0 && $requestData->pay_method != "cash" )
    $API->returnResponse( "Ошибка. Несовпадение типа и значений оплаты", 500 );

if ( $requestData->sum_card != 0 && $requestData->pay_method != "card" )
    $API->returnResponse( "Ошибка. Несовпадение типа и значений оплаты", 500 );

foreach ( $requestData->products as $product ) {

    if (
        !isset( $product->title ) ||
        !isset( $product->product_id )
    ) $API->returnResponse( "Ошибка. Продукты сформированы некорректно", 500 );

}

$userDetail = $API->DB->from( "users_stores" )
    ->innerJoin( "users on users.id = users_stores.user_id" )
    ->where( "users.id", $API::$userDetail->id );

foreach ( $userDetail as $item ) {

    $storeID = $item[ "store_id" ];
    break;

}
if ( ( $storeID ?? -1 ) != $requestData->store_id ) $API->returnResponse( "Ошибка. Филиал не корректный", 500 );


if ( $requestData->action == "sell" )
    require ( $publicAppPath . "/custom-commands/salesList/add/validation.php" );


/**
 * Создание транзакции
 */
require ( $publicAppPath . "/custom-commands/salesList/add/create-transaction.php" );


$API->returnResponse( true );