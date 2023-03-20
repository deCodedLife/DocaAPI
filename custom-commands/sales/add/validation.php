<?php


/**
 * Получение детальной информации о клиенте
 */

$clientDetails = $API->DB->from( "clients" )
    ->where( "id", $requestData->client_id )
    ->fetch();

if ( $requestData->deposit_sum > $clientDetails[ "deposit" ] )
    $API->returnResponse( "Недостаточно средств на депозитном счёте клиента", 400 );

if ( $requestData->bonus_sum > $clientDetails[ "bonuses" ] )
    $API->returnResponse( "Недостаточно средств на бонусном счёте клиента", 400 );



/**
 * Проверка корректности суммы оплаты с суммой посещения
 */

$saleSummary = 0;
$paymentsSummary =
    $requestData->deposit_sum +
    $requestData->bonus_sum +
    $requestData->card_sum +
    $requestData->cash_sum;


foreach ( $requestData->visits_ids as $visit_id ) {

    $visitDetails = $API->DB->from( "visits" )
        ->innerJoin( "visits_services ON visits_services.visit_id = visits.id" )
        ->where( "visits.id", $visit_id )
        ->limit( 1 )
        ->fetch();



    $visitPrice = $visitDetails[ "price" ];

    if ( $visitDetails[ "discount_type" ] == "fixed" ) $visitPrice -= $visitDetails[ "discount_value" ];
    if ( $visitDetails[ "discount_type" ] == "percent" ) $visitPrice -= ($visitPrice / 100) * $visitDetails[ "discount_value" ];

    $visitPrice = max( $visitPrice, 0 );
    $saleSummary += $visitPrice;

} // foreach. $requestData->visits_ids as $visit_id



/**
 * Валидация итоговой суммы
 */

if ( $paymentsSummary != $saleSummary )
    $API->returnResponse( "Сумма всех способов оплаты не совпадает с итоговой суммой посещения", 400 );