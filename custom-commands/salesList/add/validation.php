<?php


/**
 * Получение детальной информации о клиенте
 */

$clientDetails = $API->DB->from( "clients" )
    ->where( "id", $requestData->client_id )
    ->fetch();

$clientEntity = $API->DB->from( "legal_entity_clients" )
    ->where( "client_id", $requestData->client_id )
    ->fetch();

$entityDetails = $API->DB->from( "legal_entities" )
    ->where( "id", $clientEntity[ "legal_entity_id" ] )
    ->fetch();

if ( $requestData->sum_entity > $entityDetails[ "balance" ] )
    $API->returnResponse(  "Недостаточно средств на счёте юридического лица", 400 );

if ( $requestData->sum_deposit > $clientDetails[ "deposit" ] )
    $API->returnResponse( "Недостаточно средств на депозитном счёте клиента", 400 );

if ( $requestData->sum_bonus > $clientDetails[ "bonuses" ] )
    $API->returnResponse( "Недостаточно средств на бонусном счёте клиента", 400 );


/**
 * Проверка на день оплаты.
 * Оплата совершается только день в день
 */
foreach ( $requestData->visits_ids ?? [] as $visit ) {

    $object = $requestData->object ?? "visits";

    $visitDetails = $API->DB->from( $object )
        ->where( "id", $visit )
        ->fetch();

    $visitDate = explode( " ", $visitDetails[ "start_at" ] );

    if ( $visitDate[ 0 ] != date( "Y-m-d" ) )
        $API->returnResponse( "Оплаты совершаются только день в день", 500 );

} // foreach ( $requestData->visits_ids ?? [] as $visit )



/**
 * Проверка корректности суммы оплаты с суммой посещения
 */

$saleSummary = 0;
$paymentsSummary =
    $requestData->sum_deposit +
    $requestData->sum_bonus +
    $requestData->sum_card +
    $requestData->sum_cash +
    $requestData->sum_entity;



/**
 * Валидация итоговой суммы
 */

if ( $paymentsSummary != $requestData->summary )
    $API->returnResponse( "Сумма всех способов оплаты не совпадает с итоговой суммой посещения", 400 );
